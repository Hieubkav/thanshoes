<x-filament::modal id="image-organizer-modal" width="7xl">
    <x-slot name="header">
        <div class="flex items-center gap-x-4">
            <x-filament::icon
                icon="heroicon-o-squares-2x2"
                class="h-6 w-6 text-warning-500"
            />
            <h2 class="font-bold tracking-tight">Sắp xếp hình ảnh</h2>
        </div>
    </x-slot>

    <div class="space-y-4">
        <div class="bg-gray-50 dark:bg-gray-900 p-3 rounded-lg">
            <div class="text-sm mb-2">
                Kéo và thả hình ảnh để sắp xếp lại thứ tự. Thứ tự sẽ được cập nhật tự động khi bạn thả ảnh.
                <span class="font-medium">Hình ảnh bên trái sẽ hiển thị đầu tiên.</span>
            </div>

            <div id="image-grid-container" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3"
                x-data="{
                    init() {
                        if (typeof Sortable === 'undefined') {
                            console.error('SortableJS library not loaded!');
                            return;
                        }
                        
                        const sortable = new Sortable(this.$el, {
                            animation: 150,
                            ghostClass: 'bg-primary-50',
                            handle: '.image-handle',
                            onEnd: (evt) => {
                                // Lấy mảng ID của ảnh sau khi sắp xếp
                                const itemIds = Array.from(this.$el.querySelectorAll('[data-id]'))
                                    .map(el => el.dataset.id);
                                    
                                console.log('Image IDs:', itemIds);
                                
                                // Gọi phương thức Livewire để cập nhật thứ tự
                                @this.call('updateImageOrder', itemIds);
                            }
                        });
                    }
                }">
                
                @foreach($images as $image)
                    <div data-id="{{ $image->id }}" class="relative group bg-white dark:bg-gray-800 rounded-md overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm">
                        <div class="absolute inset-0 image-handle cursor-move flex items-center justify-center bg-black bg-opacity-0 opacity-0 group-hover:bg-opacity-30 group-hover:opacity-100 transition-all duration-200">
                            <div class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-full p-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
                                </svg>
                            </div>
                        </div>
                        <div class="aspect-square">
                            <img src="{{ $image->image_url }}" class="w-full h-full object-cover" alt="Product Image" />
                        </div>
                        <div class="p-2 flex justify-between items-center bg-white dark:bg-gray-800">
                            <span class="px-2 py-1 text-xs font-medium rounded bg-primary-50 text-primary-700 dark:bg-primary-900 dark:text-primary-300">
                                # {{ $image->order }}
                            </span>
                            <span class="text-xs">
                                {{ $image->type === 'upload' ? 'Upload' : 'Variant' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <x-slot name="footer">
        <div class="flex justify-between gap-x-4">
            <div>
                <x-filament::button
                    color="gray"
                    wire:click="resetImageOrder"
                >
                    Đặt lại thứ tự
                </x-filament::button>
            </div>

            <div class="flex gap-x-4">
                <x-filament::button
                    color="gray"
                    x-on:click="$dispatch('close-modal', { id: 'image-organizer-modal' })"
                >
                    Đóng
                </x-filament::button>

                <x-filament::button
                    color="primary"
                    x-on:click="$dispatch('close-modal', { id: 'image-organizer-modal' })"
                >
                    Hoàn tất
                </x-filament::button>
            </div>
        </div>
    </x-slot>
</x-filament::modal>