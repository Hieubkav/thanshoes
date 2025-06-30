<div class="topbar">
    <div class="bg-gradient-to-r from-primary-600 to-primary-500 text-white py-3 px-4 flex justify-between items-center shadow-sm">
        <div class="flex-1 flex justify-center">
            <div class="aurora-container">
                <i class="fas fa-shipping-fast mr-2"></i>
                <span class="aurora-title">{{ $setting->slogan ?? 'FREESHIP VỚI ĐƠN HÀNG TỪ 500K' }}</span>
            </div>
        </div>
        <button class="text-white/80 hover:text-white focus:outline-none transition-colors duration-200 p-1 rounded-md hover:bg-white/10">
            <i class="fas fa-times button_close_top_bar text-sm"></i>
        </button>
    </div>
</div>

{{-- CSS cho hiệu ứng Aurora --}}
<style>
    /* CSS @property cho gradient text animation */
    @property --gradient-color-1 {
        syntax: "<color>";
        inherits: false;
        initial-value: hsl(180 100% 60%);
    }

    @property --gradient-color-2 {
        syntax: "<color>";
        inherits: false;
        initial-value: hsl(120 100% 60%);
    }

    @property --gradient-color-3 {
        syntax: "<color>";
        inherits: false;
        initial-value: hsl(60 100% 60%);
    }



    .aurora-container {
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .aurora-title {
        font-size: clamp(0.875rem, 2vw, 1rem);
        font-weight: 600;
        letter-spacing: 0.5px;
        position: relative;
        overflow: hidden;

        /* Animated gradient text */
        background: linear-gradient(
            to right in oklch,
            var(--gradient-color-1),
            var(--gradient-color-2),
            var(--gradient-color-3)
        );
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        color: transparent;

        /* Animation */
        animation: gradient-change 4s ease-in-out infinite alternate;
    }





    /* Keyframes cho animated gradient text */
    @keyframes gradient-change {
        0% {
            --gradient-color-1: hsl(180 100% 60%); /* Cyan */
            --gradient-color-2: hsl(120 100% 60%); /* Green */
            --gradient-color-3: hsl(60 100% 60%);  /* Yellow */
        }
        33% {
            --gradient-color-1: hsl(120 100% 60%); /* Green */
            --gradient-color-2: hsl(60 100% 60%);  /* Yellow */
            --gradient-color-3: hsl(180 100% 60%); /* Cyan */
        }
        66% {
            --gradient-color-1: hsl(60 100% 60%);  /* Yellow */
            --gradient-color-2: hsl(180 100% 60%); /* Cyan */
            --gradient-color-3: hsl(120 100% 60%); /* Green */
        }
        100% {
            --gradient-color-1: hsl(180 100% 60%); /* Cyan */
            --gradient-color-2: hsl(120 100% 60%); /* Green */
            --gradient-color-3: hsl(60 100% 60%);  /* Yellow */
        }
    }

</style>


{{-- Script để ấn dấu x sẽ tự ẩn giao diện --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('.button_close_top_bar').addEventListener('click', function(){
            console.log('Button clicked');
            document.querySelector('.topbar').style.display = 'none';
        });
    });
</script>