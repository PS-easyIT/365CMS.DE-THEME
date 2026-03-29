/**
 * Theme JS - Dark Mode, Preferences
 *
 * @package IT_Expert_Network_Theme
 */

(function () {
    'use strict';

    const STORAGE_KEY = 'cms365-theme';
    const LEGACY_STORAGE_KEY = 'cms_dark_mode';
    const COOKIE_STORAGE_KEY = 'cms_cookie_consent';
    const DARK_CLASS = 'dark-mode';
    const body = document.body;

    function safeGetStorage(key) {
        try {
            return window.localStorage.getItem(key);
        } catch (error) {
            return null;
        }
    }

    function safeSetStorage(key, value) {
        try {
            window.localStorage.setItem(key, value);
            return true;
        } catch (error) {
            return false;
        }
    }

    function safeRemoveStorage(key) {
        try {
            window.localStorage.removeItem(key);
        } catch (error) {
            // noop
        }
    }

    function getStoredMode() {
        const stored = safeGetStorage(STORAGE_KEY);
        if (stored !== null) {
            return stored === 'dark';
        }

        const legacyStored = safeGetStorage(LEGACY_STORAGE_KEY);
        if (legacyStored !== null) {
            return legacyStored === '1';
        }

        return Boolean(window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
    }

    function applyDarkMode(isDark) {
        body.classList.toggle(DARK_CLASS, isDark);
    }

    function toggleDarkMode() {
        const isDark = body.classList.toggle(DARK_CLASS);
        safeSetStorage(STORAGE_KEY, isDark ? 'dark' : 'light');
        safeRemoveStorage(LEGACY_STORAGE_KEY);
    }

    function initCookieBanner() {
        const banner = document.querySelector('[data-cookie-banner]');
        if (!banner) {
            return;
        }

        const accent = banner.dataset.cookieAccent;
        if (accent) {
            banner.style.setProperty('--cookie-accent-color', accent);
        }

        if (safeGetStorage(COOKIE_STORAGE_KEY) !== null) {
            banner.remove();
            return;
        }

        window.requestAnimationFrame(function () {
            banner.classList.add('is-visible');
            banner.setAttribute('aria-hidden', 'false');
        });

        banner.querySelectorAll('[data-cookie-action]').forEach(function (button) {
            button.addEventListener('click', function () {
                const action = button.getAttribute('data-cookie-action') === 'all' ? 'all' : 'essential';
                safeSetStorage(COOKIE_STORAGE_KEY, action);
                banner.classList.remove('is-visible');
                banner.setAttribute('aria-hidden', 'true');
                window.setTimeout(function () {
                    banner.remove();
                }, 220);
            }, { once: true });
        });
    }

    function initScrollToTop() {
        if (document.getElementById('scrollToTop')) {
            return;
        }

        const scrollBtn = document.createElement('button');
        scrollBtn.id = 'scrollToTop';
        scrollBtn.type = 'button';
        scrollBtn.className = 'scroll-to-top';
        scrollBtn.textContent = '↑';
        scrollBtn.setAttribute('aria-label', 'Nach oben scrollen');

        document.body.appendChild(scrollBtn);

        window.addEventListener('scroll', function () {
            scrollBtn.classList.toggle('is-visible', window.scrollY > 400);
        }, { passive: true });

        scrollBtn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    function initDirectoryAutoSubmit() {
        document.querySelectorAll('[data-auto-submit-filter]').forEach(function (element) {
            element.addEventListener('change', function () {
                const form = element.form;
                if (form) {
                    form.requestSubmit();
                }
            });
        });
    }

    function initHistoryBackButtons() {
        document.querySelectorAll('[data-history-back]').forEach(function (button) {
            button.addEventListener('click', function () {
                if (window.history.length > 1) {
                    window.history.back();
                    return;
                }

                const fallbackUrl = button.getAttribute('data-history-fallback') || '/';
                window.location.href = fallbackUrl;
            });
        });
    }

    function initHeaderNetworkCanvas() {
        if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        const canvas = document.getElementById('networkCanvas');
        if (!canvas) {
            return;
        }

        const context = canvas.getContext('2d');
        if (!context) {
            return;
        }

        let nodes = [];
        let raf = null;
        let isInViewport = false;

        const nodeCount = Math.max(8, Math.min(60, Number.parseInt(canvas.dataset.nodeCount || '25', 10) || 25));
        const speedMap = { slow: 0.15, normal: 0.35, fast: 0.6 };
        const baseSpeed = speedMap[canvas.dataset.speed || 'slow'] || speedMap.slow;
        const maxDistance = 120;

        function resize() {
            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;
        }

        function resetNodes() {
            resize();
            nodes = [];

            for (let index = 0; index < nodeCount; index += 1) {
                nodes.push({
                    x: Math.random() * canvas.width,
                    y: Math.random() * canvas.height,
                    vx: (Math.random() - 0.5) * baseSpeed,
                    vy: (Math.random() - 0.5) * baseSpeed,
                    r: Math.random() * 1.5 + 0.8,
                });
            }
        }

        function stopAnimation() {
            if (raf !== null) {
                window.cancelAnimationFrame(raf);
                raf = null;
            }
        }

        function startAnimation() {
            if (raf === null && isInViewport && !document.hidden) {
                raf = window.requestAnimationFrame(drawFrame);
            }
        }

        function drawFrame() {
            context.clearRect(0, 0, canvas.width, canvas.height);
            const width = canvas.width;
            const height = canvas.height;

            for (let i = 0; i < nodes.length; i += 1) {
                for (let j = i + 1; j < nodes.length; j += 1) {
                    const dx = nodes[i].x - nodes[j].x;
                    const dy = nodes[i].y - nodes[j].y;
                    const distance = Math.sqrt(dx * dx + dy * dy);

                    if (distance < maxDistance) {
                        const alpha = 1 - distance / maxDistance;
                        context.strokeStyle = 'rgba(200, 149, 46, ' + (alpha * 0.35) + ')';
                        context.lineWidth = 0.5;
                        context.beginPath();
                        context.moveTo(nodes[i].x, nodes[i].y);
                        context.lineTo(nodes[j].x, nodes[j].y);
                        context.stroke();
                    }
                }
            }

            nodes.forEach(function (node) {
                context.fillStyle = 'rgba(200, 149, 46, 0.6)';
                context.beginPath();
                context.arc(node.x, node.y, node.r, 0, Math.PI * 2);
                context.fill();

                node.x += node.vx;
                node.y += node.vy;

                if (node.x < 0 || node.x > width) {
                    node.vx *= -1;
                }

                if (node.y < 0 || node.y > height) {
                    node.vy *= -1;
                }
            });

            raf = window.requestAnimationFrame(drawFrame);
        }

        window.addEventListener('resize', resetNodes, { passive: true });

        const observer = new IntersectionObserver(function (entries) {
            if (entries[0]) {
                isInViewport = entries[0].isIntersecting;
            }

            if (isInViewport) {
                startAnimation();
            } else {
                stopAnimation();
            }
        }, { threshold: 0.1 });

        document.addEventListener('visibilitychange', function () {
            if (document.hidden) {
                stopAnimation();
                return;
            }

            startAnimation();
        });

        observer.observe(canvas);
        resetNodes();
        startAnimation();
    }

    applyDarkMode(getStoredMode());

    document.addEventListener('DOMContentLoaded', function () {
        const themeToggle = document.getElementById('themeToggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', toggleDarkMode);
        }

        initDirectoryAutoSubmit();
        initHistoryBackButtons();
        initHeaderNetworkCanvas();
        initCookieBanner();
        initScrollToTop();

        if (window.matchMedia) {
            const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            const onChange = function (event) {
                if (safeGetStorage(STORAGE_KEY) === null && safeGetStorage(LEGACY_STORAGE_KEY) === null) {
                    applyDarkMode(event.matches);
                }
            };

            if (typeof mediaQuery.addEventListener === 'function') {
                mediaQuery.addEventListener('change', onChange);
            } else if (typeof mediaQuery.addListener === 'function') {
                mediaQuery.addListener(onChange);
            }
        }
    });

}());
