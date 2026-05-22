/**
 * MedCare Pro Theme – Navigation, Accessibility Toggles & UI
 *
 * - Sticky header scroll state
 * - Mobile menu drawer (aria-expanded / aria-hidden / aria-controls)
 * - Search panel (aria-hidden / focus restore / outside-click / Escape)
 * - Font-size + High-contrast toggles (persisted via localStorage, pre-paint)
 * - IntersectionObserver-based reveal (motion-allowed only) with
 *   immediate-visibility fallback when motion is reduced or API missing.
 *
 * Vanilla ES2017+, IIFE, no jQuery, no inline handlers.
 */
(function () {
    'use strict';

    const prefersReducedMotion = window.matchMedia
        && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // ── Accessibility persistence (pre-paint, so no FOUC) ──────────────
    try {
        if (localStorage.getItem('mc-large-text') === '1') {
            document.documentElement.classList.add('mc-pre-large-text');
            document.body && document.body.classList.add('mc-large-text');
        }
        if (localStorage.getItem('mc-high-contrast') === '1') {
            document.documentElement.classList.add('mc-pre-high-contrast');
            document.body && document.body.classList.add('mc-high-contrast');
        }
    } catch (_) { /* localStorage unavailable */ }

    const header     = document.getElementById('masthead');
    const mToggle    = document.getElementById('mobileMenuToggle');
    const nav        = document.getElementById('site-navigation');
    const srchBtn    = document.getElementById('searchToggle');
    const srchPnl    = document.getElementById('searchPanel');
    const srchCls    = document.getElementById('searchClose');
    const fontBtn    = document.getElementById('fontSizeToggle');
    const conBtn     = document.getElementById('contrastToggle');

    // ── Sticky header scroll state ─────────────────────────────────────
    if (header) {
        const onScroll = function () {
            header.classList.toggle('scrolled', window.scrollY > 20);
        };
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
    }

    // ── Mobile menu drawer ─────────────────────────────────────────────
    let mobileOpener = null;

    function openMobile() {
        if (!nav || !mToggle) return;
        mobileOpener = document.activeElement;
        nav.classList.add('is-open');
        nav.setAttribute('aria-hidden', 'false');
        mToggle.classList.add('is-active');
        mToggle.setAttribute('aria-expanded', 'true');
        mToggle.setAttribute('aria-label', 'Menü schließen');
    }

    function closeMobile() {
        if (!nav || !mToggle) return;
        nav.classList.remove('is-open');
        nav.setAttribute('aria-hidden', 'true');
        mToggle.classList.remove('is-active');
        mToggle.setAttribute('aria-expanded', 'false');
        mToggle.setAttribute('aria-label', 'Menü öffnen');
        if (mobileOpener && typeof mobileOpener.focus === 'function') {
            mobileOpener.focus();
        } else {
            mToggle.focus();
        }
    }

    if (mToggle && nav) {
        nav.setAttribute('aria-hidden', 'true');
        mToggle.addEventListener('click', function () {
            if (nav.classList.contains('is-open')) {
                closeMobile();
            } else {
                openMobile();
            }
        });
        document.addEventListener('click', function (e) {
            if (!nav.classList.contains('is-open')) return;
            if (nav.contains(e.target) || mToggle.contains(e.target)) return;
            closeMobile();
        });
    }

    // ── Search panel toggle ────────────────────────────────────────────
    let searchOpener = null;

    function openSearch() {
        if (!srchPnl || !srchBtn) return;
        searchOpener = document.activeElement;
        srchPnl.removeAttribute('hidden');
        srchPnl.setAttribute('aria-hidden', 'false');
        srchBtn.setAttribute('aria-expanded', 'true');
        const input = srchPnl.querySelector('input[type="search"]');
        if (input) input.focus();
    }

    function closeSearch() {
        if (!srchPnl || !srchBtn) return;
        srchPnl.setAttribute('hidden', '');
        srchPnl.setAttribute('aria-hidden', 'true');
        srchBtn.setAttribute('aria-expanded', 'false');
        if (searchOpener && typeof searchOpener.focus === 'function') {
            searchOpener.focus();
        } else {
            srchBtn.focus();
        }
    }

    if (srchBtn && srchPnl) {
        srchBtn.addEventListener('click', function () {
            if (srchPnl.hasAttribute('hidden')) {
                openSearch();
            } else {
                closeSearch();
            }
        });
    }
    if (srchCls) {
        srchCls.addEventListener('click', closeSearch);
    }
    document.addEventListener('click', function (e) {
        if (!srchPnl || srchPnl.hasAttribute('hidden')) return;
        if (srchPnl.contains(e.target) || (srchBtn && srchBtn.contains(e.target))) return;
        closeSearch();
    });

    // ── Global Escape: close any open panel/drawer ─────────────────────
    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        if (srchPnl && !srchPnl.hasAttribute('hidden')) closeSearch();
        if (nav && nav.classList.contains('is-open')) closeMobile();
    });

    // ── Accessibility: font-size toggle ────────────────────────────────
    if (fontBtn) {
        const cls = 'mc-large-text';
        try {
            if (localStorage.getItem(cls) === '1') {
                document.body.classList.add(cls);
                fontBtn.setAttribute('aria-pressed', 'true');
            }
        } catch (_) {}
        fontBtn.addEventListener('click', function () {
            const active = document.body.classList.toggle(cls);
            fontBtn.setAttribute('aria-pressed', active ? 'true' : 'false');
            try { localStorage.setItem(cls, active ? '1' : '0'); } catch (_) {}
        });
    }

    // ── Accessibility: high-contrast toggle ────────────────────────────
    if (conBtn) {
        const cls = 'mc-high-contrast';
        try {
            if (localStorage.getItem(cls) === '1') {
                document.body.classList.add(cls);
                conBtn.setAttribute('aria-pressed', 'true');
            }
        } catch (_) {}
        conBtn.addEventListener('click', function () {
            const active = document.body.classList.toggle(cls);
            conBtn.setAttribute('aria-pressed', active ? 'true' : 'false');
            try { localStorage.setItem(cls, active ? '1' : '0'); } catch (_) {}
        });
    }

    // ── Insurance filter (visual only – soft toggle) ───────────────────
    const insBtns = document.querySelectorAll('.mc-insurance-btn');
    insBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            insBtns.forEach(function (b) {
                b.classList.remove('is-active');
                b.setAttribute('aria-pressed', 'false');
            });
            btn.classList.add('is-active');
            btn.setAttribute('aria-pressed', 'true');
        });
    });

    // ── Reveal on scroll (motion-allowed only) ─────────────────────────
    const revealEls = document.querySelectorAll('.mc-card, .mc-specialty-card, .mc-trust-item');
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
