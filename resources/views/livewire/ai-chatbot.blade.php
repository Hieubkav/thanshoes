<div>
    <!-- Chat Window -->
    @if($isOpen)
        <div class="fixed bottom-20 right-4 w-80 md:w-96 h-96 bg-white rounded-2xl shadow-2xl border border-gray-200 z-[9999] flex flex-col overflow-hidden transform translate-y-[-100px]">
            <!-- Header -->
            <div class="bg-gradient-to-r from-orange-400 to-orange-500 p-4 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-white font-semibold text-sm">ThanShoes AI</h3>
                        <p class="text-white text-xs opacity-90">Trợ lý tư vấn</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-2">
                    <!-- Clear Chat Button -->
                    <button 
                        wire:click="clearChat"
                        class="p-1.5 hover:bg-white hover:bg-opacity-20 rounded-full transition-colors"
                        title="Xóa cuộc trò chuyện"
                    >
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                    
                    <!-- Close Button -->
                    <button 
                        wire:click="toggleChat"
                        class="p-1.5 hover:bg-white hover:bg-opacity-20 rounded-full transition-colors"
                        title="Đóng chat"
                    >
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Messages Container -->
            <div 
                id="chat-messages" 
                class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50"
                style="scroll-behavior: smooth;"
            >
                @foreach($messages as $msg)
                    <div class="flex {{ $msg['is_user'] ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-xs lg:max-w-md">
                            @if($msg['is_user'])
                                <!-- User Message -->
                                <div class="bg-orange-500 text-white rounded-2xl rounded-br-md px-4 py-2 shadow-sm">
                                    <p class="text-sm">{{ $msg['message'] }}</p>
                                    <p class="text-xs opacity-75 mt-1">{{ $msg['timestamp'] }}</p>
                                </div>
                            @else
                                <!-- AI Message -->
                                <div class="flex items-start space-x-2">
                                    <div class="w-6 h-6 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                        <svg class="w-3 h-3 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="bg-white rounded-2xl rounded-bl-md px-4 py-2 shadow-sm border {{ isset($msg['is_error']) && $msg['is_error'] ? 'border-red-200 bg-red-50' : 'border-gray-200' }}">
                                        <p class="text-sm text-gray-800 {{ isset($msg['is_error']) && $msg['is_error'] ? 'text-red-600' : '' }}">{{ $msg['message'] }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $msg['timestamp'] }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                <!-- Loading Indicator -->
                @if($isLoading)
                    <div class="flex justify-start">
                        <div class="flex items-start space-x-2">
                            <div class="w-6 h-6 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <svg class="w-3 h-3 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="bg-white rounded-2xl rounded-bl-md px-4 py-2 shadow-sm border border-gray-200">
                                <div class="flex space-x-1">
                                    <div class="w-2 h-2 bg-orange-400 rounded-full animate-bounce"></div>
                                    <div class="w-2 h-2 bg-orange-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="w-2 h-2 bg-orange-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Input Area -->
            <div class="p-4 bg-white border-t border-gray-200">
                <form wire:submit.prevent="sendMessage" class="flex space-x-2">
                    <input 
                        type="text" 
                        wire:model="message"
                        placeholder="Nhập tin nhắn..."
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm"
                        {{ $isLoading ? 'disabled' : '' }}
                    >
                    <button 
                        type="submit"
                        class="px-4 py-2 bg-orange-500 text-white rounded-full hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        {{ $isLoading ? 'disabled' : '' }}
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    @endif
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
