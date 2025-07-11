<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Http\Controllers\AiChatController;

class AiChatbot extends Component
{
    public $isOpen = false;
    public $message = '';
    public $messages = [];
    public $isLoading = false;
    public $sessionId;
    
    public function mount()
    {
        // Tạo session ID unique cho mỗi phiên chat
        $this->sessionId = session()->getId() . '_' . Str::random(8);
        
        // Tin nhắn chào mừng
        $this->messages = [
            [
                'message' => 'Xin chào! Tôi là trợ lý AI của ThanShoes. Tôi có thể giúp bạn tư vấn về giày dép, chính sách đổi trả, và hướng dẫn mua sắm. Bạn cần hỗ trợ gì?',
                'is_user' => false,
                'timestamp' => now()->format('H:i')
            ]
        ];
    }
    
    public function toggleChat()
    {
        $this->isOpen = !$this->isOpen;

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
        $this->messages[] = [
            'message' => $message,
            'is_user' => false,
            'timestamp' => now()->format('H:i'),
            'is_error' => true
        ];
    }
    
    public function clearChat()
    {
        $this->messages = [
            [
                'message' => 'Xin chào! Tôi là trợ lý AI của ThanShoes. Tôi có thể giúp bạn tư vấn về giày dép, chính sách đổi trả, và hướng dẫn mua sắm. Bạn cần hỗ trợ gì?',
                'is_user' => false,
                'timestamp' => now()->format('H:i')
            ]
        ];
        
        $this->dispatch('chat-cleared');
    }
    
    public function render()
    {
        return view('livewire.ai-chatbot');
    }
}
