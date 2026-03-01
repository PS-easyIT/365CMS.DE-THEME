/**
 * PTC Theme – Navigation & UI Interactions
 *
 * Modules:
 *   - Sticky Header (.scrolled)
 *   - Burger Menu (Mobile Drawer)
 *   - Smooth Scroll for Anchor Links
 *   - Active Nav Highlighting
 *   - Back to Top Button
 *
 * @package PTC_Theme
 */

(function () {
    'use strict';

    function init() {

        // ===== Sticky Header =====
        var header = document.getElementById('ptc-masthead');
        if (header) {
            window.addEventListener('scroll', function () {
                if (window.scrollY > 60) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            }, { passive: true });
        }

        // ===== Mobile Menu =====
        var toggle  = document.getElementById('ptcMobileToggle');
        var drawer  = document.getElementById('ptcMobileDrawer');
        var overlay = document.getElementById('ptcMobileOverlay');

        function openMenu() {
            if (!toggle || !drawer || !overlay) return;
            drawer.classList.add('is-open');
            overlay.classList.add('is-open');
            toggle.classList.add('is-active');
            toggle.setAttribute('aria-expanded', 'true');
            toggle.setAttribute('aria-label', 'Menü schließen');
            drawer.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function closeMenu() {
            if (!toggle || !drawer || !overlay) return;
            drawer.classList.remove('is-open');
            overlay.classList.remove('is-open');
            toggle.classList.remove('is-active');
            toggle.setAttribute('aria-expanded', 'false');
            toggle.setAttribute('aria-label', 'Menü öffnen');
            drawer.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            toggle.focus();
        }

        if (toggle) {
            toggle.addEventListener('click', function () {
                if (drawer && drawer.classList.contains('is-open')) {
                    closeMenu();
                } else {
                    openMenu();
                }
            });
        }

        if (overlay) {
            overlay.addEventListener('click', closeMenu);
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeMenu();
            }
        });

        // ===== Smooth Scroll for Anchor Links =====
        document.querySelectorAll('a[href^="#"], a[href*="/#"]').forEach(function (link) {
            link.addEventListener('click', function (e) {
                var href  = link.getAttribute('href');
                var hash  = href.indexOf('#') !== -1 ? '#' + href.split('#')[1] : href;
                var target = hash.length > 1 ? document.querySelector(hash) : null;
                if (target) {
                    e.preventDefault();
                    closeMenu();
                    var delay = drawer && drawer.classList.contains('is-open') ? 350 : 0;
                    setTimeout(function () {
                        var headerH = header ? header.offsetHeight : 72;
                        var top = target.getBoundingClientRect().top + window.pageYOffset - headerH;
                        window.scrollTo({ top: top, behavior: 'smooth' });
                    }, delay);
                }
            });
        });

        // ===== Active Nav =====
        var currentPath = window.location.pathname.replace(/\/$/, '') || '/';
        document.querySelectorAll('.ptc-nav li a, .ptc-mobile-drawer li a').forEach(function (link) {
            var linkPath = link.getAttribute('href');
            if (!linkPath) return;
            var parsed = linkPath.replace(/\/$/, '') || '/';
            if (parsed === currentPath) {
                link.parentElement.classList.add('active');
            }
        });

        // ===== Flash Messages Auto-Dismiss =====
        document.querySelectorAll('[data-auto-dismiss]').forEach(function (el) {
            var delay = parseInt(el.getAttribute('data-auto-dismiss'), 10) || 5000;
            setTimeout(function () {
                el.style.transition = 'opacity 0.4s ease';
                el.style.opacity = '0';
                setTimeout(function () { el.remove(); }, 400);
            }, delay);
        });
    }

    // Entry point
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

}());
