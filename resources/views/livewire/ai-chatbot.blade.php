<div>
    @if($isOpen)
        <!-- Chat Window -->
        <div class="chat-window fixed bottom-4 right-4 w-[75vw] max-w-[280px] md:max-w-[320px] h-[65vh] max-h-[450px] md:h-[420px] bg-white rounded-xl shadow-lg border border-gray-200 z-[9999] flex flex-col overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-orange-400 to-orange-500 p-2 md:p-3 flex items-center justify-between {{ $isLoading ? 'animate-pulse' : '' }}">
                <div class="flex items-center space-x-2">
                    <div class="w-5 h-5 md:w-6 md:h-6 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <svg class="w-2.5 h-2.5 md:w-3 md:h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-white font-semibold text-xs">ThanShoes 4.9⭐</h3>
                        <p class="text-white text-xs opacity-90">33k followers • 34.3k reviews</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-1">
                    <!-- Clear Chat Button -->
                    <button
                        wire:click="clearChat"
                        class="p-1 hover:bg-white hover:bg-opacity-20 rounded-full transition-colors {{ $isLoading ? 'opacity-50' : '' }}"
                        title="Xóa chat"
                    >
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>

                    <!-- Minimize Button - Always enabled -->
                    <button
                        wire:click="toggleChat"
                        class="p-1 hover:bg-white hover:bg-opacity-20 rounded-full transition-colors"
                        title="{{ $isLoading ? 'Đóng (đang chờ phản hồi)' : 'Thu nhỏ' }}"
                    >
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Messages Container -->
            <div
                id="chat-messages"
                class="flex-1 overflow-y-auto p-2 space-y-2 bg-gray-50"
                style="scroll-behavior: smooth;"
            >
                @foreach($messages as $msg)
                    <div class="flex {{ $msg['is_user'] ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[95%]">
                            @if($msg['is_user'])
                                <!-- User Message -->
                                <div class="bg-orange-500 text-white rounded-xl rounded-br-md px-3 py-1.5 shadow-sm">
                                    <p class="text-xs leading-relaxed">{{ $msg['message'] }}</p>
                                    <p class="text-xs opacity-75 mt-0.5">{{ $msg['timestamp'] }}</p>
                                </div>
                            @else
                                <!-- AI Message -->
                                <div class="flex items-start space-x-1">
                                    <div class="w-4 h-4 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                        <svg class="w-2 h-2 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="bg-white rounded-xl rounded-bl-md px-3 py-1.5 shadow-sm border {{ isset($msg['is_error']) && $msg['is_error'] ? 'border-red-200 bg-red-50' : 'border-gray-200' }}">
                                        <div class="text-xs text-gray-800 leading-relaxed {{ isset($msg['is_error']) && $msg['is_error'] ? 'text-red-600' : '' }}">
                                            {!! $this->formatMessage($msg['message']) !!}
                                        </div>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $msg['timestamp'] }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                <!-- Loading Indicator -->
                @if($isLoading)
                    <div class="flex justify-start">
                        <div class="flex items-start space-x-1 md:space-x-2">
                            <div class="w-5 h-5 md:w-6 md:h-6 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <svg class="w-2 h-2 md:w-3 md:h-3 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="bg-white rounded-xl rounded-bl-md px-3 py-1.5 shadow-sm border border-gray-200">
                                <div class="flex flex-col space-y-1">
                                    <div class="flex items-center space-x-2">
                                        <!-- Spinner -->
                                        <div class="animate-spin rounded-full h-3 w-3 border-2 border-orange-400 border-t-transparent"></div>
                                        <span class="text-xs text-gray-500">Đang trả lời...</span>
                                    </div>
                                    <div class="text-xs text-gray-400 italic">Có thể đóng chat để tiếp tục duyệt web</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Input Area -->
            <div class="p-2 bg-white border-t border-gray-200">
                <form wire:submit.prevent="sendMessage" class="flex space-x-1">
                    <input
                        type="text"
                        wire:model="message"
                        placeholder="{{ $isLoading ? 'Đang xử lý...' : 'Tìm giày...' }}"
                        class="flex-1 px-3 py-1.5 border border-gray-300 rounded-full focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-transparent text-xs {{ $isLoading ? 'bg-gray-50 text-gray-400' : '' }}"
                        {{ $isLoading ? 'disabled' : '' }}
                    >
                    <button
                        type="submit"
                        class="px-3 py-1.5 bg-orange-500 text-white rounded-full hover:bg-orange-600 focus:outline-none transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
                        {{ $isLoading ? 'disabled' : '' }}
                    >
                        @if($isLoading)
                            <div class="animate-spin rounded-full h-3 w-3 border-2 border-white border-t-transparent"></div>
                        @else
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        @endif
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- Compact Chat Styles -->
    <style>
    /* Compact chat window animation */
    .chat-window {
        animation: slideInUp 0.2s ease-out;
    }

    @keyframes slideInUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    /* Smooth spinner animation */
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }

    /* Mobile optimization - Narrower with moderate height */
    @media (max-width: 640px) {
        .chat-window {
            bottom: 0.5rem !important;
            right: 0.5rem !important;
            width: 70vw !important;
            max-width: 260px !important;
            height: 65vh !important;
            max-height: 450px !important;
        }
    }

    /* Compact scrollbar */
    #chat-messages::-webkit-scrollbar { width: 2px; }
    #chat-messages::-webkit-scrollbar-track { background: #f9fafb; }
    #chat-messages::-webkit-scrollbar-thumb { background: #fb923c; border-radius: 1px; }

    /* Link styling */
    .chat-message a {
        color: #2563eb !important;
        text-decoration: underline !important;
        font-weight: 600 !important;
        word-break: break-all !important;
        display: inline-block !important;
        max-width: 100% !important;
        overflow-wrap: break-word !important;
    }

    .chat-message a:hover {
        color: #1d4ed8 !important;
        background-color: #eff6ff !important;
        padding: 1px 2px !important;
        border-radius: 2px !important;
    }

    /* Optimized text for vertical layout */
    .chat-message {
        font-size: 0.75rem !important;
        line-height: 1.4 !important;
        word-wrap: break-word !important;
    }

    /* Better spacing for multi-line messages */
    .chat-message br {
        line-height: 1.6 !important;
    }

    /* Touch targets */
    button { min-height: 32px; min-width: 32px; }
    </style>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        // Scroll to bottom when chat opens
        Livewire.on('chat-opened', () => {
            setTimeout(() => {
                const chatMessages = document.getElementById('chat-messages');
                if (chatMessages) {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            }, 100);
        });

        // Scroll to bottom when message is sent
        Livewire.on('message-sent', () => {
            setTimeout(() => {
                const chatMessages = document.getElementById('chat-messages');
                if (chatMessages) {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            }, 100);
        });

        // Scroll to bottom when AI responds
        Livewire.on('ai-response-received', () => {
            setTimeout(() => {
                const chatMessages = document.getElementById('chat-messages');
                if (chatMessages) {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            }, 100);
        });

        // Scroll to bottom when chat is cleared
        Livewire.on('chat-cleared', () => {
            setTimeout(() => {
                const chatMessages = document.getElementById('chat-messages');
                if (chatMessages) {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            }, 100);
        });
    });
</script>
