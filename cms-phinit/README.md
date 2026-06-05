# CMS Phinit

Professionelles IT-Blog-Theme für `365CMS` mit dunklem Navy-Design, Gold-Akzenten, Sticky-Header, Dark Mode, TOC-Sidebar und Member-Bereich.

## Aktueller Stand

- Version: `1.7.12`
- Letzter Security-Stand: **PHP-8.4-Theme-Audit + Syntaxprüfung ohne Fehler** am `14.05.2026`
- Kompatibilität laut Manifest: `requires_cms: 2.5.0`, getestet bis `365CMS 3.3.47`

## Enthaltene Kernbereiche

- Blog- und Archiv-Templates (`index.php`, `blog.php`, `category.php`, `tag.php`)
- Seiten-Templates inkl. `page.php`, `page-wide.php`, `page-landing.php`
- Normale Seiten zeigen im Content-Header unterhalb des Titels eine beitragsähnliche Meta-Zeile mit Datum, Autor und optionaler Lesedauer.
- Die Core-Option für ein seitenspezifisches, eingeklapptes Header-TOC unter dem Titel wird respektiert; PHINIT unterdrückt dabei sein eigenes Customizer-TOC, damit keine doppelten Inhaltsverzeichnisse entstehen.
- Post-Templates inkl. Standard-, Wide-, Tech-, Microsoft-365-, Windows- und PowerShell-Variante
- Theme-Customizer mit Import/Export und Preview-Drawer
- Header-Optionen für Suche, Quicklinks, Member-Bar und ausblendbaren Login-/Account-Button
- Member-Bereich mit Dashboard, Profil, Favoriten, Sicherheit und Feeds
- locale-aware Seitenauflösung für EN-Custom-Slugs inkl. page-/landing-/special-page-CSS
- JSON-LD-Breadcrumbs für Artikel und Seiten ohne sichtbare Public-Breadcrumb-Leiste
- stabilisierte 365CMS-Inhaltsverzeichnis-Anker für EditorJS-/HTML-Überschriften inklusive leerer oder doppelter IDs; TOC-Links und final gerenderte Überschriften stammen aus demselben vorbereiteten HTML, sodass Public-Sprünge nicht durch eine zweite Sanitizer-/ID-Runde ins Leere laufen
- HubSites rendern das bereits vom Core vorbereitete Markup ohne zweite Theme-Sanitizer-Runde, damit sichere Template-Farbvariablen erhalten bleiben und Tabellen-Titel oberhalb eingebetteter Tabellen sichtbar sind; das HubSite-Design nutzt jetzt konsequent PHINIT-/Customizer-Tokens für Hero, Karten, Tabellen, Dark Mode und Sonderprofile.
- Dienstleistungs-HubSite: PHINIT legt einmalig einen Landing-Hub `it-dienstleistungen` mit Service-Kacheln, Kontakt-CTA und `services`-Template-Profil an.
- Die Startseiten-Sidebar kann in der Site-Identity eine konfigurierbare Dienstleistungscard mit Logo/Bild, Text und CTA ausgeben.
- Normale Seiten- und verzögerte Beitrags-Content-Wrapper sind serverseitig als sichtbar markiert, damit Inhalte auch ohne erfolgreiche Scroll-Reveal-Initialisierung lesbar bleiben.
- EditorJS-Bilder und Bild-Text-Blöcke respektieren im PHINIT-Public-Frontend die Core-/Editor-Vorschau-Breiten; Text+Bild nutzt ein stabiles Grid für Desktop-Nebeneinander, mobile Stapelung, `image-right`, echte H4-Überschriften, gespeicherte Skalierungsmodi und die Core-Vertikalausrichtung `top`, `center` oder `bottom`. PHINIT erhält die EditorJS-Strukturklassen auch nach der finalen Sanitizer-Stufe, sodass Live/Public-Ausgaben nicht mehr als Bild-oben/Text-unten ohne Listen-/Absatzformatierung erscheinen.
- EditorJS-Hinweisboxen nutzen im Public-Frontend einen farbigen Titelbalken mit heller Schrift und heller Inhaltsfläche mit dunklem Text; Info ist blau, Warnung gelb/orange, Erfolg grün und Kritisch rot gestaltet. Diese Kontraste bleiben auch im Dark Mode erhalten.
- Text+Bild-Blöcke übernehmen gespeicherte Abstände nach oben und unten über Core-Variablen, `data-spacing-top`/`data-spacing-bottom` und robuste Abstandsklassen wie `editorjs-media-text--spacing-top-30`; dadurch gewinnen PHINIT-Regeln auch gegen das globale EditorJS-Critical-CSS, das normale Nachbarblöcke auf `0px` Startabstand setzt.
- Bereits vom Core vorbereitete EditorJS-Public-HTML-Blöcke werden im PHINIT-Seitenpfad nicht mehr ein zweites Mal normalisiert; dadurch bleiben `.editorjs-media-text`-Strukturen auf Seiten erhalten.
- Vollbreite Seiten (`page-wide`) und Landing-Seiten (`page-landing`) verwenden denselben EditorJS-Renderpfad wie normale Seiten, auch wenn sie direkt vom Router mit Seitendaten versorgt werden.
- In Kombination mit Core `3.3.24` werden Dateinamen aus der Mediathek nicht mehr als sichtbare Bildunterschriften im Public-Content ausgegeben.
- Die Beitrags-Templates `microsoft-365`, `windows` und `powershell` liefern dezente zweispaltige Meta-Steckbriefe mit kompakten Website-/GitHub-Icon-Links.
- Autorenboxen auf Seiten- und Beitragsdetails verwenden gemeinsam die Einstellungen aus `Beiträge → Autorenbox & Navigation`, inklusive Bild, Name, Über-mich-Text und Dienstleistungs-HubSite-CTA.
- PHINIT-SiteTables zeigen im Frontend keine Suchleiste über der Tabelle; Pagination erscheint erst ab mindestens 20 Zeilen und nur, wenn wirklich mehrere Seiten entstehen.
- Tabellen-Pagination ist im PHINIT-Stil gestaltet, nutzt mindestens 20 Zeilen pro Seite und rendert ohne Zeilen-/Seitenstatus-Metainfo unterhalb des Tabellentitels.
- nachgeschärfter Dark Mode mit hellblauen Inhalts-Weblinks sowie lesbaren Favoriten-, Startseiten- und Member-Buttons auf dunklem Hintergrund
- Dark-Mode-Kompatibilität für `html.dark-mode`: Startseiten-Featured-Banner, Sidebar-Hover, Detailseiten-TOC, About-Me/Autorbox, Kommentarbereich, HubSite-Button-Hover und HubSite-TOC bleiben kontrastreich, auch wenn die Theme-Init-Klasse bereits am `<html>` sitzt.
- Tabellen-Captions aus dem zentralen Site-Table-Renderer bleiben in Seiten und HubSites erhalten und werden oberhalb der Tabelle kontrastreich dargestellt.
- EditorJS-Abstandsblöcke werden im Public-Frontend mit gespeicherter `data-height`-Höhe gerendert; Core `3.3.45` sichert dabei insbesondere `10px`, `100px` und `150px` auch als CSS-Fallback ab
- kompakter Seiten-/Beitragsstart mit 25px Abstand nach dem sticky Header; HubSites starten bewusst mit 0px Abstand, weil ihr eigenes Markup den Abstand mitbringt
- entlasteter Mobile-Head-Pfad mit inline Theme-Init, asynchronem UI-/Card-CSS auf Home-/Blog-Listings und intrinsischen Header-Logo-Dimensionen
- Startseite rendert oberhalb der Falz jetzt über ein eigenes `homepage-blog-critical.css`; das große Home-Stylesheet wird dort erst nach dem First Paint asynchron nachgeladen
- Das Sidebar-Widget `Empfohlene Artikel` nutzt im Rotator jetzt eingebettete Links/Rechts-Pfeile statt unterer Pagination-Dots
- Das Sidebar-Widget `Empfohlene Artikel` wurde zusätzlich um 35px erhöht; die Pfeilbuttons liegen jetzt stabil über dem Slide-Overlay und reagieren zuverlässig auf Klicks
- Normale Blog-Listings laden kein HubSite-CSS mehr, und die dekorativen Sidebar-Thumbnails im Widget `Empfohlene Artikel` vermeiden redundante Alt-Texte im selben Link
- Der mobile Startpfad überspringt Desktop-Dropdown-Logik, vermeidet den initialen Sticky-Header-Layoutread und nutzt kleinere responsive Desktop-Kandidaten für lazy geladene Artikelbilder
- Beitrags-Vorschaubilder in der Startseiten-Artikelliste haben jetzt einen stabilen Skeleton-Placeholder, laden die ersten zwei sichtnahen Karten eager und preladen das Homepage-Lead-Bild bevorzugt als AVIF/WebP ohne unnötige Format-Dopplung
- Mobile Startseiten-Listcards übernehmen das Gridcard-artige Bild-links-Layout inklusive passender 96px/80px-Bildspalte, Meta-/Auszug-Reihenfolge und Kategorie/Weiterlesen-Footer.
- AVIF-/WebP-/Thumbnail-Derivate der Startseiten-Listcards werden nach einem Medienersatz automatisch erneuert und per `filemtime`-Cachebuster ausgeliefert, damit ersetzte Beitragsbilder sofort sichtbar werden.
- Beitrags- und Featured-Artikelbilder der Startseite verwenden die direkte Original-Upload-Quelle mit `filemtime`-Cachebuster statt komprimierter Picture-/Thumbnail-Derivate; mobile Featured-Banner und seitliche Sidebar-Featured-Boxen bleiben im Bild-links-Layout neben Titel und Teaser.
- Textauszüge der mobilen Startseiten-Listcards sind zusätzlich per echter `max-height` auf maximal drei Zeilen begrenzt, damit Browser-Kantenfälle keine vierte Zeile anzeigen.
- Die Startseiten-Listcards zeigen standardmäßig wieder vier Artikel, weil der `theme.json`-Default für `article_list_count` mit Customizer- und PHP-Defaults synchronisiert ist.
- Rotierende Sidebar-Empfehlungen zeigen wieder vollflächige Cover-Bilder mit Beitragstitel als Badge oben.
- Das Sidebar-Widget `Empfohlene Artikel` ist in der Höhe exakt um 14px reduziert, ohne Breite, Overlays, Typografie oder Rotator-Verhalten zu verändern.
- Die mobile Footer-About-Sektion ist wieder als erster Footer-Block sichtbar; Kontaktbutton bleibt mobil sichtbar und Social-Media-Icons bleiben nebeneinander ausgerichtet.
- Mobile List- und Gridcards nutzen zwei Zeilen Teaser, 4px mehr Abstand zwischen Titel und Meta-Zeile, Tap-Active-Feedback und serverseitig sechs Artikel pro Seite.
- Der mobile Back-to-top-Button erscheint auf langen Seiten ab zwei Bildschirmhöhen Scroll; die Empfohlene-Artikel-Pfeile besitzen mindestens 44×44px Touch-Fläche.
- Desktop-Gridcards rücken die Meta-Zeile 8px näher an den Titel, während Listcards unverändert bleiben; mobile List-/Gridcards sind auf 165px gekürzt und die Sektion „Alle Beiträge“ zeigt mobil vier Gridcards.
- Desktop-Gridcards ziehen die Meta-Zeile nochmals deutlich näher an den Titel; Listcards und Mobile-Abstände bleiben unverändert.
- Desktop-Gridcards reduzieren den Titel-/Meta-Abstand nochmals um 4px; inline ausgelieferte CSS-Dateien werden beim Rendern minifiziert, um PageSpeed-Warnungen zu nicht zuordenbarer CSS-Nutzlast zu senken.
- Mobile Listcards und Gridcards sind hart auf maximal vier sichtbare Karten begrenzt; ein CSS-Fallback greift auch in Desktop-Browsern mit schmaler Responsive-Ansicht.
- Das mobile Burger-Menü besitzt keinen eigenen Scrollbereich mehr und öffnet vollständig im normalen Seitenfluss unterhalb des Headers.
- Rotierende Featured-/Sidebar-Bilder priorisieren nur den sichtbaren aktiven Slide; versteckte Slides laufen mit niedriger Fetch-Priority im Hintergrund an, damit sie dem LCP-Bild keine Bandbreite wegnehmen und beim Rotieren nicht leer erscheinen
- Nicht-Bild-Performance: Scroll-Fortschritt vermeidet Layoutreads pro Scroll, Homepage-Rotatoren pausieren über Page-Lifecycle/BFCache-Hooks, Google Analytics lädt externes Third-Party-JS erst nach Load/Idle und dekorative Daueranimationen wurden entschärft
- bereinigter Header-Active-State ohne `Undefined variable $_currentLocale`-Warning im Public-Frontend
- locale-aware Homepage-Lead-Image-Abfrage ohne Alias-Mix im SQL-Filter
- öffentliche verwaltete Uploads/Featured Images folgen dem Core-3.0.24-Vertrag mit direkten, hostneutralen `/uploads/...`-Referenzen und webserverlesbaren Dateirechten; private, Hidden- und Member-Pfade bleiben weiterhin über die kontrollierte `/media-file`-Delivery-Route geschützt
- Startseiten-kritisches Basis-/Header-/Homepage-CSS wird inline ausgeliefert, der Homepage-Customizer nutzt konsistente Keys und Hover-Animationen verzichten auf layout-/paintlastige `all`-/`box-shadow`-Transitions
- Das Sidebar-Widget `Empfohlene Artikel` rotiert bereits bei zwei ausgewählten Artikeln wieder als ein einzelner vollflächiger Slide im 6-Sekunden-Intervall statt beide Beiträge untereinander zu zeigen

## Sicherheit & Audit

Die wichtigsten Security-Härtungen der letzten Runde:

- dedizierter Purifier-Renderpfad für Seiten- und Landing-Content
- HubSite-kompatibles Sanitizer-Profil im Core (`hub`)
- finaler Renderpfad stabilisiert Überschriften-IDs nach der Sanitizer-Stufe, damit TOC-Ziele erhalten bleiben
- EditorJS-Spacer nutzen ausschließlich erlaubte Safe-Attribute (`data-height`, `role`, `aria-hidden`) und CSS-Fallbacks statt unsicherer Markup-Annahmen
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