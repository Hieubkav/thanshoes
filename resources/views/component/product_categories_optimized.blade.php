@php
    use App\Services\ProductCacheService;
    $products = ProductCacheService::getHomepageProducts();

    $brands = $products->pluck('brand')->filter()->unique();
    $types = $products->pluck('type')->filter()->unique();
@endphp

<!-- Slim Product Categories Section -->
<section class="py-12 bg-white css-loading" id="product-categories">
    <!-- Loading State -->
    <div class="section-loading">
        <div class="spinner"></div>
    </div>
    
    <!-- Actual Content (Hidden until CSS loads) -->
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 css-content" style="display: none;">
        <!-- Compact Header -->
        <div class="text-center mb-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-neutral-900 mb-2">Danh mục sản phẩm</h2>
            <p class="text-neutral-600 text-sm sm:text-base">Khám phá bộ sưu tập đa dạng</p>
        </div>

        <!-- Slim Categories Grid -->
        <div class="bg-gradient-to-r from-orange-50/30 to-white rounded-xl p-4 sm:p-6 border border-orange-100/50">
            <div id="category-container" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-8 gap-3">
                @foreach ($types as $index => $item)
                    @php
                        $variantCount = $products->where('type', $item)->count();
                    @endphp
                    
                    <a href="{{ route('shop.cat_filter',['type' => $item]) }}"
                       class="category-item group bg-white rounded-lg p-3 sm:p-4 text-center hover:bg-orange-50 hover:border-orange-300 transition-all duration-200 border border-neutral-200/60">
                        
                        <!-- Category Name - Larger Text -->
                        <div>
                            <h3 class="font-semibold text-sm sm:text-base text-neutral-900 mb-1 group-hover:text-orange-600 transition-colors duration-200 leading-tight truncate">
                                {{ $item }}
                            </h3>
                            <p class="text-neutral-500 text-xs">
                                {{ $variantCount }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Compact Toggle Button -->
            <div id="dropdown-button" class="text-center mt-6">
                <button id="toggleButton"
                        class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg transition-colors duration-200 text-sm sm:text-base">
                    <i id="toggleIcon" class="fas fa-plus mr-1 sm:mr-2"></i>
                    <span id="toggleText">Xem thêm</span>
                </button>
            </div>
        </div>

        <!-- Simple Bottom Link -->
        <div class="mt-8 text-center">
            <a href="{{ route('shop.cat_filter') }}"
               class="inline-flex items-center px-4 py-2 text-orange-600 hover:text-orange-700 font-medium text-sm sm:text-base transition-colors duration-200">
                Xem tất cả sản phẩm
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
    
    <script>
        // Show content when CSS is loaded
        document.addEventListener("DOMContentLoaded", function() {
            const section = document.getElementById('product-categories');
            const content = section.querySelector('.css-content');
            const loading = section.querySelector('.section-loading');
            
            // Hide loading, show content with fade
            setTimeout(function() {
                if (loading) loading.style.display = 'none';
                if (content) {
                    content.style.display = 'block';
                }
                section.classList.remove('css-loading');
                section.classList.add('css-loaded');
            }, 100); // Small delay to ensure CSS is applied
        });
    </script>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categoryContainer = document.getElementById('category-container');
        const toggleButton = document.getElementById('toggleButton');
        const toggleIcon = document.getElementById('toggleIcon');
        const toggleText = document.getElementById('toggleText');
        const items = categoryContainer.querySelectorAll('.category-item');
        const thresholdXL = 16;
        const thresholdLG = 12;
        const thresholdMD = 10;
        const thresholdSM = 8;
        const thresholdMobile = 6;

        let isExpanded = false;

        function updateView() {
            const windowWidth = window.innerWidth;
            let threshold;
            
            if (windowWidth >= 1280) {
                threshold = thresholdXL;
            } else if (windowWidth >= 1024) {
                threshold = thresholdLG;
            } else if (windowWidth >= 768) {
                threshold = thresholdMD;
            } else if (windowWidth >= 640) {
                threshold = thresholdSM;
            } else {
                threshold = thresholdMobile;
            }

            items.forEach((item, index) => {
                if (index < threshold || isExpanded) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });

            if (items.length > threshold && !isExpanded) {
                toggleButton.style.display = 'inline-flex';
                toggleText.textContent = 'Xem thêm';
                toggleIcon.className = 'fas fa-plus mr-1 sm:mr-2';
            } else if (isExpanded) {
                toggleButton.style.display = 'inline-flex';
                toggleText.textContent = 'Ẩn bớt';
                toggleIcon.className = 'fas fa-minus mr-1 sm:mr-2';
            } else {
                toggleButton.style.display = 'none';
            }
        }

        window.addEventListener('resize', function() {
            clearTimeout(window.resizeTimer);
            window.resizeTimer = setTimeout(updateView, 150);
        });
        
        updateView();

        toggleButton.addEventListener('click', function() {
            isExpanded = !isExpanded;
            updateView();
        });
    });
</script>
