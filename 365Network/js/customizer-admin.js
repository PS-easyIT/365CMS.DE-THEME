(function () {
    'use strict';

    const CSS_VAR_MAP = {
        colors_primary_color: '--primary-color',
        colors_primary_hover: '--primary-hover',
        colors_primary_light: '--primary-light',
        colors_secondary_color: '--secondary-color',
        colors_accent_color: '--accent-color',
        colors_accent_hover: '--accent-hover',
        colors_accent_light: '--accent-light',
        colors_text_color: '--text-color',
        colors_heading_color: '--heading-color',
        colors_text_light: '--text-light',
        colors_muted_color: '--muted-color',
        colors_bg_color: '--background-color',
        colors_bg_secondary: '--bg-secondary',
        colors_link_color: '--link-color',
        colors_link_hover_color: '--link-hover-color',
        colors_border_color: '--border-color',
        colors_success_color: '--success-color',
        colors_error_color: '--error-color',
        header_header_bg_color: '--header-bg',
        header_header_text_color: '--header-text',
        header_header_accent_color: '--header-text-secondary',
        footer_footer_bg_color: '--footer-bg',
        footer_footer_text_color: '--footer-text',
        footer_footer_link_color: '--footer-link-color'
    };

    function initCustomizerAdmin() {
        const form = document.getElementById('customizer-form');
        const liveStyle = document.createElement('style');
        liveStyle.id = 'customizer-live-preview';
        document.head.appendChild(liveStyle);

        function updateLivePreview() {
            let rules = ':root {\n';
            Object.entries(CSS_VAR_MAP).forEach(([name, variableName]) => {
                const picker = document.querySelector('input[name="' + name + '"][type="color"]');
                if (picker) {
                    rules += '  ' + variableName + ': ' + picker.value + ';\n';
                }
            });
            rules += '}';
            liveStyle.textContent = rules;
        }

        function syncColorFromPicker(picker) {
            const textFieldId = picker.dataset.syncText || '';
            const textField = textFieldId ? document.getElementById(textFieldId) : null;
            if (textField) {
                textField.value = picker.value;
            }
            updateLivePreview();
        }

        function syncColorFromText(textField) {
            const pickerId = textField.dataset.syncPicker || '';
            const picker = pickerId ? document.getElementById(pickerId) : null;
            const value = textField.value.trim();
            if (picker && /^#[0-9a-fA-F]{6}$/.test(value)) {
                picker.value = value;
                updateLivePreview();
            }
        }

        function createLogoPreviewImage(source, altText) {
            const image = document.createElement('img');
            image.className = 'customizer-logo-preview-image';
            image.alt = altText;
            image.src = source;
            image.addEventListener('error', () => {
                const wrapper = image.closest('[data-customizer-logo-preview]');
                if (!wrapper) {
                    return;
                }
                wrapper.replaceChildren(createLogoPreviewMessage('Bild konnte nicht geladen werden', true));
            }, { once: true });
            return image;
        }

        function createLogoPreviewMessage(message, isError) {
            const span = document.createElement('span');
            span.className = isError
                ? 'customizer-logo-preview-placeholder customizer-logo-preview-placeholder-error'
                : 'customizer-logo-preview-placeholder';
            span.textContent = message;
            return span;
        }

        function setLogoPreview(group, source, options = {}) {
            const preview = group.querySelector('[data-customizer-logo-preview]');
            if (!preview) {
                return;
            }

            if (!source) {
                preview.replaceChildren(createLogoPreviewMessage('🖼️ Noch kein Logo ausgewählt', false));
                return;
            }

            preview.replaceChildren(createLogoPreviewImage(source, options.altText || 'Logo'));
        }

        function bindLogoPreview(group) {
            const fileInput = group.querySelector('[data-customizer-logo-upload]');
            const urlInput = group.querySelector('[data-customizer-logo-url]');

            fileInput?.addEventListener('change', () => {
                const file = fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;
                if (!file) {
                    return;
                }

                const reader = new FileReader();
                reader.addEventListener('load', (event) => {
                    setLogoPreview(group, String(event.target?.result || ''), { altText: 'Logo-Vorschau' });
                    if (urlInput) {
                        urlInput.value = '';
                    }
                });
                reader.readAsDataURL(file);
            });

            urlInput?.addEventListener('input', () => {
                const url = urlInput.value.trim();
                if (url === '') {
                    setLogoPreview(group, '');
                    return;
                }

                if (/^https?:\/\//i.test(url)) {
                    setLogoPreview(group, url, { altText: 'Logo' });
                }
            });
        }

        function initPalettePreview() {
            if (!document.querySelector('input[name="colors_primary_color"]')) {
                return;
            }

            const paletteFields = [
                { name: 'colors_primary_color', label: 'Primär' },
                { name: 'colors_secondary_color', label: 'Sekundär' },
                { name: 'colors_accent_color', label: 'Akzent' },
                { name: 'colors_text_color', label: 'Text' },
                { name: 'colors_bg_color', label: 'Hintergrund' },
                { name: 'colors_bg_secondary', label: 'Surface' },
                { name: 'colors_border_color', label: 'Rahmen' }
            ];

            const palette = document.createElement('div');
            palette.className = 'customizer-palette';

            paletteFields.forEach((fieldConfig) => {
                const input = document.querySelector('input[name="' + fieldConfig.name + '"][type="color"]');
                if (!input) {
                    return;
                }

                const swatch = document.createElement('div');
                swatch.className = 'customizer-palette-item';
                const dot = document.createElement('div');
                dot.className = 'customizer-palette-dot';
                dot.style.background = input.value;
                const label = document.createElement('span');
                label.className = 'customizer-palette-label';
                label.textContent = fieldConfig.label;
                swatch.append(dot, label);
                palette.appendChild(swatch);
                input.addEventListener('input', () => {
                    dot.style.background = input.value;
                });
            });

            const firstCard = document.querySelector('.customizer-content .admin-card');
            if (!firstCard) {
                return;
            }

            const previewWrap = document.createElement('div');
            previewWrap.className = 'customizer-palette-preview';
            const title = document.createElement('div');
            title.className = 'customizer-palette-title';
            title.textContent = 'Farb-Vorschau';
            previewWrap.append(title, palette);
            firstCard.insertBefore(previewWrap, firstCard.firstChild);
        }

        function initResetModal() {
            const modal = document.getElementById('confirm-reset-modal');
            const resetForm = document.getElementById('reset-form');
            if (!modal || !resetForm) {
                return;
            }

            const openModal = () => {
                modal.hidden = false;
                modal.setAttribute('aria-hidden', 'false');
                const confirmButton = modal.querySelector('[data-customizer-reset-confirm]');
                confirmButton?.focus();
            };

            const closeModal = () => {
                modal.hidden = true;
                modal.setAttribute('aria-hidden', 'true');
            };

            document.querySelectorAll('[data-customizer-reset-open]').forEach((button) => {
                button.addEventListener('click', openModal);
            });

            document.querySelectorAll('[data-customizer-reset-close]').forEach((button) => {
                button.addEventListener('click', closeModal);
            });

            modal.querySelector('[data-customizer-reset-confirm]')?.addEventListener('click', () => {
                closeModal();
                resetForm.requestSubmit();
            });

            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !modal.hidden) {
                    event.preventDefault();
                    closeModal();
                }
            });
        }

        document.querySelectorAll('[data-customizer-color-picker]').forEach((picker) => {
            picker.addEventListener('input', () => syncColorFromPicker(picker));
        });

        document.querySelectorAll('[data-customizer-color-text]').forEach((textField) => {
            textField.addEventListener('input', () => syncColorFromText(textField));
            textField.addEventListener('change', () => syncColorFromText(textField));
        });

        document.querySelectorAll('[data-customizer-logo-group]').forEach(bindLogoPreview);

        document.addEventListener('keydown', (event) => {
            if ((event.ctrlKey || event.metaKey) && event.key === 's') {
                event.preventDefault();
                if (form) {
                    form.requestSubmit();
                }
            }
        });

        updateLivePreview();
        initPalettePreview();
        initResetModal();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCustomizerAdmin, { once: true });
    } else {
        initCustomizerAdmin();
    }
})();
