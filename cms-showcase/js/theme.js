/**
 * 365CMS Showcase – Theme-Script
 *
 * - Mobiles Menü (aria-expanded, Escape, Klick außerhalb, Fokus-Rückgabe)
 * - Header-Schatten beim Scrollen
 * - Rollen-Tabs nach WAI-ARIA (Pfeiltasten, Pos1/Ende, Roving Tabindex)
 * - Code/Terminal kopieren und Beitragslink kopieren mit Statusmeldung
 * - Inhaltsverzeichnis: aktuellen Abschnitt markieren
 * - Sanftes Einblenden beim Scrollen (respektiert „Bewegung reduzieren“)
 *
 * Vanilla JS, ohne Inline-Handler und HTML-Sinks (CSP-/Trusted-Types-konform).
 */
(function () {
    'use strict';

    var root = document.documentElement;

    // ── Statusmeldungen für Screenreader ────────────────────────────────────
    var live = document.createElement('p');
    live.className = 'sc-visually-hidden';
    live.setAttribute('role', 'status');
    document.body.appendChild(live);

    function announce(message) {
        live.textContent = '';
        window.setTimeout(function () {
            live.textContent = message;
        }, 50);
    }

    function copyText(text) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            return navigator.clipboard.writeText(text);
        }
        return Promise.reject(new Error('Zwischenablage nicht verfügbar'));
    }

    // ── Mobiles Menü ────────────────────────────────────────────────────────
    var header = document.getElementById('sc-header');
    var toggle = document.querySelector('[data-sc-menu-toggle]');
    var nav = document.getElementById('sc-nav');

    function setMenu(open, restoreFocus) {
        if (!toggle || !nav) {
            return;
        }
        nav.classList.toggle('is-open', open);
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        document.body.classList.toggle('sc-menu-open', open);
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

        document.addEventListener('click', function (event) {
            if (nav.classList.contains('is-open') && header && !header.contains(event.target)) {
                setMenu(false, false);
            }
        });

        nav.addEventListener('click', function (event) {
            var link = event.target.closest ? event.target.closest('a[href]') : null;
            if (link && link.getAttribute('href').indexOf('#') !== -1) {
                setMenu(false, false);
            }
        });

        window.addEventListener('resize', function () {
            if (nav.classList.contains('is-open') && window.innerWidth > 1024) {
                setMenu(false, false);
            }
        });
    }

    if (header) {
        var ticking = false;
        var updateHeader = function () {
            ticking = false;
            header.classList.toggle('is-scrolled', window.scrollY > 8);
        };
        window.addEventListener('scroll', function () {
            if (!ticking) {
                ticking = true;
                window.requestAnimationFrame(updateHeader);
            }
        }, { passive: true });
        updateHeader();
    }

    // ── Tabs ────────────────────────────────────────────────────────────────
    Array.prototype.forEach.call(document.querySelectorAll('[data-sc-tabs]'), function (container) {
        var tabs = Array.prototype.slice.call(container.querySelectorAll('[role="tab"]'));
        if (!tabs.length) {
            return;
        }

        function select(tab, focus) {
            tabs.forEach(function (item) {
                var selected = item === tab;
                var panel = document.getElementById(item.getAttribute('aria-controls'));
                item.setAttribute('aria-selected', selected ? 'true' : 'false');
                item.setAttribute('tabindex', selected ? '0' : '-1');
                if (panel) {
                    panel.classList.toggle('is-active', selected);
                }
            });
            if (focus) {
                tab.focus();
            }
        }

        tabs.forEach(function (tab, index) {
            tab.addEventListener('click', function () {
                select(tab, false);
            });
            tab.addEventListener('keydown', function (event) {
                var next = null;
                if (event.key === 'ArrowRight' || event.key === 'ArrowDown') {
                    next = tabs[(index + 1) % tabs.length];
                } else if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') {
                    next = tabs[(index - 1 + tabs.length) % tabs.length];
                } else if (event.key === 'Home') {
                    next = tabs[0];
                } else if (event.key === 'End') {
                    next = tabs[tabs.length - 1];
                }
                if (next) {
                    event.preventDefault();
                    select(next, true);
                }
            });
        });
    });

    // ── Kopieren ────────────────────────────────────────────────────────────
    document.addEventListener('click', function (event) {
        var target = event.target && event.target.closest ? event.target : null;
        if (!target) {
            return;
        }

        var copyButton = target.closest('[data-sc-copy]');
        if (copyButton) {
            var source = document.getElementById(copyButton.getAttribute('data-sc-copy'));
            var label = copyButton.querySelector('[data-sc-copy-label]');
            if (!source) {
                return;
            }
            copyText(source.textContent || '').then(function () {
                copyButton.classList.add('is-done');
                if (label) {
                    label.textContent = 'Kopiert';
                }
                announce('In die Zwischenablage kopiert.');
                window.setTimeout(function () {
                    copyButton.classList.remove('is-done');
                    if (label) {
                        label.textContent = 'Kopieren';
                    }
                }, 2200);
            }, function () {
                announce('Kopieren nicht möglich. Bitte den Text markieren und manuell kopieren.');
            });
            return;
        }

        var shareButton = target.closest('[data-sc-copy-url]');
        if (shareButton) {
            var status = document.querySelector('[data-sc-share-status]');
            var url = window.location.href.split('#')[0];
            copyText(url).then(function () {
                if (status) {
                    status.textContent = 'Link kopiert';
                    window.setTimeout(function () {
                        status.textContent = '';
                    }, 3000);
                }
            }, function () {
                if (status) {
                    status.textContent = 'Kopieren nicht möglich';
                }
            });
        }
    });

    // ── Inhaltsverzeichnis: aktuellen Abschnitt markieren ───────────────────
    var toc = document.querySelector('[data-sc-toc]');
    if (toc && 'IntersectionObserver' in window) {
        var links = Array.prototype.slice.call(toc.querySelectorAll('a[href^="#"]'));
        var headings = [];
        links.forEach(function (link) {
            var heading = document.getElementById(decodeURIComponent(link.getAttribute('href').slice(1)));
            if (heading) {
                headings.push({ el: heading, link: link });
            }
        });

        var visible = new Map();
        var setActive = function (link) {
            links.forEach(function (item) {
                if (item === link) {
                    item.setAttribute('aria-current', 'location');
                } else {
                    item.removeAttribute('aria-current');
                }
            });
        };

        var tocObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    visible.set(entry.target, true);
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
                for (var j = headings.length - 1; j >= 0; j--) {
                    if (headings[j].el.getBoundingClientRect().top < 0) {
                        current = headings[j];
                        break;
                    }
                }
            }
            setActive(current ? current.link : null);
        }, { rootMargin: '-80px 0px -55% 0px', threshold: 0 });

        headings.forEach(function (item) {
            tocObserver.observe(item.el);
        });
    }

    // ── Einblenden beim Scrollen ────────────────────────────────────────────
    var revealItems = Array.prototype.slice.call(document.querySelectorAll('[data-sc-reveal]'));
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (!root.classList.contains('sc-reveal-on') || reduceMotion || !('IntersectionObserver' in window)) {
        revealItems.forEach(function (item) {
            item.classList.add('is-visible');
        });
    } else {
        var revealObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });

        revealItems.forEach(function (item) {
            revealObserver.observe(item);
        });
    }
}());
