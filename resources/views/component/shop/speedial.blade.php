<div class="fixed bottom-20 right-4 flex flex-col space-y-4">
    <!-- Messenger Button -->
    <a href="https://www.facebook.com/thanshoes99" target="_blank"
        class="flex items-center justify-center w-12 h-12 md:w-14 md:h-14 bg-blue-100 rounded-full shadow-lg hover:bg-blue-200 group">
        <img src="{{ asset('images/messenger_icon.png') }}" class="h-12 w-12 bg-opacity-50 bg-purple-500 p-2 rounded-full  halo-effect" alt="Messenger Icon">
    </a>

    <!-- Zalo Button -->
    <a href="https://zalo.me/0946775145" target="_blank"
        class="flex items-center justify-center w-12 h-12 md:w-14 md:h-14 bg-blue-100 rounded-full shadow-lg hover:bg-blue-200 group">
        <img src="{{ asset('images/zalo_icon.png') }}" class="h-12 w-12 bg-opacity-50 bg-blue-500 p-2 rounded-full halo-effect" alt="Zalo Icon">
    </a>

    <!-- Phone Button -->
    <a href="javascript:void(0);" onclick="copyPhoneNumber()"
        class="flex items-center justify-center w-12 h-12 md:w-14 md:h-14 bg-blue-100 rounded-full shadow-lg hover:bg-blue-200 group">
        <img src="{{ asset('images/phone_icon.png') }}" class="h-12 w-12 bg-opacity-50 bg-red-500 p-2 rounded-full halo-effect" alt="Phone Icon">
    </a>
    <div id="phone-number" class="hidden fixed bottom-32 right-4 bg-white p-2 rounded shadow-lg">0946.775.145</div>

    <script>
        function copyPhoneNumber() {
            const phoneNumber = "0355.450.320";
            navigator.clipboard.writeText(phoneNumber).then(() => {
                const phoneNumberDiv = document.getElementById('phone-number');
                phoneNumberDiv.classList.remove('hidden');
                setTimeout(() => {
                    phoneNumberDiv.classList.add('hidden');
                }, 1000);
            });
        }
    </script>

    <!-- Thêm nút  mũi  tên đẩy lên đầu trang nhanh -->
    <a href="#top" class="flex items center justify-center w-12 h-12 md:w-14 md:h-14 rounded-full shadow-lg hover:bg-blue-200 group">
        <i class="fas fa-arrow-up text-2xl"></i>
    </a>

</div>

<style>
    /* Hiệu ứng lắc qua lắc lại */
    @keyframes shake {
        0%, 100% {
            transform: translateX(0);
        }
        25% {
            transform: translateX(-5px);
        }
        75% {
            transform: translateX(5px);
        }
    }

    .animate-shake {
        animation: shake 1s infinite ease-in-out;
    }

    /* Hiệu ứng tỏa sáng (halo effect) */
    @keyframes halo {
        0% {
            --tw-bg-opacity: 0;
            transform: rotate(-0deg);
        }
        30% {
            --tw-bg-opacity: 0.1;
            transform: rotate(-5deg);
        }
        50% {
            --tw-bg-opacity: 0.2;
            transform: rotate(-10deg);
        }
        70% {
            --tw-bg-opacity: 0.3;
            transform: rotate(5deg);
        }
        100% {
            --tw-bg-opacity: 0.5;
            transform: rotate(10deg);
        }
    }

    .halo-effect {
        animation: halo 3s infinite;
    }

    /* Tùy chỉnh nút chung */
    .fixed a {
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .fixed a:hover {
        transform: scale(1.1);
    }
</style>


