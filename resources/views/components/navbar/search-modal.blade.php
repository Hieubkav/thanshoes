@php use Illuminate\Support\Str; @endphp
<!-- Modern Search Modal -->
<div id="search_modal" tabindex="-1" aria-hidden="true" wire:ignore.self
    class="fixed inset-0 z-50 hidden overflow-y-auto"
    x-cloak data-cloak>
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>

    <!-- Modal Container -->
    <div class="flex min-h-full items-start justify-center p-2 pt-12 sm:p-4 sm:pt-16 md:pt-24">
        <div class="relative w-full max-w-xs sm:max-w-lg md:max-w-2xl transform transition-all">
            <!-- Modal content -->
            <div class="relative bg-white rounded-2xl shadow-2xl border border-neutral-200/50 overflow-hidden">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-3 sm:p-6 bg-gradient-to-r from-primary-50 to-accent border-b border-neutral-200/50">
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-primary-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-search text-white text-sm sm:text-base"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-lg sm:text-xl font-bold text-neutral-900 truncate">
                                Tìm kiếm sản phẩm
                            </h3>
                            <p class="text-xs sm:text-sm text-neutral-600 hidden sm:block">Nhập tên sản phẩm để tìm kiếm</p>
                        </div>
                    </div>
                    <button type="button"
                        class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-white/80 hover:bg-white text-neutral-500 hover:text-neutral-700 transition-all duration-200 flex items-center justify-center shadow-sm hover:shadow-md flex-shrink-0"
                        data-modal-hide="search_modal">
                        <i class="fas fa-times text-sm sm:text-lg"></i>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-3 sm:p-6">
                    <!-- Search input -->
                    <div class="relative mb-4 sm:mb-6">
                        <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-search text-neutral-400 text-sm sm:text-base"></i>
                        </div>
                        <input type="text" wire:model.live="searchTerm"
                            class="block w-full pl-10 sm:pl-12 pr-12 sm:pr-16 py-3 sm:py-4 text-sm sm:text-base text-neutral-900 bg-neutral-50 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 placeholder-neutral-500"
                            placeholder="Nhập tên sản phẩm, thương hiệu..."
                            autocomplete="off"
                            autofocus>

                        <!-- Keyboard shortcut hint -->
                        <div class="absolute top-3 sm:top-4 right-12 sm:right-16 hidden sm:flex items-center space-x-1 text-neutral-400">
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
                                   class="group flex items-center space-x-2 sm:space-x-4 p-2 sm:p-4 hover:bg-neutral-50 rounded-xl transition-all duration-200 border border-transparent hover:border-neutral-200"
                                   data-modal-hide="search_modal">
                                    <!-- Product Image -->
                                    <div class="relative flex-shrink-0">
                                        <img src="{{ ($product->variants && $product->variants->count() > 0 && $product->variants->first()->variantImage)
                                            ? $product->variants->first()->variantImage->image_url
                                            : asset('images/no-image.png') }}"
                                             class="w-12 h-12 sm:w-16 sm:h-16 object-cover rounded-lg shadow-sm group-hover:shadow-md transition-shadow duration-200"
                                             alt="{{ $product->name }}">

                                        <!-- Hover overlay -->
                                        <div class="absolute inset-0 bg-primary-500/10 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                                    </div>

                                    <!-- Product Info -->
                                    <div class="flex-1 min-w-0 overflow-hidden">
                                        <h4 class="text-sm sm:text-base font-semibold text-neutral-900 group-hover:text-primary-600 transition-colors duration-200 truncate">
                                            {{ $product->name }}
                                        </h4>
                                        <div class="flex items-center space-x-1 sm:space-x-2 mt-1 overflow-hidden">
                                            <span class="chip chip-neutral text-xs truncate max-w-20 sm:max-w-none">{{ $product->brand }}</span>
                                            @if($product->type)
                                                <span class="chip chip-neutral text-xs truncate max-w-16 sm:max-w-none hidden sm:inline-block">{{ $product->type }}</span>
                                            @endif
                                        </div>

                                        <!-- Price -->
                                        @if($product->variants && $product->variants->count() > 0)
                                            @php
                                                $minPrice = $product->variants->min('price');
                                            @endphp
                                            <p class="text-xs sm:text-sm font-semibold text-primary-600 mt-1 sm:mt-2">
                                                {{ number_format($minPrice, 0, ',', '.') }}₫
                                            </p>
                                        @endif
                                    </div>

                                    <!-- Arrow icon -->
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-arrow-right text-neutral-400 group-hover:text-primary-500 group-hover:translate-x-1 transition-all duration-200 text-sm sm:text-base"></i>
                                    </div>
                                </a>
                                @endforeach
                            </div>

                            <!-- View all results -->
                            @if(count($searchResults) >= 5)
                                <div class="mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-neutral-200">
                                    <a href="{{ route('shop.cat_filter', ['search' => $searchTerm]) }}"
                                       class="btn btn-secondary w-full text-sm sm:text-base py-2 sm:py-3"
                                       data-modal-hide="search_modal">
                                        <i class="fas fa-search mr-1 sm:mr-2 text-sm sm:text-base"></i>
                                        <span class="truncate">Xem tất cả kết quả cho "{{ Str::limit($searchTerm, 15) }}"</span>
                                    </a>
                                </div>
                            @endif

                        @elseif($searchTerm)
                            <!-- No results -->
                            <div class="text-center py-8 sm:py-12">
                                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-neutral-100 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                                    <i class="fas fa-search text-neutral-400 text-xl sm:text-2xl"></i>
                                </div>
                                <h4 class="text-base sm:text-lg font-semibold text-neutral-900 mb-2">
                                    Không tìm thấy sản phẩm
                                </h4>
                                <p class="text-sm sm:text-base text-neutral-600 mb-3 sm:mb-4 px-2">
                                    Không có sản phẩm nào phù hợp với từ khóa "<strong>{{ Str::limit($searchTerm, 20) }}</strong>"
                                </p>
                                <div class="space-y-1 sm:space-y-2 text-xs sm:text-sm text-neutral-500">
                                    <p>• Thử tìm kiếm với từ khóa khác</p>
                                    <p>• Kiểm tra chính tả</p>
                                    <p class="hidden sm:block">• Sử dụng từ khóa ngắn gọn hơn</p>
                                </div>
                            </div>
                        @else
                            <!-- Search suggestions -->
                            <div class="py-6 sm:py-8">
                                <h4 class="text-sm font-semibold text-neutral-700 mb-3 sm:mb-4">
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
                                                class="text-left px-2 sm:px-3 py-2 text-xs sm:text-sm text-neutral-600 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all duration-200 truncate">
                                            <i class="fas fa-search text-xs mr-1 sm:mr-2"></i>
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

/* Mobile responsive fixes */
@media (max-width: 640px) {
    #search_modal .max-h-96 {
        max-height: 60vh;
    }

    /* Ensure modal doesn't overflow on small screens */
    #search_modal .relative.w-full {
        margin: 0 8px;
        max-width: calc(100vw - 16px);
    }

    /* Compact chip styles for mobile */
    .chip {
        font-size: 10px !important;
        padding: 2px 6px !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
}

/* Ensure text doesn't break layout */
.truncate-mobile {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

@media (max-width: 480px) {
    #search_modal .max-h-96 {
        max-height: 50vh;
    }
}
</style>
