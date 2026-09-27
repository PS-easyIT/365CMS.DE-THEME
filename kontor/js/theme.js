/**
 * Kontor – Theme-Script
 *
 * - Mobiles Menü (aria-expanded, Escape, Klick außerhalb, Fokus-Rückgabe)
 * - Header-Schatten beim Scrollen
 * - Schließt das mobile Menü nach Klick auf eine Sprungmarke
 *
 * Vanilla JS, ohne Inline-Handler und HTML-Sinks (CSP-/Trusted-Types-konform).
 */
(function () {
    'use strict';

    var header = document.getElementById('kt-header');
    var toggle = document.querySelector('[data-kt-menu-toggle]');
    var menu = document.getElementById('kt-mobile-menu');

    function setOpen(open, restoreFocus) {
        if (!toggle || !menu) {
            return;
        }
        menu.hidden = !open;
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        document.body.classList.toggle('kt-menu-open', open);
        if (open) {
            var first = menu.querySelector('a[href]');
            if (first) {
                first.focus();
            }
        } else if (restoreFocus) {
            toggle.focus();
        }
    }

    if (toggle && menu) {
        toggle.addEventListener('click', function () {
            setOpen(menu.hidden, true);
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !menu.hidden) {
                setOpen(false, true);
            }
        });

        document.addEventListener('click', function (event) {
            if (!menu.hidden && header && !header.contains(event.target)) {
                setOpen(false, false);
            }
        });

        menu.addEventListener('click', function (event) {
            var link = event.target.closest ? event.target.closest('a[href]') : null;
            if (link && link.getAttribute('href').indexOf('#') !== -1) {
                setOpen(false, false);
            }
        });

        window.addEventListener('resize', function () {
            if (!menu.hidden && window.innerWidth > 1024) {
                setOpen(false, false);
            }
        });
    }

    if (header) {
        var ticking = false;
        var update = function () {
            ticking = false;
            header.classList.toggle('is-scrolled', window.scrollY > 10);
        };
        window.addEventListener('scroll', function () {
            if (!ticking) {
                ticking = true;
                window.requestAnimationFrame(update);
            }
        }, { passive: true });
        update();
    }
}());
