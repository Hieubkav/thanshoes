<!-- Search Modal -->
<div id="search_modal" tabindex="-1" aria-hidden="true" wire:ignore.self
    class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative w-full max-w-2xl max-h-full mx-auto">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Tìm kiếm sản phẩm
                </h3>
                <button type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="search_modal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-6 space-y-6">
                <!-- Search input -->
                <div class="relative">
                    <input type="text" wire:model.live="searchTerm"
                        class="block w-full p-4 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                        placeholder="Nhập tên sản phẩm...">
                </div>

                <!-- Search results -->
                @if($searchResults && count($searchResults) > 0)
                <div class="mt-4 space-y-4">
                    @foreach($searchResults as $product)
                    <a href="{{ route('shop.product_overview', $product->id) }}" class="flex items-center space-x-4 p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                        <img src="{{ ($product->variants && $product->variants->count() > 0 && $product->variants->first()->variantImage) 
                            ? $product->variants->first()->variantImage->image_url 
                            : asset('images/no-image.png') }}" 
                             class="w-16 h-16 object-cover rounded" alt="{{ $product->name }}">
                        <div>
                            <h4 class="text-lg font-medium text-gray-900 dark:text-white">{{ $product->name }}</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $product->brand }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
                @elseif($searchTerm)
                <div class="text-center text-gray-500 dark:text-gray-400">
                    Không tìm thấy sản phẩm phù hợp
                </div>
                @endif
            </div>
        </div>
    </div>
</div>