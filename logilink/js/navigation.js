/**
 * LogiLink Theme – Navigation & UI
 *
 * - Sticky-Header scroll state class
 * - Mobile drawer toggle (aria-expanded / aria-hidden / aria-controls)
 * - Outside-click + Escape close + body scroll-lock
 * - Search panel toggle with focus restore
 * - IntersectionObserver-based reveal (motion-allowed only) with
 *   immediate-visibility fallback when motion is reduced or API missing.
 *
 * Vanilla ES2017+, IIFE, no jQuery, no inline handlers.
 */
(function () {
    'use strict';

    const prefersReducedMotion = window.matchMedia
        && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const header       = document.getElementById('masthead');
    const mToggle      = document.getElementById('llMobileToggle');
    const drawer       = document.getElementById('llMobileDrawer');
    const overlay      = document.getElementById('llMobileOverlay');
    const searchBtn    = document.getElementById('llSearchToggle');
    const searchPanel  = document.getElementById('llSearchPanel');
    const searchClose  = document.getElementById('llSearchClose');

    // ── Sticky-Header scroll state ─────────────────────────────────────
    if (header) {
        const onScroll = function () {
            header.classList.toggle('scrolled', window.scrollY > 20);
        };
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
    }

    // ── Mobile drawer ──────────────────────────────────────────────────
    let drawerOpenerEl = null;

    function openDrawer() {
        if (!drawer || !mToggle) return;
        drawerOpenerEl = document.activeElement;
        drawer.classList.add('is-open');
        drawer.setAttribute('aria-hidden', 'false');
        if (overlay) overlay.classList.add('is-open');
        mToggle.classList.add('is-active');
        mToggle.setAttribute('aria-expanded', 'true');
        mToggle.setAttribute('aria-label', 'Menü schliessen');
        document.body.style.overflow = 'hidden';
    }

    function closeDrawer() {
        if (!drawer || !mToggle) return;
        drawer.classList.remove('is-open');
        drawer.setAttribute('aria-hidden', 'true');
        if (overlay) overlay.classList.remove('is-open');
        mToggle.classList.remove('is-active');
        mToggle.setAttribute('aria-expanded', 'false');
        mToggle.setAttribute('aria-label', 'Menü öffnen');
        document.body.style.overflow = '';
        if (drawerOpenerEl && typeof drawerOpenerEl.focus === 'function') {
            drawerOpenerEl.focus();
        } else {
            mToggle.focus();
        }
    }

    if (mToggle && drawer) {
        mToggle.addEventListener('click', function () {
            if (drawer.classList.contains('is-open')) {
                closeDrawer();
            } else {
                openDrawer();
            }
        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeDrawer);
    }

    // Outside-click safety: close drawer when click lands outside drawer + toggle.
    document.addEventListener('click', function (e) {
        if (!drawer || !drawer.classList.contains('is-open')) return;
        if (drawer.contains(e.target) || (mToggle && mToggle.contains(e.target))) return;
        closeDrawer();
    });

    // ── Search panel toggle ────────────────────────────────────────────
    let searchOpenerEl = null;

    function openSearch() {
        if (!searchPanel || !searchBtn) return;
        searchOpenerEl = document.activeElement;
        searchPanel.removeAttribute('hidden');
        searchPanel.setAttribute('aria-hidden', 'false');
        searchBtn.setAttribute('aria-expanded', 'true');
        const input = searchPanel.querySelector('input[type="search"]');
        if (input) input.focus();
    }

    function closeSearch() {
        if (!searchPanel || !searchBtn) return;
        searchPanel.setAttribute('hidden', '');
        searchPanel.setAttribute('aria-hidden', 'true');
        searchBtn.setAttribute('aria-expanded', 'false');
        if (searchOpenerEl && typeof searchOpenerEl.focus === 'function') {
            searchOpenerEl.focus();
        } else {
            searchBtn.focus();
        }
    }

    if (searchBtn && searchPanel) {
        searchBtn.addEventListener('click', function () {
            if (searchPanel.hasAttribute('hidden')) {
                openSearch();
            } else {
                closeSearch();
            }
        });
    }
    if (searchClose) {
        searchClose.addEventListener('click', closeSearch);
    }

    // Outside-click for search panel (ignore opener button).
    document.addEventListener('click', function (e) {
        if (!searchPanel || searchPanel.hasAttribute('hidden')) return;
        if (searchPanel.contains(e.target) || (searchBtn && searchBtn.contains(e.target))) return;
        closeSearch();
    });

    // ── Global Escape ──────────────────────────────────────────────────
    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        if (drawer && drawer.classList.contains('is-open')) closeDrawer();
        if (searchPanel && !searchPanel.hasAttribute('hidden')) closeSearch();
    });

    // ── Reveal on scroll (motion-allowed only) ─────────────────────────
    const revealEls = document.querySelectorAll('.ll-reveal');
    if (revealEls.length === 0) return;

    if (!prefersReducedMotion && 'IntersectionObserver' in window) {
        const obs = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });

        revealEls.forEach(function (el) { obs.observe(el); });
    } else {
        revealEls.forEach(function (el) { el.classList.add('is-visible'); });
    }
}());
