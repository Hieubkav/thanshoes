@php
    use App\Models\Product;
    $products = Product::all();

    $brands = $products->pluck('brand')->filter()->unique();
    $types = $products->pluck('type')->filter()->unique();
@endphp

<!-- Modern Product Categories Section -->
<section class="py-16 bg-white">
    <div class="max-w-screen-xl mx-auto px-6">
        <!-- Section Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-12 space-y-4 sm:space-y-0">
            <div>
                <h2 class="text-3xl font-bold text-neutral-900 mb-2">Danh mục sản phẩm</h2>
                <p class="text-neutral-600">Khám phá bộ sưu tập đa dạng của chúng tôi</p>
            </div>
            <a href="{{ route('shop.cat_filter') }}"
               class="btn btn-secondary group">
                Xem tất cả sản phẩm
                <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform duration-200"></i>
            </a>
        </div>

        <!-- Categories Container -->
        <div class="bg-gradient-to-br from-primary-50 to-accent rounded-2xl p-8">
            <div id="category-container" class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4">
                @foreach ($types as $index => $item)
                    @php
                        $product = $products->where('type', $item)->first();
                        $variantCount = $products->where('type', $item)->count();
                        $image = $product->variants->first()->variantImage->image ?? asset('images/logo.svg');
                    @endphp
                    <a href="{{ route('shop.cat_filter',['type' => $item]) }}"
                       class="category-item group bg-white rounded-xl p-4 text-center shadow-soft hover:shadow-soft-lg transition-all duration-300 border border-neutral-200/50 hover:border-primary-200">
                        <!-- Image Container -->
                        <div class="relative mb-4 overflow-hidden rounded-lg">
                            <img src="{{ $image }}" alt="{{ $item }}"
                                loading="lazy"
                                class="w-full h-20 object-cover group-hover:scale-110 transition-transform duration-300">
                            <div class="absolute inset-0 bg-primary-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </div>

                        <!-- Category Info -->
                        <div>
                            <h3 class="font-semibold text-sm text-neutral-900 mb-1 group-hover:text-primary-600 transition-colors duration-300">
                                {{ $item }}
                            </h3>
                            <p class="text-neutral-500 text-xs">
                                {{ $variantCount }} sản phẩm
                            </p>
                        </div>

                        <!-- Hover indicator -->
                        <div class="mt-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <div class="w-8 h-0.5 bg-primary-500 rounded-full mx-auto"></div>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Toggle Button -->
            <div id="dropdown-button" class="text-center mt-12 mb-4">
                <button id="toggleButton"
                        class="btn btn-primary px-6 py-3">
                    <i class="fas fa-plus mr-2"></i>
                    Xem thêm danh mục
                </button>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categoryContainer = document.getElementById('category-container');
        const toggleButton = document.getElementById('toggleButton');
        const items = categoryContainer.querySelectorAll('.category-item');
        const thresholdDesktop = 8;
        const thresholdMobile = 4;

        let isExpanded = false;

        function updateView() {
            const windowWidth = window.innerWidth;
            const threshold = windowWidth >= 1024 ? thresholdDesktop : thresholdMobile;

            items.forEach((item, index) => {
                if (index < threshold || isExpanded) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });

            if (items.length > threshold) {
                toggleButton.style.display = 'block';
            } else {
                toggleButton.style.display = 'none';
            }

            const icon = toggleButton.querySelector('i');
            if (isExpanded) {
                toggleButton.innerHTML = '<i class="fas fa-minus mr-2"></i>Ẩn bớt danh mục';
            } else {
                toggleButton.innerHTML = '<i class="fas fa-plus mr-2"></i>Xem thêm danh mục';
            }
        }

        window.addEventListener('resize', updateView);
        updateView();

        toggleButton.addEventListener('click', function() {
            isExpanded = !isExpanded;
            updateView();
        });
    });
</script>
