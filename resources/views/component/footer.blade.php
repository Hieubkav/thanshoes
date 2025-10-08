<footer class="bg-gradient-to-r from-primary-500 to-primary-600 text-white">
    <div class="mx-auto w-full max-w-screen-xl px-4 sm:px-6 py-6 sm:py-8">
        <!-- Mobile-first Layout -->
        <div class="flex flex-col space-y-6 sm:space-y-8">
            <!-- Logo & Brand Section -->
            <div class="flex flex-col items-center text-center space-y-4 sm:flex-row sm:items-start sm:text-left sm:justify-between sm:space-y-0">
                <!-- Logo & Brand -->
                <div class="flex flex-col items-center sm:items-start space-y-3">
                    <div class="relative">
                        <img src="{{asset('images/logo.png')}}" loading="lazy" class="h-10 sm:h-14 w-auto" alt="Shop Logo" />
                        <div class="absolute inset-0 bg-white/10 rounded-lg opacity-0 hover:opacity-100 transition-opacity duration-200 -z-10"></div>
                    </div>
                    <div class="text-center sm:text-left">
                        <span class="text-xl sm:text-2xl font-bold tracking-tight">Than.Shoes</span>
                        <p class="text-primary-100 text-xs sm:text-sm mt-1">Chất lượng - Uy tín - Phong cách</p>
                    </div>
                </div>

                <!-- Social Links - Mobile Optimized -->
                <div class="flex flex-col items-center space-y-3 sm:items-end">
                    <span class="text-primary-100 text-sm font-medium hidden sm:block">Theo dõi chúng tôi:</span>
                    <div class="flex space-x-2 sm:space-x-3">
                        <a href="{{ App\Models\Setting::first()->facebook }}" target="_blank"
                           class="w-9 h-9 sm:w-10 sm:h-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white hover:text-primary-100 transition-all duration-200 hover:scale-110">
                            <i class="fab fa-facebook-f text-sm sm:text-lg"></i>
                            <span class="sr-only">Facebook page</span>
                        </a>
                        <a href="{{ App\Models\Setting::first()->link_tiktok }}" target="_blank"
                           class="w-9 h-9 sm:w-10 sm:h-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white hover:text-primary-100 transition-all duration-200 hover:scale-110">
                            <i class="fab fa-tiktok text-sm sm:text-lg"></i>
                            <span class="sr-only">TikTok page</span>
                        </a>
                    </div>
                    <!-- Mobile label for social links -->
                    <span class="text-primary-100 text-xs sm:hidden">Follow us</span>
                </div>
            </div>

            <!-- Slogan Section - Centered on mobile -->
            <div class="text-center max-w-2xl mx-auto">
                <p class="text-base sm:text-lg font-medium italic text-primary-50 leading-relaxed">
                    "Bước Chân Hoàn Hảo, Phong Cách Đỉnh Cao"
                </p>
            </div>

            <!-- Copyright Section -->
            <div class="border-t border-primary-400/30 pt-6 sm:pt-8">
                <div class="flex flex-col space-y-2 sm:flex-row sm:justify-between sm:items-center text-primary-100 text-xs sm:text-sm">
                    <p class="text-center sm:text-left">&copy; {{ date('Y') }} Than.Shoes. Tất cả quyền được bảo lưu.</p>
                    <p class="text-center sm:text-right">Thiết kế bởi <span class="font-medium text-white">ThanShoes Team</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile-friendly bottom padding for safe areas -->
    <div class="h-[env(safe-area-inset-bottom,1rem)] sm:h-0 bg-gradient-to-r from-primary-500 to-primary-600"></div>
</footer>
