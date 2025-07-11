<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\JsonResponse;

class AiChatController extends Controller
{
    private const GEMINI_API_URL = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent';

    /**
     * Test API connection
     */
    public function testConnection(): JsonResponse
    {
        $apiKey = config('services.gemini.api_key');

        if (!$apiKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'API key not configured'
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

            $response = Http::timeout(10)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post(self::GEMINI_API_URL . '?key=' . $apiKey, $testPayload);

            return response()->json([
                'status' => $response->successful() ? 'success' : 'error',
                'http_status' => $response->status(),
                'response' => $response->json(),
                'api_key_prefix' => substr($apiKey, 0, 10) . '...'
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
            
            // Chuẩn bị payload cho Gemini API
            $payload = $this->buildGeminiPayload($systemPrompt, $conversationHistory, $userMessage);
            
            // Gọi Gemini API
            $apiKey = config('services.gemini.api_key');

            if (!$apiKey) {
                Log::error('Gemini API key not configured');
                return response()->json([
                    'error' => 'Dịch vụ AI chưa được cấu hình. Vui lòng liên hệ quản trị viên.'
                ], 500);
            }

            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post(self::GEMINI_API_URL . '?key=' . $apiKey, $payload);
            
            if (!$response->successful()) {
                Log::error('Gemini API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'url' => self::GEMINI_API_URL . '?key=' . substr($apiKey, 0, 10) . '...',
                    'payload' => $payload
                ]);

                $errorMessage = 'Xin lỗi, tôi đang gặp sự cố kỹ thuật. Vui lòng thử lại sau.';

                if ($response->status() === 401) {
                    $errorMessage = 'API key không hợp lệ. Vui lòng kiểm tra cấu hình.';
                } elseif ($response->status() === 429) {
                    $errorMessage = 'Đã vượt quá giới hạn API. Vui lòng thử lại sau.';
                }

                return response()->json(['error' => $errorMessage], 500);
            }
            
            $responseData = $response->json();

            // Log response để debug
            Log::info('Gemini API Response', [
                'response' => $responseData
            ]);

            // Trích xuất phản hồi từ Gemini
            $aiResponse = $this->extractGeminiResponse($responseData);

            if (!$aiResponse) {
                Log::error('Failed to extract AI response', [
                    'response_data' => $responseData
                ]);

                return response()->json([
                    'error' => 'Không thể xử lý phản hồi từ AI. Vui lòng thử lại.'
                ], 500);
            }
            
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
     * Tạo system prompt cho ThanShoes
     */
    private function getSystemPrompt(): string
    {
        return "Bạn là trợ lý AI của ThanShoes - cửa hàng giày dép trực tuyến hàng đầu Việt Nam. 

Thông tin về ThanShoes:
- Chuyên bán giày dép chất lượng cao với đa dạng mẫu mã
- Có các dòng sản phẩm: giày thể thao, giày công sở, dép, sandal
- Cam kết chất lượng và dịch vụ khách hàng tốt nhất
- Hỗ trợ đổi trả trong 7 ngày
- Giao hàng toàn quốc

Nhiệm vụ của bạn:
1. Tư vấn sản phẩm giày dép phù hợp với nhu cầu khách hàng
2. Giải đáp thắc mắc về sản phẩm, chính sách, dịch vụ
3. Hướng dẫn khách hàng mua sắm trên website
4. Luôn thân thiện, chuyên nghiệp và hữu ích

Lưu ý:
- Trả lời bằng tiếng Việt
- Giữ câu trả lời ngắn gọn, dễ hiểu
- Khuyến khích khách hàng xem sản phẩm trên website
- Nếu không biết thông tin cụ thể, hãy khuyên khách hàng liên hệ trực tiếp";
    }
    
    /**
     * Xây dựng payload cho Gemini API
     */
    private function buildGeminiPayload(string $systemPrompt, array $conversationHistory, string $userMessage): array
    {
        $contents = [];

        // Kết hợp system prompt với tin nhắn đầu tiên
        $firstMessage = $systemPrompt . "\n\nTin nhắn từ khách hàng: " . $userMessage;

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
        // Kiểm tra các cấu trúc response có thể có
        if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
            return trim($responseData['candidates'][0]['content']['parts'][0]['text']);
        }

        // Kiểm tra cấu trúc khác
        if (isset($responseData['candidates'][0]['output'])) {
            return trim($responseData['candidates'][0]['output']);
        }

        // Kiểm tra nếu có error trong response
        if (isset($responseData['error'])) {
            Log::error('Gemini API returned error', ['error' => $responseData['error']]);
            return null;
        }

        // Kiểm tra nếu bị block bởi safety filters
        if (isset($responseData['candidates'][0]['finishReason']) &&
            $responseData['candidates'][0]['finishReason'] === 'SAFETY') {
            return 'Xin lỗi, tôi không thể trả lời câu hỏi này do chính sách an toàn. Vui lòng thử câu hỏi khác.';
        }

        return null;
    }
}
