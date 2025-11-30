document.addEventListener('DOMContentLoaded', function () {
    const parallaxBg = document.querySelector('.parallax-bg');
    const heroSection = document.getElementById('heroSection');

    if (parallaxBg && heroSection) {
        let ticking = false;

        function updateParallax() {
            const scrolled = window.pageYOffset;
            const heroHeight = heroSection.offsetHeight;

            if (scrolled < heroHeight) {
                const yPos = scrolled * 0.5;
                parallaxBg.style.transform = `translate3d(0, ${yPos}px, 0)`;
            }
            ticking = false;
        }

        function requestTick() {
            if (!ticking) {
                window.requestAnimationFrame(updateParallax);
                ticking = true;
            }
        }

        window.addEventListener('scroll', requestTick, {
            passive: true,
        });
    }
});
