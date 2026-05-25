/**
 * CMS Phinit Theme – Navigation & UI Interactions
 * vanilla JS, kein jQuery, ES2020+
 */
(function () {
    'use strict';

    const scriptBaseUrl = (() => {
        const currentScript = document.currentScript;
        if (currentScript instanceof HTMLScriptElement && currentScript.src) {
            return new URL('./', currentScript.src).href;
        }

        return new URL('./', window.location.href).href;
    })();
    const loadedFeatureModules = new Set();
    const scheduleBackgroundTask = (callback) => {
        if (typeof callback !== 'function') {
            return;
        }

        if (typeof window.requestIdleCallback === 'function') {
            window.requestIdleCallback(() => callback(), { timeout: 1500 });
            return;
        }

        window.setTimeout(callback, 1);
    };
    const scheduleNextFrame = (callback) => {
        if (typeof callback !== 'function') {
            return;
        }

        if (typeof window.requestAnimationFrame === 'function') {
            window.requestAnimationFrame(() => callback());
            return;
        }

        window.setTimeout(callback, 16);
    };

    /* ── Helpers: Data-Attribute Toggles vom Body lesen ──────── */
    const bodyData = () => document.body.dataset;
    const isEnabled = (key, fallback = true) => {
        const v = bodyData()[key];
        return v !== undefined ? v === '1' : fallback;
    };
    const prefersReducedMotion = () =>
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const hasDesktopNavigation = () =>
        window.matchMedia('(min-width: 769px) and (hover: hover) and (pointer: fine)').matches;

    /* ── DOMContentLoaded ──────────────────────────────────────── */
    document.addEventListener('DOMContentLoaded', () => {
        if (isEnabled('stickyHeader'))  initStickyHeader();
        initBurgerMenu();
        initArticleImagePlaceholders();
        initDesktopDropdowns();
        initDarkMode();
        if (isEnabled('progressBar'))   initScrollProgress();
        if (isEnabled('scrollAnims') && !prefersReducedMotion())  initScrollAnimations();
        initActiveNav();
        initFlashMessages();
        if (isEnabled('backToTop'))     initBackToTop();
        initConsentBanner();
        deferFeatureModules();
    });

    function deferFeatureModules() {
        const triggerDeferredModules = () => {
            scheduleBackgroundTask(loadDeferredFeatureModules);
        };

        if (document.readyState === 'complete') {
            triggerDeferredModules();
            return;
        }

        window.addEventListener('load', triggerDeferredModules, { once: true });
    }

    function loadDeferredFeatureModules() {
        const moduleQueue = [];

        if (needsContentInteractions()) {
            moduleQueue.push('content-interactions.js');
        }

        if (needsHomepageWidgets()) {
            moduleQueue.push('homepage-widgets.js');
        }

        if (needsMemberSecurity()) {
            moduleQueue.push('member-security.js');
        }

        moduleQueue.forEach((moduleFile) => {
            scheduleBackgroundTask(() => {
                loadFeatureModule(moduleFile);
            });
        });
    }

    function loadFeatureModule(moduleFile) {
        if (loadedFeatureModules.has(moduleFile)) {
            return;
        }

        loadedFeatureModules.add(moduleFile);
        import(new URL(moduleFile, scriptBaseUrl).href).catch(() => {
            loadedFeatureModules.delete(moduleFile);
        });
    }

    function needsContentInteractions() {
        return Boolean(
            document.querySelector('.toc-list')
            || document.querySelector('[data-inline-toc]')
            || document.querySelector('.cms-page-title-toc')
            || document.querySelector('[data-cms-toc-root]')
            || document.querySelector('[data-share-copy]')
            || document.querySelector('[data-share-print]')
            || document.querySelector('pre > code')
        );
    }

    function needsHomepageWidgets() {
        if (window.CMSPhinitHomepageWidgetsLoaded === true || window.CMSPhinitHomepageWidgetsBooted === true) {
            return false;
        }

        return Boolean(
            document.querySelector('[data-featured-banner-rotator]')
            || document.querySelector('[data-featured-rotator]')
            || document.querySelector('[data-sidebar-carousel]')
        );
    }

    function needsMemberSecurity() {
        return Boolean(
            document.querySelector('[data-passkey-form]')
            || document.querySelector('[data-passkey-register]')
            || document.querySelector('.member-backup-codes')
        );
    }

    /* ── Artikelbilder: Skeleton erst bei echtem Laden ausblenden ── */
    function initArticleImagePlaceholders() {
        document.querySelectorAll('.article-thumb--has-image').forEach((thumb) => {
            const image = thumb.querySelector('img');
            if (!(image instanceof HTMLImageElement)) {
                return;
            }

            const markLoaded = () => {
                thumb.classList.add('is-loaded');
                thumb.classList.remove('article-thumb--js-loading');
            };

            if (image.complete && image.naturalWidth > 0) {
                markLoaded();
                return;
            }

            thumb.classList.add('article-thumb--js-loading');
            if (image.complete) {
                thumb.classList.add('has-image-error');
                return;
            }

            image.addEventListener('load', markLoaded, { once: true });
            image.addEventListener('error', () => {
                thumb.classList.add('has-image-error');
            }, { once: true });
        });
    }

    /* ── Sticky Header ─────────────────────────────────────────── */
    function initStickyHeader() {
        const header = document.querySelector('.site-header');
        if (!header) return;

        let isTicking = false;
        let lastScrolledState = false;

        const updateHeaderState = () => {
            isTicking = false;
            const nextScrolledState = window.scrollY > 60;
            if (nextScrolledState === lastScrolledState) {
                return;
            }

            lastScrolledState = nextScrolledState;
            header.classList.toggle('scrolled', nextScrolledState);
        };

        const onScroll = () => {
            if (isTicking) {
                return;
            }

            isTicking = true;
            scheduleNextFrame(updateHeaderState);
        };

        window.addEventListener('scroll', onScroll, { passive: true });
    }

    /* ── Burger Menü (Mobile) ──────────────────────────────────── */
    function initBurgerMenu() {
        const btn    = document.getElementById('burger-toggle') || document.querySelector('.burger-btn');
        const menu   = document.getElementById('mobile-menu') || document.querySelector('.mobile-menu');
        const header = document.querySelector('.site-header');
        if (!btn || !menu) return;

        const closeMenu = () => {
            menu.classList.remove('open');
            btn.classList.remove('open');
            btn.setAttribute('aria-expanded', 'false');
            btn.setAttribute('aria-label', 'Menü öffnen');
            menu.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('mobile-menu-open');
        };

        btn.addEventListener('click', () => {
            const open = menu.classList.toggle('open');
            btn.classList.toggle('open', open);
            btn.setAttribute('aria-expanded', String(open));
            btn.setAttribute('aria-label', open ? 'Menü schließen' : 'Menü öffnen');
            menu.setAttribute('aria-hidden', String(!open));
            document.body.classList.toggle('mobile-menu-open', open);
        });

        // Escape schließt Menü
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && menu.classList.contains('open')) {
                closeMenu();
                btn.focus();
            }
        });

        // Klick außerhalb schließt Menü
        document.addEventListener('click', (e) => {
            if (header && !header.contains(e.target)) {
                closeMenu();
            }
        });

        menu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', closeMenu);
        });
    }

    /* ── Hauptnavigation: Desktop-Dropdowns ───────────────────── */
    function initDesktopDropdowns() {
        if (!hasDesktopNavigation()) return;

        const dropdowns = Array.from(document.querySelectorAll('[data-nav-dropdown]'));
        if (!dropdowns.length) return;
        const pendingAlignmentFrames = new WeakMap();

        const getImmediateDropdownPanel = (dropdown) => {
            return Array.from(dropdown.children).find((child) => child.classList && child.classList.contains('dropdown')) || null;
        };

        const getAncestorPath = (dropdown) => {
            const path = [];
            let current = dropdown;

            while (current) {
                path.push(current);
                current = current.parentElement ? current.parentElement.closest('[data-nav-dropdown]') : null;
            }

            return path;
        };

        const updateDropdownAlignment = (dropdown) => {
            const panel = getImmediateDropdownPanel(dropdown);
            if (!(panel instanceof HTMLElement)) {
                return;
            }

            const dropdownDepth = Number.parseInt(dropdown.dataset.navDepth || '0', 10);
            const dropdownRect = dropdown.getBoundingClientRect();
            const panelWidth = Number.parseInt(dropdown.dataset.navPanelWidth || '', 10) || (dropdownDepth > 0 ? 260 : 240);
            const viewportWidth = document.documentElement.clientWidth || window.innerWidth;
            let nextAlignment = '';

            if (dropdownDepth <= 0) {
                if (dropdownRect.left + panelWidth > viewportWidth - 16) {
                    nextAlignment = 'end';
                }
            } else {
                const spaceRight = viewportWidth - dropdownRect.right;
                const spaceLeft = dropdownRect.left;
                if (spaceRight < panelWidth && spaceLeft > spaceRight) {
                    nextAlignment = 'left';
                }
            }

            dropdown.classList.toggle('is-align-left', nextAlignment === 'left');
            dropdown.classList.toggle('is-align-end', nextAlignment === 'end');
        };

        const scheduleDropdownAlignment = (dropdown) => {
            const pendingFrame = pendingAlignmentFrames.get(dropdown);
            if (pendingFrame && typeof window.cancelAnimationFrame === 'function') {
                window.cancelAnimationFrame(pendingFrame);
            }

            if (typeof window.requestAnimationFrame === 'function') {
                const frameId = window.requestAnimationFrame(() => {
                    pendingAlignmentFrames.delete(dropdown);
                    updateDropdownAlignment(dropdown);
                });
                pendingAlignmentFrames.set(dropdown, frameId);
                return;
            }

            window.setTimeout(() => updateDropdownAlignment(dropdown), 16);
        };

        const closeAll = (exceptions = []) => {
            const keepOpen = new Set(Array.isArray(exceptions) ? exceptions : [exceptions]);

            dropdowns.forEach((dropdown) => {
                if (keepOpen.has(dropdown)) {
                    return;
                }

                dropdown.classList.remove('is-open');
                const toggle = dropdown.querySelector('.main-nav__toggle');
                if (toggle) {
                    toggle.setAttribute('aria-expanded', 'false');
                }
            });
        };

        dropdowns.forEach((dropdown) => {
            const toggle = dropdown.querySelector('.main-nav__toggle');
            if (!toggle) return;

            dropdown.addEventListener('mouseenter', () => {
                scheduleDropdownAlignment(dropdown);
            });

            dropdown.addEventListener('focusin', () => {
                scheduleDropdownAlignment(dropdown);
            });

            toggle.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();

                const willOpen = !dropdown.classList.contains('is-open');
                const keepOpen = willOpen ? getAncestorPath(dropdown) : getAncestorPath(dropdown).slice(1);
                updateDropdownAlignment(dropdown);
                closeAll(keepOpen);
                dropdown.classList.toggle('is-open', willOpen);
                toggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            });
        });

        window.addEventListener('resize', () => {
            dropdowns.forEach((dropdown) => {
                scheduleDropdownAlignment(dropdown);
            });
        }, { passive: true });

        document.addEventListener('click', (event) => {
            const target = event.target;
            if (!(target instanceof Element) || !target.closest('[data-nav-dropdown]')) {
                closeAll();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key !== 'Escape') {
                return;
            }

            const openDropdown = dropdowns.find((dropdown) => dropdown.classList.contains('is-open'));
            if (!openDropdown) {
                return;
            }

            const toggle = openDropdown.querySelector('.main-nav__toggle');
            closeAll();
            if (toggle instanceof HTMLElement) {
                toggle.focus();
            }
        });
    }

    /* ── Dark Mode ─────────────────────────────────────────────── */
    function initDarkMode() {
        const STORAGE_KEY = 'phinit_theme';
        const LEGACY_STORAGE_KEYS = ['cms365-theme', 'cms-phinit-theme'];
        const root = document.documentElement;
        const buttons = document.querySelectorAll('.util-dark-toggle, [data-dark-toggle]');

        const readStoredTheme = () => {
            let stored = localStorage.getItem(STORAGE_KEY);
            if (stored === 'dark' || stored === 'light') return stored;
            stored = LEGACY_STORAGE_KEYS.map(key => localStorage.getItem(key)).find(value => value === 'dark' || value === 'light') || 'light';
            localStorage.setItem(STORAGE_KEY, stored);
            return stored;
        };

        const applyTheme = (isDark) => {
            root.classList.toggle('dark-mode', isDark);
            root.classList.toggle('light-mode', !isDark);
            localStorage.setItem(STORAGE_KEY, isDark ? 'dark' : 'light');
            buttons.forEach(button => updateDarkToggle(button, isDark));
        };

        applyTheme(readStoredTheme() === 'dark');

        buttons.forEach(button => {
            button.addEventListener('click', () => applyTheme(!root.classList.contains('dark-mode')));
        });
    }

    function updateDarkToggle(btn, isDark) {
        btn.setAttribute('aria-pressed', String(isDark));
        btn.setAttribute('title', isDark ? 'Light Mode aktivieren' : 'Dark Mode aktivieren');
        const icon = btn.querySelector('[data-dark-toggle-icon]');
        if (icon) {
            icon.textContent = isDark ? '☀' : '☾';
            return;
        }

        btn.textContent = isDark ? '☀' : '☾';
    }

    /* ── Reading Progress Bar ──────────────────────────────────── */
    function initScrollProgress() {
        const bar = document.getElementById('scroll-progress');
        if (!bar) return;

        let isTicking = false;
        let maxScroll = 0;

        const recomputeMaxScroll = () => {
            maxScroll = Math.max(0, document.documentElement.scrollHeight - window.innerHeight);
        };

        const update = () => {
            isTicking = false;
            const progress = maxScroll > 0 ? (window.scrollY / maxScroll) * 100 : 0;
            bar.style.transform = 'scaleX(' + (Math.min(progress, 100) / 100).toFixed(4) + ')';
        };

        const onScroll = () => {
            if (isTicking) {
                return;
            }

            isTicking = true;
            scheduleNextFrame(update);
        };

        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', () => {
            recomputeMaxScroll();
            onScroll();
        }, { passive: true });
        window.addEventListener('load', () => {
            recomputeMaxScroll();
            onScroll();
        }, { once: true });
        window.addEventListener('pageshow', () => {
            recomputeMaxScroll();
            onScroll();
        });
        recomputeMaxScroll();
        update();
    }

    /* ── Scroll Animations (IntersectionObserver) ──────────────── */
    function initScrollAnimations() {
        const els = Array.from(document.querySelectorAll('[data-anim]'));
        if (!els.length) return;

        const revealElement = (element) => {
            element.classList.add('is-visible');
        };

        if (typeof window.IntersectionObserver !== 'function') {
            els.forEach(revealElement);
            return;
        }

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) {
                        return;
                    }

                    revealElement(entry.target);
                    observer.unobserve(entry.target);
                });
            },
            { threshold: 0.12 }
        );

        els.forEach((element) => {
            if (!element.classList.contains('is-visible')) {
                observer.observe(element);
            }
        });
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

    /* ── Back to Top ───────────────────────────────────────────── */
    function initBackToTop() {
        const btn = document.getElementById('back-to-top');
        if (!btn) return;

        let isTicking = false;
        const mobileViewport = window.matchMedia('(max-width: 768px)');
        const visibilityThreshold = () => mobileViewport.matches ? window.innerHeight * 2 : 400;
        const updateVisibility = () => {
            isTicking = false;
            btn.classList.toggle('visible', window.scrollY > visibilityThreshold());
        };

        window.addEventListener('scroll', () => {
            if (isTicking) {
                return;
            }

            isTicking = true;
            scheduleNextFrame(updateVisibility);
        }, { passive: true });
        window.addEventListener('resize', updateVisibility, { passive: true });

        updateVisibility();
        btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
    }

    /* ── Flash Messages ───────────────────────────────────────── */
    function initFlashMessages() {
        document.querySelectorAll('[data-auto-dismiss]').forEach((element) => {
            const delay = Number.parseInt(element.getAttribute('data-auto-dismiss') || '0', 10);
            if (!Number.isFinite(delay) || delay <= 0) {
                return;
            }

            window.setTimeout(() => {
                element.classList.add('is-hidden');
            }, delay);
        });
    }

    /* ── Consent Banner ───────────────────────────────────────── */
    function initConsentBanner() {
        if (window.CMS_COOKIECONSENT_CONFIG || document.getElementById('cc-main')) {
            return;
        }

        const banner  = document.getElementById('consent-banner');
        const btnOk   = document.getElementById('consent-accept');
        const btnDecl = document.getElementById('consent-decline');
        if (!banner) return;

        if (localStorage.getItem('cms-consent')) {
            banner.style.display = 'none';
            return;
        }

        function notifyConsentChange(state) {
            window.dispatchEvent(new CustomEvent('cms-cookie-consent-change', {
                detail: {
                    state: state,
                    accepted: state === 'accepted'
                }
            }));
        }

        if (btnOk) btnOk.addEventListener('click', () => {
            localStorage.setItem('cms-consent', 'accepted');
            banner.style.display = 'none';
            notifyConsentChange('accepted');
        });
        if (btnDecl) btnDecl.addEventListener('click', () => {
            localStorage.setItem('cms-consent', 'declined');
            banner.style.display = 'none';
            notifyConsentChange('declined');
        });
    }
})();
