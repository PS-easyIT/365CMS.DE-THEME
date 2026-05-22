/**
 * Business Theme – Navigation: Mobile Menu
 *
 * @package IT_Business_Theme
 */

(function () {
    'use strict';
    const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function init() {

        // ===== Header Scroll =====
        const header = document.getElementById('biz-masthead');
        if (header) {
            window.addEventListener('scroll', function () {
                header.classList.toggle('scrolled', window.scrollY > 60);
            }, { passive: true });
        }

        // ===== Mobile Menu =====
        const toggle  = document.getElementById('bizMobileToggle');
        const drawer  = document.getElementById('bizMobileDrawer');
        const overlay = document.getElementById('bizMobileOverlay');

        function open() {
            if (!toggle || !drawer || !overlay) return;
            drawer.classList.add('is-open');
            overlay.classList.add('is-open');
            toggle.classList.add('is-active');
            toggle.setAttribute('aria-expanded', 'true');
            drawer.setAttribute('aria-hidden', 'false');
            document.body.classList.add('mobile-menu-open');
        }

        function close() {
            if (!toggle || !drawer || !overlay) return;
            drawer.classList.remove('is-open');
            overlay.classList.remove('is-open');
            toggle.classList.remove('is-active');
            toggle.setAttribute('aria-expanded', 'false');
            drawer.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('mobile-menu-open');
        }

        function findAnchorTarget(hash) {
            if (!hash || hash.length <= 1 || !/^#[A-Za-z][A-Za-z0-9_-]*$/.test(hash)) {
                return null;
            }

            return document.getElementById(hash.slice(1));
        }

        if (toggle) {
            toggle.addEventListener('click', function () {
                drawer && drawer.classList.contains('is-open') ? close() : open();
            });
        }

        if (overlay) {
            overlay.addEventListener('click', close);
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') close();
        });

        // ===== Smooth Scroll for Anchor Links =====
        document.querySelectorAll('a[href^="#"], a[href*="/#"]').forEach(function (link) {
            link.addEventListener('click', function (e) {
                const href  = link.getAttribute('href');
                const hash  = href.includes('#') ? '#' + href.split('#')[1] : href;
                const target = findAnchorTarget(hash);
                if (target) {
                    e.preventDefault();
                    close();
                    setTimeout(function () {
                        target.scrollIntoView({ behavior: prefersReducedMotion ? 'auto' : 'smooth', block: 'start' });
                    }, drawer && drawer.classList.contains('is-open') ? 300 : 0);
                }
            });
        });

    }

    // Sicherer Einstiegspunkt
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

}());
