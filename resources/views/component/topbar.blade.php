<div class="topbar">
    <div class="bg-gradient-to-r from-primary-600 to-primary-500 text-white py-3 px-4 flex justify-between items-center shadow-sm">
        <div class="flex-1 flex justify-center">
            <p class="text-center text-sm md:text-base font-medium tracking-wide">
                <i class="fas fa-shipping-fast mr-2"></i>
                {{ $setting->slogan ?? 'FREESHIP VỚI ĐƠN HÀNG TỪ 500K' }}
            </p>
        </div>
        <button class="text-white/80 hover:text-white focus:outline-none transition-colors duration-200 p-1 rounded-md hover:bg-white/10">
            <i class="fas fa-times button_close_top_bar text-sm"></i>
        </button>
    </div>
</div>

{{-- viết script để ấn dấu x nó sẽ tự ẩn giao diện  này --}}
<script>
    document.querySelector('.button_close_top_bar').addEventListener('click', function(){
        console.log('Button clicked'); // Kiểm tra xem sự kiện click có được kích hoạt không
        document.querySelector('.topbar').style.display = 'none';
    });
</script>