# PTC Theme – Changelog

## 1.1.0 (2025-06)

### Neue Features
- **Services erweitert:** Bis zu 12 Service-Karten (vorher 8)
- **Icons deaktivierbar:** Checkbox `services_show_icons` blendet Icons auf Service-Karten aus
- **MS Booking Integration:** Iframe-Embed für Microsoft Booking im Termine-Bereich
- **Network Bar:** Netzwerk-Leiste unter dem Footer (PHIN IT, 365CMS, 365 Network + Name)
- **CTA-Farben:** Eigene Farbeinstellungen für die CTA-Sektion (Gradient + Textfarbe)
- **Seitenrand-Farben:** Konfigurierbare Hintergrundfarbe und Akzentstreifen an Seitenrändern
- **Customizer-Navigation:** Startseiten-Tabs (Hero, Services, Events, FAQ) gruppiert unter „🏠 Startseite"

### Bugfixes
- **Logo-Upload Bug:** Beim Speichern des Customizers wurde ein hochgeladenes Logo durch den alten POST-Wert überschrieben
- **CSS-Variablen:** Header-Höhe und Logo-Höhe verwendeten hardcodierte Werte statt CSS-Custom-Properties
- **Sticky Footer:** Footer schwebt nicht mehr in der Seitenmitte bei kurzem Content

### CSS-Verbesserungen
- Sticky Footer via Flexbox (`.ptc-site`)
- `.ptc-header-inner` nutzt `var(--ptc-header-height, 72px)`
- `.ptc-logo-img` nutzt `var(--ptc-logo-height, 40px)`
- `.ptc-content` nutzt `var(--ptc-header-height)` für padding-top
- CTA-Sektion (``.ptc-cta-section``) nutzt CSS-Variablen für Gradient und Textfarbe
- Neue Button-Klasse `.btn-ptc-outline`
- Service-Card Stilvarianten: shadow, flat, filled (via `data-card-style`)
- Dekorative Seitenrand-Akzentlinien (via `body::before`/`::after`)
- Network Bar responsive (Stack bei ≤768px)
- MS Booking Iframe responsive (`max-width: 100%`)

## 1.0.0 (2025-03)

### Erstveröffentlichung
- Corporate Theme für PTC GmbH Personaldienstleistungen
- Marineblau + Gold Corporate Identity
- Sektionen: Hero, Dienstleistungen (6 Karten), Termine, FAQ, Kontakt-CTA
- Vollständiger Theme-Customizer mit 10 Tabs
- Responsive Design (Mobile, Tablet, Desktop)
- Dark-Mode-Unterstützung
- Sticky Header mit Scroll-Effekt
- Plugin-Integration (cms-events)
