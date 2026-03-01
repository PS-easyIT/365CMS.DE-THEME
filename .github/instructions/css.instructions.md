---
applyTo: "*/style.css,*/css/*.css"
---

# 365CMS Theme – CSS-Richtlinien

## style.css – Pflicht-Header

```css
/*
Theme Name: Mein Theme
Description: Kurze Beschreibung
Version: 1.0.0
Author: Euer Name
Author URI: https://example.com
*/
```

## CSS Custom Properties (Design Tokens)

```css
:root {
    /* Brand Colors */
    --primary-color:    #1e3a5f;
    --primary-dark:     #0f2240;
    --primary-light:    #2563eb;
    --accent-color:     #e8a838;
    --secondary-color:  #64748b;

    /* Text */
    --text-primary:     #1e293b;
    --text-secondary:   #64748b;
    --text-light:       #94a3b8;

    /* Backgrounds */
    --bg-primary:       #ffffff;
    --bg-secondary:     #f1f5f9;
    --bg-tertiary:      #e2e8f0;

    /* Borders */
    --border-color:     #e2e8f0;

    /* Shadows – zurückhaltend, kein Neon-Glow */
    --shadow-sm:    0 1px 3px 0 rgb(0 0 0 / 0.07);
    --shadow-md:    0 4px 12px -1px rgb(0 0 0 / 0.12);

    /* Radius – NICHT 50px (pill-shape vermeiden) */
    --border-radius:    8px;
    --border-radius-lg: 16px;

    /* Transitions */
    --transition:       all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-fast:  all 0.15s ease;

    /* Layout */
    --container-width:  1280px;

    /* Z-Index Scale */
    --z-header:      1000;
    --z-mobile-menu: 1100;
    --z-overlay:     1200;
    --z-modal:       1300;
}
```

## Schriftfamilie

```css
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}
```

Typografie fluid skalieren via `clamp()`:
```css
h1 { font-size: clamp(1.5rem, 3vw, 2rem);    font-weight: 700; }
h2 { font-size: clamp(1.25rem, 2.5vw, 1.5rem); font-weight: 700; }
h3 { font-size: 1.125rem;                     font-weight: 700; }
```

## VERBOTEN: Typischer AI-Look

```css
/* ❌ NICHT verwenden */
backdrop-filter: blur(20px);               /* Glassmorphismus */
background: linear-gradient(135deg, ...);  /* überall Gradient */
border-radius: 50px;                       /* Pill-shape für alles */
box-shadow: 0 25px 50px rgba(0,0,0,0.4);  /* übertriebene Schatten */
color: #00f5ff;                            /* Neon-Farben */
animation: float 3s ease-in-out infinite;  /* schwebende Elemente */
```

## BEVORZUGT: Menschlich, editorial, klar

```css
/* ✅ RICHTIG */
background: #ffffff;
border: 1px solid #e2e8f0;
border-radius: 8px;
box-shadow: 0 1px 3px rgba(0,0,0,.05), 0 4px 12px rgba(0,0,0,.06);
color: #1e293b;
transition: box-shadow .2s, transform .15s;
```

## Dark Mode

```css
body.dark-mode {
    --bg-primary:    #1e293b;
    --bg-secondary:  #0f172a;
    --text-primary:  #f1f5f9;
    --border-color:  #334155;
}
```

## Responsive Breakpoints

| Breakpoint | Breite |
|---|---|
| Desktop | `> 1024px` |
| Tablet | `≤ 1024px` |
| Mobile | `≤ 768px` |
| Small Mobile | `≤ 480px` |

**Mobile-First** CSS bevorzugen. Grids mit `auto-fit`/`auto-fill` + `minmax()`.

## Inline-Styles

- **Verboten:** Statische Layout-Werte (`margin`, `display`, `font-size`, etc.)
- **Erlaubt:** Dynamische Werte per CSS-Variable auf dem Element setzen:
  ```php
  <div class="card" style="--card-accent: <?= htmlspecialchars($color) ?>;">
  ```
