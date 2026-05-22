# Changelog

## 1.0.2 - 2026-05-19 Nachtrag

### Abschlussdokumentation v3/PHP 8.4

- `functions.php`, alle Basis-Templates, `style.css`, `js/navigation.js`, `theme.json`, `update.json` und README sind als finaler Logistik-Hardening-Stand dokumentiert.
- Nachgetragen sind die operativen Detailänderungen an Mobile-Drawer, Search-Panel, Focus-Restore, Tracking-Quick-Search, Status-System, Customizer-Mapping und Asset-Pipeline.
- Der Update-Feed trägt das Nachtragsdatum vom 19.05.2026, ohne die Version `1.0.2` erneut zu erhöhen.

## 1.0.2 - 2026-05-18

### v3 / PHP 8.4 / Hardening
- `LOGILINK_THEME_VERSION` als Theme-Konstante eingeführt; `theme.json`,
  `style.css`, `update.json` und `functions.php` synchronisiert.
- `style.css`-Header um `Requires PHP: 8.4`, `License`, `Text Domain` ergänzt.
- `enqueueStyles()` (`head:1`) und `enqueueScripts()` (`before_footer:99`)
  ersetzen das hartcodierte `<link rel="stylesheet">` und das alte
  `outputNavigationScript()` – beides versioniert via `?v=<theme-version>`,
  Stylesheet zusätzlich als `<link rel="preload" as="style">`.
- `outputMetaTags()` schreibt Description, OG-URL, Theme-Color und Robots.
- `outputCustomStyles()` mappt jetzt jeden Customizer-Schlüssel aus
  `theme.json`: Farben, Status-System, Typografie (size/line-height/weight),
  Layout (container/padding/radius/section-spacing/kpi_columns),
  Header (bg/text/height/logo-height), Footer (bg/text/link), Buttons
  (radius/padding/font-weight/transform). Booleans (sticky header, dashboard
  mode) gehen durch `filter_var(..., FILTER_VALIDATE_BOOLEAN)`.
- `mapFontChoice()` erweitert auf alle in `theme.json` deklarierten
  Familien – `Inter`, `Roboto`, `Open Sans`, `Lato`, `Roboto Condensed`,
  `Montserrat`, `Barlow Condensed`, `JetBrains Mono`, `Roboto Mono`,
  `Fira Code`, plus System-Monospace; Fallback-Ketten je `body`/`heading`/`mono`-Slot.
- Google-Fonts-URL lädt nur tatsächlich gewählte Familien (System/Mono-only
  Auswahl überspringt das `<link>`-Tag komplett), `preconnect`-Hints sind
  HTML-escaped.
- `custom_css` wird über `sanitizeCustomCss()` gegen `</style>`-Injection
  defensiv gefiltert.
- `cms_init` + `init` Doppelregistrierung der Menü-Locations bleibt erhalten;
  zusätzlich `register_menu_locations`-Filter.

### Helper-Set
- Theme-lokales Helper-Set hinzugefügt, parallel zum `medcarepro`-Vorbild:
  `theme_is_logged_in`, `theme_route_url`, `theme_nav_menu`, `get_header`,
  `get_footer`, `ll_get_setting`, `ll_get_flash`, `ll_set_flash`, `ll_href`,
  `ll_site_url`, `ll_site_title`, `ll_body_class`, `ll_safe_headline`.
- `ll_body_class()` rendert Sticky-Header- und Dashboard-Mode-Klassen
  serverseitig (kein JS-Class-Mutation).

### Templates
- `header.php`: hartcodierter Stylesheet entfernt; Logo mit
  `focusable="false"`/`aria-hidden="true"` SVG-Mark, `ll-focus-shadow`
  Fokus-Utility; Tracking-Quick-Search trägt `data-no-result` aus
  `logistics_tracking.tracking_no_result_text`; neuer Mobile-Drawer und
  Search-Panel mit `aria-controls`/`aria-expanded`/`aria-hidden`.
- `footer.php`: nutzt `gmdate('Y')`-Template `{year}` / `{site_title}`,
  alle URLs durch `theme_route_url()` aufgelöst, `theme_nav_menu('footer-nav')`
  und `theme_nav_menu('footer-legal')` eingebunden, keine Inline-Styles mehr.
- `home.php`: vollständig neu strukturiert. Hero mit Route-Linien-Visual
  (Origin/Hub/Destination, Status-Stoppunkte), Status-Stepper über alle
  sechs Stufen, Demo-Shipment-Cards mit links-anchored Status-Indikator,
  KPI-Cards mit Monospace-Zahlen und Delta-Indikatoren, Service-Grid mit
  SVG-Icons, Capacity-Booking-Skizze, Partner-Row, Registrierungs-CTA.
  Jede in `theme.json/logistics_content` deklarierte Customizer-Variable
  wird tatsächlich gerendert (`services_section_title`,
  `capacity_booking_title`, `capacity_units_label`,
  `kpi_punctuality_label`, `kpi_efficiency_label`, `kpi_damage_label`,
  `network_section_title`, `cta_section_title`, `cta_section_text`).
- `index.php`, `page.php`, `404.php`, `error.php`: alle Inline-`style=""`-
  Attribute entfernt, durch CSS-Klassen ersetzt; URLs durchgehend
  `htmlspecialchars`-escaped; `theme_route_url()` für Home/Tracking-Links.

### CSS
- Komplette Neuschreibung von `style.css` mit `--ll-*`-Token-System.
  Status-System mit WCAG-AA-tauglichen Fg/Bg-Paaren: `warehouse #475569 /
  #e2e8f0`, `picked #b45309 / #fef3c7`, `transit #1d4ed8 / #dbeafe`,
  `delivered #15803d / #dcfce7`, `delayed #b91c1c / #fee2e2`,
  `returned #6d28d9 / #ede9fe`.
- Eyebrow-Kicker in Monospace-Caps (Route-Nummern, Sektions-Labels) statt
  generischer Pills.
- Tracking-ID-Klasse `.ll-tracking-id` als operatives Code-Element.
- Shipment-Card-Pattern mit links-anchored Status-Stripe.
- KPI-Cards mit Monospace tnum/lnum + Delta-Pfeilen.
- Sicht-Reveal über `.ll-reveal` + `.is-visible`.
- Fokus-Token `--focus-ring`, Mouse/Keyboard-Fokus getrennt
  (`:focus:not(:focus-visible)` neutralisiert + sichtbarer
  `:focus-visible`-Ring + `.ll-focus-shadow`-Utility).
- Responsive Brüche bei 480 / 768 / 1024 Pixeln; Mobile-Drawer ersetzt
  das alte Inline-`.open`-Menü.

### JS
- `js/navigation.js` neu geschrieben nach dem `cms-newspaper`-Pattern:
  Sticky-State, Mobile-Drawer mit `aria-hidden`/`aria-expanded`/
  Outside-Click/Escape/Focus-Restore/Scroll-Lock, separates Search-Panel
  mit Focus-Restore zum Opener, reveal nur bei
  `!prefers-reduced-motion && 'IntersectionObserver' in window`, sonst
  unmittelbares `is-visible`-Fallback.

### Dokumentation
- `README.md`: stark erweitert, beschreibt vollständiges Designprofil,
  Customizer-Mapping, Helper-Set, Asset-Pipeline und Sicherheits-/A11y-
  Praktiken.
- `update.json`: auf `1.0.2`, neuer Changelog-Eintrag, `min_cms_version`
  auf `3.0.0` aktualisiert.

## 1.0.1 - 2026-05-17

- Added `cms_init` menu registration fallback for 365CMS v3 bootstrap flows.
- Fixed multiple Customizer key mismatches between `theme.json` and runtime
  mapping.
- Added font choice mapping for base, heading, and data fonts.
- Corrected status color mapping to `colors.status_*` tokens.
- Hardened URL/button handling in header and home templates.
- Replaced inline layout styles with dedicated CSS classes.
- Added keyboard-visible focus styling and focus ring token.
- Added reduced-motion guard for reveal animations.
- Added theme documentation (`README.md` and this changelog).

## 1.0.0 - 2026-02-21

- Erstveröffentlichung: Logistik & Netzwerk-Theme.
