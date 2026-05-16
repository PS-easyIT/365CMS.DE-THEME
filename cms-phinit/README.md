# CMS Phinit

Professionelles IT-Blog-Theme für `365CMS` mit dunklem Navy-Design, Gold-Akzenten, Sticky-Header, Dark Mode, TOC-Sidebar und Member-Bereich.

## Aktueller Stand

- Version: `1.5.47`
- Letzter Security-Stand: **PHP-8.4-Theme-Audit + Syntaxprüfung ohne Fehler** am `14.05.2026`
- Kompatibilität laut Manifest: `requires_cms: 2.5.0`

## Enthaltene Kernbereiche

- Blog- und Archiv-Templates (`index.php`, `blog.php`, `category.php`, `tag.php`)
- Seiten-Templates inkl. `page.php`, `page-wide.php`, `page-landing.php`
- Post-Templates inkl. Standard-, Wide- und Tech-Variante
- Theme-Customizer mit Import/Export und Preview-Drawer
- Header-Optionen für Suche, Quicklinks, Member-Bar und ausblendbaren Login-/Account-Button
- Member-Bereich mit Dashboard, Profil, Favoriten, Sicherheit und Feeds
- locale-aware Seitenauflösung für EN-Custom-Slugs inkl. page-/landing-/special-page-CSS
- JSON-LD-Breadcrumbs für Artikel und Seiten ohne sichtbare Public-Breadcrumb-Leiste
- kompakter Seiten-/Beitragsstart mit 25px Abstand nach dem sticky Header; HubSites starten bewusst mit 0px Abstand, weil ihr eigenes Markup den Abstand mitbringt
- entlasteter Mobile-Head-Pfad mit inline Theme-Init, asynchronem UI-/Card-CSS auf Home-/Blog-Listings und intrinsischen Header-Logo-Dimensionen
- Startseite rendert oberhalb der Falz jetzt über ein eigenes `homepage-blog-critical.css`; das große Home-Stylesheet wird dort erst nach dem First Paint asynchron nachgeladen
- Das Sidebar-Widget `Empfohlene Artikel` nutzt im Rotator jetzt eingebettete Links/Rechts-Pfeile statt unterer Pagination-Dots
- Das Sidebar-Widget `Empfohlene Artikel` wurde zusätzlich um 35px erhöht; die Pfeilbuttons liegen jetzt stabil über dem Slide-Overlay und reagieren zuverlässig auf Klicks
- Normale Blog-Listings laden kein HubSite-CSS mehr, und die dekorativen Sidebar-Thumbnails im Widget `Empfohlene Artikel` vermeiden redundante Alt-Texte im selben Link
- Der mobile Startpfad überspringt Desktop-Dropdown-Logik, vermeidet den initialen Sticky-Header-Layoutread und nutzt kleinere responsive Desktop-Kandidaten für lazy geladene Artikelbilder
- Beitrags-Vorschaubilder in der Startseiten-Artikelliste haben jetzt einen stabilen Skeleton-Placeholder, laden die ersten zwei sichtnahen Karten eager und preladen das Homepage-Lead-Bild bevorzugt als AVIF/WebP ohne unnötige Format-Dopplung
- Rotierende Featured-/Sidebar-Bilder priorisieren nur den sichtbaren aktiven Slide; versteckte Slides laufen mit niedriger Fetch-Priority im Hintergrund an, damit sie dem LCP-Bild keine Bandbreite wegnehmen und beim Rotieren nicht leer erscheinen
- Nicht-Bild-Performance: Scroll-Fortschritt vermeidet Layoutreads pro Scroll, Homepage-Rotatoren pausieren über Page-Lifecycle/BFCache-Hooks, Google Analytics lädt externes Third-Party-JS erst nach Load/Idle und dekorative Daueranimationen wurden entschärft
- bereinigter Header-Active-State ohne `Undefined variable $_currentLocale`-Warning im Public-Frontend
- locale-aware Homepage-Lead-Image-Abfrage ohne Alias-Mix im SQL-Filter
- verwaltete Uploads/Featured Images werden im Public-Frontend über die `/media-file`-Delivery-Route normalisiert, damit ersetzte Bilder nicht an direkten `/uploads`-403 scheitern

## Sicherheit & Audit

Die wichtigsten Security-Härtungen der letzten Runde:

- dedizierter Purifier-Renderpfad für Seiten- und Landing-Content
- HubSite-kompatibles Sanitizer-Profil im Core (`hub`)
- kontrollierter Temp-Staging-Flow für Customizer-Importe
- Public-URL-/Media-Allowlist in Frontend- und Member-Pfaden
- Post-/Hub-Content wird ausschließlich über den zentralen Purifier-Renderpfad ausgegeben
- arraysichere Request-Helper für Query-, Formular-, Favoriten- und Member-Pfade
- Customizer-Feldattribute sind direkt escaped und Autorenübersichten vermeiden N+1-Profilabrufe
- Server-Request-, Host-, Cookie-Consent-, Notification- und 404-Post-Logik läuft über zentrale Theme-Helper
- PHP-8.4-Customizer-Config-Snapshot nutzt Property Hooks und `public private(set)`, damit Runtime-Konfigurationen normalisiert lesbar, aber nicht versehentlich überschreibbar sind
- Customizer-URLs und CSRF-Hidden-Fields nutzen explizites `ENT_QUOTES`-/`UTF-8`-Escaping plus `rawurlencode()` für Tab-Parameter
- Dashboard-URLs, Header-Textfragmente und Bildarchiv-Beschreibungen werden direkt am Ausgabesink escaped bzw. erneut über den zentralen Sanitizer-Renderer geführt
- zusätzliche PHP-8.4-Array-Helper-Nutzung: Homepage-Sidebar erkennt Custom-Featured-Images per `array_any()` statt manueller Boolean-Suchschleife

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