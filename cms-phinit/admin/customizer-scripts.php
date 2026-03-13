<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}
?>
<script>
(function () {
    'use strict';

    // Unsaved-Changes-Warnung + Strg+S
    const form    = document.getElementById('customizer-form');
    const hint    = document.getElementById('unsaved-hint');
    const section = document.getElementById('active_section_input');
    let   changed = false;

    if (form) {
        form.querySelectorAll('input, select, textarea').forEach(el => {
            el.addEventListener('change', markChanged);
            el.addEventListener('input',  markChanged);
        });
        form.addEventListener('submit', () => { changed = false; });
    }

    function markChanged() {
        if (!changed) {
            changed = true;
            if (hint) hint.style.display = 'inline';
        }
    }

    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            if (form) form.requestSubmit();
        }
    });

    window.addEventListener('beforeunload', function (e) {
        if (changed) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // Farbfeld: color-Picker ↔ Text-Input synchronisieren
    window.syncColor = function (cpId, txtId, hiddenId) {
        const cp  = document.getElementById(cpId);
        const txt = document.getElementById(txtId);
        const hid = document.getElementById(hiddenId);
        if (cp && txt) { txt.value = cp.value; }
        if (hid && cp) { hid.value = cp.value; }
        markChanged();
    };
    window.syncColorTxt = function (cpId, txtId, hiddenId) {
        const cp  = document.getElementById(cpId);
        const txt = document.getElementById(txtId);
        const hid = document.getElementById(hiddenId);
        const v   = txt ? txt.value : '';
        if (cp && /^#[0-9a-f]{6}$/i.test(v)) { cp.value = v; }
        if (hid) { hid.value = v; }
        markChanged();
    };

    // ── 1.5 Live-Vorschau Drawer ─────────────────────────────────────────────
    const pxDrawer = document.getElementById('px-drawer');
    const pxIframe = document.getElementById('px-iframe');
    const pxLabel  = document.getElementById('px-label');
    const pxDevBtns = document.querySelectorAll('.px-dev-btn');
    const SITE_ORIGIN = <?php echo json_encode(rtrim(SITE_URL, '/') . '/'); ?>;

    function pxOpen() {
        if (!pxDrawer) return;
        pxDrawer.style.display = 'flex';
        pxDrawer.removeAttribute('aria-hidden');
        if (pxIframe && !pxIframe.src) { pxIframe.src = SITE_ORIGIN; }
    }
    function pxClose() {
        if (!pxDrawer) return;
        pxDrawer.style.display = 'none';
        pxDrawer.setAttribute('aria-hidden', 'true');
    }
    function pxRefresh() {
        if (!pxIframe) return;
        const s = pxIframe.src; pxIframe.src = ''; pxIframe.src = s;
    }
    function pxSetDevice(w) {
        if (!pxIframe) return;
        pxIframe.style.width = w + 'px';
        const L = {1280:'Desktop (1280 px)', 768:'Tablet (768 px)', 375:'Mobil (375 px)'};
        if (pxLabel) pxLabel.textContent = L[w] || (w + ' px');
        pxDevBtns.forEach(b => {
            const active = +b.dataset.width === w;
            b.style.background = active ? '#1e293b' : 'none';
            b.style.color = active ? '#e2e8f0' : '#94a3b8';
        });
    }

    document.getElementById('preview-toggle-btn')?.addEventListener('click', pxOpen);
    document.getElementById('px-close-btn')?.addEventListener('click', pxClose);
    document.getElementById('px-refresh-btn')?.addEventListener('click', pxRefresh);
    pxDevBtns.forEach(b => b.addEventListener('click', () => pxSetDevice(+b.dataset.width)));
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && pxDrawer && pxDrawer.style.display !== 'none') pxClose();
    });

    // ── 1.7 Farb-Palette Presets ─────────────────────────────────────────────
    const COLOR_PRESETS = {
        phinit:    {primary_color:'#1e3a5f',primary_dark:'#0f2340',primary_mid:'#1a3255',primary_light:'#2a4f7c',accent_color:'#e8a838',accent_hover:'#d4922a',accent_blue:'#4a9eff',accent_blue2:'#2d7dd2',accent_teal:'#0d9488',accent_teal_light:'#14b8a6',bg_header1:'#111827',bg_header2:'#162030',bg_header3:'#0e1a28',bg_primary:'#ffffff',bg_secondary:'#f1f5f9',bg_dark:'#0a0f1a',text_primary:'#1e293b',text_secondary:'#4a5568',text_muted:'#7a8898',text_nav:'#e2e8f0',text_nav_member:'#e2e8f0',text_nav_main:'#e2e8f0',text_nav_quicklinks:'#b0bec5',text_nav_dropdown:'#1e293b',logo_suffix_color:'#e8a838',border_light:'#dde3ea',footer_bg:'#0d1828',footer_bottom_bg:'#080d15',footer_border:'#2d7dd2',success_color:'#16a34a',error_color:'#dc2626',progress_bar_start:'#2d7dd2',progress_bar_end:'#e8a838'},
        bluesteel: {primary_color:'#1a2744',primary_dark:'#0d1a33',primary_mid:'#162140',primary_light:'#233b6e',accent_color:'#3b82f6',accent_hover:'#2563eb',accent_blue:'#60a5fa',accent_blue2:'#3b82f6',accent_teal:'#0ea5e9',accent_teal_light:'#38bdf8',bg_header1:'#0d1a33',bg_header2:'#111f3d',bg_header3:'#091528',bg_primary:'#f8fafc',bg_secondary:'#eff6ff',bg_dark:'#060d1a',text_primary:'#0f172a',text_secondary:'#334155',text_muted:'#64748b',text_nav:'#e2e8f0',text_nav_member:'#e2e8f0',text_nav_main:'#e2e8f0',text_nav_quicklinks:'#94a3b8',text_nav_dropdown:'#0f172a',logo_suffix_color:'#60a5fa',border_light:'#e2e8f0',footer_bg:'#0b1630',footer_bottom_bg:'#060e1e',footer_border:'#3b82f6',success_color:'#22c55e',error_color:'#ef4444',progress_bar_start:'#3b82f6',progress_bar_end:'#60a5fa'},
        greentech: {primary_color:'#064e3b',primary_dark:'#022c22',primary_mid:'#065f46',primary_light:'#047857',accent_color:'#10b981',accent_hover:'#059669',accent_blue:'#34d399',accent_blue2:'#10b981',accent_teal:'#0d9488',accent_teal_light:'#2dd4bf',bg_header1:'#022c22',bg_header2:'#0a3728',bg_header3:'#001a14',bg_primary:'#f0fdf4',bg_secondary:'#ecfdf5',bg_dark:'#01110b',text_primary:'#064e3b',text_secondary:'#065f46',text_muted:'#6b7280',text_nav:'#d1fae5',text_nav_member:'#d1fae5',text_nav_main:'#d1fae5',text_nav_quicklinks:'#6ee7b7',text_nav_dropdown:'#064e3b',logo_suffix_color:'#10b981',border_light:'#d1fae5',footer_bg:'#031c15',footer_bottom_bg:'#010e0a',footer_border:'#10b981',success_color:'#10b981',error_color:'#ef4444',progress_bar_start:'#10b981',progress_bar_end:'#2dd4bf'},
        slate:     {primary_color:'#1e293b',primary_dark:'#0f172a',primary_mid:'#1c2944',primary_light:'#334155',accent_color:'#f59e0b',accent_hover:'#d97706',accent_blue:'#818cf8',accent_blue2:'#6366f1',accent_teal:'#06b6d4',accent_teal_light:'#22d3ee',bg_header1:'#0f172a',bg_header2:'#1e293b',bg_header3:'#0b1120',bg_primary:'#ffffff',bg_secondary:'#f8fafc',bg_dark:'#060c16',text_primary:'#0f172a',text_secondary:'#334155',text_muted:'#64748b',text_nav:'#f1f5f9',text_nav_member:'#f1f5f9',text_nav_main:'#f1f5f9',text_nav_quicklinks:'#94a3b8',text_nav_dropdown:'#1e293b',logo_suffix_color:'#f59e0b',border_light:'#e2e8f0',footer_bg:'#0c1527',footer_bottom_bg:'#060b15',footer_border:'#6366f1',success_color:'#22c55e',error_color:'#ef4444',progress_bar_start:'#6366f1',progress_bar_end:'#f59e0b'},
        ruby:      {primary_color:'#7f1d1d',primary_dark:'#450a0a',primary_mid:'#6b1b1b',primary_light:'#991b1b',accent_color:'#ef4444',accent_hover:'#dc2626',accent_blue:'#f87171',accent_blue2:'#ef4444',accent_teal:'#f59e0b',accent_teal_light:'#fbbf24',bg_header1:'#1c0a0a',bg_header2:'#280d0d',bg_header3:'#140707',bg_primary:'#fffbfb',bg_secondary:'#fef2f2',bg_dark:'#0a0404',text_primary:'#1c0707',text_secondary:'#450a0a',text_muted:'#6b7280',text_nav:'#fee2e2',text_nav_member:'#fee2e2',text_nav_main:'#fee2e2',text_nav_quicklinks:'#fca5a5',text_nav_dropdown:'#450a0a',logo_suffix_color:'#f59e0b',border_light:'#fecaca',footer_bg:'#1a0707',footer_bottom_bg:'#0d0404',footer_border:'#ef4444',success_color:'#16a34a',error_color:'#dc2626',progress_bar_start:'#ef4444',progress_bar_end:'#f59e0b'}
    };

    document.querySelectorAll('.color-preset-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const preset = COLOR_PRESETS[btn.dataset.preset];
            if (!preset) return;
            Object.entries(preset).forEach(([key, val]) => {
                const cp  = document.getElementById('f_colors_' + key);
                const txt = document.getElementById('f_colors_' + key + '_txt');
                const hid = document.getElementById('colors_' + key);
                if (cp)  cp.value  = val;
                if (txt) txt.value = val;
                if (hid) hid.value = val;
            });
            markChanged();
        });
    });

    // ── 1.6 Font-Preview Widget ───────────────────────────────────────────────
    (function initFontPreviews() {
        const loaded = new Set();
        const GF = {
            'barlow':'Barlow','inter':'Inter','roboto':'Roboto','open-sans':'Open+Sans',
            'lato':'Lato','montserrat':'Montserrat','poppins':'Poppins',
            'source-sans':'Source+Sans+3','nunito':'Nunito',
            'barlow-condensed':'Barlow+Condensed','roboto-condensed':'Roboto+Condensed',
            'oswald':'Oswald','rajdhani':'Rajdhani','exo2':'Exo+2',
            'jetbrains-mono':'JetBrains+Mono','fira-code':'Fira+Code','source-code':'Source+Code+Pro'
        };
        const CN = {
            'barlow':'Barlow','inter':'Inter','roboto':'Roboto','open-sans':'"Open Sans"',
            'lato':'Lato','montserrat':'Montserrat','poppins':'Poppins',
            'source-sans':'"Source Sans 3"','nunito':'Nunito','system':'system-ui,sans-serif',
            'barlow-condensed':'"Barlow Condensed"','roboto-condensed':'"Roboto Condensed"',
            'oswald':'Oswald','rajdhani':'Rajdhani','exo2':'"Exo 2"',
            'jetbrains-mono':'"JetBrains Mono",monospace','fira-code':'"Fira Code",monospace',
            'source-code':'"Source Code Pro",monospace','cascadia':'"Cascadia Code",monospace',
            'system-mono':'monospace'
        };
        function loadFont(slug) {
            if (!GF[slug] || loaded.has(slug)) return;
            loaded.add(slug);
            const l = document.createElement('link');
            l.rel  = 'stylesheet';
            l.href = 'https://fonts.googleapis.com/css2?family=' + GF[slug] + ':wght@400;700&display=swap';
            document.head.appendChild(l);
        }
        ['f_typography_font_family_ui','f_typography_font_family_brand','f_typography_font_family_code'].forEach(fid => {
            const sel = document.getElementById(fid);
            if (!sel) return;
            const prev = document.createElement('div');
            prev.style.cssText = 'margin-top:.4rem;padding:.4rem .7rem;background:#f8fafc;border:1px solid #e2e8f0;border-radius:4px;font-size:1rem;color:#1e293b;';
            prev.textContent   = 'AaBbCc 0123 – PowerShell & M365 Administration';
            sel.after(prev);
            (function update(v) { loadFont(v); prev.style.fontFamily = CN[v] || 'inherit'; })(sel.value);
            sel.addEventListener('change', e => {
                loadFont(e.target.value);
                prev.style.fontFamily = CN[e.target.value] || 'inherit';
            });
        });
    })();

})();
</script>
