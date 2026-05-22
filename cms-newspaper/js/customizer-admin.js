(function () {
    'use strict';

    function initCustomizerAdmin() {
        const form = document.getElementById('customizer-form');

        function syncColorFromPicker(picker) {
            const textFieldId = picker.dataset.syncText || '';
            const textField = textFieldId ? document.getElementById(textFieldId) : null;
            if (textField) {
                textField.value = picker.value;
            }
        }

        function syncColorFromText(textField) {
            const pickerId = textField.dataset.syncPicker || '';
            const picker = pickerId ? document.getElementById(pickerId) : null;
            const value = textField.value.trim();
            if (picker && /^#[0-9a-fA-F]{6}$/.test(value)) {
                picker.value = value;
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
                const span = document.createElement('span');
                span.className = 'customizer-logo-preview-placeholder customizer-logo-preview-placeholder-error';
                span.textContent = 'Bild konnte nicht geladen werden';
                wrapper.replaceChildren(span);
            }, { once: true });
            return image;
        }

        function setLogoPreview(group, source) {
            const preview = group.querySelector('[data-customizer-logo-preview]');
            if (!preview) {
                return;
            }
            if (!source) {
                const span = document.createElement('span');
                span.className = 'customizer-logo-preview-placeholder';
                span.textContent = '🖼️ Noch kein Bild ausgewählt';
                preview.replaceChildren(span);
                return;
            }
            preview.replaceChildren(createLogoPreviewImage(source, 'Vorschau'));
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
                    setLogoPreview(group, String(event.target?.result || ''));
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
                if (/^https?:\/\//i.test(url) || url.startsWith('/')) {
                    setLogoPreview(group, url);
                }
            });
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
                modal.querySelector('[data-customizer-reset-confirm]')?.focus();
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
                form?.requestSubmit();
            }
        });

        initResetModal();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCustomizerAdmin, { once: true });
    } else {
        initCustomizerAdmin();
    }
})();
