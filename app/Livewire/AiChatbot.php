<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Http\Controllers\AiChatController;

class AiChatbot extends Component
{
    public $isOpen = true; // Mở mặc định
    public $message = '';
    public $messages = [];
    public $isLoading = false;
    public $sessionId;
    
    public function mount()
    {
        // Tạo session ID unique cho mỗi phiên chat
        $this->sessionId = session()->getId() . '_' . Str::random(8);
        
        // Tin nhắn chào mừng với social proof mạnh
        $this->messages = [
            [
                'message' => '👋 Chào bạn! ThanShoes
🏆 33k followers Shopee
⭐ 34.3k đánh giá 4.9/5 sao
💰 Website giá tốt hơn - Tư vấn ngay?',
                'is_user' => false,
                'timestamp' => now()->format('H:i')
            ]
        ];
    }
    
    public function toggleChat()
    {
        $this->isOpen = !$this->isOpen;

        // Nếu đang loading và user đóng chat, cancel request
        if (!$this->isOpen && $this->isLoading) {
            $this->isLoading = false;
            $this->message = '';

            // Thêm thông báo
            $this->messages[] = [
                'message' => '⏹️ Đã hủy yêu cầu. Bạn có thể tiếp tục chat bất cứ lúc nào!',
                'is_user' => false,
                'timestamp' => now()->format('H:i'),
                'is_error' => false
            ];
        }

        // Dispatch event để scroll xuống cuối khi mở chat
        if ($this->isOpen) {
            $this->dispatch('chat-opened');
        }
    }

    #[On('open-ai-chat')]
    public function openChat()
    {
        $this->isOpen = true;
        $this->dispatch('chat-opened');
    }
    
    public function sendMessage()
    {
        if (empty(trim($this->message))) {
            return;
        }
        
        $userMessage = trim($this->message);
        
        // Thêm tin nhắn của user vào danh sách
        $this->messages[] = [
            'message' => $userMessage,
            'is_user' => true,
            'timestamp' => now()->format('H:i')
        ];
        
        // Reset input và set loading state
        $this->message = '';
        $this->isLoading = true;
        
        // Dispatch event để scroll xuống cuối
        $this->dispatch('message-sent');
        
        // Gọi API để lấy phản hồi từ AI
        $this->getAiResponse($userMessage);
    }
    
    private function getAiResponse($userMessage)
    {
        try {
            // Chuẩn bị lịch sử hội thoại (chỉ lấy 8 tin nhắn gần nhất để tránh payload quá lớn)
            $conversationHistory = collect($this->messages)
                ->take(-8)
                ->filter(function ($msg) {
                    // Loại bỏ tin nhắn chào mừng và tin nhắn hiện tại
                    return !str_contains($msg['message'], 'Xin chào! Tôi là trợ lý AI của ThanShoes');
                })
                ->map(function ($msg) {
                    return [
                        'message' => $msg['message'],
                        'is_user' => $msg['is_user']
                    ];
                })
                ->values()
                ->toArray();

            // Tạo fake request để gọi controller
            $request = new \Illuminate\Http\Request();
            $request->merge([
                'message' => $userMessage,
                'conversation_history' => $conversationHistory
            ]);

            // Gọi trực tiếp controller
            $controller = new AiChatController();
            $response = $controller->sendMessage($request);

            // Lấy data từ response
            $responseData = $response->getData(true);

            if ($response->getStatusCode() === 200 && isset($responseData['response'])) {
                // Thêm phản hồi của AI vào danh sách
                $this->messages[] = [
                    'message' => $responseData['response'],
                    'is_user' => false,
                    'timestamp' => now()->format('H:i')
                ];
            } else {
                $errorMessage = $responseData['error'] ?? 'Đã xảy ra lỗi khi kết nối với AI.';

                // Log chi tiết lỗi để debug
                Log::error('AI Chat Error in Livewire', [
                    'status_code' => $response->getStatusCode(),
                    'response_data' => $responseData,
                    'user_message' => $userMessage
                ]);

                $this->addErrorMessage($errorMessage);
            }

        } catch (\Exception $e) {
            // Log exception để debug
            Log::error('AI Chat Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_message' => $userMessage
            ]);

            $this->addErrorMessage('Kết nối không ổn định. Vui lòng thử lại sau.');
        }

        $this->isLoading = false;

        // Dispatch event để scroll xuống cuối sau khi có phản hồi
        $this->dispatch('ai-response-received');
    }
    
    private function addErrorMessage($message)
    {
        // Tùy chỉnh thông báo lỗi cho thân thiện hơn
        $friendlyError = $this->getFriendlyErrorMessage($message);

        $this->messages[] = [
            'message' => $friendlyError,
            'is_user' => false,
            'timestamp' => now()->format('H:i'),
            'is_error' => true
        ];
    }

    private function getFriendlyErrorMessage(string $error): string
    {
        // Compact error messages
        $errorMappings = [
            'Xin lỗi, tôi đang gặp sự cố kỹ thuật' => '🤖 Lỗi kỹ thuật. Thử lại sau vài giây?',
            'Quá nhiều yêu cầu' => '⏰ Gửi hơi nhanh! Đợi chút nhé.',
            'AI đang bận' => '🔄 AI bận. Thử lại sau.',
            'Không thể kết nối' => '🌐 Lỗi mạng. Thử lại?',
            'Dịch vụ AI tạm thời không khả dụng' => '⚠️ AI bảo trì. Thử lại sau 5p.',
        ];

        foreach ($errorMappings as $pattern => $friendlyMessage) {
            if (strpos($error, $pattern) !== false) {
                return $friendlyMessage;
            }
        }

        // Compact fallback
        $setting = \App\Models\Setting::first();
        $contact = $setting && $setting->zalo ? " Zalo: {$setting->zalo}" : "";

        return '🤖 Lỗi xảy ra. Thử lại hoặc liên hệ:' . $contact;
    }
    
    public function clearChat()
    {
        $this->messages = [
            [
                'message' => '👋 Chào bạn! ThanShoes
🏆 33k followers Shopee
⭐ 34.3k đánh giá 4.9/5 sao
💰 Website giá tốt hơn - Tư vấn ngay?',
                'is_user' => false,
                'timestamp' => now()->format('H:i')
            ]
        ];
        
        $this->dispatch('chat-cleared');
    }

    public function testConnection()
    {
        $this->isLoading = true;

        try {
            $controller = new AiChatController();
            $response = $controller->testConnection();
            $responseData = $response->getData(true);

            if ($responseData['status'] === 'success') {
                $this->messages[] = [
                    'message' => '✅ Kết nối AI hoạt động bình thường. Bạn có thể tiếp tục chat!',
                    'is_user' => false,
                    'timestamp' => now()->format('H:i')
                ];
            } else {
                $this->messages[] = [
                    'message' => '❌ Có vấn đề với dịch vụ AI. Vui lòng liên hệ quản trị viên.',
                    'is_user' => false,
                    'timestamp' => now()->format('H:i'),
                    'is_error' => true
                ];
            }
        } catch (\Exception $e) {
            $this->messages[] = [
                'message' => '🔧 Không thể kiểm tra kết nối AI. Vui lòng thử lại sau.',
                'is_user' => false,
                'timestamp' => now()->format('H:i'),
                'is_error' => true
            ];
        }

        $this->isLoading = false;
        $this->dispatch('ai-response-received');
    }

    /**
     * Format message để hiển thị link clickable
     */
    public function formatMessage($message)
    {
        // Convert URLs to clickable links
        $message = preg_replace(
            '/(http[s]?:\/\/[^\s]+)/',
            '<a href="$1" target="_blank" class="text-blue-600 hover:text-blue-800 underline font-medium">$1</a>',
            $message
        );

        // Convert line breaks to <br>
        $message = nl2br($message);

        // Make emojis and special characters stand out
        $message = str_replace('👉', '<span class="text-orange-500">👉</span>', $message);
        $message = str_replace('🛍️', '<span class="text-green-500">🛍️</span>', $message);
        $message = str_replace('💡', '<span class="text-yellow-500">💡</span>', $message);

        return $message;
    }

    public function render()
    {
        return view('livewire.ai-chatbot');
    }
}
