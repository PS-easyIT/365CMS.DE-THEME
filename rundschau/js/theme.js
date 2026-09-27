/**
 * Rundschau – Theme-Script
 *
 * - Such- und Menü-Panel (aria-expanded, Escape, Klick außerhalb, Fokus-Rückgabe)
 * - Nachrichten-Ticker: gleichmäßige Geschwindigkeit, Pause-Schalter
 * - Ressort-Leiste: Schatten beim Fixieren, Verlaufskanten beim horizontalen Scrollen
 * - „Link kopieren“ und „Drucken“
 *
 * Vanilla JS, ohne Inline-Handler und HTML-Sinks (CSP-/Trusted-Types-konform).
 */
(function () {
    'use strict';

    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // ── Panels ──────────────────────────────────────────────────────────────
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
        document.body.classList.remove('rs-has-panel');
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
        document.body.classList.add('rs-has-panel');
        openToggle = toggle;
        var target = panel.querySelector('input[type="search"], a[href]');
        if (target) {
            target.focus();
        }
    }

    Array.prototype.slice.call(document.querySelectorAll('[data-rs-toggle]')).forEach(function (toggle) {
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
        if (!openToggle) {
            return;
        }
        var panel = panelFor(openToggle);
        if (panel && !panel.contains(event.target) && !openToggle.contains(event.target)) {
            closePanel(false);
        }
    });

    // ── Ticker ──────────────────────────────────────────────────────────────
    var ticker = document.querySelector('[data-rs-ticker]');
    if (ticker) {
        var track = ticker.querySelector('[data-rs-ticker-track]');
        var toggle = ticker.querySelector('[data-rs-ticker-toggle]');
        var speeds = { slow: 35, normal: 55, fast: 85 };
        var speed = speeds.normal;
        Object.keys(speeds).forEach(function (key) {
            if (ticker.classList.contains('rs-ticker--' + key)) {
                speed = speeds[key];
            }
        });

        var setDuration = function () {
            if (!track) {
                return;
            }
            var distance = track.scrollWidth / 2;
            if (distance > 0) {
                track.style.animationDuration = Math.max(12, Math.round(distance / speed)) + 's';
            }
        };

        if (!reduceMotion) {
            setDuration();
            window.addEventListener('resize', setDuration);
            ticker.classList.add('is-animated');
        }

        if (toggle) {
            if (reduceMotion) {
                toggle.hidden = true;
            }
            toggle.addEventListener('click', function () {
                var paused = ticker.classList.toggle('is-paused');
                toggle.setAttribute('aria-pressed', paused ? 'true' : 'false');
                var label = toggle.querySelector('.rs-visually-hidden');
                if (label) {
                    label.textContent = paused ? 'Ticker fortsetzen' : 'Ticker anhalten';
                }
            });
        }
    }

    // ── Ressort-Leiste ──────────────────────────────────────────────────────
    var navbar = document.querySelector('.rs-navbar');
    var scroller = document.querySelector('[data-rs-scroll-shadow]');
    var list = scroller ? scroller.querySelector('ul') : null;

    function updateEdges() {
        if (!scroller || !list) {
            return;
        }
        var max = list.scrollWidth - list.clientWidth;
        scroller.classList.toggle('has-left', list.scrollLeft > 4);
        scroller.classList.toggle('has-right', max - list.scrollLeft > 4);
    }

    if (list) {
        list.addEventListener('scroll', updateEdges, { passive: true });
        window.addEventListener('resize', updateEdges);
        updateEdges();
        var active = list.querySelector('[aria-current="page"]');
        if (active && typeof active.scrollIntoView === 'function' && list.scrollWidth > list.clientWidth) {
            list.scrollLeft = Math.max(0, active.offsetLeft - 24);
            updateEdges();
        }
    }

    if (navbar) {
        var onScroll = function () {
            navbar.classList.toggle('is-stuck', navbar.getBoundingClientRect().top <= 0 && window.scrollY > 0);
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }

    // ── Teilen: Link kopieren & Drucken ─────────────────────────────────────
    var status = document.querySelector('[data-rs-copy-status]');
    Array.prototype.slice.call(document.querySelectorAll('[data-rs-copy]')).forEach(function (button) {
        button.addEventListener('click', function () {
            var url = button.getAttribute('data-rs-copy') || window.location.href;
            var report = function (text) {
                button.classList.add('is-done');
                if (status) {
                    status.textContent = text;
                }
                window.setTimeout(function () {
                    button.classList.remove('is-done');
                }, 2000);
            };
            if (navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
                navigator.clipboard.writeText(url).then(function () {
                    report('Link kopiert');
                }, function () {
                    report('Kopieren nicht möglich');
                });
            } else {
                report('Kopieren nicht möglich');
            }
        });
    });

    Array.prototype.slice.call(document.querySelectorAll('[data-rs-print]')).forEach(function (button) {
        button.addEventListener('click', function () {
            window.print();
        });
    });
}());
