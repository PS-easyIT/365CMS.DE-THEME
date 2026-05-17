# 365Network Theme

Version: 3.4.14
Target: 365CMS v3.x.x, PHP 8.4

## Focus

Professionelles Networking-Dashboard für IT-Experten, Firmen, Events, Speaker, Jobs und Feed-/Buchungs-Module. Deep Navy + Gold sind kein Dekor, sondern leiten das Layout: dunkler Header und Footer als Frame, helles Dashboard-Grid in der Mitte, Gold ausschließlich auf bedeutungsvollen Aktionen (CTAs, aktive Navigation, Statuspunkte, Speaker-Zähler).

## Modernization Highlights

- v3 Bootstrap-konforme Hooks (`init` + `cms_init`), Customizer-Service und ThemeManager überall verwendet.
- Alle Directory-Queries (`experts`, `companies`, `events`, `jobs`, `speakers`, `feeds`, `booking`) nutzen jetzt prepared `LIMIT ? OFFSET ?` statt String-Interpolation.
- COUNT-Row-Zugriffe akzeptieren Array- und Objekt-Form sicher (`is_array($row) ? ['cnt'] : ->cnt`).
- Stylesheet wird im Head als `preload` + `stylesheet` ausgeliefert; Google Fonts laufen über `preconnect`/`dns-prefetch` mit Local-Fonts-Override.
- Customizer-Booleans laufen ausschließlich durch `filter_var(..., FILTER_VALIDATE_BOOLEAN)`.
- Externe URLs (Logo, Social, Feed-Items) werden in `theme_safe_external_url()` validiert; interne Routen über `theme_route_url()`.
- CSS deklariert `--focus-ring-color` / `--focus-ring-shadow` als Tokens; `:focus:not(:focus-visible)` blendet Tastatur-fremde Outlines aus, `:focus-visible` rendert sie sichtbar.
- JS respektiert `prefers-reduced-motion`; das Header-Canvas pausiert außerhalb des Viewports und in versteckten Tabs.
- CTA-Gradient nutzt jetzt das identitätsstiftende Navy → Gold (statt eines generischen Navy → Royal Blue) und vermeidet damit klassisches KI-Slop-Design.
- `style.css` deklariert `Requires PHP: 8.4` im Stylesheet-Header.

## Files

- `theme.json` — Metadaten, Menü-Locations, alle Customizer-Kategorien (Single Source of Truth).
- `functions.php` — Theme-Bootstrap (`IT_Expert_Network_Theme::instance()`), Hooks, Default-Sidebar-Widgets, Routen-/URL-Helper.
- `header.php`, `footer.php` — Dashboard-Frame mit Profil-Dropdown und vier-spaltigem Footer.
- `home.php` — Hook-basierte Startseite (15 Actions + 3 Filter, siehe `DOC/365Network/HOMEPAGE-HOOKS.md`).
- `experts.php`, `companies.php`, `events.php`, `jobs.php`, `speakers.php`, `feeds.php`, `booking.php` — Directory-Templates mit Filter-Sidebar, Toolbar und Pagination.
- `blog.php`, `blog-single.php`, `page.php`, `search.php`, `404.php`, `error.php`, `index.php` — Content-Templates.
- `login.php`, `register.php` — Auth-Templates mit CSRF-Schutz.
- `admin/customizer.php` — eingebetteter Theme-Customizer (9 Tabs).
- `js/navigation.js`, `js/theme.js` — Vanilla-JS Module (kein jQuery, keine Inline-Handler).
- `style.css` — Token-basiertes Design-System; Dark-Mode-Override, High-Contrast und Print-Styles inklusive.

Vollständige Dokumentation: `DOC/365Network/README.md`.
