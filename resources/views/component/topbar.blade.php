<div class="topbar">
    <div class="bg-black text-white py-2 px-4 flex justify-between items-center">
        <div class="flex-1 flex justify-center">
            <p class="text-center text-sm md:text-base">FREESHIP VỚI ĐƠN HÀNG TỪ 500K</p>
        </div>
        <button class="text-gray-400 hover:text-white focus:outline-none">
            <i class="fas fa-times button_close_top_bar"></i>
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