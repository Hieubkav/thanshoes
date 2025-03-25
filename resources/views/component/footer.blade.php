<footer class="bg-orange-500 text-white">
    <div class="mx-auto w-full max-w-screen-xl px-4 py-4">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <a href="" class="flex items-center">
                <img src="{{asset('images/logo.png')}}" class="h-12 me-3" alt="Shop Logo" />
                <span class="self-center text-2xl font-semibold whitespace-nowrap">Than.Shoes</span>
            </a>
            <p class="text-lg font-medium italic">
                "Bước Chân Hoàn Hảo, Phong Cách Đỉnh Cao"
            </p>
            <div class="flex space-x-6">
                <a href="{{ App\Models\Setting::first()->facebook }}" target="_blank" class="text-white hover:text-gray-200 text-xl transition-colors duration-300">
                    <i class="fab fa-facebook-f"></i>
                    <span class="sr-only">Facebook page</span>
                </a>
                <a href="{{ App\Models\Setting::first()->link_tiktok }}" target="_blank" class="text-white hover:text-gray-200 text-xl transition-colors duration-300">
                    <i class="fab fa-tiktok"></i>
                    <span class="sr-only">TikTok page</span>
                </a>
            </div>
        </div>
    </div>
</footer>
