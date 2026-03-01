---
applyTo: "*/js/*.js"
---

# 365CMS Theme – JavaScript-Richtlinien

## Grundregeln

- **Kein jQuery** – ausschließlich Vanilla JavaScript (ES2020+)
- Alle Module in IIFE gekapselt: `(function(){...})()`
- Initialisierung nach `DOMContentLoaded`
- `<script defer>` für nicht-kritische Scripts
- Passive Event-Listener für Scroll-Events
- Keine externen JS-Bibliotheken laden (außer bereits im Projekt vorhandene)

## navigation.js – Standard-Module

Jedes Theme sollte diese Module in `js/navigation.js` bereitstellen:

### initStickyHeader()
`.scrolled` Klasse auf `#site-header` bei `scrollY > 60`.

### initBurgerMenu()
Mobile-Menü über `#burger-toggle` + `#mobile-menu` steuern:
- Click-Toggle mit `aria-expanded` + `aria-hidden`
- Click außerhalb → schließen
- `Escape` → schließen + Fokus auf Toggle zurück
- `body.mobile-menu-open` → `overflow: hidden`

### initDarkMode()
1. `localStorage.getItem('cms365-theme')` prüfen
2. Falls nicht gesetzt: `prefers-color-scheme: dark` prüfen
3. `.dark-mode` Klasse auf `body` setzen/entfernen
4. Storage-Key: `cms365-theme` (Werte: `'dark'` | `'light'`)

### initBackToTop()
`#back-to-top` Button bei `scrollY > 400` mit CSS-Klasse `.visible`.

### initScrollAnimations()
`IntersectionObserver` für `[data-anim]` Elemente:
- Threshold: `0.12`
- `.is-visible` Klasse bei Sichtbarkeit
- `unobserve()` nach erstem Trigger

### initActiveNav()
`.active` Klasse auf aktuellen Nav-Link basierend auf `window.location.pathname`.

### initFlashMessages()
Auto-dismiss für Elemente mit `data-auto-dismiss` Attribut (Wert = Delay in ms).

## AJAX / Fetch

```javascript
async function loadData(url) {
    try {
        const res = await fetch(url);
        const data = await res.json();
        if (data.success) {
            // Verarbeitung
        }
    } catch (e) {
        console.error('Netzwerkfehler:', e.message);
    }
}
```

## Keine Bestätigungsdialoge mit window.confirm()

Stattdessen eigenes Modal verwenden.

## Custom Events (geplant)

```javascript
document.addEventListener('cms365:menuOpen', function(e) { ... });
document.addEventListener('cms365:themeChange', function(e) {
    console.log(e.detail.isDark);
});
```
