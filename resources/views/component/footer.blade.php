<footer class="bg-gradient-to-r from-primary-500 to-primary-600 text-white">
    <div class="mx-auto w-full max-w-screen-xl px-6 py-8">
        <div class="flex items-center justify-between flex-wrap gap-6">
            <!-- Logo & Brand -->
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <img src="{{asset('images/logo.png')}}" loading="lazy" class="h-14 w-auto" alt="Shop Logo" />
                    <div class="absolute inset-0 bg-white/10 rounded-lg opacity-0 hover:opacity-100 transition-opacity duration-200 -z-10"></div>
                </div>
                <div>
                    <span class="text-2xl font-bold tracking-tight">Than.Shoes</span>
                    <p class="text-primary-100 text-sm mt-1">Chất lượng - Uy tín - Phong cách</p>
                </div>
            </div>

            <!-- Slogan -->
            <div class="text-center flex-1 max-w-md">
                <p class="text-lg font-medium italic text-primary-50 leading-relaxed">
                    "Bước Chân Hoàn Hảo, Phong Cách Đỉnh Cao"
                </p>
            </div>

            <!-- Social Links -->
            <div class="flex items-center space-x-4">
                <span class="text-primary-100 text-sm font-medium hidden sm:block">Theo dõi chúng tôi:</span>
                <div class="flex space-x-3">
                    <a href="{{ App\Models\Setting::first()->facebook }}" target="_blank"
                       class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white hover:text-primary-100 transition-all duration-200 hover:scale-110">
                        <i class="fab fa-facebook-f text-lg"></i>
                        <span class="sr-only">Facebook page</span>
                    </a>
                    <a href="{{ App\Models\Setting::first()->link_tiktok }}" target="_blank"
                       class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white hover:text-primary-100 transition-all duration-200 hover:scale-110">
                        <i class="fab fa-tiktok text-lg"></i>
                        <span class="sr-only">TikTok page</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Bottom Border -->
        <div class="mt-6 pt-6 border-t border-primary-400/30">
            <div class="flex flex-col sm:flex-row justify-between items-center text-primary-100 text-sm">
                <p>&copy; {{ date('Y') }} Than.Shoes. Tất cả quyền được bảo lưu.</p>
                <p class="mt-2 sm:mt-0">Thiết kế bởi <span class="font-medium text-white">ThanShoes Team</span></p>
            </div>
        </div>
    </div>
</footer>
