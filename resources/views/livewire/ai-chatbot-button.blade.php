<button 
    wire:click="openChat"
    class="flex items-center justify-center w-12 h-12 md:w-14 md:h-14 bg-gradient-to-r from-orange-400 to-orange-500 rounded-full shadow-lg hover:from-orange-500 hover:to-orange-600 transition-all duration-300 transform hover:scale-110 group"
    title="Chat với AI"
>
    <svg class="w-6 h-6 md:w-7 md:h-7 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
    </svg>
    
    <!-- Notification dot -->
    <div class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full animate-bounce"></div>
</button>
