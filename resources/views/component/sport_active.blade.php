@props(['websiteDesign'])

@php
    use App\Models\Tag;
    
    // Lấy các tag có hình ảnh để hiển thị trong carousel
    $tags = Tag::whereNotNull('image')->take(120)->get();
@endphp

@if($websiteDesign->effect_status)
<div class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Khám phá bộ sưu tập</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Tìm kiếm sản phẩm theo danh mục yêu thích của bạn</p>
        </div>
        
        <div class="tag-carousel-container relative">
            <div class="tag-carousel overflow-hidden">
                <div class="flex gap-4 tag-carousel-inner transition-all duration-300">
                    @foreach($tags as $tag)
                    <div class="tag-item flex-shrink-0" style="width: 240px;">
                        <a href="{{ route('shop.cat_filter', ['tag' => $tag->name]) }}" class="block group">
                            <div class="relative overflow-hidden rounded-lg shadow-lg">
                                <div class="h-64 w-full">
                                    @if($tag->image)
                                        <img src="{{ asset('storage/' . $tag->image) }}" 
                                            alt="{{ $tag->name }}" 
                                            class="w-full h-full object-cover transition duration-500 transform group-hover:scale-110">
                                    @else
                                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                            <span class="text-gray-400">Không có hình</span>
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-black/20 flex flex-col items-center justify-end p-6 opacity-90 transition-opacity group-hover:opacity-100">
                                        <h3 class="text-white text-xl font-bold mb-1">{{ $tag->name }}</h3>
                                        {{-- <span class="text-white text-sm font-medium bg-blue-600 px-3 py-1 rounded-full">{{ $tag->products_count ?? 0 }} sản phẩm</span> --}}
                                        <div class="w-0 group-hover:w-full h-0.5 bg-white mt-3 transition-all duration-300"></div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Navigation buttons -->
            <button id="prevBtn" class="absolute left-0 top-1/2 -translate-y-1/2 -ml-4 w-12 h-12 rounded-full bg-white shadow-lg flex items-center justify-center hover:bg-gray-100 focus:outline-none z-10">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 text-gray-800">
                    <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 010-1.06l7.5-7.5a.75.75 0 111.06 1.06L9.31 12l6.97 6.97a.75.75 0 11-1.06 1.06l-7.5-7.5z" clip-rule="evenodd" />
                </svg>
            </button>
            <button id="nextBtn" class="absolute right-0 top-1/2 -translate-y-1/2 -mr-4 w-12 h-12 rounded-full bg-white shadow-lg flex items-center justify-center hover:bg-gray-100 focus:outline-none z-10">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 text-gray-800">
                    <path fill-rule="evenodd" d="M16.28 11.47a.75.75 0 010 1.06l-7.5 7.5a.75.75 0 01-1.06-1.06L14.69 12 7.72 5.03a.75.75 0 011.06-1.06l7.5-7.5z" clip-rule="evenodd" />
                </svg>
            </button>
            
            <!-- Dots indicator -->
            <div class="flex justify-center mt-6 gap-2" id="carousel-dots">
                <!-- Dots will be added here by JavaScript -->
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const carousel = document.querySelector('.tag-carousel-inner');
        const items = document.querySelectorAll('.tag-item');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const dotsContainer = document.getElementById('carousel-dots');
        
        const itemWidth = 240 + 16; // Item width + gap
        const itemsPerView = Math.floor(carousel.offsetWidth / itemWidth);
        const totalGroups = Math.ceil(items.length / itemsPerView);
        let currentIndex = 0;
        
        // Create indicator dots
        for (let i = 0; i < totalGroups; i++) {
            const dot = document.createElement('button');
            dot.classList.add('w-3', 'h-3', 'rounded-full', 'bg-gray-300', 'hover:bg-gray-500', 'focus:outline-none');
            if (i === 0) dot.classList.add('bg-blue-600');
            dot.setAttribute('data-index', i);
            dot.addEventListener('click', () => {
                goToSlide(i);
            });
            dotsContainer.appendChild(dot);
        }
        
        // Update indicator dots
        function updateDots() {
            const dots = dotsContainer.querySelectorAll('button');
            dots.forEach((dot, index) => {
                if (index === currentIndex) {
                    dot.classList.remove('bg-gray-300');
                    dot.classList.add('bg-blue-600');
                } else {
                    dot.classList.remove('bg-blue-600');
                    dot.classList.add('bg-gray-300');
                }
            });
        }
        
        // Go to specific slide
        function goToSlide(index) {
            currentIndex = index;
            const offset = -index * itemsPerView * itemWidth;
            carousel.style.transform = `translateX(${offset}px)`;
            updateDots();
        }
        
        // Previous slide
        prevBtn.addEventListener('click', () => {
            if (currentIndex > 0) {
                goToSlide(currentIndex - 1);
            } else {
                goToSlide(totalGroups - 1); // Loop to end
            }
        });
        
        // Next slide
        nextBtn.addEventListener('click', () => {
            if (currentIndex < totalGroups - 1) {
                goToSlide(currentIndex + 1);
            } else {
                goToSlide(0); // Loop to beginning
            }
        });
        
        // Auto slide
        let autoSlideInterval = setInterval(() => {
            if (currentIndex < totalGroups - 1) {
                goToSlide(currentIndex + 1);
            } else {
                goToSlide(0);
            }
        }, 5000);
        
        // Pause auto slide on hover
        const carouselContainer = document.querySelector('.tag-carousel-container');
        carouselContainer.addEventListener('mouseenter', () => {
            clearInterval(autoSlideInterval);
        });
        
        carouselContainer.addEventListener('mouseleave', () => {
            autoSlideInterval = setInterval(() => {
                if (currentIndex < totalGroups - 1) {
                    goToSlide(currentIndex + 1);
                } else {
                    goToSlide(0);
                }
            }, 5000);
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
            const newItemsPerView = Math.floor(carousel.offsetWidth / itemWidth);
            if (newItemsPerView !== itemsPerView) {
                // Reload the page to recalculate everything
                // This is a simple approach; a more complex one would dynamically adjust
                location.reload();
            }
        });
        
        // Initial setup
        goToSlide(0);
    });
</script>
@endif
