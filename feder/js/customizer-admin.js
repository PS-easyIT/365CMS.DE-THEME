/**
 * Theme-Customizer (Admin) – Farbfelder synchronisieren, Bildvorschau, Rückfrage beim Zurücksetzen.
 * CSP-/Trusted-Types-konform: keine Inline-Handler, keine HTML-Sinks.
 */
(function () {
    'use strict';

    var root = document.querySelector('[data-theme-customizer]');
    if (!root) {
        return;
    }

    // Farbwähler ↔ Hex-Textfeld
    Array.prototype.slice.call(root.querySelectorAll('[data-customizer-color-text]')).forEach(function (text) {
        var picker = document.getElementById(text.getAttribute('data-customizer-color-text') || '');
        if (!picker) {
            return;
        }
        picker.addEventListener('input', function () {
            text.value = picker.value;
        });
        text.addEventListener('input', function () {
            var value = text.value.trim();
            if (/^#[0-9a-fA-F]{6}$/.test(value)) {
                picker.value = value.toLowerCase();
            }
        });
    });

    // Bildfelder: Vorschau für URL-Eingabe und Dateiauswahl
    function showPreview(container, source) {
        var preview = container.querySelector('[data-customizer-image-preview]');
        if (!preview) {
            return;
        }
        while (preview.firstChild) {
            preview.removeChild(preview.firstChild);
        }
        if (!source) {
            var empty = document.createElement('span');
            empty.textContent = 'Noch kein Bild gewählt';
            preview.appendChild(empty);
            return;
        }
        var image = document.createElement('img');
        image.alt = 'Vorschau';
        image.src = source;
        image.addEventListener('error', function () {
            var failed = document.createElement('span');
            failed.textContent = 'Bild konnte nicht geladen werden';
            preview.replaceChildren(failed);
        }, { once: true });
        preview.appendChild(image);
    }

    Array.prototype.slice.call(root.querySelectorAll('[data-customizer-image]')).forEach(function (container) {
        var url = container.querySelector('[data-customizer-image-url]');
        var file = container.querySelector('[data-customizer-image-file]');

        if (url) {
            url.addEventListener('change', function () {
                var value = url.value.trim();
                showPreview(container, /^(https?:\/\/|\/)/i.test(value) ? value : '');
            });
        }
        if (file) {
            file.addEventListener('change', function () {
                var selected = file.files && file.files[0] ? file.files[0] : null;
                if (selected && window.URL && typeof window.URL.createObjectURL === 'function') {
                    showPreview(container, window.URL.createObjectURL(selected));
                }
            });
        }
    });

    // Rückfrage vor dem Zurücksetzen eines Bereichs
    Array.prototype.slice.call(root.querySelectorAll('[data-customizer-reset]')).forEach(function (button) {
        button.addEventListener('click', function (event) {
            if (!window.confirm('Alle Einstellungen dieses Bereichs auf die Standardwerte zurücksetzen?')) {
                event.preventDefault();
            }
        });
    });

    // Strg/Cmd + S speichert den aktuellen Bereich
    var form = document.getElementById('customizer-form');
    document.addEventListener('keydown', function (event) {
        if ((event.ctrlKey || event.metaKey) && (event.key === 's' || event.key === 'S') && form) {
            event.preventDefault();
            if (typeof form.requestSubmit === 'function') {
                form.requestSubmit();
            } else {
                form.submit();
            }
        }
    });
}());
