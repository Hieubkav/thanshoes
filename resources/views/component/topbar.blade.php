<div class="topbar">
    <div class="enhanced-topbar bg-gradient-to-r from-orange-600 via-orange-500 to-orange-400 text-white py-2 lg:py-3 px-4 flex justify-between items-center shadow-lg relative overflow-hidden">
        <!-- Background Animation Elements -->
        <div class="absolute inset-0 opacity-30">
            <div class="floating-shapes">
                <div class="shape shape-1"></div>
                <div class="shape shape-2"></div>
                <div class="shape shape-3"></div>
                <div class="shape shape-4"></div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex justify-center items-center relative z-10">
            <div class="topbar-content-container flex items-center justify-center gap-3">
                <!-- Animated Icon -->
                <div class="icon-container">
                    <i class="fas fa-shipping-fast animated-icon"></i>
                </div>
                
                <!-- Enhanced Text Animation -->
                <div class="text-container">
                    <span id="topbar-text" class="enhanced-text">{{ $setting->slogan ?? 'FREESHIP - ĐỒNG KIỂM - TẶNG VỚ - HỖ TRỢ ĐỔI SIZE' }}</span>
                </div>
                
                <!-- Sparkle Effects -->
                <div class="sparkles">
                    <div class="sparkle sparkle-1"></div>
                    <div class="sparkle sparkle-2"></div>
                    <div class="sparkle sparkle-3"></div>
                </div>
            </div>
        </div>
        
        <!-- Enhanced Close Button -->
        <button class="close-button text-white/90 hover:text-white focus:outline-none transition-all duration-300 p-2 rounded-full hover:bg-white/20 hover:scale-110 hover:rotate-90 relative z-10">
            <i class="fas fa-times button_close_top_bar text-lg"></i>
        </button>
    </div>
</div>

{{-- CSS cho Enhanced Topbar --}}
<style>
    /* CSS Variables for dynamic animations */
    .enhanced-topbar {
        background: linear-gradient(45deg, #ea580c, #f97316, #fb923c, #fed7aa, #fb923c, #f97316, #ea580c);
        background-size: 300% 300%;
        animation: gradientShift 6s ease infinite;
        position: relative;
        border-bottom: 2px solid rgba(255, 255, 255, 0.2);
    }

    /* Gradient Background Animation */
    @keyframes gradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* Floating Background Shapes */
    .floating-shapes {
        position: absolute;
        width: 100%;
        height: 100%;
        overflow: hidden;
    }

    .shape {
        position: absolute;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }

    .shape-1 {
        width: 20px;
        height: 20px;
        left: 10%;
        animation-delay: 0s;
        animation-duration: 8s;
    }

    .shape-2 {
        width: 15px;
        height: 15px;
        left: 80%;
        animation-delay: 2s;
        animation-duration: 6s;
    }

    .shape-3 {
        width: 25px;
        height: 25px;
        left: 60%;
        animation-delay: 4s;
        animation-duration: 10s;
    }

    .shape-4 {
        width: 12px;
        height: 12px;
        left: 30%;
        animation-delay: 1s;
        animation-duration: 7s;
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0px) rotate(0deg);
            opacity: 0.4;
        }
        50% {
            transform: translateY(-20px) rotate(180deg);
            opacity: 0.8;
        }
    }

    /* Enhanced Icon Animation */
    .icon-container {
        position: relative;
        margin-right: 12px;
    }

    .animated-icon {
        font-size: 1.5rem;
        color: #fff;
        animation: iconBounce 2s ease-in-out infinite;
        text-shadow: 0 0 10px rgba(255, 255, 255, 0.8);
        transition: all 0.3s ease;
    }

    .animated-icon:hover {
        transform: scale(1.2);
        text-shadow: 0 0 15px rgba(255, 255, 255, 1), 0 0 25px rgba(255, 255, 255, 0.6);
    }

    @keyframes iconBounce {
        0%, 100% {
            transform: translateY(0px) scale(1);
        }
        50% {
            transform: translateY(-5px) scale(1.05);
        }
    }

    /* Enhanced Text Container */
    .text-container {
        position: relative;
        overflow: hidden;
    }

    .enhanced-text {
        font-size: clamp(0.875rem, 2.5vw, 1.1rem);
        font-weight: 700;
        letter-spacing: 1px;
        color: #ffffff;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.4), 0 0 10px rgba(255, 255, 255, 0.3);
        position: relative;
        display: inline-block;
        animation: textGlow 3s ease-in-out infinite;
    }

    @keyframes textGlow {
        0%, 100% {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.4), 0 0 10px rgba(255, 255, 255, 0.3);
        }
        50% {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.4), 0 0 20px rgba(255, 255, 255, 0.6), 0 0 30px rgba(255, 193, 7, 0.4);
        }
    }

    /* Sparkle Effects */
    .sparkles {
        position: absolute;
        width: 100%;
        height: 100%;
        pointer-events: none;
    }

    .sparkle {
        position: absolute;
        width: 6px;
        height: 6px;
        background: #ffffff;
        border-radius: 50%;
        box-shadow: 0 0 6px #ffffff, 0 0 12px rgba(255, 193, 7, 0.6);
        animation: sparkle 2s linear infinite;
    }

    .sparkle-1 {
        top: 20%;
        left: 15%;
        animation-delay: 0s;
    }

    .sparkle-2 {
        top: 60%;
        right: 20%;
        animation-delay: 0.7s;
    }

    .sparkle-3 {
        top: 40%;
        left: 70%;
        animation-delay: 1.4s;
    }

    @keyframes sparkle {
        0%, 100% {
            opacity: 0;
            transform: scale(0);
        }
        50% {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* Enhanced Close Button */
    .close-button {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .close-button:hover {
        background: rgba(255, 255, 255, 0.25);
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.4);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .enhanced-topbar {
            padding: 12px 16px;
        }
        
        .topbar-content-container {
            gap: 8px;
        }
        
        .animated-icon {
            font-size: 1.25rem;
        }
        
        .enhanced-text {
            font-size: clamp(0.75rem, 3vw, 0.9rem);
            letter-spacing: 0.5px;
        }
    }

    @media (max-width: 480px) {
        .topbar-content-container {
            flex-direction: column;
            gap: 4px;
        }
        
        .icon-container {
            margin-right: 0;
            margin-bottom: 4px;
        }
    }

    /* Animation for text content changes */
    .text-fade-in {
        animation: fadeInText 0.8s ease-in-out;
    }

    @keyframes fadeInText {
        0% {
            opacity: 0;
            transform: translateY(-10px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Pulse effect for attention */
    .pulse-attention {
        animation: pulseGlow 2s ease-in-out infinite;
    }

    @keyframes pulseGlow {
        0%, 100% {
            box-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
        }
        50% {
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.8), 0 0 30px rgba(255, 165, 0, 0.6);
        }
    }
</style>


{{-- Enhanced Script with Animation Effects --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Enhanced close button with animation
        const closeButton = document.querySelector('.button_close_top_bar');
        const topbar = document.querySelector('.topbar');
        
        if (closeButton && topbar) {
            closeButton.addEventListener('click', function(){
                console.log('Topbar close button clicked');
                
                // Add closing animation
                topbar.style.animation = 'slideUp 0.5s ease-in-out forwards';
                
                setTimeout(() => {
                    topbar.style.display = 'none';
                }, 500);
            });
        }
        
        // Add CSS for slide up animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideUp {
                0% {
                    transform: translateY(0);
                    opacity: 1;
                }
                100% {
                    transform: translateY(-100%);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
        
        // Dynamic text animation effect
        const topbarText = document.getElementById('topbar-text');
        if (topbarText) {
            // Add individual character animation
            function animateText() {
                const text = topbarText.textContent;
                const words = text.split(' ');
                let animatedHTML = '';
                
                words.forEach((word, wordIndex) => {
                    const letters = word.split('');
                    let wordHTML = '';
                    
                    letters.forEach((letter, letterIndex) => {
                        const delay = (wordIndex * 0.1) + (letterIndex * 0.05);
                        if (letter === ' ') {
                            wordHTML += ' ';
                        } else {
                            wordHTML += `<span style="animation-delay: ${delay}s" class="animated-letter">${letter}</span>`;
                        }
                    });
                    
                    animatedHTML += `<span class="animated-word">${wordHTML}</span>`;
                    if (wordIndex < words.length - 1) {
                        animatedHTML += ' ';
                    }
                });
                
                topbarText.innerHTML = animatedHTML;
            }
            
            // Apply text animation
            animateText();
            
            // Add CSS for letter animation
            const letterStyle = document.createElement('style');
            letterStyle.textContent = `
                .animated-letter {
                    display: inline-block;
                    animation: letterBounce 2s ease-in-out infinite;
                }
                
                .animated-word:hover .animated-letter {
                    animation: letterWave 0.6s ease-in-out;
                }
                
                @keyframes letterBounce {
                    0%, 100% {
                        transform: translateY(0);
                    }
                    50% {
                        transform: translateY(-3px);
                    }
                }
                
                @keyframes letterWave {
                    0%, 100% {
                        transform: translateY(0) scale(1);
                    }
                    50% {
                        transform: translateY(-5px) scale(1.1);
                    }
                }
            `;
            document.head.appendChild(letterStyle);
        }
        
        // Add pulse effect periodically
        const enhancedTopbar = document.querySelector('.enhanced-topbar');
        if (enhancedTopbar) {
            setInterval(() => {
                enhancedTopbar.classList.add('pulse-attention');
                setTimeout(() => {
                    enhancedTopbar.classList.remove('pulse-attention');
                }, 2000);
            }, 10000); // Pulse every 10 seconds
        }
        
        // Add responsive behavior
        function handleResize() {
            const isMobile = window.innerWidth <= 768;
            const textContainer = document.querySelector('.text-container');
            
            if (textContainer) {
                if (isMobile) {
                    textContainer.classList.add('mobile-optimized');
                } else {
                    textContainer.classList.remove('mobile-optimized');
                }
            }
        }
        
        window.addEventListener('resize', handleResize);
        handleResize(); // Initial call
    });
</script>
