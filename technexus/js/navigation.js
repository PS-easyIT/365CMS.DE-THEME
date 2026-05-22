/**
 * TechNexus Theme – Navigation & UI
 * Mobile menu, search panel, dark mode, sticky header
 */
(function () {
    'use strict';

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const html    = document.documentElement;
    const header  = document.getElementById('masthead');
    const mToggle = document.getElementById('mobileMenuToggle');
    const nav     = document.getElementById('site-navigation');
    const srchBtn = document.getElementById('searchToggle');
    const srchPnl = document.getElementById('searchPanel');
    const srchCls = document.getElementById('searchClose');
    const themeBtn = document.getElementById('themeToggle');

    let searchTrigger = null;

    const applyTheme = (mode) => {
        html.setAttribute('data-theme', mode);
        if (themeBtn) {
            themeBtn.setAttribute(
                'aria-label',
                mode === 'dark' ? 'Hellmodus aktivieren' : 'Dunkelmodus aktivieren'
            );
        }
    };

    const savedTheme = localStorage.getItem('tn-color-scheme');
    if (savedTheme === 'dark' || savedTheme === 'light') {
        applyTheme(savedTheme);
    } else if (html.getAttribute('data-theme') !== 'dark' && html.getAttribute('data-theme') !== 'light') {
        if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            applyTheme('dark');
        }
    }

    themeBtn?.addEventListener('click', () => {
        const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        applyTheme(next);
        localStorage.setItem('tn-color-scheme', next);
    });

    if (header && !prefersReducedMotion) {
        const onScroll = () => {
            header.classList.toggle('scrolled', window.scrollY > 20);
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    } else if (header) {
        header.classList.add('scrolled');
    }

    const closeMobileMenu = () => {
        if (!mToggle || !nav) return;
        nav.classList.remove('open');
        mToggle.setAttribute('aria-expanded', 'false');
        mToggle.classList.remove('active');
    };

    const openMobileMenu = () => {
        if (!mToggle || !nav) return;
        nav.classList.add('open');
        mToggle.setAttribute('aria-expanded', 'true');
        mToggle.classList.add('active');
    };

    if (mToggle && nav) {
        mToggle.addEventListener('click', () => {
            const isOpen = mToggle.getAttribute('aria-expanded') === 'true';
            if (isOpen) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        });

        document.addEventListener('click', (e) => {
            if (!nav.contains(e.target) && !mToggle.contains(e.target)) {
                closeMobileMenu();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeMobileMenu();
            }
        });
    }

    const closeSearch = () => {
        if (!srchPnl || !srchBtn) return;
        srchPnl.setAttribute('hidden', '');
        srchBtn.setAttribute('aria-expanded', 'false');
        if (searchTrigger && typeof searchTrigger.focus === 'function') {
            searchTrigger.focus();
        }
        searchTrigger = null;
    };

    const openSearch = () => {
        if (!srchPnl || !srchBtn) return;
        searchTrigger = document.activeElement;
        srchPnl.removeAttribute('hidden');
        srchBtn.setAttribute('aria-expanded', 'true');
        const input = srchPnl.querySelector('input[type="search"]');
        if (input) {
            if (prefersReducedMotion) {
                input.focus();
            } else {
                setTimeout(() => input.focus(), 50);
            }
        }
    };

    if (srchBtn && srchPnl) {
        srchBtn.addEventListener('click', () => {
            const isOpen = srchBtn.getAttribute('aria-expanded') === 'true';
            if (isOpen) {
                closeSearch();
            } else {
                openSearch();
            }
        });

        srchCls?.addEventListener('click', closeSearch);

        document.addEventListener('click', (e) => {
            if (
                srchPnl.hasAttribute('hidden') === false &&
                !srchPnl.contains(e.target) &&
                e.target !== srchBtn &&
                !srchBtn.contains(e.target)
            ) {
                closeSearch();
            }
        });

        srchPnl.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeSearch();
            }
        });
    }

    if (!prefersReducedMotion && 'IntersectionObserver' in window) {
        const revealObserver = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        revealObserver.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.08, rootMargin: '0px 0px -32px 0px' }
        );

        document.querySelectorAll('.tech-card').forEach((el) => {
            revealObserver.observe(el);
        });
    }
})();
