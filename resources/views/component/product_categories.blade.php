{{-- @php
    use App\Models\Product;
    $products = Product::all();

    // Lấy ra danh sách những thuộc tính khác nhau có thể có của product->brand trừ rỗng
    $brands = $products->pluck('brand')->filter()->unique();

    // lấy ra danh sách những bảng ghi khác nhau có thể có của product->type
    $types = $products->pluck('type')->filter()->unique();

@endphp

<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-6 p-6 max-w-screen-xl mx-auto">
    @foreach ($types as $item)
    @php
        $product = $products->where('type', $item)->first();
        $variantCount = $products->where('type', $item)->count();
        $image = $product && $product->variants->first() ? $product->variants->first()->variant_images->first()->image : 'default_image.jpg';
    @endphp
    <div class="flex flex-col items-center p-4 bg-gray-50 rounded-lg text-center">
        <img src="{{ $image }}" alt="{{ $item }}" class="mb-3 w-full h-auto object-cover">
        <h3 class="font-semibold text-base">{{ $item }}</h3>
        <p class="text-gray-500 text-sm">{{ $variantCount }} sản phẩm</p>
    </div>
    @endforeach
</div>



 --}}



@php
    use App\Models\Product;
    $products = Product::all();

    $brands = $products->pluck('brand')->filter()->unique();
    $types = $products->pluck('type')->filter()->unique();
@endphp

<div class="p-6 max-w-screen-xl mx-auto grid grid-cols-2">
    <h2 class="font-bold text-2xl text-start">Danh mục sản phẩm</h2>
    <a class="text-end text-blue-600 italic" href="">Xem toàn bộ sản phẩm</a>
</div>
<div class="p-6 max-w-screen-xl mx-auto bg-blue-100 rounded-lg">
    <div id="category-container" class="grid grid-cols-2 sm:grid-cols-6 lg:grid-cols-8 gap-6">
        @foreach ($types as $index => $item)
            @php
                $product = $products->where('type', $item)->first();
                $variantCount = $products->where('type', $item)->count();
                $image = $product->variants->first()->variant_images->first()->image ?? 'default_image.jpg';
            @endphp
            <div class="category-item flex flex-col items-center p-4 bg-gray-50 rounded-lg text-center">
                <img src="{{ $image }}" alt="{{ $item }}"
                    class="mb-3 w-full h-auto object-cover rounded-md">
                <h3 class="font-semibold text-base">{{ $item }}</h3>
                <p class="text-gray-500 text-sm">{{ $variantCount }} sản phẩm</p>
            </div>
        @endforeach
    </div>
    <div id="dropdown-button" class="text-center mt-4">
        <button id="toggleButton" class="px-4 py-2 bg-blue-500 text-white rounded-md">Xem thêm danh mục</button>
    </div>
</div>

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

            toggleButton.textContent = isExpanded ? 'Ẩn bớt danh mục' : 'Xem thêm danh mục';
        }

        window.addEventListener('resize', updateView);
        updateView();

        toggleButton.addEventListener('click', function() {
            isExpanded = !isExpanded;
            updateView();
        });
    });
</script>
