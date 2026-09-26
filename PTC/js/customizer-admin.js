/**
 * PTC Theme – Admin-Customizer
 *
 * CSP-konform (365CMS 3.4): keine Inline-Handler, kein innerHTML mit Nutzerdaten.
 * Wird von admin/customizer.php im Theme Editor geladen.
 */
(function () {
    'use strict';

    var cssVarMap = {
        'colors_primary_color':       '--color-primary',
        'colors_primary_hover':       '--color-primary-hover',
        'colors_primary_light':       '--color-primary-light',
        'colors_accent_color':        '--color-accent',
        'colors_accent_hover':        '--color-accent-hover',
        'colors_accent_light':        '--color-accent-light',
        'colors_secondary_color':     '--color-secondary',
        'colors_text_color':          '--color-text',
        'colors_heading_color':       '--color-heading',
        'colors_text_light':          '--color-on-dark',
        'colors_muted_color':         '--color-muted',
        'colors_bg_color':            '--color-bg',
        'colors_bg_secondary':        '--color-bg-alt',
        'colors_link_color':          '--color-link',
        'colors_link_hover_color':    '--color-link-hover',
        'colors_border_color':        '--color-border',
        'colors_success_color':       '--color-success',
        'colors_error_color':         '--color-error',
        'header_header_bg_color':     '--color-primary',
        'header_header_text_color':   '--color-on-dark',
        'header_header_accent_color': '--color-accent',
        'footer_footer_bg_color':     '--footer-bg',
        'footer_footer_text_color':   '--footer-text',
        'footer_footer_link_color':   '--footer-link'
    };
    var HEX_COLOR = /^#[0-9a-fA-F]{6}$/;

    // Live-Vorschau über CSSOM (erlaubt unter der CSP, anders als <style>-Inhalte ohne Nonce).
    function updateLivePreview() {
        var root = document.documentElement;
        Object.keys(cssVarMap).forEach(function (name) {
            var input = document.querySelector('input[type="color"][name="' + name + '"]');
            if (input && HEX_COLOR.test(input.value)) {
                root.style.setProperty(cssVarMap[name], input.value);
            }
        });
    }

    function initColorSync() {
        document.querySelectorAll('input[type="color"]').forEach(function (picker) {
            var textInput = picker.nextElementSibling;
            if (!textInput || textInput.tagName !== 'INPUT' || textInput.type !== 'text') {
                return;
            }
            picker.addEventListener('input', function () {
                textInput.value = picker.value;
                updateLivePreview();
            });
            var syncFromText = function () {
                var value = textInput.value.trim();
                if (HEX_COLOR.test(value)) {
                    picker.value = value;
                    updateLivePreview();
                }
            };
            textInput.addEventListener('input', syncFromText);
            textInput.addEventListener('change', syncFromText);
        });
    }

    function initPalette() {
        if (!document.querySelector('input[name="colors_primary_color"]')) {
            return;
        }
        var paletteFields = [
            { name: 'colors_primary_color',   label: 'Navy' },
            { name: 'colors_accent_color',    label: 'Gold' },
            { name: 'colors_secondary_color', label: 'Slate' },
            { name: 'colors_text_color',      label: 'Text' },
            { name: 'colors_bg_color',        label: 'Hintergrund' },
            { name: 'colors_bg_secondary',    label: 'Surface' },
            { name: 'colors_border_color',    label: 'Rahmen' }
        ];
        var palette = document.createElement('div');
        palette.className = 'ptc-customizer-color-swatches';

        paletteFields.forEach(function (field) {
            var input = document.querySelector('input[type="color"][name="' + field.name + '"]');
            if (!input) {
                return;
            }
            var swatch = document.createElement('div');
            swatch.className = 'ptc-customizer-swatch';
            var dot = document.createElement('div');
            dot.className = 'ptc-customizer-swatch-dot';
            dot.style.background = input.value;
            var label = document.createElement('span');
            label.className = 'ptc-customizer-swatch-label';
            label.textContent = field.label;
            swatch.appendChild(dot);
            swatch.appendChild(label);
            palette.appendChild(swatch);
            input.addEventListener('input', function () {
                dot.style.background = input.value;
            });
        });

        var firstCard = document.querySelector('.customizer-content .admin-card');
        if (firstCard) {
            var wrap = document.createElement('div');
            wrap.className = 'ptc-customizer-preview-wrap';
            var title = document.createElement('div');
            title.className = 'ptc-customizer-preview-title';
            title.textContent = 'Farb-Vorschau';
            wrap.appendChild(title);
            wrap.appendChild(palette);
            firstCard.insertBefore(wrap, firstCard.firstChild);
        }
    }

    function showImage(wrap, id, src, className) {
        if (!wrap) {
            return;
        }
        var img = document.createElement('img');
        img.id = id;
        img.className = className;
        img.alt = 'Vorschau';
        img.src = src;
        img.addEventListener('error', function () {
            var message = document.createElement('span');
            message.className = 'ptc-customizer-preview-error';
            message.textContent = 'Bild konnte nicht geladen werden';
            wrap.replaceChildren(message);
        });
        wrap.replaceChildren(img);
    }

    function readImageFile(input, callback) {
        var file = input.files && input.files[0];
        if (!file || !/^image\/(jpeg|png|gif|webp)$/.test(file.type)) {
            return;
        }
        var reader = new FileReader();
        reader.addEventListener('load', function () {
            if (typeof reader.result === 'string' && reader.result.indexOf('data:image/') === 0) {
                callback(reader.result);
            }
        });
        reader.readAsDataURL(file);
    }

    function initUploads() {
        document.querySelectorAll('input[type="file"][data-ptc-logo-upload]').forEach(function (input) {
            input.addEventListener('change', function () {
                readImageFile(input, function (dataUrl) {
                    showImage(document.getElementById('logo-preview-wrap'), 'logo-preview-img', dataUrl, 'ptc-customizer-logo-preview');
                    var urlField = document.querySelector('input[name="header_logo_url"]');
                    if (urlField) {
                        urlField.value = '';
                    }
                });
            });
        });

        document.querySelectorAll('input[type="file"][data-ptc-image-preview]').forEach(function (input) {
            var prefix = (input.getAttribute('data-ptc-image-preview') || '').replace(/[^a-z0-9_-]/gi, '');
            if (prefix === '') {
                return;
            }
            input.addEventListener('change', function () {
                readImageFile(input, function (dataUrl) {
                    showImage(document.getElementById(prefix + '-wrap'), prefix + '-img', dataUrl, 'ptc-customizer-image-preview');
                });
            });
        });

        document.querySelectorAll('input[data-ptc-logo-url]').forEach(function (input) {
            input.addEventListener('input', function () {
                var url = input.value.trim();
                if (/^https?:\/\/[^\s"'<>]+$/i.test(url)) {
                    showImage(document.getElementById('logo-preview-wrap'), 'logo-preview-img', url, 'ptc-customizer-logo-preview');
                }
            });
        });
    }

    function initResetModal() {
        var modal = document.getElementById('confirm-reset-modal');
        var resetForm = document.getElementById('reset-form');
        if (!modal) {
            return;
        }
        var open = function () {
            modal.hidden = false;
            modal.setAttribute('aria-hidden', 'false');
        };
        var close = function () {
            modal.hidden = true;
            modal.setAttribute('aria-hidden', 'true');
        };

        document.addEventListener('click', function (event) {
            var target = event.target instanceof Element ? event.target : null;
            if (!target) {
                return;
            }
            if (target.closest('[data-ptc-reset-open]')) {
                event.preventDefault();
                open();
            } else if (target.closest('[data-ptc-reset-close]') || target === modal) {
                event.preventDefault();
                close();
            } else if (target.closest('[data-ptc-reset-confirm]')) {
                event.preventDefault();
                close();
                if (resetForm) {
                    resetForm.submit();
                }
            }
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !modal.hidden) {
                close();
            }
        });
    }

    function initSaveShortcut() {
        document.addEventListener('keydown', function (event) {
            if ((event.ctrlKey || event.metaKey) && event.key === 's') {
                var button = document.querySelector('#customizer-form button[type="submit"], form button[type="submit"].btn-primary');
                if (button) {
                    event.preventDefault();
                    button.click();
                }
            }
        });
    }

    function init() {
        initColorSync();
        initPalette();
        initUploads();
        initResetModal();
        initSaveShortcut();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
