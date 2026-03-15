/**
 * CMS Phinit Theme – Homepage Widgets
 * vanilla JS, kein jQuery, ES2020+
 */
(function () {
    'use strict';

    const prefersReducedMotion = () =>
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    document.addEventListener('DOMContentLoaded', () => {
        initFeaturedSidebarTitleBadges();
        initFeaturedSidebarRotators();
    });

    function initFeaturedSidebarTitleBadges() {
        const badges = Array.from(document.querySelectorAll('.sb-featured-title-badge'));
        if (!badges.length) return;

        let resizeTimerId = 0;

        const getLineCount = (element) => {
            const styles = window.getComputedStyle(element);
            let lineHeight = Number.parseFloat(styles.lineHeight);

            if (!Number.isFinite(lineHeight) || lineHeight <= 0) {
                const fontSize = Number.parseFloat(styles.fontSize) || 12;
                lineHeight = fontSize * 1.25;
            }

            const height = element.getBoundingClientRect().height;
            return Math.max(1, Math.round(height / lineHeight));
        };

        const syncBadges = () => {
            badges.forEach((badge) => {
                const text = badge.querySelector('.sb-featured-title-badge__text');
                if (!(text instanceof HTMLElement)) {
                    return;
                }

                const isMultiline = getLineCount(text) > 1;
                badge.classList.toggle('is-multiline', isMultiline);
            });
        };

        const scheduleSync = () => {
            window.requestAnimationFrame(syncBadges);
        };

        scheduleSync();
        window.addEventListener('load', scheduleSync, { once: true });
        window.addEventListener('resize', () => {
            window.clearTimeout(resizeTimerId);
            resizeTimerId = window.setTimeout(scheduleSync, 120);
        }, { passive: true });
    }

    function initFeaturedSidebarRotators() {
        const rotators = document.querySelectorAll('[data-featured-rotator]');
        if (!rotators.length) return;

        rotators.forEach((rotator) => {
            const slides = Array.from(rotator.querySelectorAll('[data-featured-slide]'));
            const dots = Array.from(rotator.querySelectorAll('[data-featured-dot]'));
            const interval = Number.parseInt(rotator.getAttribute('data-rotate-interval') || '6000', 10);

            if (slides.length <= 1) {
                return;
            }

            let currentIndex = Math.max(0, slides.findIndex((slide) => slide.classList.contains('is-active')));
            let timerId = 0;
            let paused = prefersReducedMotion();

            const showSlide = (nextIndex) => {
                currentIndex = ((nextIndex % slides.length) + slides.length) % slides.length;

                slides.forEach((slide, index) => {
                    const isActive = index === currentIndex;
                    slide.classList.toggle('is-active', isActive);
                    slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');
                    slide.tabIndex = isActive ? 0 : -1;
                });

                dots.forEach((dot, index) => {
                    const isActive = index === currentIndex;
                    dot.classList.toggle('is-active', isActive);
                    dot.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                });
            };

            const stopRotation = () => {
                if (timerId) {
                    window.clearInterval(timerId);
                    timerId = 0;
                }
            };

            const startRotation = () => {
                stopRotation();

                if (paused || !Number.isFinite(interval) || interval < 2000) {
                    return;
                }

                timerId = window.setInterval(() => {
                    showSlide(currentIndex + 1);
                }, interval);
            };

            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    showSlide(index);
                    startRotation();
                });
            });

            rotator.addEventListener('mouseenter', () => {
                paused = true;
                stopRotation();
            });

            rotator.addEventListener('mouseleave', () => {
                paused = prefersReducedMotion();
                startRotation();
            });

            rotator.addEventListener('focusin', () => {
                paused = true;
                stopRotation();
            });

            rotator.addEventListener('focusout', (event) => {
                if (event.relatedTarget instanceof Node && rotator.contains(event.relatedTarget)) {
                    return;
                }

                paused = prefersReducedMotion();
                startRotation();
            });

            document.addEventListener('visibilitychange', () => {
                paused = document.hidden || prefersReducedMotion();
                if (paused) {
                    stopRotation();
                    return;
                }

                startRotation();
            });

            showSlide(currentIndex);
            startRotation();
        });
    }
})();