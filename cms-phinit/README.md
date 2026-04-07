# CMS Phinit

Professionelles IT-Blog-Theme für `365CMS` mit dunklem Navy-Design, Gold-Akzenten, Sticky-Header, Dark Mode, TOC-Sidebar und Member-Bereich.

## Aktueller Stand

- Version: `1.5.21`
- Letzter Security-Stand: **Snyk Code Scan mit 0 aktiven Findings** am `04.04.2026`
- Kompatibilität laut Manifest: `requires_cms: 2.5.0`

## Enthaltene Kernbereiche

- Blog- und Archiv-Templates (`index.php`, `blog.php`, `category.php`, `tag.php`)
- Seiten-Templates inkl. `page.php`, `page-wide.php`, `page-landing.php`
- Post-Templates inkl. Standard-, Wide- und Tech-Variante
- Theme-Customizer mit Import/Export und Preview-Drawer
- Header-Optionen für Suche, Quicklinks, Member-Bar und ausblendbaren Login-/Account-Button
- Member-Bereich mit Dashboard, Profil, Favoriten, Sicherheit und Feeds

## Sicherheit & Audit

Die wichtigsten Security-Härtungen der letzten Runde:

- dedizierter Purifier-Renderpfad für Seiten- und Landing-Content
- HubSite-kompatibles Sanitizer-Profil im Core (`hub`)
- kontrollierter Temp-Staging-Flow für Customizer-Importe
- Public-URL-/Media-Allowlist in Frontend- und Member-Pfaden

Ausführliche Details stehen in:

- `DOC/THEME-AUDIT.md`
- `CHANGELOG.md`
- `DOC/TEMPLATES.md`

## Wichtige Dateien

- `functions.php` — Theme-Bootstrap und Hook-Registrierung
- `theme.json` — Metadaten, Templates und Customizer-Schema
- `update.json` — Release-/Update-Metadaten
- `admin/customizer.php` — Einstieg für den Theme-Customizer
- `includes/` — zentrale Helper und Traits

## Hinweis zur Doku

Dieses `README.md` ist die kurze Einstiegsversion. Die vollständige technische Audit- und Template-Dokumentation liegt bewusst in `DOC/`.