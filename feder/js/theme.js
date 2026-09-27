/**
 * Feder – Theme-Script
 *
 * - Such- und Menü-Panel (aria-expanded, Escape, Klick außerhalb, Fokus-Rückgabe)
 * - Hell-/Dunkel-Schalter mit Speicherung (localStorage, fehlertolerant)
 * - Lesefortschritt im Beitrag
 * - „Link kopieren“ (Clipboard-API)
 *
 * Vanilla JS ohne Abhängigkeiten, ohne Inline-Handler und ohne HTML-Sinks
 * (CSP- und Trusted-Types-konform).
 */
(function () {
    'use strict';

    var root = document.documentElement;
    var header = document.getElementById('fd-header');

    // ── Panels (Suche / Menü) ────────────────────────────────────────────────
    var toggles = Array.prototype.slice.call(document.querySelectorAll('[data-fd-toggle]'));
    var openToggle = null;

    function panelFor(toggle) {
        var id = toggle.getAttribute('aria-controls');
        return id ? document.getElementById(id) : null;
    }

    function closePanel(restoreFocus) {
        if (!openToggle) {
            return;
        }
        var panel = panelFor(openToggle);
        if (panel) {
            panel.hidden = true;
        }
        openToggle.setAttribute('aria-expanded', 'false');
        if (header) {
            header.classList.remove('has-open-panel');
        }
        if (restoreFocus) {
            openToggle.focus();
        }
        openToggle = null;
    }

    function openPanel(toggle) {
        var panel = panelFor(toggle);
        if (!panel) {
            return;
        }
        closePanel(false);
        panel.hidden = false;
        toggle.setAttribute('aria-expanded', 'true');
        if (header) {
            header.classList.add('has-open-panel');
        }
        openToggle = toggle;
        var focusTarget = panel.querySelector('input[type="search"], a[href]');
        if (focusTarget) {
            focusTarget.focus();
        }
    }

    toggles.forEach(function (toggle) {
        toggle.addEventListener('click', function () {
            if (openToggle === toggle) {
                closePanel(true);
            } else {
                openPanel(toggle);
            }
        });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && openToggle) {
            closePanel(true);
        }
    });

    document.addEventListener('click', function (event) {
        if (!openToggle || !header) {
            return;
        }
        if (!header.contains(event.target)) {
            closePanel(false);
        }
    });

    window.addEventListener('resize', function () {
        if (openToggle && openToggle.getAttribute('data-fd-toggle') === 'menu' && window.innerWidth > 900) {
            closePanel(false);
        }
    });

    // ── Farbschema ──────────────────────────────────────────────────────────
    var schemeToggle = document.querySelector('[data-fd-scheme-toggle]');
    var darkQuery = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;

    function effectiveScheme() {
        var explicit = root.getAttribute('data-theme');
        if (explicit === 'dark' || explicit === 'light') {
            return explicit;
        }
        return darkQuery && darkQuery.matches ? 'dark' : 'light';
    }

    function syncSchemeToggle() {
        if (schemeToggle) {
            schemeToggle.setAttribute('aria-pressed', effectiveScheme() === 'dark' ? 'true' : 'false');
        }
    }

    if (schemeToggle) {
        schemeToggle.addEventListener('click', function () {
            var next = effectiveScheme() === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-theme', next);
            try {
                window.localStorage.setItem('feder-color-scheme', next);
            } catch (e) {
                /* Speicher nicht verfügbar – Auswahl gilt nur für diese Seite. */
            }
            syncSchemeToggle();
        });
        if (darkQuery && typeof darkQuery.addEventListener === 'function') {
            darkQuery.addEventListener('change', syncSchemeToggle);
        }
        syncSchemeToggle();
    }

    // ── Header-Schatten beim Scrollen ────────────────────────────────────────
    var ticking = false;
    var progressBar = document.querySelector('[data-fd-progress]');
    var articleBody = document.querySelector('[data-fd-article-body]');

    function update() {
        ticking = false;
        if (header) {
            header.classList.toggle('is-scrolled', window.scrollY > 8);
        }
        if (progressBar && articleBody) {
            var rect = articleBody.getBoundingClientRect();
            var total = rect.height - window.innerHeight * 0.6;
            var ratio = total > 0 ? Math.min(1, Math.max(0, -rect.top / total)) : 1;
            progressBar.style.transform = 'scaleX(' + ratio.toFixed(4) + ')';
        }
    }

    function requestUpdate() {
        if (!ticking) {
            ticking = true;
            window.requestAnimationFrame(update);
        }
    }

    window.addEventListener('scroll', requestUpdate, { passive: true });
    window.addEventListener('resize', requestUpdate);
    update();

    // ── Link kopieren ───────────────────────────────────────────────────────
    Array.prototype.slice.call(document.querySelectorAll('[data-fd-copy]')).forEach(function (button) {
        var label = button.querySelector('[data-fd-copy-label]');
        var original = label ? label.textContent : '';

        button.addEventListener('click', function () {
            var url = button.getAttribute('data-fd-copy') || window.location.href;
            var done = function (text) {
                if (label) {
                    label.textContent = text;
                    window.setTimeout(function () {
                        label.textContent = original;
                    }, 2200);
                }
            };

            if (navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
                navigator.clipboard.writeText(url).then(function () {
                    done('Link kopiert');
                }, function () {
                    done('Kopieren nicht möglich');
                });
            } else {
                done('Kopieren nicht möglich');
            }
        });
    });
}());
