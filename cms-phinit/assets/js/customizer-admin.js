/**
 * CMS Phinit Theme – Customizer Admin Interactions
 */
(function () {
    'use strict';

    const COLOR_PRESETS = {
        phinit: {primary_color:'#1e3a5f',primary_dark:'#0f2340',primary_mid:'#1a3255',primary_light:'#2a4f7c',accent_color:'#e8a838',accent_hover:'#d4922a',accent_blue:'#4a9eff',accent_blue2:'#2d7dd2',accent_teal:'#0d9488',accent_teal_light:'#14b8a6',bg_header1:'#111827',bg_header2:'#162030',bg_header3:'#0e1a28',bg_primary:'#ffffff',bg_secondary:'#f1f5f9',bg_dark:'#0a0f1a',text_primary:'#1e293b',text_secondary:'#4a5568',text_muted:'#7a8898',text_nav:'#e2e8f0',text_nav_member:'#e2e8f0',text_nav_main:'#e2e8f0',text_nav_quicklinks:'#b0bec5',text_nav_dropdown:'#e2e8f0',logo_suffix_color:'#e8a838',border_light:'#dde3ea',footer_bg:'#0d1828',footer_bottom_bg:'#080d15',footer_border:'#2d7dd2',success_color:'#16a34a',error_color:'#dc2626',progress_bar_start:'#2d7dd2',progress_bar_end:'#e8a838'},
        bluesteel: {primary_color:'#1a2744',primary_dark:'#0d1a33',primary_mid:'#162140',primary_light:'#233b6e',accent_color:'#3b82f6',accent_hover:'#2563eb',accent_blue:'#60a5fa',accent_blue2:'#3b82f6',accent_teal:'#0ea5e9',accent_teal_light:'#38bdf8',bg_header1:'#0d1a33',bg_header2:'#111f3d',bg_header3:'#091528',bg_primary:'#f8fafc',bg_secondary:'#eff6ff',bg_dark:'#060d1a',text_primary:'#0f172a',text_secondary:'#334155',text_muted:'#64748b',text_nav:'#e2e8f0',text_nav_member:'#e2e8f0',text_nav_main:'#e2e8f0',text_nav_quicklinks:'#94a3b8',text_nav_dropdown:'#e2e8f0',logo_suffix_color:'#60a5fa',border_light:'#e2e8f0',footer_bg:'#0b1630',footer_bottom_bg:'#060e1e',footer_border:'#3b82f6',success_color:'#22c55e',error_color:'#ef4444',progress_bar_start:'#3b82f6',progress_bar_end:'#60a5fa'},
        greentech: {primary_color:'#064e3b',primary_dark:'#022c22',primary_mid:'#065f46',primary_light:'#047857',accent_color:'#10b981',accent_hover:'#059669',accent_blue:'#34d399',accent_blue2:'#10b981',accent_teal:'#0d9488',accent_teal_light:'#2dd4bf',bg_header1:'#022c22',bg_header2:'#0a3728',bg_header3:'#001a14',bg_primary:'#f0fdf4',bg_secondary:'#ecfdf5',bg_dark:'#01110b',text_primary:'#064e3b',text_secondary:'#065f46',text_muted:'#6b7280',text_nav:'#d1fae5',text_nav_member:'#d1fae5',text_nav_main:'#d1fae5',text_nav_quicklinks:'#6ee7b7',text_nav_dropdown:'#d1fae5',logo_suffix_color:'#10b981',border_light:'#d1fae5',footer_bg:'#031c15',footer_bottom_bg:'#010e0a',footer_border:'#10b981',success_color:'#10b981',error_color:'#ef4444',progress_bar_start:'#10b981',progress_bar_end:'#2dd4bf'},
        slate: {primary_color:'#1e293b',primary_dark:'#0f172a',primary_mid:'#1c2944',primary_light:'#334155',accent_color:'#f59e0b',accent_hover:'#d97706',accent_blue:'#818cf8',accent_blue2:'#6366f1',accent_teal:'#06b6d4',accent_teal_light:'#22d3ee',bg_header1:'#0f172a',bg_header2:'#1e293b',bg_header3:'#0b1120',bg_primary:'#ffffff',bg_secondary:'#f8fafc',bg_dark:'#060c16',text_primary:'#0f172a',text_secondary:'#334155',text_muted:'#64748b',text_nav:'#f1f5f9',text_nav_member:'#f1f5f9',text_nav_main:'#f1f5f9',text_nav_quicklinks:'#94a3b8',text_nav_dropdown:'#f1f5f9',logo_suffix_color:'#f59e0b',border_light:'#e2e8f0',footer_bg:'#0c1527',footer_bottom_bg:'#060b15',footer_border:'#6366f1',success_color:'#22c55e',error_color:'#ef4444',progress_bar_start:'#6366f1',progress_bar_end:'#f59e0b'},
        ruby: {primary_color:'#7f1d1d',primary_dark:'#450a0a',primary_mid:'#6b1b1b',primary_light:'#991b1b',accent_color:'#ef4444',accent_hover:'#dc2626',accent_blue:'#f87171',accent_blue2:'#ef4444',accent_teal:'#f59e0b',accent_teal_light:'#fbbf24',bg_header1:'#1c0a0a',bg_header2:'#280d0d',bg_header3:'#140707',bg_primary:'#fffbfb',bg_secondary:'#fef2f2',bg_dark:'#0a0404',text_primary:'#1c0707',text_secondary:'#450a0a',text_muted:'#6b7280',text_nav:'#fee2e2',text_nav_member:'#fee2e2',text_nav_main:'#fee2e2',text_nav_quicklinks:'#fca5a5',text_nav_dropdown:'#fee2e2',logo_suffix_color:'#f59e0b',border_light:'#fecaca',footer_bg:'#1a0707',footer_bottom_bg:'#0d0404',footer_border:'#ef4444',success_color:'#16a34a',error_color:'#dc2626',progress_bar_start:'#ef4444',progress_bar_end:'#f59e0b'}
    };

    const GOOGLE_FONT_FAMILIES = {
        'barlow':'Barlow','inter':'Inter','roboto':'Roboto','open-sans':'Open+Sans',
        'lato':'Lato','montserrat':'Montserrat','poppins':'Poppins',
        'source-sans':'Source+Sans+3','nunito':'Nunito',
        'barlow-condensed':'Barlow+Condensed','roboto-condensed':'Roboto+Condensed',
        'oswald':'Oswald','rajdhani':'Rajdhani','exo2':'Exo+2',
        'jetbrains-mono':'JetBrains+Mono','fira-code':'Fira+Code','source-code':'Source+Code+Pro'
    };

    const FONT_FAMILY_NAMES = {
        'barlow':'Barlow','inter':'Inter','roboto':'Roboto','open-sans':'"Open Sans"',
        'lato':'Lato','montserrat':'Montserrat','poppins':'Poppins',
        'source-sans':'"Source Sans 3"','nunito':'Nunito','system':'system-ui,sans-serif',
        'barlow-condensed':'"Barlow Condensed"','roboto-condensed':'"Roboto Condensed"',
        'oswald':'Oswald','rajdhani':'Rajdhani','exo2':'"Exo 2"',
        'jetbrains-mono':'"JetBrains Mono",monospace','fira-code':'"Fira Code",monospace',
        'source-code':'"Source Code Pro",monospace','cascadia':'"Cascadia Code",monospace',
        'system-mono':'monospace'
    };

    function readConfig() {
        const configElement = document.getElementById('phinit-customizer-config');
        if (!configElement) {
            return {};
        }

        try {
            return JSON.parse(configElement.textContent || '{}');
        } catch (error) {
            return {};
        }
    }

    function initCustomizerAdmin() {
        const config = readConfig();
        const form = document.getElementById('customizer-form');
        const hint = document.getElementById('unsaved-hint');
        const pxDrawer = document.getElementById('px-drawer');
        const pxIframe = document.getElementById('px-iframe');
        const pxLabel = document.getElementById('px-label');
        const pxDevBtns = document.querySelectorAll('.px-dev-btn');
        const confirmOverlay = document.getElementById('phinit-customizer-confirm');
        const confirmMessage = document.getElementById('phinit-customizer-confirm-message');
        const confirmAccept = confirmOverlay ? confirmOverlay.querySelector('[data-confirm-accept]') : null;
        const confirmCancel = confirmOverlay ? confirmOverlay.querySelector('[data-confirm-cancel]') : null;
        const loadedFonts = new Set();
        let changed = false;
        let pendingConfirmButton = null;

        function markChanged() {
            if (!changed) {
                changed = true;
                if (hint) {
                    hint.style.display = 'inline';
                }
            }
        }

        function syncColor(cpId, txtId, hiddenId) {
            const cp = document.getElementById(cpId);
            const txt = document.getElementById(txtId);
            const hid = document.getElementById(hiddenId);
            if (cp && txt) {
                txt.value = cp.value;
            }
            if (hid && cp) {
                hid.value = cp.value;
            }
            markChanged();
        }

        function syncColorTxt(cpId, txtId, hiddenId) {
            const cp = document.getElementById(cpId);
            const txt = document.getElementById(txtId);
            const hid = document.getElementById(hiddenId);
            const value = txt ? txt.value : '';

            if (cp && /^#[0-9a-f]{6}$/i.test(value)) {
                cp.value = value;
            }
            if (hid) {
                hid.value = value;
            }
            markChanged();
        }

        window.syncColor = syncColor;
        window.syncColorTxt = syncColorTxt;

        document.querySelectorAll('[data-color-picker]').forEach((element) => {
            element.addEventListener('input', function () {
                syncColor(
                    element.id,
                    element.dataset.syncTargetText || '',
                    element.dataset.syncTargetHidden || ''
                );
            });
        });

        document.querySelectorAll('[data-color-text]').forEach((element) => {
            element.addEventListener('input', function () {
                syncColorTxt(
                    element.dataset.syncTargetPicker || '',
                    element.id,
                    element.dataset.syncTargetHidden || ''
                );
            });
        });

        function closeConfirmModal() {
            if (!confirmOverlay) {
                return;
            }

            confirmOverlay.hidden = true;
            confirmOverlay.setAttribute('aria-hidden', 'true');
            pendingConfirmButton?.focus();
        }

        function openConfirmModal(button) {
            if (!confirmOverlay || !confirmMessage) {
                return;
            }

            pendingConfirmButton = button;
            confirmMessage.textContent = button.dataset.confirmMessage || 'Möchtest du fortfahren?';
            confirmOverlay.hidden = false;
            confirmOverlay.setAttribute('aria-hidden', 'false');
            confirmAccept?.focus();
        }

        document.querySelectorAll('[data-confirm-message][data-confirm-submit-target]').forEach((element) => {
            element.addEventListener('click', function (event) {
                event.preventDefault();
                openConfirmModal(element);
            });
        });

        confirmCancel?.addEventListener('click', closeConfirmModal);
        confirmOverlay?.addEventListener('click', function (event) {
            if (event.target === confirmOverlay) {
                closeConfirmModal();
            }
        });
        confirmAccept?.addEventListener('click', function () {
            if (!pendingConfirmButton) {
                closeConfirmModal();
                return;
            }

            const submitTargetId = pendingConfirmButton.dataset.confirmSubmitTarget || '';
            const submitTarget = submitTargetId ? document.getElementById(submitTargetId) : null;
            closeConfirmModal();
            submitTarget?.click();
        });

        if (form) {
            form.querySelectorAll('input, select, textarea').forEach((element) => {
                element.addEventListener('change', markChanged);
                element.addEventListener('input', markChanged);
            });
            form.addEventListener('submit', function () {
                changed = false;
            });
        }

        document.addEventListener('keydown', function (event) {
            if ((event.ctrlKey || event.metaKey) && event.key === 's') {
                event.preventDefault();
                if (form) {
                    form.requestSubmit();
                }
            }

            if (event.key === 'Escape' && confirmOverlay && !confirmOverlay.hidden) {
                event.preventDefault();
                closeConfirmModal();
            }
        });

        window.addEventListener('beforeunload', function (event) {
            if (!changed) {
                return;
            }

            event.preventDefault();
            event.returnValue = '';
        });

        function pxOpen() {
            if (!pxDrawer) {
                return;
            }

            pxDrawer.style.display = 'flex';
            pxDrawer.removeAttribute('aria-hidden');
            if (pxIframe && !pxIframe.src && config.siteOrigin) {
                pxIframe.src = config.siteOrigin;
            }
        }

        function pxClose() {
            if (!pxDrawer) {
                return;
            }

            pxDrawer.style.display = 'none';
            pxDrawer.setAttribute('aria-hidden', 'true');
        }

        function pxRefresh() {
            if (!pxIframe || !pxIframe.src) {
                return;
            }

            const currentSrc = pxIframe.src;
            pxIframe.src = '';
            pxIframe.src = currentSrc;
        }

        function pxSetDevice(width) {
            if (!pxIframe) {
                return;
            }

            pxIframe.style.width = width + 'px';
            const labels = {1280:'Desktop (1280 px)', 768:'Tablet (768 px)', 375:'Mobil (375 px)'};
            if (pxLabel) {
                pxLabel.textContent = labels[width] || (width + ' px');
            }

            pxDevBtns.forEach((button) => {
                button.classList.toggle('active', Number(button.dataset.width) === width);
            });
        }

        document.getElementById('preview-toggle-btn')?.addEventListener('click', pxOpen);
        document.getElementById('px-close-btn')?.addEventListener('click', pxClose);
        document.getElementById('px-refresh-btn')?.addEventListener('click', pxRefresh);
        pxDevBtns.forEach((button) => {
            button.addEventListener('click', function () {
                pxSetDevice(Number(button.dataset.width || '1280'));
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && pxDrawer && pxDrawer.style.display !== 'none') {
                pxClose();
            }
        });

        document.querySelectorAll('.color-preset-btn').forEach((button) => {
            button.addEventListener('click', function () {
                const preset = COLOR_PRESETS[button.dataset.preset || ''];
                if (!preset) {
                    return;
                }

                Object.entries(preset).forEach(([key, value]) => {
                    const cp = document.getElementById('f_colors_' + key);
                    const txt = document.getElementById('f_colors_' + key + '_txt');
                    const hid = document.getElementById('colors_' + key);
                    if (cp) {
                        cp.value = value;
                    }
                    if (txt) {
                        txt.value = value;
                    }
                    if (hid) {
                        hid.value = value;
                    }
                });

                markChanged();
            });
        });

        function loadFont(slug) {
            if (loadedFonts.has(slug)) {
                return;
            }

            loadedFonts.add(slug);
            const link = document.createElement('link');
            link.rel = 'stylesheet';

            if (config.preferLocalFonts && config.localFontCssMap && config.localFontCssMap[slug]) {
                link.href = config.localFontCssMap[slug];
                document.head.appendChild(link);
                return;
            }

            if (!GOOGLE_FONT_FAMILIES[slug]) {
                return;
            }

            link.href = 'https://fonts.googleapis.com/css2?family=' + GOOGLE_FONT_FAMILIES[slug] + ':wght@400;700&display=swap';
            document.head.appendChild(link);
        }

        ['f_typography_font_family_ui', 'f_typography_font_family_brand', 'f_typography_font_family_code'].forEach((fieldId) => {
            const select = document.getElementById(fieldId);
            if (!select) {
                return;
            }

            const preview = document.createElement('div');
            preview.className = 'phinit-customizer__font-preview';
            preview.textContent = 'AaBbCc 0123 – PowerShell & M365 Administration';
            select.after(preview);

            const updatePreview = (value) => {
                loadFont(value);
                preview.style.fontFamily = FONT_FAMILY_NAMES[value] || 'inherit';
            };

            updatePreview(select.value);
            select.addEventListener('change', function (event) {
                updatePreview(event.target.value);
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCustomizerAdmin, { once: true });
    } else {
        initCustomizerAdmin();
    }
})();