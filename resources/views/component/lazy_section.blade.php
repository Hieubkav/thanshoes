@props(['class' => '', 'threshold' => 0.1])

<div 
    class="lazy-section {{ $class }}" 
    data-threshold="{{ $threshold }}"
    style="min-height: 200px;"
>
    <div class="lazy-placeholder flex items-center justify-center">
        <div class="animate-pulse text-gray-400">
            <i class="fas fa-spinner fa-spin text-2xl"></i>
        </div>
    </div>
    <div class="lazy-content hidden">
        
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const lazySections = document.querySelectorAll('.lazy-section');
        
        if ('IntersectionObserver' in window) {
            const sectionObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const section = entry.target;
                        const placeholder = section.querySelector('.lazy-placeholder');
                        const content = section.querySelector('.lazy-content');
                        
                        // Show content and hide placeholder
                        placeholder.classList.add('hidden');
                        content.classList.remove('hidden');
                        
                        // Stop observing this element
                        sectionObserver.unobserve(section);
                    }
                });
            }, {
                rootMargin: '50px 0px',
                threshold: section => parseFloat(section.dataset.threshold)
            });
            
            lazySections.forEach(section => {
                sectionObserver.observe(section);
            });
        } else {
            // Fallback for older browsers
            lazySections.forEach(section => {
                const placeholder = section.querySelector('.lazy-placeholder');
                const content = section.querySelector('.lazy-content');
                
                setTimeout(() => {
                    placeholder.classList.add('hidden');
                    content.classList.remove('hidden');
                }, 100);
            });
        }
    });
</script>
