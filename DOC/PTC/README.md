# PTC GmbH – Corporate Theme

> **Slug:** `PTC`  
> **Version:** 1.1.0  
> **Autor:** PHIN IT Solutions / Andreas Hepp

Corporate Theme für Personaldienstleistungen – PTC GmbH.  
Marineblau + Gold CI. Fokus auf Seriosität, Klarheit und Vertrauen.

---

## Dateien

| Datei | Zweck |
|---|---|
| `theme.json` | Metadaten, Customizer-Definitionen |
| `update.json` | Versionsinformation für Auto-Updates |
| `style.css` | Haupt-CSS mit PTC Corporate Identity |
| `functions.php` | Theme-Klasse `PTCTheme`, Hooks, CSS-Variablen |
| `header.php` | Header mit Logo, Navigation, Burger-Menü |
| `footer.php` | Footer mit Kontakt, Social, Copyright, Network Bar |
| `home.php` | Homepage: Hero → Dienstleistungen → Termine → FAQ → CTA |
| `page.php` | Einzelseiten-Template |
| `login.php` / `register.php` | Auth-Templates |
| `search.php` | Suchseite |
| `404.php` / `error.php` | Fehlerseiten |
| `admin/customizer.php` | Theme-Customizer (Admin-Bereich) |
| `js/navigation.js` | Burger-Menü, Sticky Header, Dark Mode, Scroll-Animationen |

---

## Sektionen (Homepage)

1. **Hero** – Badge, Überschrift, Text, 2 CTA-Buttons, Hero-Bild
2. **Dienstleistungen** – Bis zu 12 Service-Karten (Icon + Titel + Text + Link)
3. **Termine** – Automatisch (cms-events Plugin) oder manuell (bis zu 6 Einträge) + MS Booking Embed
4. **FAQ** – Bis zu 8 Frage-Antwort-Paare im Accordion
5. **CTA** – Call-to-Action mit Navy-Gold Gradient, 2 Buttons

---

## Customizer-Tabs

| Tab | Inhalt |
|---|---|
| 🎨 Farben | Markenfarben, Text, Hintergrund, CTA-Sektion, Seitenränder |
| 🔤 Typografie | Schriftarten, Größe, Zeilenhöhe, Gewichtung |
| 📐 Layout | Container, Padding, Radius, Sektionsabstand |
| 🖼️ Header & Logo | Logo-Bild/Text, Farben, Höhe, CTA-Button, Login/Register |
| 🏠 Startseite | Gruppiert: Hero & CTA, Dienstleistungen, Termine, FAQ |
| 🔻 Footer | Farben, Kontaktdaten, Social Media, Network Bar |
| 🔘 Buttons | Radius, Padding, Schriftgewicht, Transform |
| 🔧 Erweitert | Custom CSS, Custom Head/Body Scripts |

---

## CSS-Variablen (Custom Properties)

Alle Variablen werden über `functions.php → outputCustomStyles()` in `:root` gesetzt.

### Farben
- `--ptc-navy`, `--ptc-navy-dark`, `--ptc-navy-light`
- `--ptc-gold`, `--ptc-gold-dark`, `--ptc-gold-light`
- `--ptc-slate`, `--ptc-text`, `--ptc-heading`, `--ptc-white`, `--ptc-muted`
- `--ptc-bg`, `--ptc-bg-alt`, `--ptc-link`, `--ptc-link-hover`
- `--ptc-border`, `--ptc-success`, `--ptc-error`

### CTA & Seitenränder
- `--ptc-cta-bg`, `--ptc-cta-bg-to`, `--ptc-cta-text`
- `--ptc-side-color`, `--ptc-side-accent`, `--ptc-side-width`

### Layout
- `--ptc-container-width`, `--ptc-content-padding`, `--ptc-radius`, `--ptc-section-spacing`
- `--ptc-header-height`, `--ptc-logo-height`
- `--ptc-header-bg`, `--ptc-header-text`, `--ptc-header-accent`
- `--ptc-footer-bg`, `--ptc-footer-text`, `--ptc-footer-link`
- `--ptc-btn-radius`, `--ptc-btn-px`, `--ptc-btn-py`

---

## Hooks

### Registriert in `functions.php`
| Hook | Priorität | Methode |
|---|---|---|
| `head` | 10 | `enqueueStyles()` – CSS einbinden |
| `head` | 20 | `outputCustomStyles()` – CSS-Variablen |
| `head` | 25 | `outputCustomHeadScripts()` – Head-Scripts |
| `before_footer` | 10 | `enqueueScripts()` – JS einbinden |
| `body_end` | 10 | `outputCustomBodyScripts()` – Body-Scripts |

### Verfügbar in Templates
| Hook | Ort |
|---|---|
| `home_before_hero` | Vor Hero-Sektion |
| `home_after_hero` | Nach Hero |
| `home_after_services` | Nach Dienstleistungen |
| `home_after_events` | Nach Termine |
| `home_after_faq` | Nach FAQ |
| `home_content` | Nach CTA (Ende der Homepage) |

---

## Network Bar

Dünne Leiste unterhalb des Footers mit Links zum Netzwerk (PHIN IT, 365CMS, 365 Network).  
Konfigurierbar über Footer-Tab → Network Bar:

| Setting | Default |
|---|---|
| `show_network_bar` | `true` |
| `network_bar_name` | `Andreas Hepp` |
| `network_bar_link1_label` / `_url` | `PHIN IT` / `https://phinit.de` |
| `network_bar_link2_label` / `_url` | `365CMS` / `https://365cms.de` |
| `network_bar_link3_label` / `_url` | `365 Network` / `https://365network.de` |

---

## MS Booking Integration

Im Termine-Tab des Customizers:
- **events_booking_url** – Microsoft Booking Embed-URL
- **events_booking_title** – Überschrift über dem Iframe (default: „Online-Termin buchen")
- **events_booking_height** – Iframe-Höhe in px (default: 600)

Das Iframe wird nach den Event-Karten und dem CTA-Button eingebettet.  
Leer = keine Anzeige.

---

## Changelog

### 1.1.0
- **Customizer:** Services auf 12 Karten erweitert, Icons deaktivierbar
- **Customizer:** MS Booking Integration (Iframe-Embed)
- **Customizer:** Network Bar (Footer, 3 konfigurierbare Links)
- **Customizer:** CTA-Farben und Seitenrand-Farben in Farb-Tab
- **Customizer:** Navigation gruppiert (Startseite → Hero, Services, Events, FAQ)
- **CSS:** Sticky Footer (Flexbox)
- **CSS:** Header-Höhe und Logo-Höhe nutzen CSS-Variablen
- **CSS:** Seitenrand-Akzentstreifen (konfigurierbare Breite/Farbe)
- **CSS:** Service-Card Stilvarianten (shadow, flat, filled)
- **Bugfix:** Logo-Upload wurde durch POST-Werte überschrieben

### 1.0.0
- Erstveröffentlichung
