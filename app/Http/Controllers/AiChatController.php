<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;
use App\Models\Product;
use App\Models\Setting;

class AiChatController extends Controller
{
    private const GEMINI_API_URL = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent';

    /**
     * Test API connection
     */
    public function testConnection(): JsonResponse
    {
        $apiKeys = $this->getApiKeys();

        if (empty($apiKeys)) {
            return response()->json([
                'status' => 'error',
                'message' => 'API keys not configured'
            ]);
        }

        try {
            $testPayload = [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [['text' => 'Hello, this is a test message.']]
                    ]
                ]
            ];

            // Test với API key đầu tiên
            $apiKey = $apiKeys[0];
            $response = Http::timeout(5)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post(self::GEMINI_API_URL . '?key=' . $apiKey, $testPayload);

            return response()->json([
                'status' => $response->successful() ? 'success' : 'error',
                'http_status' => $response->status(),
                'response' => $response->json(),
                'api_keys_count' => count($apiKeys),
                'tested_key_prefix' => substr($apiKey, 0, 10) . '...'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Xử lý tin nhắn chat và gọi Gemini API
     */
    public function sendMessage(Request $request): JsonResponse
    {
        // Rate limiting - giới hạn 10 requests/phút cho mỗi IP
        $key = 'ai-chat:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return response()->json([
                'error' => 'Quá nhiều yêu cầu. Vui lòng thử lại sau.'
            ], 429);
        }
        
        RateLimiter::hit($key, 60);
        
        // Validate input
        $request->validate([
            'message' => 'required|string|max:1000',
            'conversation_history' => 'array|max:20' // Giới hạn lịch sử hội thoại
        ]);
        
        $userMessage = $request->input('message');
        $conversationHistory = $request->input('conversation_history', []);
        
        try {
            // Tạo system prompt cho ThanShoes
            $systemPrompt = $this->getSystemPrompt();
            
            // Chuẩn bị payload cho Gemini API với thông tin sản phẩm bổ sung
            $additionalContext = $this->getAdditionalProductContext($userMessage);
            $payload = $this->buildGeminiPayload($systemPrompt, $conversationHistory, $userMessage, $additionalContext);
            
            // Gọi Gemini API với retry mechanism và load balancing
            $apiKeys = $this->getApiKeys();

            if (empty($apiKeys)) {
                Log::error('Gemini API keys not configured');
                return response()->json([
                    'error' => 'Dịch vụ AI chưa được cấu hình. Vui lòng liên hệ quản trị viên.'
                ], 500);
            }

            Log::info('Starting AI chat request', [
                'api_keys_count' => count($apiKeys),
                'user_message_length' => strlen($userMessage)
            ]);

            $response = $this->callGeminiApiWithLoadBalancing($apiKeys, $payload);

            if (!$response) {
                Log::error('All API keys failed');
                return response()->json([
                    'error' => 'Không thể kết nối đến dịch vụ AI. Vui lòng thử lại sau.'
                ], 500);
            }

            if (!$response->successful()) {
                return $this->handleApiError($response, 'multiple_keys', $payload);
            }
            
            $responseData = $response->json();

            // Log response để debug (chỉ log khi có lỗi)
            if (!isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                Log::info('Gemini API Response Structure', [
                    'has_candidates' => isset($responseData['candidates']),
                    'candidates_count' => isset($responseData['candidates']) ? count($responseData['candidates']) : 0,
                    'first_candidate_keys' => isset($responseData['candidates'][0]) ? array_keys($responseData['candidates'][0]) : [],
                    'response_keys' => array_keys($responseData)
                ]);
            }

            // Trích xuất phản hồi từ Gemini
            $aiResponse = $this->extractGeminiResponse($responseData);

            if (!$aiResponse) {
                Log::error('Failed to extract AI response', [
                    'response_structure' => array_keys($responseData),
                    'candidates_available' => isset($responseData['candidates']),
                    'error_in_response' => isset($responseData['error'])
                ]);

                return response()->json([
                    'error' => 'AI đang bận, vui lòng thử lại sau ít phút.'
                ], 500);
            }

            // Cải thiện response để đảm bảo có link cụ thể
            $aiResponse = $this->enhanceResponseWithLinks($aiResponse, $userMessage);

            // Clean up markdown links để tránh lỗi format
            $aiResponse = $this->cleanMarkdownLinks($aiResponse);
            
            return response()->json([
                'response' => $aiResponse,
                'success' => true
            ]);
            
        } catch (\Exception $e) {
            Log::error('AI Chat Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Đã xảy ra lỗi không mong muốn. Vui lòng thử lại sau.'
            ], 500);
        }
    }
    
    /**
     * Tạo system prompt cho ThanShoes với thông tin chi tiết về website
     */
    private function getSystemPrompt(): string
    {
        // Lấy thông tin từ Setting model và sản phẩm
        $settingInfo = $this->getSettingInfo();
        $productInfo = $this->getProductInfo();
        $baseUrl = url(''); // Lấy base URL động

        return "Bạn là sales AI của ThanShoes - 33k followers Shopee, 34.3k đánh giá 4.9⭐.

MISSION: Bán hàng nhanh, ngắn gọn, hiệu quả.

SOCIAL PROOF: 33k followers + 34.3k reviews = Uy tín vượt trội!

KEY POINTS:
- Website giá tốt hơn Shopee (không phí nền tảng)
- COD toàn quốc, đổi trả 7 ngày
- Link sản phẩm: {$baseUrl}/catfilter
- Checkout: {$baseUrl}/checkout

{$settingInfo}

THÔNG TIN SẢN PHẨM HIỆN CÓ:
{$productInfo}

CÁC TRANG QUAN TRỌNG TRÊN WEBSITE:
1. Trang chủ: http://127.0.0.1:8000/
2. Tất cả sản phẩm: http://127.0.0.1:8000/catfilter
3. Tìm sản phẩm theo loại: http://127.0.0.1:8000/catfilter?type=[tên_loại]
4. Tìm sản phẩm theo thương hiệu: http://127.0.0.1:8000/catfilter?brand=[tên_thương_hiệu]
5. Tất vớ, dép: http://127.0.0.1:8000/catfilter?tatvo=true
6. Phụ kiện: http://127.0.0.1:8000/catfilter?phukien=true
7. Trang thanh toán: http://127.0.0.1:8000/checkout
8. Chi tiết sản phẩm: http://127.0.0.1:8000/product/[slug-sản-phẩm]

RULES:
- Trả lời TỐI ĐA 2-3 câu
- LUÔN có link sản phẩm hoặc checkout
- Tập trung CONVERSION, không giải thích dài
- Format: Câu trả lời ngắn + Link thuần + CTA
- QUAN TRỌNG: Chỉ viết link thuần, KHÔNG dùng markdown [text](url)

RESPONSES:
- Giày thể thao → {$baseUrl}/catfilter?type=Giày thể thao
- Nike/Adidas → {$baseUrl}/catfilter?brand=[brand]
- Mua hàng → {$baseUrl}/checkout
- Tất cả → {$baseUrl}/catfilter

STYLE: Ngắn gọn, thân thiện, sales-oriented.
VÍ DỤ ĐÚNG: 'Giày Nike chất lượng 4.9⭐!
{$baseUrl}/catfilter?brand=Nike
Đặt ngay nhé!'

VÍ DỤ SAI: 'Giày Nike [xem tại đây]({$baseUrl}/catfilter?brand=Nike)' - TUYỆT ĐỐI KHÔNG làm thế này!";
    }
    
    /**
     * Lấy thông tin sản phẩm bổ sung dựa trên câu hỏi của user
     */
    private function getAdditionalProductContext(string $userMessage): string
    {
        $userMessageLower = strtolower($userMessage);
        $context = "";
        $baseUrl = url(''); // Lấy base URL động

        // Tìm sản phẩm cụ thể nếu user hỏi về loại sản phẩm
        if (strpos($userMessageLower, 'giày') !== false) {
            $products = Product::where('name', 'like', '%giày%')
                ->with(['variants'])
                ->take(10)
                ->get();

            if ($products->count() > 0) {
                $context .= "\nSẢN PHẨM GIÀY HIỆN CÓ (Đã được hàng nghìn khách hàng tin tưởng trên Shopee):\n";
                foreach ($products as $product) {
                    $context .= "- {$product->name} - Link: {$baseUrl}/product/{$product->slug}\n";
                }
                $context .= "\n💡 Lưu ý: Giá trên website tốt hơn Shopee do không có phí nền tảng!\n";
            }
        }

        // Tìm theo thương hiệu
        $brands = ['nike', 'adidas', 'converse', 'vans', 'puma'];
        foreach ($brands as $brand) {
            if (strpos($userMessageLower, $brand) !== false) {
                $products = Product::where('brand', 'like', '%' . $brand . '%')
                    ->with(['variants'])
                    ->take(5)
                    ->get();

                if ($products->count() > 0) {
                    $context .= "\nSẢN PHẨM THƯƠNG HIỆU " . strtoupper($brand) . " (Chất lượng đã được khẳng định qua 4.9 sao trên Shopee):\n";
                    foreach ($products as $product) {
                        $context .= "- {$product->name} - Link: {$baseUrl}/product/{$product->slug}\n";
                    }
                    $context .= "\n🎯 Mua trên website = Giá tốt hơn + Dịch vụ trực tiếp!\n";
                }
                break;
            }
        }

        return $context;
    }

    /**
     * Xây dựng payload cho Gemini API
     */
    private function buildGeminiPayload(string $systemPrompt, array $conversationHistory, string $userMessage, string $additionalContext = ''): array
    {
        $contents = [];

        // Kết hợp system prompt với tin nhắn đầu tiên và thông tin bổ sung
        $firstMessage = $systemPrompt . $additionalContext . "\n\nTin nhắn từ khách hàng: " . $userMessage;

        if (empty($conversationHistory)) {
            // Nếu chưa có lịch sử, chỉ gửi system prompt + tin nhắn hiện tại
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $firstMessage]]
            ];
        } else {
            // Nếu có lịch sử, thêm system prompt vào đầu
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $systemPrompt]]
            ];

            $contents[] = [
                'role' => 'model',
                'parts' => [['text' => 'Tôi hiểu. Tôi sẽ hỗ trợ bạn với vai trò là trợ lý AI của ThanShoes.']]
            ];

            // Thêm lịch sử hội thoại (chỉ lấy 8 tin nhắn gần nhất để tránh payload quá lớn)
            $recentHistory = array_slice($conversationHistory, -8);
            foreach ($recentHistory as $message) {
                $contents[] = [
                    'role' => $message['is_user'] ? 'user' : 'model',
                    'parts' => [['text' => $message['message']]]
                ];
            }

            // Thêm tin nhắn hiện tại
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $userMessage]]
            ];
        }

        return [
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.7,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 1024,
                'stopSequences' => []
            ],
            'safetySettings' => [
                [
                    'category' => 'HARM_CATEGORY_HARASSMENT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ],
                [
                    'category' => 'HARM_CATEGORY_HATE_SPEECH',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ],
                [
                    'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ],
                [
                    'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ]
            ]
        ];
    }
    
    /**
     * Trích xuất phản hồi từ Gemini API response
     */
    private function extractGeminiResponse(array $responseData): ?string
    {
        // Kiểm tra nếu có error trong response
        if (isset($responseData['error'])) {
            Log::error('Gemini API returned error', ['error' => $responseData['error']]);
            return null;
        }

        // Kiểm tra nếu không có candidates
        if (!isset($responseData['candidates']) || empty($responseData['candidates'])) {
            Log::warning('No candidates in Gemini response', ['response' => $responseData]);
            return null;
        }

        $candidate = $responseData['candidates'][0];

        // Kiểm tra nếu bị block bởi safety filters
        if (isset($candidate['finishReason'])) {
            $finishReason = $candidate['finishReason'];

            if ($finishReason === 'SAFETY') {
                return 'Xin lỗi, tôi không thể trả lời câu hỏi này do chính sách an toàn. Vui lòng thử câu hỏi khác về sản phẩm giày dép.';
            }

            if (in_array($finishReason, ['RECITATION', 'OTHER'])) {
                Log::warning('Gemini blocked response', ['reason' => $finishReason]);
                return null;
            }
        }

        // Kiểm tra cấu trúc response chính
        if (isset($candidate['content']['parts'][0]['text'])) {
            $text = trim($candidate['content']['parts'][0]['text']);
            return !empty($text) ? $text : null;
        }

        // Kiểm tra cấu trúc khác (fallback)
        if (isset($candidate['output'])) {
            $text = trim($candidate['output']);
            return !empty($text) ? $text : null;
        }

        // Kiểm tra nếu có parts nhưng không có text
        if (isset($candidate['content']['parts']) && is_array($candidate['content']['parts'])) {
            foreach ($candidate['content']['parts'] as $part) {
                if (isset($part['text']) && !empty(trim($part['text']))) {
                    return trim($part['text']);
                }
            }
        }

        Log::warning('Could not extract text from Gemini response', [
            'candidate_keys' => array_keys($candidate),
            'has_content' => isset($candidate['content']),
            'content_keys' => isset($candidate['content']) ? array_keys($candidate['content']) : []
        ]);

        return null;
    }

    /**
     * Lấy thông tin từ Setting model
     */
    private function getSettingInfo(): string
    {
        return Cache::remember('ai_setting_info', 1800, function () { // Cache 30 phút
            $setting = Setting::first();

            if (!$setting) {
                return "THÔNG TIN LIÊN HỆ: Chưa cấu hình";
            }

            $info = "THÔNG TIN LIÊN HỆ VÀ CỬA HÀNG:\n";

            if ($setting->app_name) {
                $info .= "- Tên cửa hàng: {$setting->app_name}\n";
            }

            if ($setting->slogan) {
                $info .= "- Slogan: {$setting->slogan}\n";
            }

            if ($setting->phone) {
                $info .= "- Số điện thoại: {$setting->phone}\n";
            }

            if ($setting->email) {
                $info .= "- Email: {$setting->email}\n";
            }

            if ($setting->address) {
                $info .= "- Địa chỉ: {$setting->address}\n";
            }

            if ($setting->zalo) {
                $info .= "- Zalo: {$setting->zalo}\n";
            }

            if ($setting->messenger) {
                $info .= "- Messenger: {$setting->messenger}\n";
            }

            if ($setting->facebook) {
                $info .= "- Facebook: {$setting->facebook}\n";
            }

            // Thông tin thanh toán
            if ($setting->bank_name && $setting->bank_number && $setting->bank_account_name) {
                $info .= "\nTHÔNG TIN THANH TOÁN:\n";
                $info .= "- Ngân hàng: {$setting->bank_name}\n";
                $info .= "- Số tài khoản: {$setting->bank_number}\n";
                $info .= "- Chủ tài khoản: {$setting->bank_account_name}\n";
            }

            return $info;
        });
    }

    /**
     * Lấy thông tin sản phẩm để đưa vào system prompt
     */
    private function getProductInfo(): string
    {
        $baseUrl = url(''); // Lấy base URL động

        return Cache::remember('ai_product_info_' . md5($baseUrl), 3600, function () use ($baseUrl) {
            $products = Product::with(['variants'])
                ->where('name', 'not like', '%test%')
                ->take(50) // Lấy 50 sản phẩm đại diện
                ->get();

            $brands = $products->pluck('brand')->filter()->unique()->sort()->values();
            $types = $products->pluck('type')->filter()->unique()->sort()->values();

            $productInfo = "THƯƠNG HIỆU CÓ SẴN: " . $brands->implode(', ') . "\n\n";
            $productInfo .= "LOẠI SẢN PHẨM CÓ SẴN: " . $types->implode(', ') . "\n\n";

            $productInfo .= "MỘT SỐ SẢN PHẨM TIÊU BIỂU:\n";
            foreach ($products->take(20) as $product) {
                $productInfo .= "- {$product->name} ({$product->type}) - Link: {$baseUrl}/product/{$product->slug}\n";
            }

            return $productInfo;
        });
    }

    /**
     * Cải thiện response của AI để đảm bảo có link cụ thể
     */
    private function enhanceResponseWithLinks(string $response, string $userMessage): string
    {
        $baseUrl = url(''); // Lấy base URL động

        // Nếu response đã có link thì không cần xử lý thêm
        if (strpos($response, $baseUrl) !== false) {
            return $response;
        }

        $enhancedResponse = $response;

        // Phân tích user message để đưa ra link phù hợp
        $userMessageLower = strtolower($userMessage);

        // Mapping các từ khóa với link tương ứng (sử dụng base URL động)
        $linkMappings = [
            // Loại sản phẩm
            'giày thể thao' => $baseUrl . '/catfilter?type=Giày thể thao',
            'giày công sở' => $baseUrl . '/catfilter?type=Giày công sở',
            'giày cao gót' => $baseUrl . '/catfilter?type=Giày cao gót',
            'giày boot' => $baseUrl . '/catfilter?type=Boot',
            'dép' => $baseUrl . '/catfilter?tatvo=true',
            'tất' => $baseUrl . '/catfilter?tatvo=true',
            'vớ' => $baseUrl . '/catfilter?tatvo=true',
            'phụ kiện' => $baseUrl . '/catfilter?phukien=true',

            // Thương hiệu phổ biến
            'nike' => $baseUrl . '/catfilter?brand=Nike',
            'adidas' => $baseUrl . '/catfilter?brand=Adidas',
            'converse' => $baseUrl . '/catfilter?brand=Converse',
            'vans' => $baseUrl . '/catfilter?brand=Vans',

            // Từ khóa chung
            'tất cả sản phẩm' => $baseUrl . '/catfilter',
            'xem sản phẩm' => $baseUrl . '/catfilter',
            'mua hàng' => $baseUrl . '/catfilter',
            'thanh toán' => $baseUrl . '/checkout',
            'giỏ hàng' => $baseUrl . '/checkout',
            'đặt hàng' => $baseUrl . '/checkout',

            // Shopee store
            'shopee' => 'https://shopee.vn/thanshoes99',
            'cửa hàng shopee' => 'https://shopee.vn/thanshoes99',
        ];

        // Tìm link phù hợp nhất
        $suggestedLink = null;
        foreach ($linkMappings as $keyword => $link) {
            if (strpos($userMessageLower, $keyword) !== false) {
                $suggestedLink = $link;
                break;
            }
        }

        // Nếu không tìm thấy link cụ thể, đưa về trang tất cả sản phẩm
        if (!$suggestedLink) {
            if (strpos($userMessageLower, 'giày') !== false ||
                strpos($userMessageLower, 'sản phẩm') !== false ||
                strpos($userMessageLower, 'mua') !== false) {
                $suggestedLink = 'http://127.0.0.1:8000/catfilter';
            }
        }

        // Compact responses cho conversion
        if (strpos($userMessageLower, 'giỏ hàng') !== false ||
            strpos($userMessageLower, 'thanh toán') !== false ||
            strpos($userMessageLower, 'đặt hàng') !== false) {
            $enhancedResponse .= "\n👉 " . $baseUrl . '/checkout' . " - Đặt ngay!";
        }
        elseif (strpos($userMessageLower, 'shopee') !== false) {
            $enhancedResponse .= "\n🏆 Shopee: 33k followers, 34.3k reviews 4.9⭐";
            $enhancedResponse .= "\nhttps://shopee.vn/thanshoes99";
            $enhancedResponse .= "\n💰 Website giá tốt hơn: " . $baseUrl . '/catfilter';
        }
        elseif (strpos($userMessageLower, 'liên hệ') !== false ||
                 strpos($userMessageLower, 'hỗ trợ') !== false) {
            $setting = Setting::first();
            if ($setting && $setting->zalo) {
                $enhancedResponse .= "\n📞 Zalo: {$setting->zalo}";
            }
        }
        elseif ($suggestedLink) {
            $enhancedResponse .= "\n👉 " . $suggestedLink;
        }

        // Fallback CTA
        if (!strpos($enhancedResponse, $baseUrl) &&
            (strpos($userMessageLower, 'giày') !== false || strpos($userMessageLower, 'sản phẩm') !== false)) {
            $enhancedResponse .= "\n🛍️ " . $baseUrl . '/catfilter';
        }

        return $enhancedResponse;
    }

    /**
     * Clean up markdown links để tránh lỗi format
     */
    private function cleanMarkdownLinks(string $response): string
    {
        // Pattern để tìm markdown links: [text](url)
        $pattern = '/\[([^\]]*)\]\(([^)]+)\)/';

        // Replace với chỉ URL thuần
        $cleaned = preg_replace($pattern, '$2', $response);

        // Loại bỏ các ký tự markdown khác có thể gây lỗi
        $cleaned = str_replace(['**', '*', '`'], '', $cleaned);

        // Loại bỏ dấu ngoặc vuông thừa
        $cleaned = str_replace(['[', ']'], '', $cleaned);

        return $cleaned;
    }

    /**
     * Lấy danh sách API keys
     */
    private function getApiKeys(): array
    {
        $keys = [];

        $key1 = config('services.gemini.api_key');
        $key2 = config('services.gemini.api_key_2');
        $key3 = config('services.gemini.api_key_3');

        if ($key1) $keys[] = $key1;
        if ($key2) $keys[] = $key2;
        if ($key3) $keys[] = $key3;

        return $keys;
    }

    /**
     * Gọi Gemini API với load balancing và retry
     */
    private function callGeminiApiWithLoadBalancing(array $apiKeys, array $payload, int $maxRetries = 2)
    {
        $lastException = null;

        // Shuffle keys để load balance
        shuffle($apiKeys);

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            foreach ($apiKeys as $apiKey) {
                try {
                    Log::info("Gemini API attempt {$attempt} with key " . substr($apiKey, 0, 10) . '...');

                    $response = Http::timeout(5) // Giảm xuống 5s
                        ->withHeaders([
                            'Content-Type' => 'application/json',
                        ])
                        ->post(self::GEMINI_API_URL . '?key=' . $apiKey, $payload);

                    // Nếu thành công thì return ngay
                    if ($response->successful()) {
                        return $response;
                    }

                    // Nếu lỗi không retry được (401, 403) thì thử key khác
                    if (!$this->shouldRetry($response)) {
                        Log::warning("API key failed permanently: " . substr($apiKey, 0, 10) . '...', [
                            'status' => $response->status()
                        ]);
                        continue; // Thử key tiếp theo
                    }

                    Log::warning("API key failed temporarily: " . substr($apiKey, 0, 10) . '...', [
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);

                } catch (\Exception $e) {
                    $lastException = $e;
                    Log::warning("API key exception: " . substr($apiKey, 0, 10) . '... - ' . $e->getMessage());
                    continue; // Thử key tiếp theo
                }
            }

            // Delay trước khi retry tất cả keys
            if ($attempt < $maxRetries) {
                sleep(1);
            }
        }

        // Nếu tất cả attempts đều fail
        if ($lastException) {
            throw $lastException;
        }

        return null;
    }



    /**
     * Kiểm tra xem có nên retry không
     */
    private function shouldRetry($response): bool
    {
        if (!$response) return true;

        $status = $response->status();

        // Retry cho các lỗi tạm thời
        return in_array($status, [429, 500, 502, 503, 504]);
    }

    /**
     * Xử lý lỗi API
     */
    private function handleApiError($response, string $keyInfo, array $payload)
    {
        $status = $response->status();
        $body = $response->body();

        Log::error('Gemini API Error', [
            'status' => $status,
            'body' => $body,
            'key_info' => $keyInfo,
            'payload_size' => strlen(json_encode($payload))
        ]);

        $errorMessage = 'Xin lỗi, tôi đang gặp sự cố kỹ thuật. Vui lòng thử lại sau.';

        switch ($status) {
            case 400:
                $errorMessage = 'Yêu cầu không hợp lệ. Vui lòng thử lại với câu hỏi khác.';
                break;
            case 401:
                $errorMessage = 'Dịch vụ AI chưa được cấu hình đúng. Vui lòng liên hệ quản trị viên.';
                break;
            case 403:
                $errorMessage = 'Không có quyền truy cập dịch vụ AI. Vui lòng liên hệ quản trị viên.';
                break;
            case 429:
                $errorMessage = 'Quá nhiều yêu cầu. Vui lòng đợi một chút rồi thử lại.';
                break;
            case 500:
            case 502:
            case 503:
            case 504:
                $errorMessage = 'Dịch vụ AI tạm thời không khả dụng. Vui lòng thử lại sau ít phút.';
                break;
        }

        return response()->json(['error' => $errorMessage], 500);
    }
}
