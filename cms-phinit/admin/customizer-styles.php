<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}
?>
<style>
    .phinit-customizer__color-group { max-width: 260px; }
    .phinit-customizer__color-input { max-width: 52px; padding: 2px; }
    .phinit-customizer__color-text { max-width: 130px; }
    .phinit-customizer__number-input { max-width: 140px; }
    .phinit-customizer__sticky-card { top: 1rem; }
    .phinit-customizer__nav-label { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #94a3b8; background: #f8fafc; border: none; }
    .phinit-customizer__nav-link { font-size: .875rem; }
    .phinit-customizer__export-title { font-size: .85rem; }
    .phinit-customizer__unsaved-hint { display: none; font-size: .85rem; }
    .phinit-customizer__shortcut-hint { font-size: .8rem; }
    .phinit-customizer__preset-card { background: #1e293b; border-color: #334155; }
    .phinit-customizer__preset-label { font-size: .8rem; font-weight: 600; color: #94a3b8; }
    .phinit-customizer__preset-btn--phinit { background: #1e3a5f; color: #e8a838; border-color: #2a4f7c; }
    .phinit-customizer__preset-btn--bluesteel { background: #1a2744; color: #60a5fa; border-color: #233b6e; }
    .phinit-customizer__preset-btn--greentech { background: #064e3b; color: #10b981; border-color: #047857; }
    .phinit-customizer__preset-btn--slate { background: #1e293b; color: #f59e0b; border-color: #334155; }
    .phinit-customizer__preset-btn--ruby { background: #7f1d1d; color: #f87171; border-color: #991b1b; }
    .phinit-customizer__preset-note { color: #64748b; }
    .phinit-customizer__menu-icon { font-size: 1.5rem; }
    .phinit-customizer__drawer { display: none; position: fixed; top: 0; right: 0; bottom: 0; z-index: 9050; width: min(900px, 96vw); flex-direction: column; background: #111827; box-shadow: -6px 0 32px rgba(0,0,0,.6); }
    .phinit-customizer__drawer-toolbar { display: flex; align-items: center; gap: .5rem; padding: .5rem .875rem; background: #0d1528; border-bottom: 1px solid #1e3a5f; flex-shrink: 0; }
    .phinit-customizer__toolbar-btn, .phinit-customizer__toolbar-link { background: none; border: 1px solid #334155; color: #94a3b8; border-radius: 4px; padding: .2rem .6rem; cursor: pointer; font-size: .85rem; text-decoration: none; }
    .phinit-customizer__device-switcher { display: flex; gap: .375rem; margin: 0 auto; }
    .phinit-customizer__device-btn { background: none; border: 1px solid #334155; color: #94a3b8; border-radius: 4px; padding: .25rem .65rem; cursor: pointer; font-size: .8rem; }
    .phinit-customizer__device-btn.active { background: #1e293b; color: #e2e8f0; }
    .phinit-customizer__drawer-body { flex: 1; overflow: auto; display: flex; justify-content: center; align-items: flex-start; background: #475569; padding: 4px; }
    .phinit-customizer__iframe { background: #fff; border: none; border-radius: 2px; height: calc(100vh - 60px); width: 1280px; max-width: 100%; transition: width .25s ease; }
    .phinit-customizer__drawer-label { padding: .25rem .875rem; background: #0d1528; font-size: .7rem; color: #475569; text-align: center; }
</style>
