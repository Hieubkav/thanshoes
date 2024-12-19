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
    <div class="mx-auto max-w-9xl px-5 py-16 md:px-10 md:py-24">
        <!-- Component -->
        <div class="flex flex-col gap-12">
            <!-- Title -->
            <div class="flex flex-col gap-5">
                <h3 class="text-2xl font-bold md:text-4xl">
                    Danh sách sản phẩm
                </h3>
                {{-- <p class="text-sm text-[#808080] sm:text-base">tìm hết ở đây...</p> --}}
            </div>
            <!-- Content -->
            <div class="grid gap-8 md:gap-10 lg:grid-cols-[max-content_1fr]">
                <!-- Filters -->
                <div class="mb-4 max-w-none 2xl:max-w-sm">
                    <form  name="wf-form-Filter-2" method="get" class="flex-col gap-6" action="{{ route('shop.cat_filter') }}">
                        <!-- Filters title -->
                        <div
                            class="mb-6 flex items-center justify-between py-4 [border-bottom:1px_solid_rgb(217,_217,_217)]">
                            <h5 class="text-xl font-bold">Bộ lọc</h5>
                            <button type="submit" class="text-sm bg-blue-500 text-white px-4 py-2 rounded-md">
                                Áp dụng
                            </button>
                        </div>
                        <!-- Search input -->
                        <input type="text" name="search"
                            class="mb-10 block h-9 min-h-[44px] w-full rounded-md border border-solid border-[#cccccc] bg-[#f2f2f7] bg-[16px_center] bg-no-repeat py-3 pl-11 pr-4 text-sm font-bold text-[#333333] [background-size:18px] [border-bottom:1px_solid_rgb(215,_215,_221)]"
                            placeholder="Tìm kiếm" value="{{ request()->search ?? '' }}"
                            style="background-image: url('https://assets.website-files.com/6458c625291a94a195e6cf3a/64b7a3a33cd5dc368f46daaa_MagnifyingGlass.svg');" />
                        <!-- Categories -->
                        <div class="flex flex-col gap-6">
                            <p class="font-semibold">Phân mục</p>
                            <div class="flex flex-wrap items-center gap-2">
                                <a name="giay" href="#" class="category-filter flex gap-3 rounded-md bg-[#f2f2f7] p-3 font-semibold" data-category="shoes">
                                    <i class="fa-solid fa-shoe-prints"></i>
                                    <p>Giày</p>
                                </a>
                                <a name="ao" href="#" class="category-filter flex gap-3 rounded-md bg-[#f2f2f7] p-3 font-semibold" data-category="shirts">
                                    <i class="fa-solid fa-shirt"></i>
                                    <p>Áo</p>
                                </a>
                                <a name="tatvo" href="#" class="category-filter flex gap-3 rounded-md bg-[#f2f2f7] p-3 font-semibold" data-category="socks">
                                    <i class="fa-solid fa-socks"></i>
                                    <p>Tất, vớ</p>
                                </a>
                                <a name="phukien" href="#" class="category-filter flex gap-3 rounded-md bg-[#f2f2f7] p-3 font-semibold" data-category="accessories">
                                    <i class="fa-solid fa-suitcase-rolling"></i>
                                    <p>Phụ kiện</p>
                                </a>
                            </div>

                            <script>
                                document.querySelectorAll('.category-filter').forEach(item => {
                                    item.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        this.classList.toggle('bg-blue-500');
                                        this.classList.toggle('text-white');
                                        const input = document.createElement('input');
                                        input.type = 'hidden';
                                        input.name = this.getAttribute('name');
                                        input.value = this.classList.contains('bg-blue-500') ? 'true' : 'false';
                                        this.closest('form').appendChild(input);
                                    });
                                });
                            </script>
                        </div>
                        <!-- Divider -->
                        <div class="mb-6 mt-6 h-px w-full bg-[#d9d9d9]"></div>
                        {{-- <!-- Rating -->
                        <div class="flex flex-col gap-6">
                            <p class="font-semibold">Rating</p>
                            <div class="flex flex-wrap gap-2 lg:justify-between">
                                <div
                                    class="flex h-9 w-14 cursor-pointer items-center justify-center rounded-md border border-solid border-[#cccccc] bg-[#f2f2f7] text-sm font-semibold">
                                    <span>1</span>
                                </div>
                                <div
                                    class="flex h-9 w-14 cursor-pointer items-center justify-center rounded-md border border-solid border-[#cccccc] bg-black text-sm font-semibold text-white">
                                    <span>2</span>
                                </div>
                                <div
                                    class="flex h-9 w-14 cursor-pointer items-center justify-center rounded-md border border-solid border-[#cccccc] bg-[#f2f2f7] text-sm font-semibold">
                                    <span>3</span>
                                </div>
                                <div
                                    class="flex h-9 w-14 cursor-pointer items-center justify-center rounded-md border border-solid border-[#cccccc] bg-[#f2f2f7] text-sm font-semibold">
                                    <span>4</span>
                                </div>
                                <div
                                    class="flex h-9 w-14 cursor-pointer items-center justify-center rounded-md border border-solid border-[#cccccc] bg-[#f2f2f7] text-sm font-semibold">
                                    <span>5</span>
                                </div>
                            </div>
                        </div> --}}
                        <!-- Divider -->
                        <div class="mb-6 mt-6 h-px w-full bg-[#d9d9d9]"></div>
                        <!-- FIlter One -->
                        <div class="flex flex-col gap-6">
                            <div
                                class="flex cursor-pointer items-center justify-between py-4 [border-top:1px_solid_rgba(0,_0,_0,_0)] md:py-0">
                                <p class="font-semibold">Thương hiệu</p>
                                <a href="#" class="inline-block text-sm text-black">
                                    <p>Clear</p>
                                </a>
                            </div>
                            <div class="flex flex-col gap-3">
                                @foreach ($brands as $brand)
                                    <label class="flex items-center text-sm font-medium">
                                        <input type="checkbox" name="brand[]" value="{{ $brand }}" class="mr-3 h-5 w-5 cursor-pointer rounded-sm border border-solid bg-[#f2f2f7]">
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
                                <a href="#" class="inline-block text-sm text-black">
                                    <p>Clear</p>
                                </a>
                            </div>
                            <div class="flex flex-col gap-3">
                                @foreach ($types as $type)
                                    <label class="flex items-center text-sm font-medium">
                                        <input type="checkbox" name="type[]" value="{{ $type }}" class="mr-3 h-5 w-5 cursor-pointer rounded-sm border border-solid bg-[#f2f2f7]">
                                        <span class="inline-block cursor-pointer">
                                            {{ $type }}
                                            ({{ $products->where('type', $type)->count() }})
                                        </span>
                                    </label>
                                @endforeach

                            </div>
                        </div>
                    </form>
                </div>
                <!-- Decor -->
                <div class="w-full [border-left:1px_solid_rgb(217,_217,_217)] px-2">
                    <!-- Hiện thị số sản phẩm tìm kiếm được và bộ lọc  -->
                    <div class="flex items-center justify-between py-4">
                        <p class="text-sm font-medium">
                            Hiển thị {{ $products->count() }} sản phẩm
                        </p>
                        <div class="flex items-center gap-4">
                            <p class="text-sm font-medium">Sắp xếp theo:</p>
                            <select name="sort" class="h-9 rounded-md border border-solid border-[#cccccc] bg-[#f2f2f7] px-3 py-1 text-sm font-medium">
                                <option value="default">Mặc định</option>
                                <option value="latest">Mới nhất</option>
                                <option value="price_asc">Giá: Thấp đến cao</option>
                                <option value="price_desc">Giá: Cao đến thấp</option>
                            </select>
                        </div>
                    </div>


                    <div class="h-full bg-gray-50 ">
                        @include('component.shop.product_cat_list',compact('products'))
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
