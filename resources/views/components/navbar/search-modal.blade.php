<!-- Modern Search Modal -->
<div id="search_modal" tabindex="-1" aria-hidden="true" wire:ignore.self
    class="fixed inset-0 z-50 hidden overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>

    <!-- Modal Container -->
    <div class="flex min-h-full items-start justify-center p-4 pt-16 sm:pt-24">
        <div class="relative w-full max-w-2xl transform transition-all">
            <!-- Modal content -->
            <div class="relative bg-white rounded-2xl shadow-2xl border border-neutral-200/50 overflow-hidden">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-6 bg-gradient-to-r from-primary-50 to-accent border-b border-neutral-200/50">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-primary-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-search text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-neutral-900">
                                Tìm kiếm sản phẩm
                            </h3>
                            <p class="text-sm text-neutral-600">Nhập tên sản phẩm để tìm kiếm</p>
                        </div>
                    </div>
                    <button type="button"
                        class="w-10 h-10 rounded-full bg-white/80 hover:bg-white text-neutral-500 hover:text-neutral-700 transition-all duration-200 flex items-center justify-center shadow-sm hover:shadow-md"
                        data-modal-hide="search_modal">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-6">
                    <!-- Search input -->
                    <div class="relative mb-6">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-search text-neutral-400"></i>
                        </div>
                        <input type="text" wire:model.live="searchTerm"
                            class="block w-full pl-12 pr-16 py-4 text-base text-neutral-900 bg-neutral-50 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 placeholder-neutral-500"
                            placeholder="Nhập tên sản phẩm, thương hiệu..."
                            autocomplete="off"
                            autofocus>

                        <!-- Keyboard shortcut hint -->
                        <div class="absolute top-4 right-16 hidden sm:flex items-center space-x-1 text-neutral-400">
                            <kbd class="px-2 py-1 text-xs bg-neutral-200 rounded border">Ctrl</kbd>
                            <span class="text-xs">+</span>
                            <kbd class="px-2 py-1 text-xs bg-neutral-200 rounded border">K</kbd>
                        </div>

                        <!-- Clear button -->
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center" wire:loading.remove>
                            @if($searchTerm)
                                <button type="button" wire:click="$set('searchTerm', '')"
                                    class="text-neutral-400 hover:text-neutral-600 transition-colors duration-200">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            @endif
                        </div>

                        <!-- Loading indicator -->
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center" wire:loading>
                            <svg class="animate-spin h-5 w-5 text-primary-500" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Search results -->
                    <div class="max-h-96 overflow-y-auto">
                        @if($searchResults && count($searchResults) > 0)
                            <!-- Results header -->
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-sm font-semibold text-neutral-700">
                                    Kết quả tìm kiếm ({{ count($searchResults) }})
                                </h4>
                                <span class="text-xs text-neutral-500">
                                    Nhấn Enter để xem tất cả
                                </span>
                            </div>

                            <!-- Results list -->
                            <div class="space-y-2">
                                @foreach($searchResults as $index => $product)
                                <a href="{{ route('shop.product_overview', $product->slug) }}"
                                   class="group flex items-center space-x-4 p-4 hover:bg-neutral-50 rounded-xl transition-all duration-200 border border-transparent hover:border-neutral-200"
                                   data-modal-hide="search_modal">
                                    <!-- Product Image -->
                                    <div class="relative flex-shrink-0">
                                        <img src="{{ ($product->variants && $product->variants->count() > 0 && $product->variants->first()->variantImage)
                                            ? $product->variants->first()->variantImage->image_url
                                            : asset('images/no-image.png') }}"
                                             class="w-16 h-16 object-cover rounded-lg shadow-sm group-hover:shadow-md transition-shadow duration-200"
                                             alt="{{ $product->name }}">

                                        <!-- Hover overlay -->
                                        <div class="absolute inset-0 bg-primary-500/10 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                                    </div>

                                    <!-- Product Info -->
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-base font-semibold text-neutral-900 group-hover:text-primary-600 transition-colors duration-200 truncate">
                                            {{ $product->name }}
                                        </h4>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <span class="chip chip-neutral text-xs">{{ $product->brand }}</span>
                                            @if($product->type)
                                                <span class="chip chip-neutral text-xs">{{ $product->type }}</span>
                                            @endif
                                        </div>

                                        <!-- Price -->
                                        @if($product->variants && $product->variants->count() > 0)
                                            @php
                                                $minPrice = $product->variants->min('price');
                                            @endphp
                                            <p class="text-sm font-semibold text-primary-600 mt-2">
                                                {{ number_format($minPrice, 0, ',', '.') }}₫
                                            </p>
                                        @endif
                                    </div>

                                    <!-- Arrow icon -->
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-arrow-right text-neutral-400 group-hover:text-primary-500 group-hover:translate-x-1 transition-all duration-200"></i>
                                    </div>
                                </a>
                                @endforeach
                            </div>

                            <!-- View all results -->
                            @if(count($searchResults) >= 5)
                                <div class="mt-4 pt-4 border-t border-neutral-200">
                                    <a href="{{ route('shop.cat_filter', ['search' => $searchTerm]) }}"
                                       class="btn btn-secondary w-full"
                                       data-modal-hide="search_modal">
                                        <i class="fas fa-search mr-2"></i>
                                        Xem tất cả kết quả cho "{{ $searchTerm }}"
                                    </a>
                                </div>
                            @endif

                        @elseif($searchTerm)
                            <!-- No results -->
                            <div class="text-center py-12">
                                <div class="w-20 h-20 bg-neutral-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-search text-neutral-400 text-2xl"></i>
                                </div>
                                <h4 class="text-lg font-semibold text-neutral-900 mb-2">
                                    Không tìm thấy sản phẩm
                                </h4>
                                <p class="text-neutral-600 mb-4">
                                    Không có sản phẩm nào phù hợp với từ khóa "<strong>{{ $searchTerm }}</strong>"
                                </p>
                                <div class="space-y-2 text-sm text-neutral-500">
                                    <p>• Thử tìm kiếm với từ khóa khác</p>
                                    <p>• Kiểm tra chính tả</p>
                                    <p>• Sử dụng từ khóa ngắn gọn hơn</p>
                                </div>
                            </div>
                        @else
                            <!-- Search suggestions -->
                            <div class="py-8">
                                <h4 class="text-sm font-semibold text-neutral-700 mb-4">
                                    <i class="fas fa-lightbulb text-primary-500 mr-2"></i>
                                    Gợi ý tìm kiếm
                                </h4>
                                <div class="grid grid-cols-2 gap-2">
                                    @php
                                        $suggestions = ['Nike', 'Adidas', 'Giày thể thao', 'Sneaker', 'Giày chạy bộ', 'Converse'];
                                    @endphp
                                    @foreach($suggestions as $suggestion)
                                        <button type="button"
                                                wire:click="$set('searchTerm', '{{ $suggestion }}')"
                                                class="text-left px-3 py-2 text-sm text-neutral-600 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all duration-200">
                                            <i class="fas fa-search text-xs mr-2"></i>
                                            {{ $suggestion }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search Modal JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchModal = document.getElementById('search_modal');
    const searchInput = searchModal.querySelector('input[wire\\:model\\.live="searchTerm"]');

    // Keyboard shortcut (Ctrl+K) to open search
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            // Trigger modal open
            const modalToggle = document.querySelector('[data-modal-target="search_modal"]');
            if (modalToggle) {
                modalToggle.click();
            }
        }

        // ESC to close modal
        if (e.key === 'Escape' && !searchModal.classList.contains('hidden')) {
            const modalHide = document.querySelector('[data-modal-hide="search_modal"]');
            if (modalHide) {
                modalHide.click();
            }
        }
    });

    // Auto focus input when modal opens
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                if (!searchModal.classList.contains('hidden')) {
                    setTimeout(() => {
                        searchInput.focus();
                    }, 100);
                }
            }
        });
    });

    observer.observe(searchModal, {
        attributes: true,
        attributeFilter: ['class']
    });

    // Add animation classes
    searchModal.addEventListener('show.bs.modal', function() {
        searchModal.classList.add('animate-fade-in');
    });

    searchModal.addEventListener('hide.bs.modal', function() {
        searchModal.classList.remove('animate-fade-in');
    });
});
</script>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}

.animate-fade-in {
    animation: fadeIn 0.2s ease-out;
}

/* Custom scrollbar for search results */
.max-h-96::-webkit-scrollbar {
    width: 6px;
}

.max-h-96::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.max-h-96::-webkit-scrollbar-thumb {
    background: #FF6B35;
    border-radius: 3px;
}

.max-h-96::-webkit-scrollbar-thumb:hover {
    background: #E55722;
}
</style>