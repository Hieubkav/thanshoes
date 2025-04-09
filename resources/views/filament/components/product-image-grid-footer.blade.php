@php
use App\Livewire\ProductImageOrganizer;
@endphp

<div x-data>
    <div class="p-2 mt-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-500 dark:text-gray-400 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                </svg>
                <span>Mẹo: Kéo và thả các ảnh để thay đổi thứ tự hoặc sử dụng tính năng "Sắp xếp trực quan" để sắp xếp nhiều ảnh cùng lúc.</span>
            </div>
            
            <button
                type="button"
                class="text-primary-600 dark:text-primary-400 text-sm font-medium hover:underline flex items-center"
                x-on:click="$dispatch('open-modal', { id: 'image-organizer-modal' })"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M12 17.25h8.25" />
                </svg>
                Sắp xếp trực quan
            </button>
        </div>
    </div>
    
    <livewire:product-image-organizer :product="$record" />
</div>