/**
 * Kompass – Theme-Script
 *
 * - Schriftgröße (A / A+ / A++) und hoher Kontrast, gespeichert in localStorage
 * - Mobiles Menü (aria-expanded, Escape, Fokus-Rückgabe)
 * - Inhaltsverzeichnis: aktuellen Abschnitt markieren (aria-current)
 * - Drucken und „Link kopieren“ mit Statusmeldung
 *
 * Vanilla JS, ohne Inline-Handler und HTML-Sinks (CSP-/Trusted-Types-konform).
 */
(function () {
    'use strict';

    var root = document.documentElement;

    function store(key, value) {
        try {
            if (value === null) {
                window.localStorage.removeItem(key);
            } else {
                window.localStorage.setItem(key, value);
            }
        } catch (e) {
            // Speicher nicht verfügbar (z. B. privater Modus) – Einstellung gilt nur für diese Seite.
        }
    }

    // ── Schriftgröße ────────────────────────────────────────────────────────
    var sizeButtons = Array.prototype.slice.call(document.querySelectorAll('[data-kp-fontsize]'));

    function applySize(size) {
        if (size === 'large' || size === 'xlarge') {
            root.setAttribute('data-fontsize', size);
        } else {
            size = 'normal';
            root.removeAttribute('data-fontsize');
        }
        sizeButtons.forEach(function (button) {
            button.setAttribute('aria-pressed', button.getAttribute('data-kp-fontsize') === size ? 'true' : 'false');
        });
        return size;
    }

    if (sizeButtons.length) {
        applySize(root.getAttribute('data-fontsize') || 'normal');
        sizeButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var size = applySize(button.getAttribute('data-kp-fontsize'));
                store('kompass-fontsize', size === 'normal' ? null : size);
            });
        });
    }

    // ── Kontrast ────────────────────────────────────────────────────────────
    var contrastButton = document.querySelector('[data-kp-contrast]');

    function applyContrast(high) {
        if (high) {
            root.setAttribute('data-contrast', 'high');
        } else {
            root.removeAttribute('data-contrast');
        }
        if (contrastButton) {
            contrastButton.setAttribute('aria-pressed', high ? 'true' : 'false');
        }
    }

    if (contrastButton) {
        applyContrast(root.getAttribute('data-contrast') === 'high');
        contrastButton.addEventListener('click', function () {
            var high = root.getAttribute('data-contrast') !== 'high';
            applyContrast(high);
            store('kompass-contrast', high ? 'high' : null);
        });
    }

    // ── Mobiles Menü ────────────────────────────────────────────────────────
    var toggle = document.querySelector('[data-kp-menu-toggle]');
    var nav = document.getElementById('kp-nav');

    function setMenu(open, restoreFocus) {
        if (!toggle || !nav) {
            return;
        }
        nav.classList.toggle('is-open', open);
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (open) {
            var first = nav.querySelector('a[href]');
            if (first) {
                first.focus();
            }
        } else if (restoreFocus) {
            toggle.focus();
        }
    }

    if (toggle && nav) {
        toggle.addEventListener('click', function () {
            setMenu(!nav.classList.contains('is-open'), true);
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && nav.classList.contains('is-open')) {
                setMenu(false, true);
            }
        });

        window.addEventListener('resize', function () {
            if (nav.classList.contains('is-open') && window.innerWidth > 860) {
                setMenu(false, false);
            }
        });
    }

    // ── Inhaltsverzeichnis: aktuellen Abschnitt markieren ───────────────────
    var toc = document.querySelector('[data-kp-toc]');
    if (toc && 'IntersectionObserver' in window) {
        var links = Array.prototype.slice.call(toc.querySelectorAll('a[href^="#"]'));
        var headings = [];
        links.forEach(function (link) {
            var target = document.getElementById(decodeURIComponent(link.getAttribute('href').slice(1)));
            if (target) {
                headings.push({ el: target, link: link });
            }
        });

        var setActive = function (link) {
            links.forEach(function (item) {
                if (item === link) {
                    item.setAttribute('aria-current', 'location');
                } else {
                    item.removeAttribute('aria-current');
                }
            });
        };

        var visible = new Map();
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    visible.set(entry.target, entry.boundingClientRect.top);
                } else {
                    visible.delete(entry.target);
                }
            });

            var current = null;
            for (var i = 0; i < headings.length; i++) {
                if (visible.has(headings[i].el)) {
                    current = headings[i];
                    break;
                }
            }
            if (!current) {
                // Kein Abschnittsanfang sichtbar: letzte Überschrift oberhalb des Viewports.
                for (var j = headings.length - 1; j >= 0; j--) {
                    if (headings[j].el.getBoundingClientRect().top < 0) {
                        current = headings[j];
                        break;
                    }
                }
            }
            setActive(current ? current.link : null);
        }, { rootMargin: '0px 0px -55% 0px', threshold: 0 });

        headings.forEach(function (item) {
            observer.observe(item.el);
        });
    }

    // ── Drucken & Link kopieren ─────────────────────────────────────────────
    var status = document.querySelector('[data-kp-copy-status]');

    function announce(message) {
        if (!status) {
            return;
        }
        status.textContent = message;
        window.setTimeout(function () {
            status.textContent = '';
        }, 5000);
    }

    document.addEventListener('click', function (event) {
        var target = event.target && event.target.closest ? event.target : null;
        if (!target) {
            return;
        }

        if (target.closest('[data-kp-print]')) {
            window.print();
            return;
        }

        if (target.closest('[data-kp-copy]')) {
            var url = window.location.href.split('#')[0];
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(function () {
                    announce('Link wurde in die Zwischenablage kopiert.');
                }, function () {
                    announce('Kopieren nicht möglich. Link: ' + url);
                });
            } else {
                announce('Kopieren nicht möglich. Link: ' + url);
            }
        }
    });
}());
