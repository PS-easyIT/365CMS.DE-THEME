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
        initDesktopDropdowns();
        initDarkMode();
        if (isEnabled('progressBar'))   initScrollProgress();
        if (isEnabled('scrollAnims') && !prefersReducedMotion())  initScrollAnimations();
        initActiveNav();
        initFlashMessages();
        initTocHighlight();
        initInlineToc();
        if (isEnabled('backToTop'))     initBackToTop();
        initShareButtons();
        initConsentBanner();
        initCodeCopyButtons();
        initFeaturedSidebarRotators();
        initPasskeys();
        initBackupCodeCopy();
    });

    function toBase64Url(uint8Array) {
        let binary = '';
        uint8Array.forEach((byte) => {
            binary += String.fromCharCode(byte);
        });

        return btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/g, '');
    }

    function toBase64UrlFromBufferSource(bufferSource) {
        if (!bufferSource) {
            return '';
        }

        const view = bufferSource instanceof Uint8Array
            ? bufferSource
            : new Uint8Array(bufferSource);

        return toBase64Url(view);
    }

    function fromBase64Url(value) {
        if (!value) {
            return new Uint8Array();
        }

        const normalized = value.replace(/-/g, '+').replace(/_/g, '/');
        const padded = normalized + '==='.slice((normalized.length + 3) % 4);
        const binary = atob(padded);
        const bytes = new Uint8Array(binary.length);

        for (let index = 0; index < binary.length; index += 1) {
            bytes[index] = binary.charCodeAt(index);
        }

        return bytes;
    }

    function normalizePublicKeyOptions(options) {
        if (!options || typeof options !== 'object') {
            return options;
        }

        const publicKey = options.publicKey && typeof options.publicKey === 'object'
            ? options.publicKey
            : options;

        if (publicKey.challenge) {
            publicKey.challenge = fromBase64Url(publicKey.challenge);
        }

        if (publicKey.user && publicKey.user.id) {
            publicKey.user.id = fromBase64Url(publicKey.user.id);
        }

        if (Array.isArray(publicKey.excludeCredentials)) {
            publicKey.excludeCredentials = publicKey.excludeCredentials.map((credential) => {
                if (credential.id) {
                    credential.id = fromBase64Url(credential.id);
                }

                return credential;
            });
        }

        return publicKey;
    }

    /* ── Sticky Header ─────────────────────────────────────────── */
    function initStickyHeader() {
        const header = document.querySelector('.site-header');
        if (!header) return;
        const onScroll = () => header.classList.toggle('scrolled', window.scrollY > 60);
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
        const dropdowns = Array.from(document.querySelectorAll('[data-nav-dropdown]'));
        if (!dropdowns.length) return;

        const closeAll = (except = null) => {
            dropdowns.forEach((dropdown) => {
                if (except !== null && dropdown === except) {
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

            toggle.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();

                const willOpen = !dropdown.classList.contains('is-open');
                closeAll(dropdown);
                dropdown.classList.toggle('is-open', willOpen);
                toggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            });
        });

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
        const STORAGE_KEY = 'cms365-theme';
        const LEGACY_STORAGE_KEY = 'cms-phinit-theme';
        const body        = document.body;

        // System-Präferenz auslesen
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const legacy      = localStorage.getItem(LEGACY_STORAGE_KEY);
        const stored      = localStorage.getItem(STORAGE_KEY) ?? legacy;
        const dark        = stored ? stored === 'dark' : prefersDark;

        if (legacy !== null && localStorage.getItem(STORAGE_KEY) === null) {
            localStorage.setItem(STORAGE_KEY, legacy);
        }

        if (dark) body.classList.add('dark-mode');
        document.documentElement.classList.toggle('dark-mode', dark);

        // Toggle-Buttons (kann mehrere geben)
        document.querySelectorAll('.util-dark-toggle, [data-dark-toggle]').forEach(btn => {
            updateDarkToggle(btn, dark);
            btn.addEventListener('click', () => {
                const isDark = body.classList.toggle('dark-mode');
                document.documentElement.classList.toggle('dark-mode', isDark);
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

    function initInlineToc() {
        const inlineTocs = document.querySelectorAll('[data-inline-toc]');
        if (!inlineTocs.length) return;

        inlineTocs.forEach((toc) => {
            const summary = toc.querySelector('summary');
            if (!summary) return;

            toc.open = false;

            const syncState = () => {
                summary.setAttribute('aria-expanded', toc.open ? 'true' : 'false');
                toc.classList.toggle('is-open', toc.open);
            };

            syncState();
            toc.addEventListener('toggle', syncState);

            toc.querySelectorAll('a[href^="#"]').forEach((link) => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 1024) {
                        window.setTimeout(() => {
                            toc.open = false;
                        }, 120);
                    }
                });
            });
        });
    }

    /* ── Back to Top ───────────────────────────────────────────── */
    function initBackToTop() {
        const btn = document.getElementById('back-to-top');
        if (!btn) return;
        window.addEventListener('scroll', () => {
            btn.classList.toggle('visible', window.scrollY > 400);
        }, { passive: true });
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

    function initPasskeys() {
        const form = document.querySelector('[data-passkey-form]');
        const trigger = document.querySelector('[data-passkey-register]');

        if (!form || !trigger || !window.PublicKeyCredential || !navigator.credentials || typeof navigator.credentials.create !== 'function') {
            return;
        }

        trigger.addEventListener('click', async () => {
            const optionsJson = form.getAttribute('data-passkey-options') || '{}';
            let options;

            try {
                options = JSON.parse(optionsJson);
            } catch (_error) {
                window.alert('Die Passkey-Optionen konnten nicht gelesen werden.');
                return;
            }

            try {
                trigger.setAttribute('disabled', 'disabled');

                const credential = await navigator.credentials.create({
                    publicKey: normalizePublicKeyOptions(options)
                });

                if (!credential || !credential.response || !credential.response.clientDataJSON || !credential.response.attestationObject) {
                    window.alert('Der Passkey konnte nicht erstellt werden.');
                    return;
                }

                form.querySelector('input[name="client_data_json"]').value = toBase64UrlFromBufferSource(credential.response.clientDataJSON);
                form.querySelector('input[name="attestation_object"]').value = toBase64UrlFromBufferSource(credential.response.attestationObject);
                form.submit();
            } catch (error) {
                window.alert(error && error.message ? error.message : 'Passkey-Registrierung wurde abgebrochen.');
            } finally {
                trigger.removeAttribute('disabled');
            }
        });
    }

    function initBackupCodeCopy() {
        const codes = document.querySelector('.member-backup-codes');

        if (!codes || !navigator.clipboard) {
            return;
        }

        codes.addEventListener('click', () => {
            const text = Array.from(codes.querySelectorAll('code')).map((node) => node.textContent || '').join('\n');
            navigator.clipboard.writeText(text).catch(() => undefined);
        });
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

    /* ── Homepage Sidebar Featured Rotator ──────────────────── */
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
