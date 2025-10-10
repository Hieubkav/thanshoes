{{-- Dữ liệu đã được share từ ViewServiceProvider với cache tối ưu --}}
@php
    use App\Helpers\PriceHelper;
    // Settings đã được cache trong ViewServiceProvider, sử dụng global $setting
@endphp

@if($so_luong_types > 0)
<section class="max-w-screen-xl mx-auto px-6 py-12">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-neutral-900 mb-2">
                {{ $type_name }}
            </h2>
            <p class="text-neutral-600 flex items-center">
                <i class="fas fa-box mr-2 text-primary-500"></i>
                {{ $so_luong_types }} sản phẩm có sẵn
            </p>
        </div>
        <a href="{{ route('shop.cat_filter',['type' => $type_name]) }}"
           class="btn btn-secondary group">
            Xem tất cả
            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform duration-200"></i>
        </a>
    </div>

    <!-- Product Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
        @foreach ($danh_sach_types ?? [] as $item)
            <!-- Modern Product Card -->
            <div class="group bg-white rounded-xl shadow-soft hover:shadow-soft-lg transition-all duration-300 overflow-hidden border border-neutral-200/50 hover:border-primary-200">
                <a href="{{route('shop.product_overview',$item->slug)}}">
                    <div class="relative overflow-hidden bg-neutral-50">
                        @php
                            $firstVariant = $item->variants->first();
                            $image_variant = asset('images/logo.svg'); // Default image

                            if ($firstVariant && $firstVariant->variantImage && $firstVariant->variantImage->image) {
                                $image_variant = $firstVariant->variantImage->image;
                            }

                            // Get minimum price from all variants
                            $minPrice = $item->variants->min('price');

                            // Calculate the discounted price
                            $discountedPrice = PriceHelper::calculateDiscountedPrice($minPrice);

                            // Calculate original/display price
                            $originalPrice = PriceHelper::getDisplayOriginalPrice($minPrice);

                            // Calculate discount percentage
                            $discountPercent = PriceHelper::getDiscountPercentage();

                            // Get discount type
                            $discountType = PriceHelper::getDiscountType();
                        @endphp

                        <img src="{{ $image_variant }}" loading="lazy" alt="{{ $item->name }}"
                             class="w-full aspect-square object-cover transition-transform duration-500 group-hover:scale-105">

                        <!-- Badges -->
                        @if ($discountPercent > 0)
                            <!-- Discount Badge - Top Left -->
                            <div class="absolute top-3 left-3">
                                <span class="inline-block px-1.5 py-1 rounded text-xs font-bold bg-red-500 text-white leading-none" style="font-size: 11px; min-width: auto; width: fit-content;">
                                    @if($discountType == 'percent')
                                        -{{ $discountPercent }}%
                                    @else
                                        -{{ number_format($discountPercent, 0, ',', '.') }}₫
                                    @endif
                                </span>
                            </div>
                        @endif

                        @if ($item->variants->min('price') > 500000)
                            <!-- Freeship Badge - Top Right -->
                            <div class="absolute top-3 right-3">
                                <span class="chip chip-success text-xs font-semibold">
                                    <i class="fas fa-shipping-fast mr-1"></i>
                                    FREESHIP
                                </span>
                            </div>
                        @endif
                    </div>
                </a>
                <!-- Product Info -->
                <div class="p-3">
                    <!-- Product Name -->
                    <h3 class="text-sm font-semibold text-neutral-900 mb-2 line-clamp-2 group-hover:text-primary-600 transition-colors duration-200">
                        {{ $item->name }}
                    </h3>

                    <!-- Price Section -->
                    <div class="flex items-center space-x-2 mb-3">
                        <span class="text-base font-bold text-primary-600">
                            {{ number_format($discountedPrice, 0, ',', '.') }}₫
                        </span>
                        @if($discountPercent > 0)
                            <span class="text-xs text-neutral-500 line-through">
                                {{ number_format($originalPrice, 0, ',', '.') }}₫
                            </span>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-2">
                        <button onclick="openProductVariantModal({{ $item->id }}, 'buy')"
                                class="flex-1 px-2 py-2 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white text-xs font-semibold rounded-lg transition-all duration-200 text-center">
                            Mua ngay
                        </button>
                        <button onclick="openProductVariantModal({{ $item->id }}, 'cart')"
                                class="flex-1 px-2 py-2 bg-primary-500 hover:bg-primary-600 text-white text-xs font-semibold rounded-lg transition-all duration-200 text-center">
                            Thêm giỏ
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif

@once
<div id="productVariantModal" class="fixed inset-0 z-[60] hidden">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeProductVariantModal()" role="presentation"></div>
    <div class="fixed bottom-0 left-0 right-0 bg-white rounded-t-3xl shadow-2xl transform transition-transform duration-300 max-h-[90vh] overflow-hidden" id="modalContent">
        <div class="flex justify-center pt-3 pb-2">
            <div class="w-12 h-1 bg-gray-300 rounded-full"></div>
        </div>

        <button type="button" onclick="closeProductVariantModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            <span class="sr-only">Dong</span>
        </button>

        <div class="px-6 pb-6 pt-2 max-h-[80vh] overflow-y-auto">
            <div class="flex items-center gap-4 mb-6 pb-4 border-b">
                <img id="modalProductImage" src="" alt="" class="w-20 h-20 object-cover rounded-lg">
                <div class="flex-1">
                    <h3 id="modalProductName" class="font-semibold text-lg text-gray-900 mb-1"></h3>
                    <div class="flex items-center gap-2">
                        <span id="modalProductPrice" class="text-lg font-bold text-primary-600"></span>
                        <span id="modalProductOriginalPrice" class="text-sm text-gray-500 line-through hidden"></span>
                    </div>
                </div>
            </div>

            <div id="colorSelection" class="mb-6">
                <h4 class="font-semibold text-sm text-gray-700 mb-3">Chon mau sac</h4>
                <div id="colorOptions" class="flex flex-wrap gap-2"></div>
            </div>

            <div id="sizeSelection" class="mb-6">
                <h4 class="font-semibold text-sm text-gray-700 mb-3">Chon size</h4>
                <div id="sizeOptions" class="flex flex-wrap gap-2"></div>
            </div>

            <div id="stockInfo" class="mb-4 text-center text-sm text-gray-600"></div>

            <div class="flex gap-3">
                <button id="addToCartBtn"
                        type="button"
                        data-original-text="Them vao gio hang"
                        data-loading-text="Dang them..."
                        onclick="addToCartFromModal()"
                        class="flex-1 px-4 py-3 bg-primary-500 hover:bg-primary-600 text-white font-semibold rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                    Them vao gio hang
                </button>
                <button id="buyNowBtn"
                        type="button"
                        data-original-text="Mua ngay"
                        data-loading-text="Dang xu ly..."
                        onclick="buyNowFromModal()"
                        class="flex-1 px-4 py-3 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-semibold rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                    Mua ngay
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function (window, document) {
    const state = {
        productMap: new Map(),
        currentProduct: null,
        currentAction: 'cart',
        selectedColor: null,
        selectedSize: null
    };

    const elements = {
        modal: document.getElementById('productVariantModal'),
        modalContent: document.getElementById('modalContent'),
        productName: document.getElementById('modalProductName'),
        productImage: document.getElementById('modalProductImage'),
        productPrice: document.getElementById('modalProductPrice'),
        productOriginalPrice: document.getElementById('modalProductOriginalPrice'),
        colorSection: document.getElementById('colorSelection'),
        colorOptions: document.getElementById('colorOptions'),
        sizeSection: document.getElementById('sizeSelection'),
        sizeOptions: document.getElementById('sizeOptions'),
        stockInfo: document.getElementById('stockInfo'),
        addToCartBtn: document.getElementById('addToCartBtn'),
        buyNowBtn: document.getElementById('buyNowBtn')
    };

    function hasColorOptions(product = state.currentProduct) {
        return Array.isArray(product?.variants)
            && product.variants.some(variant => Boolean(variant.color));
    }

    function hasSizeOptions(product = state.currentProduct) {
        return Array.isArray(product?.variants)
            && product.variants.some(variant => Boolean(variant.size));
    }

    function ensureGlobalProducts() {
        if (!Array.isArray(window.productsData)) {
            window.productsData = [];
        }
    }

    function registerProducts(products = []) {
        ensureGlobalProducts();

        const existingIds = new Set(window.productsData.map(item => item.id));

        products.forEach(product => {
            if (!product || typeof product.id === 'undefined') {
                return;
            }

            if (!existingIds.has(product.id)) {
                window.productsData.push(product);
                existingIds.add(product.id);
            } else {
                const index = window.productsData.findIndex(item => item.id === product.id);
                if (index !== -1) {
                    window.productsData[index] = product;
                }
            }

            state.productMap.set(product.id, product);
        });

        console.log('Total products loaded:', window.productsData.length);
    }

    function resetSelections() {
        state.selectedColor = null;
        state.selectedSize = null;
    }

    function populateProductInfo(product) {
        elements.productName.textContent = product?.name || '';
        elements.productImage.src = product?.image || '/images/logo.svg';
        elements.productImage.alt = product?.name || '';
    }

    function renderColorOptions(product) {
        const colors = Array.from(
            new Set((product?.variants || [])
                .map(variant => variant.color)
                .filter(Boolean))
        );

        elements.colorOptions.innerHTML = '';

        if (colors.length === 0) {
            elements.colorSection.style.display = 'none';
            return;
        }

        elements.colorSection.style.display = 'block';

        colors.forEach(color => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'color-btn px-4 py-2 border-2 border-gray-200 rounded-lg text-sm font-medium transition-all hover:border-primary-300';
            button.dataset.color = color;
            button.textContent = color;
            button.addEventListener('click', () => selectColor(color));
            elements.colorOptions.appendChild(button);
        });
    }

    function renderSizeOptions(product) {
        const sizes = Array.from(
            new Set((product?.variants || [])
                .map(variant => variant.size)
                .filter(Boolean))
        );

        elements.sizeOptions.innerHTML = '';

        if (sizes.length === 0) {
            elements.sizeSection.style.display = 'none';
            return;
        }

        elements.sizeSection.style.display = 'block';

        sizes.forEach(size => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'size-btn px-4 py-2 border-2 border-gray-200 rounded-lg text-sm font-medium transition-all hover:border-gray-400';
            button.dataset.size = size;
            button.textContent = size;
            button.addEventListener('click', () => selectSize(size));
            elements.sizeOptions.appendChild(button);
        });
    }

    function toggleActionButtons(action) {
        const isBuy = action === 'buy';
        elements.buyNowBtn.style.display = isBuy ? 'block' : 'none';
        elements.addToCartBtn.style.display = isBuy ? 'none' : 'block';
    }

    function resetButtonState(button) {
        if (!button) {
            return;
        }
        const originalText = button.dataset.originalText || button.textContent.trim();
        button.dataset.originalText = originalText;
        button.textContent = originalText;
        button.disabled = false;
        delete button.dataset.previousDisabled;
    }

    function getActiveVariant() {
        const product = state.currentProduct;

        if (!product || !Array.isArray(product.variants)) {
            return null;
        }

        const requireColorSelection = hasColorOptions(product);
        const requireSizeSelection = hasSizeOptions(product);

        if ((requireColorSelection && !state.selectedColor) || (requireSizeSelection && !state.selectedSize)) {
            return null;
        }

        return product.variants.find(variant => {
            const matchColor = !state.selectedColor || variant.color === state.selectedColor;
            const matchSize = !state.selectedSize || variant.size === state.selectedSize;
            return matchColor && matchSize;
        }) || null;
    }

    function formatCurrency(value) {
        const number = Number(value) || 0;
        return new Intl.NumberFormat('vi-VN').format(number) + '₫';
    }

    function setButtonState(button, isEnabled) {
        if (!button) {
            return;
        }
        button.disabled = !isEnabled;
    }

    function updateOptionStyles(type) {
        const selectedValue = type === 'color' ? state.selectedColor : state.selectedSize;
        const selector = type === 'color' ? '.color-btn' : '.size-btn';
        elements[`${type}Options`].querySelectorAll(selector).forEach(button => {
            const dataKey = type === 'color' ? button.dataset.color : button.dataset.size;
            button.classList.remove('border-primary-500', 'bg-primary-50', 'text-primary-700');
            button.classList.add('border-gray-200');
            if (selectedValue && selectedValue === dataKey) {
                button.classList.remove('border-gray-200');
                button.classList.add('border-primary-500', 'bg-primary-50', 'text-primary-700');
            }
        });
    }

    function updateSizeAvailability() {
        if (!elements.sizeOptions) {
            return;
        }

        const availableSizes = (state.currentProduct?.variants || [])
            .filter(variant => !state.selectedColor || variant.color === state.selectedColor)
            .map(variant => variant.size)
            .filter(Boolean);

        elements.sizeOptions.querySelectorAll('.size-btn').forEach(button => {
            const size = button.dataset.size;
            const isAvailable = !state.selectedColor || availableSizes.includes(size);
            button.disabled = !isAvailable;
            button.classList.toggle('opacity-50', !isAvailable);
            button.classList.toggle('cursor-not-allowed', !isAvailable);
        });
    }

    function updatePriceAndStock() {
        const variant = getActiveVariant();
        const hasVariant = Boolean(variant);
        const stock = Number(variant?.stock ?? 0);
        const hasStock = stock > 0;
        const hasColors = elements.colorSection.style.display !== 'none';
        const hasSizes = elements.sizeSection.style.display !== 'none';

        if (hasVariant) {
            const price = variant.discounted_price || variant.price;
            elements.productPrice.textContent = formatCurrency(price);

            if (variant.discounted_price && Number(variant.discounted_price) < Number(variant.price)) {
                elements.productOriginalPrice.textContent = formatCurrency(variant.price);
                elements.productOriginalPrice.classList.remove('hidden');
            } else {
                elements.productOriginalPrice.classList.add('hidden');
            }

            if (hasStock) {
                elements.stockInfo.innerHTML = `<span class="text-green-600 font-medium">Con ${stock} san pham</span>`;
            } else {
                elements.stockInfo.innerHTML = '<span class="text-red-500">San pham het hang</span>';
            }
        } else {
            elements.productPrice.textContent = state.currentProduct ? 'Chon phan loai' : '';
            elements.productOriginalPrice.classList.add('hidden');

            if (hasColors && hasSizes) {
                elements.stockInfo.innerHTML = '<span class="text-orange-500">Vui long chon mau va size</span>';
            } else if (hasColors) {
                elements.stockInfo.innerHTML = '<span class="text-orange-500">Vui long chon mau sac</span>';
            } else if (hasSizes) {
                elements.stockInfo.innerHTML = '<span class="text-orange-500">Vui long chon size</span>';
            } else {
                elements.stockInfo.innerHTML = '<span class="text-red-500">San pham tam het</span>';
            }
        }

        setButtonState(elements.buyNowBtn, hasVariant && hasStock);
        setButtonState(elements.addToCartBtn, hasVariant && hasStock);
    }

    function setButtonLoading(button, isLoading, action) {
        if (!button) {
            return;
        }
        const originalText = button.dataset.originalText || button.textContent.trim();
        const loadingText = button.dataset.loadingText || (action === 'buy' ? 'Dang xu ly...' : 'Dang them...');

        button.dataset.originalText = originalText;
        button.dataset.loadingText = loadingText;

        if (isLoading) {
            button.dataset.previousDisabled = button.disabled ? 'true' : 'false';
            button.disabled = true;
            button.textContent = loadingText;
        } else {
            const wasDisabled = button.dataset.previousDisabled === 'true';
            delete button.dataset.previousDisabled;
            button.disabled = wasDisabled;
            button.textContent = originalText;
        }
    }

    async function handleVariantAction(action) {
        const button = action === 'buy' ? elements.buyNowBtn : elements.addToCartBtn;
        const variant = getActiveVariant();

        if (!variant) {
            showNotification('Vui long chon day du phan loai san pham.', 'error');
            return;
        }

        if (!variant.stock || Number(variant.stock) < 1) {
            showNotification('San pham da het hang.', 'error');
            return;
        }

        setButtonLoading(button, true, action);

        try {
            const response = await fetch('/api/add-to-cart', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({
                    variant_id: variant.id,
                    quantity: 1
                })
            });

            let data = null;
            let success = false;

            try {
                data = await response.json();
                success = data?.success === true;
            } catch (jsonError) {
                console.warn('Cannot parse add-to-cart response as JSON:', jsonError);
            }

            if (success) {
                updateCartCounter(Number(data?.cart_count) || 0);

                if (action === 'cart') {
                    closeModal();
                    showNotification(data?.message || 'Da them san pham vao gio hang.', 'success');
                } else {
                    window.location.href = '/checkout';
                }

                if (window.Livewire && typeof window.Livewire.dispatch === 'function') {
                    window.Livewire.dispatch('cart_updated');
                }
                return;
            }

            const fallbackMessage = data?.message
                || (!response.ok ? `Khong the them san pham (ma loi ${response.status}).` : 'Khong the them san pham vao gio hang.');
            throw new Error(fallbackMessage);
        } catch (error) {
            console.error('Error performing cart action:', error);
            showNotification(error.message || 'Co loi xay ra, vui long thu lai.', 'error');
        } finally {
            setButtonLoading(button, false, action);
        }
    }

    function showModal() {
        elements.modal.classList.remove('hidden');
        setTimeout(() => {
            elements.modalContent.classList.add('translate-y-0');
            elements.modalContent.classList.remove('translate-y-full');
        }, 50);
    }

    function closeModal() {
        elements.modalContent.classList.add('translate-y-full');
        elements.modalContent.classList.remove('translate-y-0');
        setTimeout(() => {
            elements.modal.classList.add('hidden');
        }, 300);
        state.currentProduct = null;
        state.currentAction = 'cart';
    }

    function selectColor(color) {
        state.selectedColor = color;
        state.selectedSize = null;
        updateOptionStyles('color');
        updateSizeAvailability();
        updateOptionStyles('size');
        updatePriceAndStock();
    }

    function selectSize(size) {
        state.selectedSize = size;
        updateOptionStyles('size');
        updatePriceAndStock();
    }

    function updateCartCounter(count) {
        const counter = document.querySelector('.cart-counter');
        if (counter) {
            counter.textContent = count;
            counter.style.display = count > 0 ? 'inline-block' : 'none';
        }
    }

    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white font-medium z-[70] transform transition-all duration-300 ${
            type === 'success' ? 'bg-green-500' :
            type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        }`;
        notification.textContent = message;

        document.body.appendChild(notification);

        requestAnimationFrame(() => {
            notification.classList.add('translate-x-0');
        });

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    function openModal(productId, action = 'cart') {
        const product = state.productMap.get(productId) || window.productsData?.find(item => item.id === productId);

        if (!product) {
            showNotification('Khong tim thay thong tin san pham, vui long tai lai trang.', 'error');
            return;
        }

        state.currentProduct = product;
        state.currentAction = action;
        resetSelections();

        populateProductInfo(product);
        renderColorOptions(product);
        renderSizeOptions(product);
        toggleActionButtons(action);
        resetButtonState(elements.addToCartBtn);
        resetButtonState(elements.buyNowBtn);
        updatePriceAndStock();

        showModal();
    }

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            closeModal();
        }
    });

    window.newArrivalModal = {
        registerProducts,
        state
    };

    window.openProductVariantModal = openModal;
    window.closeProductVariantModal = closeModal;
    window.selectColor = selectColor;
    window.selectSize = selectSize;
    window.addToCartFromModal = () => handleVariantAction('cart');
    window.buyNowFromModal = () => handleVariantAction('buy');
    window.updateCartCounter = updateCartCounter;
    window.showNotification = showNotification;
})(window, document);
</script>
@endonce

@if ($so_luong_types > 0)
    @php
        $componentProducts = $danh_sach_types->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'image' => optional(optional($item->variants->first())->variantImage)->image ?? asset('images/logo.svg'),
                'variants' => $item->variants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'color' => $variant->color,
                        'size' => $variant->size,
                        'price' => $variant->price,
                        'stock' => $variant->stock,
                        'discounted_price' => \App\Helpers\PriceHelper::calculateDiscountedPrice($variant->price),
                    ];
                })->values()->all(),
            ];
        })->values();
    @endphp

    <script>
        (function registerNewArrivalProducts() {
            if (!window.newArrivalModal || typeof window.newArrivalModal.registerProducts !== 'function') {
                return;
            }

            const products = @json($componentProducts);
            window.newArrivalModal.registerProducts(products);
        })();
    </script>
@endif
