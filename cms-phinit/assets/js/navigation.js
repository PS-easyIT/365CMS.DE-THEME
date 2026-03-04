/**
 * CMS Phinit Theme – Navigation & UI Interactions
 * vanilla JS, kein jQuery, ES2020+
 */
(function () {
    'use strict';

    /* ── Helpers: Data-Attribute Toggles vom Body lesen ──────── */
    const bodyData = () => document.body.dataset;
    const isEnabled = (key, fallback = true) => {
        const v = bodyData()[key];
        return v !== undefined ? v === '1' : fallback;
    };
    const prefersReducedMotion = () =>
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ── DOMContentLoaded ──────────────────────────────────────── */
    document.addEventListener('DOMContentLoaded', () => {
        if (isEnabled('stickyHeader'))  initStickyHeader();
        initBurgerMenu();
        initDarkMode();
        if (isEnabled('progressBar'))   initScrollProgress();
        if (isEnabled('scrollAnims') && !prefersReducedMotion())  initScrollAnimations();
        initActiveNav();
        initTocHighlight();
        if (isEnabled('backToTop'))     initBackToTop();
        initShareButtons();
        initConsentBanner();
        initCodeCopyButtons();
    });

    /* ── Sticky Header ─────────────────────────────────────────── */
    function initStickyHeader() {
        const header = document.querySelector('.site-header');
        if (!header) return;
        const onScroll = () => header.classList.toggle('scrolled', window.scrollY > 60);
        window.addEventListener('scroll', onScroll, { passive: true });
    }

    /* ── Burger Menü (Mobile) ──────────────────────────────────── */
    function initBurgerMenu() {
        const btn    = document.querySelector('.burger-btn');
        const menu   = document.querySelector('.mobile-menu');
        const header = document.querySelector('.site-header');
        if (!btn || !menu) return;

        btn.addEventListener('click', () => {
            const open = menu.classList.toggle('open');
            btn.classList.toggle('open', open);
            btn.setAttribute('aria-expanded', String(open));
            btn.setAttribute('aria-label', open ? 'Menü schließen' : 'Menü öffnen');
        });

        // Escape schließt Menü
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && menu.classList.contains('open')) {
                menu.classList.remove('open');
                btn.classList.remove('open');
                btn.setAttribute('aria-expanded', 'false');
                btn.focus();
            }
        });

        // Klick außerhalb schließt Menü
        document.addEventListener('click', (e) => {
            if (header && !header.contains(e.target)) {
                menu.classList.remove('open');
                btn.classList.remove('open');
                btn.setAttribute('aria-expanded', 'false');
            }
        });
    }

    /* ── Dark Mode ─────────────────────────────────────────────── */
    function initDarkMode() {
        const STORAGE_KEY = 'cms-phinit-theme';
        const body        = document.body;

        // System-Präferenz auslesen
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const stored      = localStorage.getItem(STORAGE_KEY);
        const dark        = stored ? stored === 'dark' : prefersDark;

        if (dark) body.classList.add('dark-mode');

        // Toggle-Buttons (kann mehrere geben)
        document.querySelectorAll('.util-dark-toggle, [data-dark-toggle]').forEach(btn => {
            updateDarkToggle(btn, dark);
            btn.addEventListener('click', () => {
                const isDark = body.classList.toggle('dark-mode');
                localStorage.setItem(STORAGE_KEY, isDark ? 'dark' : 'light');
                document.querySelectorAll('.util-dark-toggle, [data-dark-toggle]')
                    .forEach(b => updateDarkToggle(b, isDark));
            });
        });
    }

    function updateDarkToggle(btn, isDark) {
        btn.setAttribute('aria-pressed', String(isDark));
        btn.setAttribute('title', isDark ? 'Light Mode aktivieren' : 'Dark Mode aktivieren');
        btn.textContent = isDark ? '☀' : '🌙';
    }

    /* ── Reading Progress Bar ──────────────────────────────────── */
    function initScrollProgress() {
        const bar = document.getElementById('scroll-progress');
        if (!bar) return;
        const update = () => {
            const total    = document.documentElement.scrollHeight - window.innerHeight;
            const progress = total > 0 ? (window.scrollY / total) * 100 : 0;
            bar.style.width = Math.min(progress, 100) + '%';
        };
        window.addEventListener('scroll', update, { passive: true });
        update();
    }

    /* ── Scroll Animations (IntersectionObserver) ──────────────── */
    function initScrollAnimations() {
        const els = document.querySelectorAll('[data-anim]');
        if (!els.length) return;
        const observer = new IntersectionObserver(
            (entries) => entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('is-visible'); observer.unobserve(e.target); } }),
            { threshold: 0.08, rootMargin: '0px 0px -40px 0px' }
        );
        els.forEach(el => observer.observe(el));
    }

    /* ── Active Nav Link ───────────────────────────────────────── */
    function initActiveNav() {
        const path = window.location.pathname;
        document.querySelectorAll('.main-nav a, .mobile-menu a').forEach(a => {
            try {
                const url = new URL(a.href, window.location.origin);
                let active = false;
                if (url.pathname === '/') {
                    active = path === '/'; // Startseite: nur exakter Treffer
                } else {
                    active = path === url.pathname || path.startsWith(url.pathname + '/');
                }
                if (active) {
                    a.classList.add('active');
                    a.setAttribute('aria-current', 'page');
                }
            } catch (_) {}
        });
    }

    /* ── TOC Highlight (Scroll-Spy) ────────────────────────────── */
    function initTocHighlight() {
        const toc      = document.querySelector('.toc-list');
        const headings = document.querySelectorAll('.post-body h2, .post-body h3');
        if (!toc || !headings.length) return;

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach(entry => {
                    if (!entry.isIntersecting) return;
                    const id = entry.target.id;
                    toc.querySelectorAll('a').forEach(a => {
                        a.classList.toggle('active', a.getAttribute('href') === '#' + id);
                    });
                });
            },
            { rootMargin: '-20% 0px -70% 0px' }
        );
        headings.forEach(h => { if (h.id) observer.observe(h); });
    }

    /* ── Back to Top ───────────────────────────────────────────── */
    function initBackToTop() {
        const btn = document.getElementById('back-to-top');
        if (!btn) return;
        window.addEventListener('scroll', () => {
            btn.style.display = window.scrollY > 400 ? 'flex' : 'none';
        }, { passive: true });
        btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
    }

    /* ── Share Buttons ────────────────────────────────────────── */
    function initShareButtons() {
        const copyBtn = document.querySelector('.share-btn.cp');
        if (!copyBtn) return;
        copyBtn.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(window.location.href);
                const orig = copyBtn.textContent;
                copyBtn.textContent = '✓ Kopiert!';
                setTimeout(() => { copyBtn.textContent = orig; }, 2000);
            } catch (_) {}
        });
    }

    /* ── Consent Banner ───────────────────────────────────────── */
    function initConsentBanner() {
        const banner  = document.getElementById('consent-banner');
        const btnOk   = document.getElementById('consent-accept');
        const btnDecl = document.getElementById('consent-decline');
        if (!banner) return;

        if (localStorage.getItem('cms-consent')) {
            banner.style.display = 'none';
            return;
        }

        if (btnOk) btnOk.addEventListener('click', () => {
            localStorage.setItem('cms-consent', 'accepted');
            banner.style.display = 'none';
        });
        if (btnDecl) btnDecl.addEventListener('click', () => {
            localStorage.setItem('cms-consent', 'declined');
            banner.style.display = 'none';
        });
    }
    /* ── Code-Block Copy Buttons ─────────────────────────────── */
    function initCodeCopyButtons() {
        document.querySelectorAll('pre > code').forEach(codeEl => {
            const pre = codeEl.parentElement;
            if (pre.querySelector('.code-copy-btn')) return;
            const btn = document.createElement('button');
            btn.className = 'code-copy-btn';
            btn.setAttribute('aria-label', 'Code kopieren');
            btn.setAttribute('title', 'Code kopieren');
            btn.textContent = '\uD83D\uDCCB'; // 📋
            pre.appendChild(btn);
            btn.addEventListener('click', async () => {
                const code = codeEl.textContent || '';
                try {
                    await navigator.clipboard.writeText(code);
                    btn.textContent = '\u2713'; // ✓
                    btn.classList.add('copied');
                    setTimeout(() => { btn.textContent = '\uD83D\uDCCB'; btn.classList.remove('copied'); }, 2000);
                } catch (_) {
                    const range = document.createRange();
                    range.selectNodeContents(codeEl);
                    window.getSelection()?.removeAllRanges();
                    window.getSelection()?.addRange(range);
                }
            });
        });
    }

})();
