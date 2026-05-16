/**
 * CMS Phinit Theme – Homepage Widgets
 * vanilla JS, kein jQuery, ES2020+
 */
(function () {
    'use strict';

    const globalScope = window;
    if (globalScope.CMSPhinitHomepageWidgetsBooted) {
        return;
    }

    globalScope.CMSPhinitHomepageWidgetsLoaded = true;

    const prefersReducedMotion = () =>
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const lifecycleControls = new Set();
    let lifecycleEventsBound = false;

    function bindLifecycleEvents() {
        if (lifecycleEventsBound) {
            return;
        }

        lifecycleEventsBound = true;

        const pauseAll = () => {
            lifecycleControls.forEach((control) => control.pause());
        };

        const resumeAll = () => {
            lifecycleControls.forEach((control) => control.resume());
        };

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                pauseAll();
                return;
            }

            resumeAll();
        }, { passive: true });
        window.addEventListener('pagehide', pauseAll, { passive: true });
        window.addEventListener('pageshow', resumeAll, { passive: true });
    }

    function registerLifecycleControl(control) {
        lifecycleControls.add(control);
        bindLifecycleEvents();

        if (document.hidden) {
            control.pause();
        }
    }

    const boot = () => {
        if (globalScope.CMSPhinitHomepageWidgetsBooted) {
            return;
        }

        globalScope.CMSPhinitHomepageWidgetsBooted = true;
        initHomepageFeaturedBannerRotation();
        initFeaturedSidebarRotators();
        initSidebarArticleCarousels();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
    } else {
        boot();
    }

    function initHomepageFeaturedBannerRotation() {
        const rotators = document.querySelectorAll('[data-featured-banner-rotator]');
        if (!rotators.length) return;

        rotators.forEach((rotator) => {
            const slides = Array.from(rotator.querySelectorAll('[data-featured-banner-slide]'));
            const requestedInterval = Number.parseInt(rotator.getAttribute('data-rotate-interval') || '5000', 10);
            const interval = Number.isFinite(requestedInterval)
                ? Math.min(6000, Math.max(4000, requestedInterval))
                : 5000;

            if (slides.length <= 1) {
                return;
            }

            const rotationKey = rotator.getAttribute('data-rotation-key') || 'default';
            const storageKey = `cms-phinit-featured-banner:${rotationKey}`;
            let currentIndex = 0;
            let timerId = 0;
            let interactionPaused = false;
            let lifecyclePaused = document.hidden;

            const rotationPaused = () => interactionPaused || lifecyclePaused || prefersReducedMotion();

            try {
                const storedIndex = Number.parseInt(window.localStorage.getItem(storageKey) || '', 10);
                currentIndex = Number.isFinite(storedIndex) ? storedIndex % slides.length : 0;
            } catch (error) {
                currentIndex = Math.floor(Date.now() / interval) % slides.length;
            }

            const showSlide = (nextIndex) => {
                currentIndex = ((nextIndex % slides.length) + slides.length) % slides.length;

                slides.forEach((slide, index) => {
                    const isActive = index === currentIndex;
                    slide.classList.toggle('is-active', isActive);
                    slide.hidden = !isActive;
                    slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');

                    slide.querySelectorAll('a, button').forEach((control) => {
                        if (control instanceof HTMLElement) {
                            control.tabIndex = isActive ? 0 : -1;
                        }
                    });
                });

                try {
                    window.localStorage.setItem(storageKey, String(currentIndex));
                } catch (error) {
                    // Storage kann z. B. im privaten Modus blockiert sein; Rotation läuft trotzdem.
                }
            };

            const stopRotation = () => {
                if (timerId) {
                    window.clearInterval(timerId);
                    timerId = 0;
                }
            };

            const startRotation = () => {
                stopRotation();

                if (rotationPaused()) {
                    return;
                }

                timerId = window.setInterval(() => {
                    showSlide(currentIndex + 1);
                }, interval);
            };

            rotator.addEventListener('mouseenter', () => {
                interactionPaused = true;
                stopRotation();
            });

            rotator.addEventListener('mouseleave', () => {
                interactionPaused = false;
                startRotation();
            });

            rotator.addEventListener('focusin', () => {
                interactionPaused = true;
                stopRotation();
            });

            rotator.addEventListener('focusout', (event) => {
                if (event.relatedTarget instanceof Node && rotator.contains(event.relatedTarget)) {
                    return;
                }

                interactionPaused = false;
                startRotation();
            });

            registerLifecycleControl({
                pause: () => {
                    lifecyclePaused = true;
                    stopRotation();
                },
                resume: () => {
                    lifecyclePaused = document.hidden;
                    startRotation();
                }
            });

            showSlide(currentIndex);
            startRotation();
        });
    }

    function initFeaturedSidebarRotators() {
        const rotators = document.querySelectorAll('[data-featured-rotator]');
        if (!rotators.length) return;

        rotators.forEach((rotator) => {
            const slides = Array.from(rotator.querySelectorAll('[data-featured-slide]'));
            const dots = Array.from(rotator.querySelectorAll('[data-featured-dot]'));
            const prev = rotator.querySelector('[data-featured-prev]');
            const next = rotator.querySelector('[data-featured-next]');
            const interval = Number.parseInt(rotator.getAttribute('data-rotate-interval') || '6000', 10);

            if (slides.length <= 1) {
                return;
            }

            let currentIndex = Math.max(0, slides.findIndex((slide) => slide.classList.contains('is-active')));
            let timerId = 0;
            let interactionPaused = false;
            let lifecyclePaused = document.hidden;

            const rotationPaused = () => interactionPaused || lifecyclePaused || prefersReducedMotion();

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

                if (rotationPaused() || !Number.isFinite(interval) || interval < 2000) {
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

            prev?.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                showSlide(currentIndex - 1);
                startRotation();
            });

            next?.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                showSlide(currentIndex + 1);
                startRotation();
            });

            rotator.addEventListener('mouseenter', () => {
                interactionPaused = true;
                stopRotation();
            });

            rotator.addEventListener('mouseleave', () => {
                interactionPaused = false;
                startRotation();
            });

            rotator.addEventListener('focusin', () => {
                interactionPaused = true;
                stopRotation();
            });

            rotator.addEventListener('focusout', (event) => {
                if (event.relatedTarget instanceof Node && rotator.contains(event.relatedTarget)) {
                    return;
                }

                interactionPaused = false;
                startRotation();
            });

            registerLifecycleControl({
                pause: () => {
                    lifecyclePaused = true;
                    stopRotation();
                },
                resume: () => {
                    lifecyclePaused = document.hidden;
                    startRotation();
                }
            });

            showSlide(currentIndex);
            startRotation();
        });
    }

    function initSidebarArticleCarousels() {
        const carousels = document.querySelectorAll('[data-sidebar-carousel]');
        if (!carousels.length) return;

        carousels.forEach((carousel) => {
            const slides = Array.from(carousel.querySelectorAll('[data-sidebar-carousel-slide]'));
            const dots = Array.from(carousel.querySelectorAll('[data-sidebar-carousel-dot]'));
            const prev = carousel.querySelector('[data-sidebar-carousel-prev]');
            const next = carousel.querySelector('[data-sidebar-carousel-next]');
            const interval = Number.parseInt(carousel.getAttribute('data-rotate-interval') || '7000', 10);

            if (slides.length <= 1) {
                return;
            }

            let currentIndex = Math.max(0, slides.findIndex((slide) => slide.classList.contains('is-active')));
            let timerId = 0;
            let interactionPaused = false;
            let lifecyclePaused = document.hidden;

            const rotationPaused = () => interactionPaused || lifecyclePaused || prefersReducedMotion();

            const showSlide = (nextIndex) => {
                currentIndex = ((nextIndex % slides.length) + slides.length) % slides.length;

                slides.forEach((slide, index) => {
                    const isActive = index === currentIndex;
                    slide.classList.toggle('is-active', isActive);
                    slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');
                    const link = slide.querySelector('.sb-carousel-link');
                    if (link instanceof HTMLElement) {
                        link.tabIndex = isActive ? 0 : -1;
                    }
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

                if (rotationPaused() || !Number.isFinite(interval) || interval < 3000) {
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

            prev?.addEventListener('click', () => {
                showSlide(currentIndex - 1);
                startRotation();
            });

            next?.addEventListener('click', () => {
                showSlide(currentIndex + 1);
                startRotation();
            });

            carousel.addEventListener('mouseenter', () => {
                interactionPaused = true;
                stopRotation();
            });

            carousel.addEventListener('mouseleave', () => {
                interactionPaused = false;
                startRotation();
            });

            carousel.addEventListener('focusin', () => {
                interactionPaused = true;
                stopRotation();
            });

            carousel.addEventListener('focusout', (event) => {
                if (event.relatedTarget instanceof Node && carousel.contains(event.relatedTarget)) {
                    return;
                }

                interactionPaused = false;
                startRotation();
            });

            registerLifecycleControl({
                pause: () => {
                    lifecyclePaused = true;
                    stopRotation();
                },
                resume: () => {
                    lifecyclePaused = document.hidden;
                    startRotation();
                }
            });

            showSlide(currentIndex);
            startRotation();
        });
    }
})();