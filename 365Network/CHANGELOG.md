# Changelog

## 3.4.14 - 2026-05-17

Re-Audit-Pass für 365Network (v3.x.x / PHP 8.4):

- **SQL-Härtung:** `experts.php`, `companies.php`, `events.php`, `jobs.php`, `speakers.php`, `feeds.php` und `booking.php` binden `LIMIT`/`OFFSET` jetzt als Parameter (`LIMIT ? OFFSET ?`) statt sie zu interpolieren. Damit folgen die Directory-Templates demselben Muster wie die bereits geprüften Sidebar-Widgets.
- **Robustheit:** Count-Row-Zugriffe (`$cRow->cnt`) sind in den Directory-Templates auf den Array-/Objekt-sicheren Fallback `is_array($cRow) ? ($cRow['cnt'] ?? 0) : ($cRow->cnt ?? 0)` umgestellt — kein versehentlicher Property-Zugriff auf `false` mehr.
- **Markup-Fix:** `blog-single.php` Related-Posts-Bild hatte ein gebrochenes `<img>`-Tag (Attribute leakten ins Folge-Markup). Repariert.
- **Design / Anti-KI-Slop:** `.homepage-cta--gradient` ersetzt den generischen Navy→Royal-Blue-Verlauf durch ein theme-identisches Navy → Primary-Light → Gold mit dezentem Gold-Glow auf der rechten Seite. Customizer-Label in `theme.json` und `admin/customizer.php` von „Gradient (Navy → Blau)" auf „Gradient (Navy → Gold)" angepasst.
- **Versionierung:** `THEME_VERSION` (functions.php), `style.css`, `theme.json` und `update.json` synchron auf 3.4.14 angehoben.

## 3.4.13 - 2026-05-17

- Cache-Busting: `THEME_VERSION` in `functions.php` auf den Stand von `style.css` und `theme.json` angeglichen.

## 3.0.0 - 2026-03-29

- Sicherer Setting-Zugriff über interne Helper, Local-Fonts-Override.
- Feed-/Blog-Sidebar und Homepage-`LIMIT` als prepared Statements.
- Stylesheet-Preload und Fonts-Preconnect für besseres LCP.
- `:focus-visible`-Pattern mit expliziten Focus-Tokens.
- `Requires PHP: 8.4` im Stylesheet deklariert.
