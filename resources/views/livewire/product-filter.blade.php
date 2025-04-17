<div>
    @php
        use App\Models\Product;
        $all_product = Product::all();

        // Lấy ra danh sách những thuộc tính khác nhau có thể có của product->brand trừ rỗng
        $brands = $all_product->pluck('brand')->filter()->unique();

        // lấy ra danh sách những bảng ghi khác nhau có thể có của product->type
        $types = $all_product->pluck('type')->filter()->unique();

    @endphp


    <section>
        <!-- Container -->
        <div class="mx-auto max-w-9xl px-4 py-16 md:px-10 md:py-24">
            <!-- Component -->
            <div class="flex flex-col gap-12">
                <!-- Title -->
                <div class="flex flex-col gap-5">
                    <h3 class="text-2xl font-bold md:text-4xl">
                        Danh sách sản phẩm
                    </h3>
                </div>
                <!-- Content -->
                <div class="grid gap-8 md:gap-10 lg:grid-cols-[max-content_1fr]">
                    <!-- Filters -->
                    <div class="mb-4 max-w-none 2xl:max-w-sm">
                        <form method="get" class="flex-col gap-6" action="{{ route('shop.cat_filter') }}">
                            <!-- Filters title -->
                            <div
                                class="mb-6 flex items-center justify-between py-4 [border-bottom:1px_solid_rgb(217,_217,_217)]">
                                <h5 class="text-xl font-bold">Bộ lọc</h5>
                                <a wire:click='clearfilter' class="cursor-pointer text-sm text-red-500 px-4 py-2 rounded-md hover:bg-red-100">
                                    Clear All
                                </a>
                            </div>
                            <!-- Search input -->
                            <input type="text"
                                class="mb-10 block h-9 min-h-[44px] w-full rounded-md border border-solid border-[#cccccc] bg-[#f2f2f7] bg-[16px_center] bg-no-repeat py-3 pl-11 pr-4 text-sm font-bold text-[#333333] [background-size:18px] [border-bottom:1px_solid_rgb(215,_215,_221)]"
                                placeholder="Tìm kiếm" wire:model.live.debounce.300ms="search"
                                style="background-image: url('https://assets.website-files.com/6458c625291a94a195e6cf3a/64b7a3a33cd5dc368f46daaa_MagnifyingGlass.svg');" />
                            <!-- Categories -->
                            <div class="flex flex-col gap-6">
                                <p class="font-semibold">Phân mục</p>
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="#" wire:click="filterCategory('giay')"
                                        class="category-filter flex gap-3 rounded-md p-3 font-semibold transition-colors duration-200
                                        {{ $giay == 'true' ? 'bg-blue-500 text-white hover:bg-blue-600' : 'bg-slate-100 hover:bg-slate-200' }}"
                                        data-category="shoes">
                                        <i class="fa-solid fa-shoe-prints"></i>
                                        <p>Giày </p>
                                    </a>
                                    <a href="#" wire:click="filterCategory('ao')"
                                        class="category-filter flex gap-3 rounded-md p-3 font-semibold transition-colors duration-200
                                        {{ $ao == 'true' ? 'bg-blue-500 text-white hover:bg-blue-600' : 'bg-slate-100 hover:bg-slate-200' }}"
                                        data-category="shirts">
                                        <i class="fa-solid fa-shirt"></i>
                                        <p>Áo</p>
                                    </a>
                                    <a href="#" wire:click="filterCategory('tatvo')"
                                        class="category-filter flex gap-3 rounded-md p-3 font-semibold transition-colors duration-200
                                        {{ $tatvo == 'true' ? 'bg-blue-500 text-white hover:bg-blue-600' : 'bg-slate-100 hover:bg-slate-200' }}"
                                        data-category="socks">
                                        <i class="fa-solid fa-socks"></i>
                                        <p>Tất, vớ, dép</p>
                                    </a>
                                    <a href="#" wire:click="filterCategory('phukien')"
                                        class="category-filter flex gap-3 rounded-md p-3 font-semibold transition-colors duration-200
                                        {{ $phukien == 'true' ? 'bg-blue-500 text-white hover:bg-blue-600' : 'bg-slate-100 hover:bg-slate-200' }}"
                                        data-category="accessories">
                                        <i class="fa-solid fa-suitcase-rolling"></i>
                                        <p>Phụ kiện</p>
                                    </a>
                                </div>
                            </div>

                            <!-- Divider -->
                            <div class="mb-6 mt-6 h-px w-full bg-[#d9d9d9]"></div>
                            <!-- FIlter One -->
                            <div class="flex flex-col gap-6">
                                <div
                                    class="flex cursor-pointer items-center justify-between py-4 [border-top:1px_solid_rgba(0,_0,_0,_0)] md:py-0">
                                    <p class="font-semibold">Thương hiệu</p>
                                </div>
                                <div class="flex flex-col gap-3">
                                    @foreach ($brands as $brand)
                                        <label class="flex items-center text-sm font-medium">
                                            <input type="checkbox" value="{{ $brand }}"
                                                wire:model.live.debounce.300ms="brandSelected"
                                                class="mr-3 h-5 w-5 cursor-pointer rounded-sm border border-solid bg-[#f2f2f7]">
                                            <span class="inline-block cursor-pointer">{{ $brand }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <!-- Divider -->
                            <div class="mb-6 mt-6 h-px w-full bg-[#d9d9d9]"></div>
                            <!-- FIlter Two -->
                            <div class="flex flex-col gap-6">
                                <div
                                    class="flex cursor-pointer items-center justify-between py-4 [border-top:1px_solid_rgba(0,_0,_0,_0)] md:py-0">
                                    <p class="font-semibold">Danh mục giày</p>
                                </div>
                                <div class="flex flex-col gap-3">
                                    @foreach ($types as $type)
                                        <label class="flex items-center text-sm font-medium">
                                            <input type="checkbox" value="{{ $type }}"
                                                wire:model.live.debounce.300ms="typeSelected"
                                                class="mr-3 h-5 w-5 cursor-pointer rounded-sm border border-solid bg-[#f2f2f7]">
                                            <span class="inline-block cursor-pointer">
                                                {{ $type }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <!-- Divider -->
                            <div class="mb-6 mt-6 h-px w-full bg-[#d9d9d9]"></div>
                            <!-- Filter Tags -->
                            <div class="flex flex-col gap-6">
                                <div
                                    class="flex cursor-pointer items-center justify-between py-4 [border-top:1px_solid_rgba(0,_0,_0,_0)] md:py-0">
                                    <p class="font-semibold">Thẻ gán</p>
                                </div>
                                <div class="flex flex-col gap-3">
                                    @forelse ($tags as $tag)
                                        <label class="flex items-center text-sm font-medium">
                                            <input type="checkbox" value="{{ $tag->name }}"
                                                wire:model.live.debounce.300ms="tagSelected"
                                                class="mr-3 h-5 w-5 cursor-pointer rounded-sm border border-solid bg-[#f2f2f7]">
                                            <span class="inline-block cursor-pointer">
                                                {{ $tag->name }} 
                                                {{-- <span class="text-xs text-gray-500">({{ $tag->products_count }})</span> --}}
                                            </span>
                                        </label>
                                    @empty
                                        <p class="text-sm text-gray-500">Không có thẻ gán</p>
                                    @endforelse
                                </div>
                            </div>
                            <div class="mb-6 mt-6 h-px w-full bg-[#d9d9d9]"></div>
                        </form>
                    </div>
                    <!-- Product List -->
                    <div class="w-full [border-left:1px_solid_rgb(217,_217,_217)] px-2">
                        <!-- Hiện thị số sản phẩm tìm kiếm được và bộ lọc  -->
                        <div class="flex items-center justify-between py-4">
                            <p class="text-sm font-medium">
                                Hiển thị {{ $products->count() }} trong {{ $tong_giay }} sản phẩm
                            </p>
                            <div class="flex items-center gap-4">
                                <p class="text-sm font-medium">Sắp xếp theo:</p>
                                <select wire:model.live.debouce.300ms="sort"
                                    class="h-9 rounded-md border border-solid border-[#cccccc] bg-[#f2f2f7] px-3 py-1 text-sm font-medium">
                                    <option value="latest">Mới nhất</option>
                                    <option value="price_asc">Giá: Thấp đến cao</option>
                                    <option value="price_desc">Giá: Cao đến thấp</option>
                                </select>
                            </div>
                        </div>

                        <div class="h-full">
                            @include('component.shop.product_cat_list', compact('products'))
                            <!-- Vùng tải thêm -->
                            @if ($tong_giay > $products->count())
                                <div x-data x-intersect="$wire.loadMore()" class="mt-4 text-center">
                                    <p wire:loading wire:target="loadMore">
                                        @include('partials.loading')
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
