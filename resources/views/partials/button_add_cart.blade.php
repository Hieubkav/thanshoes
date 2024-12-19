<button class="cartBtn flex items-center justify-center gap-2 w-40 h-12 bg-blue-600 text-white font-medium shadow-lg transition-transform transform hover:scale-95" x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false">
    <svg class="cart" :class="{ 'animate-slide-in-left': hover }" fill="white" viewBox="0 0 576 512" height="1em" xmlns="http://www.w3.org/2000/svg"><path d="M0 24C0 10.7 10.7 0 24 0H69.5c22 0 41.5 12.8 50.6 32h411c26.3 0 45.5 25 38.6 50.4l-41 152.3c-8.5 31.4-37 53.3-69.5 53.3H170.7l5.4 28.5c2.2 11.3 12.1 19.5 23.6 19.5H488c13.3 0 24 10.7 24 24s-10.7 24-24 24H199.7c-34.6 0-64.3-24.6-70.7-58.5L77.4 54.5c-.7-3.8-4-6.5-7.9-6.5H24C10.7 48 0 37.3 0 24zM128 464a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm336-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z"></path></svg>
    Thêm giỏ hàng
    <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 640 512" class="product absolute w-3 left-6 bottom-6 opacity-0" :class="{ 'animate-slide-in-top': hover }"><path d="M528.12 301.319l47.273-208.319c3.442-15.151-8.008-29.319-23.273-29.319h-111.999c-8.837 0-16 7.163-16 16v32h-32v-32c0-8.837-7.163-16-16-16h-111.999c-15.265 0-26.715 14.168-23.273 29.319l47.273 208.319c-28.676 20.676-47.273 54.676-47.273 92.681 0 61.856 50.144 112 112 112s112-50.144 112-112c0-38.005-18.597-72.005-47.273-92.681zm-47.273 92.681c0 26.467-21.533 48-48 48s-48-21.533-48-48c0-26.467 21.533-48 48-48s48 21.533 48 48z"></path></svg>
</button>

<style>
@keyframes slide-in-top {
    0% {
        transform: translateY(-30px);
        opacity: 1;
    }
    100% {
        transform: translateY(0) rotate(-90deg);
        opacity: 1;
    }
}

@keyframes slide-in-left {
    0% {
        transform: translateX(-10px);
        opacity: 0;
    }
    100% {
        transform: translateX(0);
        opacity: 1;
    }
}

.animate-slide-in-top {
    animation: slide-in-top 1.2s cubic-bezier(0.250, 0.460, 0.450, 0.940) both;
}

.animate-slide-in-left {
    animation: slide-in-left 1s cubic-bezier(0.250, 0.460, 0.450, 0.940) both;
}
</style>
