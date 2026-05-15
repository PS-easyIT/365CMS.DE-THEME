# CMS PHINIT Theme – Changelog

## 📋 Legende

| Symbol | Typ | Bedeutung |
|--------|-----|-----------|
| 🟢 | `feat` | Neues Feature |
| 🔴 | `fix` | Bugfix |
| 🟡 | `refactor` | Code-Umbau ohne Funktionsänderung |
| 🎨 | `style` | Design- / UI-Änderungen |
| 🔵 | `docs` | Dokumentation |
| 🛡️ | `security` | Sicherheits- / Hardening-Maßnahme |

---

## Unreleased

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Theme Editor / Admin Customizer | `admin/customizer.php` lädt die zentralen Request-Helper bei Bedarf selbst nach, damit der eingebettete Theme-Editor auch dann startet, wenn `functions.php` im Admin-Kontext noch nicht vollständig initialisiert wurde. |
| 🔴 fix | Plugin-Seiten / Breadcrumb | `includes/theme-head-trait.php` unterdrückt den Theme-Breadcrumb-Bereich direkt unter dem Quicklinksband jetzt auch für öffentliche Plugin-Routen inklusive M365-License-Public, damit Plugins ihre eigene Inhaltsnavigation bestimmen. |
| 🔴 fix | Header / Quicklinks | `assets/css/header-navigation.css` entfernt die unsichtbare Top-Level-Dropdown-Hoverbrücke im Hauptmenü und setzt Dropdowns bündig an die Menüleiste, damit Quicklink-Klicks nicht mehr versehentlich Hauptmenü-Dropdowns auslösen. |
| 🎨 style | Header / Quicklinks | `assets/css/header-navigation.css` färbt die Trennlinie zwischen Hauptmenüband und Quicklinksbar in der Quicklinks-Hintergrundfarbe, damit kein heller Zwischenstreifen mehr sichtbar ist. |
| 🎨 style | Startseite / Footer-Banner | `footer.php`, `partials/home-info-grid.php`, `partials/home-repo-card.php`, `style.css` und `assets/css/homepage-blog.css` verschieben den GitHub-Repo-Banner unter die Startseiten-Pagination direkt über den Theme-Footer, vollbreit und ohne Abstand zum Footer. |
| 🔴 fix | Header / Hauptmenü | `header.php` und `assets/css/header-navigation.css` markieren die aktive Hauptseite im Hauptmenü inklusive Dropdown-Elternpunkt mit `active`/`aria-current`, damit die aktuelle Hauptseite sichtbar hervorgehoben ist. |
| 🟢 feat | Startseite / Sidebar | `partials/home-article-list.php`, `includes/theme-home-helpers.php`, `includes/theme-assets-trait.php`, `assets/js/homepage-widgets.js` und `assets/css/homepage-blog.css` ergänzen ein kompaktes Artikel-Karussell mit begrenzter Vorschaubildhöhe und überarbeiten die Sidebar-Widgets zu konsistenten Karten. |
| 🟢 feat | Theme Customizer / Sidebar | `admin/customizer-schema.php`, `admin/customizer-field-renderer.php`, `admin/customizer-request-handler.php`, `assets/js/customizer-admin.js` und `assets/css/customizer-admin.css` ersetzen die manuelle Sidebar-Reihenfolge per Schlüssel-Textarea durch eine Button-basierte Sortierliste. |
| 🟢 feat | Startseite / Featured-Banner | `index.php`, `includes/theme-home-helpers.php`, `partials/home-featured-banner.php` und `admin/customizer-schema.php` ergänzen oben auf der Startseite einen dezent hervorgehobenen, per Customizer wählbaren Featured-Artikel-Banner. |
| 🟢 feat | Startseite / Sidebar | `partials/home-article-list.php`, `includes/theme-home-helpers.php`, `admin/customizer-schema.php` und `assets/css/homepage-blog.css` ergänzen ein About-Me-Widget und eine Customizer-gesteuerte Reihenfolge für Sidebar-Bereiche. |
| 🎨 style | Startseite / Cards | `partials/home-article-list.php`, `partials/home-post-grid.php`, `assets/css/homepage-blog.css` und `assets/css/content-cards.css` vereinheitlichen Abschnittstrenner im Themenbereiche-Design, begrenzen Sidebar-Artikelbilder auf maximal die Teaserhälfte und stellen „Weiter lesen“ als dezente Buttons dar. |

---

## v1.5.33 — 15. Mai 2026

### Kompakter Seitenstart nach dem Header

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | Header / Seiten / Beiträge | `assets/css/header-navigation.css`, `style.css`, `assets/css/page-detail.css` und `assets/css/post-sidebar.css` lassen den Header sticky im normalen Dokumentfluss stehen und setzen für normale Seiten und Beiträge einen echten Header-zu-Content-Abstand von `25px`, ohne die alte Headerhöhen-Reservierung wieder einzubauen. |
| 🎨 style | HubSites / Breadcrumbs | `includes/theme-head-trait.php`, `style.css` und `assets/css/hub-sites.css` erkennen HubSites separat, lassen die HubSite-Hintergrundfläche bündig unter dem Theme-Header beginnen und richten die HubSite-Shell exakt an der normalen `.container`-/Header-/Footer-Breite aus. Der HubSite-Content-Header bekommt innen `25px` Abstand nach oben, der HubSite-Inhalt bleibt links/rechts `10px` schmaler; sichtbare Breadcrumbs unter den Quicklinks bleiben unterdrückt. |
| 🎨 style | Startseite / Customizer-Abstand | `index.php`, `includes/theme-home-helpers.php`, `style.css` und `assets/css/homepage-blog.css` lassen auf der Startseite wieder die globale Customizer-Einstellung `spacing_header_content` (`Header zu Content Abstand`) greifen, damit der GitHub-Repo-Bereich sichtbar Abstand zum Theme-Header bekommt. |
| 🎨 style | Knowledgebase / Header-Abstand | `style.css` reduziert den äußeren Theme-Abstand Header→Content für Knowledgebase-Seiten (`/kb`, `/glossar`) auf die Hälfte des normalen Theme-Abstands, damit der KB-Content deutlich näher am Header startet. |
| 🎨 style | M365 License Public / Plugin-Abstände | `style.css` setzt nur für öffentliche M365-License-Seiten mit `m365lic-theme-embed` den äußeren Theme-Abstand Header→Content auf `0px`; alle übrigen Plugin-Seiten bleiben beim normalen Theme-Abstand von `25px` bzw. der globalen Customizer-Einstellung. |
| 🎨 style | Plugin-Content / Footer-Abstand | `includes/theme-head-trait.php`, `style.css` und `assets/css/footer-consent.css` markieren öffentliche Plugin-Content-Routen mit `is-plugin-content` und setzen den äußeren Theme-Abstand Content→Footer sowie den Theme-Footer-`margin-top` für Plugin-Seiten auf `0px`, damit die Plugins ihren unteren Abschlussabstand selbst bestimmen. |
| 🔴 fix | Layout / Horizontaler Scroll | `style.css` und `assets/css/hub-sites.css` kappen horizontalen Seiten-Overflow und begrenzen HubSite-Container auf die Viewport-Breite, damit Chrome keinen leeren rechten Scrollbereich mehr anzeigt. |
| 🔴 fix | CSS Cache-Busting | `includes/theme-assets-trait.php` kombiniert einen optional gesetzten Customizer-Cache-Buster jetzt mit `filemtime()`, sodass geänderte Theme-CSS-Dateien auch bei festem Cache-Buster frisch ausgeliefert werden. |
| 🎨 style | Theme Customizer | `admin/customizer-schema.php` und `theme.json` verwenden für `spacing_header_content` jetzt den Standardwert `25px`. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.33` synchronisiert. |

---

## v1.5.32 — 15. Mai 2026

### Live-Admin-Audit-Fixes

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Seiten-Rendering | `page.php` bereitet Router-geladene Editor.js-Seiten wieder vor dem Sanitizing über den zentralen Renderpfad auf, damit veröffentlichte Seiteninhalte sichtbar bleiben. |
| 🔴 fix | Admin Customizer | `admin/customizer-schema.php` und `admin/customizer-field-renderer.php` liefern nativen Color-Pickern nur noch valide `#rrggbb`-Werte und normalisieren gespeicherte Altwerte auf den Feld-Default. |
| 🔴 fix | Footer / Kontaktlink | `footer.php` und `includes/theme-navigation-trait.php` verweisen im Standardmenü auf die kanonische Kontaktformular-Route `/contact`, statt auf die nicht registrierte Route `/kontakt`. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.32` synchronisiert. |

---

## v1.5.31 — 14. Mai 2026

### Multi-Level-Audit-Fortsetzung: Sink-Härtung und Array-Helper

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🛡️ security | Header / Ausgabe-Sinks | `header.php` escaped Member-Greeting, Body-Class und Logo-Textfragmente jetzt konsequent mit `ENT_QUOTES`/`UTF-8` direkt am Ausgabesink. |
| 🛡️ security | Member Dashboard | `member/dashboard.php` hält Dashboard-, Hero-, Plugin- und Quicklink-URLs wieder als Rohwerte und escaped sie erst direkt im jeweiligen `href`-Attribut. |
| 🛡️ security | Bildarchiv | `partials/page-image-archive.php` rendert vorbereitete Seitenbeschreibung zusätzlich über `phinit_render_sanitized_content()`, statt den vorbereiteten HTML-String direkt auszugeben. |
| 🟡 refactor | PHP 8.4 / array_any | `partials/home-article-list.php` ersetzt die manuelle Boolean-Suchschleife für Custom-Featured-Images durch `array_any()`. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.31` synchronisiert. |

---

## v1.5.30 — 14. Mai 2026

### Multi-Level-Audit mit PHP-8.4-Config-Härtung

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🛡️ security | Theme Customizer | `admin/customizer-form.php`, `admin/customizer-sidebar.php` und `admin/customizer-form-hidden-fields.php` escapen Customizer-Actions, Tab-Links und CSRF-Hidden-Fields jetzt explizit mit `ENT_QUOTES`/`UTF-8`; Tab-Parameter werden per `rawurlencode()` URL-konform kodiert. |
| 🟡 refactor | PHP 8.4 / Config | `admin/customizer-config-builder.php` ergänzt `CMS_Phinit_Customizer_Config_Snapshot` mit Property Hooks und `public private(set)`, sodass Config-Kategorien und Tab-Gruppen normalisiert lesbar, aber nicht versehentlich von außen überschreibbar sind. |
| 🟡 refactor | PHP 8.4 / array_find | `includes/theme-assets-trait.php` und `includes/theme-head-trait.php` ersetzen manuelle Suchschleifen für lokale Font-Kandidaten und Member-Route-Titel durch `array_find()`. |
| 🛡️ security | Audit / Strict Types | Der Multi-Level-Audit bestätigt: alle 93 PHP-Dateien enthalten `declare(strict_types=1);`, Theme-eigene POST-Formulare führen CSRF-Token mit, und es bleiben keine wortgenauen Raw-Sinks `echo $content`, `echo $html`, `echo $id` oder `echo $name`. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.30` synchronisiert. |

---

## v1.5.29 — 14. Mai 2026

### Request-/Template-Logik weiter zentralisiert

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🛡️ security | Request Handling | `includes/theme-template-helpers.php` ergänzt zentrale Helper für URI, Pfad, Query-String, Request-Methode und Host; Werte werden defensiv von Kontrollzeichen bereinigt und begrenzt. |
| 🟡 refactor | Templates / DB-Logik | `404.php`, `header.php` und `footer.php` nutzen jetzt Helper für 404-Beitragsvorschläge, Notification-Zähler und Cookie-Consent-Status statt direkter Template-DB-Logik. |
| 🟡 refactor | Request-Kontext | Seiten-, Post-, Member- und interne Asset-/Head-Pfade lesen Request-Daten jetzt über `phinit_current_request_path()`, `phinit_current_request_uri()`, `phinit_current_request_query()`, `phinit_request_method()` und `phinit_current_host()`. |
| 🛡️ security | Formulare | Auth-, Favoriten-, Feed-, Newsletter-, Member-Post- und Kommentarformulare nutzen für Submit-/Checkbox-Flags arraysichere Request-Helper. |
| 🔵 docs | Release | Der zuvor vermischte Changelog wurde wieder in getrennte Release-Abschnitte aufgeteilt; `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.29` synchronisiert. |

---

## v1.5.28 — 14. Mai 2026

### Audit-Restpunkte im Customizer und in Spezialseiten geschlossen

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🛡️ security | Theme Customizer | `admin/customizer-field-renderer.php` escaped generierte `id`-/`name`-Attribute jetzt direkt vor der Ausgabe, sodass auch schema-basierte Feldnamen keinen Raw-Attribut-Sink mehr bilden. |
| 🛡️ security | Theme Customizer | `admin/customizer-request-handler.php` begrenzt gepostete Customizer-Feldwerte defensiv auf skalare Werte, bevor die feldtypspezifische Normalisierung greift. |
| 🟡 refactor | Spezialseiten / Autoren | `includes/theme-special-pages-helpers.php` ersetzt die N+1-Schleife über `getPublicAuthorProfile()` durch eine aggregierte Batch-Abfrage für Autorenübersichten. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.28` synchronisiert. |

---

## v1.5.27 — 14. Mai 2026

### PHP-8.4-Audit-Fixes, sichere Content-Sinks und Header-Shrink

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🛡️ security | Content Rendering | `includes/theme-content-helpers.php`, `post.php`, `post-wide.php` und `post-tech.php` rendern Post- und Hub-Content jetzt ausschließlich über den zentralen Purifier-Renderpfad; direkte Raw-HTML-Sinks wurden entfernt. |
| 🛡️ security | Request Handling | `includes/theme-template-helpers.php` ergänzt arraysichere Input-Helper; öffentliche Archive, Auth-/Member-Formulare, Favoriten und Customizer-Steuerfelder nutzen diese Helper für Query- und POST-Werte. |
| 🔴 fix | Post Templates | `post-wide.php` und `post-tech.php` übernehmen den Router-/Fallback-Flow des Standard-Templates: keine doppelte Content-Aufbereitung und keine doppelte View-Zählung bei router-geladenen Beiträgen. |
| 🔴 fix | Member Dashboard | `member/dashboard.php` importiert die URL-Builder-Closure korrekt in den Favoriten-Mapper, damit aktuelle Favoriten wieder kanonische Post-URLs erzeugen. |
| 🟡 refactor | Head / Performance | `includes/theme-head-trait.php` nutzt vorhandene `$GLOBALS['post']`-/`$GLOBALS['page']`-Payloads als Cache-Quelle und vermeidet redundante Head-DB-Abfragen. |
| 🎨 style | Header / Logo | `assets/css/header-navigation.css`, `assets/js/navigation.js` und `includes/theme-assets-trait.php` binden die Logo-Höhe konsequent an `header.logo_max_height`; beim Scrollen schrumpft der Logo-/Suchbereich auf 75% der normalen Höhe. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.27` synchronisiert. |

---

## v1.5.26 — 14. Mai 2026

### Homepage-Lead-Image-SQL ohne Alias-Mix und Mobile-UI-Nachschärfung

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Theme Assets / Homepage | `includes/theme-assets-trait.php` deklariert die Homepage-Lead-Image-Abfrage jetzt mit `FROM posts p` und nutzt `phinit_post_publication_where('p')`, sodass der locale-aware Filter aus `phinit_build_homepage_post_locale_condition()` nicht mehr mit `Unknown column 'p.content'` im PHP Error-Log scheitert. |
| 🟢 feat | Theme Editor / Header | `admin/customizer-schema.php`, `theme.json` und `header.php` ergänzen die neue Header-Option `show_login_button`, mit der sich der komplette Login-/Account-Button im Desktop-Header sowie der mobile Login-Link gezielt ausblenden lassen. |
| 🎨 style | Post/Page Detail | `assets/css/post-detail.css` und `assets/css/page-detail.css` blenden das Headerbild auf Beitrags- und Seitendetailseiten bei `≤ 768px` aus, damit der mobile Content-Header kompakter startet und Titel/Meta sofort sichtbar bleiben. |
| 🎨 style | Mobile Header/Home | `assets/css/header-navigation.css` zeigt das Theme-Logo im mobilen Header wieder links an und begrenzt Bildlogos auf maximal `30px` Höhe; `style.css` reduziert den Abstand zwischen Header und dem ersten Homepage-Band („Aktuelle Beiträge“) mobil auf `10px`. |
| 🎨 style | Post Detail / Share | `assets/css/post-detail.css` ordnet die Teilen-Buttons auf Beitragsdetailseiten in der Mobileansicht jetzt immer in einem festen 3er-Raster an. |
| 🎨 style | Post Sidebar | `partials/sidebar.php` und `assets/css/post-sidebar.css` verteilen Social-Buttons kompakter und rendern Tags als zweispaltiges Grid mit Ellipsis. |
| 🔵 docs | Release | `theme.json`, `functions.php`, `style.css`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.26` synchronisiert. |

---

## v1.5.25 — 15. April 2026

### Header-Warning für locale-aware Navigation beseitigt

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Header / Locale-Aktivzustand | `header.php` übernimmt `$_currentLocale` jetzt explizit in die Closure zur Desktop-Navigationsprüfung, sodass der Active-State lokalisierte Pfade weiter korrekt bewertet, aber der Public-Warning `Undefined variable $_currentLocale` nicht mehr ausgelöst wird. |
| 🔵 docs | Release | `functions.php`, `theme.json`, `update.json` und `README.md` wurden auf Version `1.5.25` synchronisiert. |

---

## v1.5.24 — 15. April 2026

### Mobile-Performance im Head- und Header-Pfad nachgeschärft

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Performance / Head | `header.php` gibt den kleinen Theme-Init für den Dark-Mode jetzt wieder direkt inline im `<head>` aus. Dadurch entfällt der zusätzliche blockierende Request auf `assets/js/theme-init.js` vor dem ersten Paint. |
| 🔴 fix | Performance / CSS | `includes/theme-assets-trait.php` lädt `assets/css/ui-chrome.css` und `assets/css/content-cards.css` auf Home-/Blog-Listing-Routen nun asynchron nach, statt beide Bundles im kritischen Render-Pfad mitzuschleppen. |
| 🔴 fix | Header / CLS | `header.php` ergänzt für Bildlogos im Header jetzt echte intrinsische Bildmaße über `phinit_image_dimension_attributes()`, damit das Logo stabiler reserviert wird und Lighthouse keine fehlenden `width`/`height`-Attribute mehr moniert. |
| 🔵 docs | Release | `functions.php`, `theme.json`, `update.json` und `README.md` wurden auf Version `1.5.24` synchronisiert. |

---

## v1.5.23 — 15. April 2026

### BreadcrumbList für Suchmaschinen, aber unsichtbar im Public-Frontend

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | SEO / Structured Data | `includes/theme-head-trait.php` gibt für Artikel und Seiten jetzt eine zusätzliche `BreadcrumbList` als JSON-LD aus, damit Google einen sauberen Navigationspfad lesen kann, ohne dass dafür sichtbare Breadcrumbs auf der Seite erscheinen. |
| 🔴 fix | Frontend / Breadcrumb Output | Für Beitrags- und Seiten-Detailseiten unterdrückt dasselbe Trait die bisherige sichtbare Breadcrumb-Leiste nach dem Header; im Public-Frontend bleibt der Pfad damit unsichtbar und nur die strukturierte Suchmaschinen-Version erhalten. |
| 🔵 docs | Release | `functions.php`, `theme.json`, `update.json` und `README.md` wurden auf Version `1.5.23` synchronisiert. |

---

## v1.5.22 — 15. April 2026

### EN-Custom-Slugs im Public Theme wieder vollständig stylesicher auflösen

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Locale/Page Resolution | `includes/theme-template-helpers.php`, `page.php`, `page-wide.php` und `page-landing.php` lösen Seiten-Fallbacks jetzt locale-aware über `slug`/`slug_en` auf, damit EN-Seiten mit eigenem Slug wieder zuverlässig die richtige Page-Payload und das korrekte Template erhalten. |
| 🔴 fix | Theme Assets | `includes/theme-assets-trait.php` erkennt Cookie-/Bildarchiv-/Detailseiten nun über die tatsächlich aufgelöste Seiten-Payload statt ausschließlich über starre DE-Pfade; dadurch laden die zugehörigen CSS-Bundles auch bei lokalisierten oder benutzerdefinierten EN-Slugs wieder konsistent. |
| 🔴 fix | Head / Meta | `includes/theme-head-trait.php` liest aktuelle Seiten für Titel/Meta ebenfalls locale-aware, sodass EN-Custom-Slugs nicht mehr in die alte DE-only Seitenerkennung fallen. |
| 🔵 docs | Release | `functions.php`, `theme.json`, `update.json` und `README.md` wurden auf Version `1.5.22` synchronisiert. |

---

## v1.5.21 — 4. April 2026

### Snyk-Restbefunde im Seiten- und Customizer-Flow vollständig geschlossen

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🛡️ security | Page Templates | `page.php`, `page-wide.php`, `page-landing.php` und `includes/theme-content-helpers.php` rendern vorbereiteten Seiten-/Landing-Content jetzt ausschließlich über einen dedizierten Purifier-Renderpfad, wodurch die letzten Snyk-XSS-Befunde in den Seitentemplates entfallen. |
| 🛡️ security | Hub-Markup | `CMS/core/Services/PurifierService.php` ergänzt das Profil `hub`, damit HubSite-Markup im `cms-phinit`-Page-Template erneut purifier-gesichert werden kann, ohne `section`-/`article`-/`nav`-Struktur oder CSS-Klassen zu verlieren. |
| 🛡️ security | Customizer Import | `admin/customizer-request-handler.php` validiert, staged und liest Import-Uploads jetzt in einem einzigen kontrollierten Temp-Flow; damit ist auch der letzte Snyk-Path-Traversal-Befund im Theme-Customizer geschlossen. |
| 🔵 docs | Audit | `DOC/THEME-AUDIT.md` dokumentiert den verifizierten Stand `0` aktive Snyk-Findings für `cms-phinit`. |
| 🔵 docs | Release | `functions.php`, `theme.json`, `update.json` und das neue `README.md` wurden auf Version `1.5.21` synchronisiert. |

---

## v1.5.20 — 29. März 2026

### Homepage-Widget-Pfade und Sidebar-Medien weiter konsolidiert

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🛡️ security | Homepage/Sidebar | `partials/home-article-list.php` normalisiert Sidebar-Identity-Logo, Projektkarten-Logos und Featured-Thumbnails jetzt über `phinit_normalize_public_media_url()`, damit die Homepage-Widgets keine rohen Medienpfade mehr direkt als Bildquelle oder CSS-Hintergrund übernehmen. |
| 🛡️ security | Homepage/Links | Identity-, Projekt-, Featured-, Status- und Notice-Links der Listen-Sidebar laufen jetzt fail-closed über die Public-URL-Allowlist, statt rohe Customizer- oder ViewModel-Werte direkt als `href` zu rendern. |
| 🔵 docs | Audit | `DOC/THEME-AUDIT.md` ergänzt die Homepage-Widget-Härtung als `SEC-21`. |
| 🔵 docs | Release | `functions.php`, `theme.json` und `update.json` auf Version `1.5.20` angehoben. |

---

## v1.5.19 — 29. März 2026

### Wiederverwendete Post-Bilder und Header-Meta weiter gehärtet

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🛡️ security | Post-Partials | `partials/home-post-grid.php`, `partials/post-card.php` und `partials/post-header.php` normalisieren Featured Images jetzt über `phinit_normalize_public_media_url()`, sodass rohe Medienpfade nicht mehr direkt als `img src` durch die wiederverwendeten Karten-/Header-Bausteine gehen. |
| 🛡️ security | Post-Links/Meta | Die betroffenen Post-Partials sichern kanonische Beitrags-Links zusätzlich über die Public-URL-Allowlist ab; `partials/post-header.php` rendert das Veröffentlichungsdatum außerdem fail-closed statt implizit mit einem `now`-Fallback. |
| 🔵 docs | Audit | `DOC/THEME-AUDIT.md` ergänzt die konsolidierten Post-Bildpfade als `SEC-20`. |
| 🔵 docs | Release | `functions.php`, `theme.json` und `update.json` auf Version `1.5.19` angehoben. |

---

## v1.5.18 — 29. März 2026

### Archiv- und Sidebar-Links über Public-Helper konsolidiert

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🛡️ security | Bildarchiv | `partials/page-image-archive.php` normalisiert Download-Ziele, Vorschaubilder und verknüpfte Artikel-URLs jetzt über die vorhandene Public-URL-/Media-Allowlist, sodass rohe Archiv-Payloads nicht mehr direkt als `href` oder `img src` gerendert werden. |
| 🛡️ security | Sidebar | `partials/post-sidebar-social.php` und `partials/page-sidebar-nav.php` verwerfen ungültige Social-/Navigationsziele jetzt fail-closed statt sie ungeprüft als klickbare Frontend-Links auszugeben. |
| 🔵 docs | Audit | `DOC/THEME-AUDIT.md` ergänzt die konsolidierten Archiv-/Sidebar-Pfade als `SEC-19`. |
| 🔵 docs | Release | `functions.php`, `theme.json` und `update.json` auf Version `1.5.18` angehoben. |

---

## v1.5.17 — 29. März 2026

### Landing-CTAs und öffentliche Medienpfade fail-closed normalisiert

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🛡️ security | Landing/Page | `page-landing.php` validiert Hero-CTA-, Feature- und Abschluss-CTA-Links jetzt über die Public-URL-Allowlist und rendert das optionale Hero-Bild nur noch über `phinit_normalize_public_media_url()`, sodass schemenfremde Ziele oder rohe Medienpfade fail-closed aus dem Frontend herausfallen. |
| 🛡️ security | Frontend/Media | `partials/page-header-block.php`, `author.php` und `authors.php` normalisieren Seiten-Hero-, Avatar- und Autor-Post-Bilder jetzt ebenfalls über den zentralen Public-Media-Vertrag statt rohe Meta-/DB-Werte direkt als `img src` zu übernehmen. |
| 🔵 docs | Audit | `DOC/THEME-AUDIT.md` ergänzt die URL-/Medien-Härtung als `SEC-18` und dokumentiert den verbleibenden Scanner-Restbefund für den bereits sanitisierten Landing-Content in `page-landing.php` als False Positive. |
| 🔵 docs | Release | `functions.php`, `theme.json` und `update.json` auf Version `1.5.17` angehoben. |

---

## v1.5.16 — 29. März 2026

### Restliche Member-Datumsanzeigen fail-closed gehärtet

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🛡️ security | Member/Profile | `member/profile.php` zeigt „Mitglied seit“ jetzt defensiv als Datum oder `—` statt implizit über einen `now`-Fallback an. |
| 🛡️ security | Member/Security | `member/security.php` formatiert letzte Aktivität und Sitzungszeiten fail-closed; ungültige Zeitstempel erzeugen keine scheinbar gültigen Session-Zeiten mehr. |
| 🛡️ security | Member/Forum | `member/forum.php` typisiert Thread-Statistiken explizit, formatiert Thread-Daten defensiv und baut Thread-Links nicht mehr als rohe String-Verkettung ohne Public-URL-Sicherung. |
| 🛡️ security | Member/Posts | `member/posts.php` gibt Einreichungszähler explizit als Integer aus, rendert Änderungsdaten fail-closed und sichert veröffentlichte Post-Links zusätzlich über die Public-URL-Allowlist ab. |
| 🔵 docs | Audit | `DOC/THEME-AUDIT.md` ergänzt die Rest-Härtung als `SEC-17`. |
| 🔵 docs | Release | `functions.php`, `theme.json` und `update.json` auf Version `1.5.16` angehoben. |

---

## v1.5.15 — 29. März 2026

### Defensive Frontend-Datumsanzeige in Seiten, Suche und Kommentaren

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🛡️ security | Frontend/Dates | `404.php`, `page-wide.php`, `page.php`, `partials/post-comments.php`, `partials/post-tech-card.php` und `partials/search-result-row.php` rendern Datumswerte jetzt fail-closed statt implizit über `strtotime()` in scheinbar valide Anzeigen zu kippen. |
| 🛡️ security | Frontend/Links | Die 404-Vorschlagskarten und Suchergebnis-Ziele normalisieren Fallback-URLs/-Medien jetzt defensiver über die vorhandenen Public-URL-/Media-Helfer, statt rohe Fallback-Pfade direkt weiterzugeben. |
| 🔵 docs | Audit | `DOC/THEME-AUDIT.md` ergänzt die Template-Härtung als `SEC-16`. |
| 🔵 docs | Release | `functions.php`, `theme.json` und `update.json` auf Version `1.5.15` angehoben. |

---

## v1.5.14 — 29. März 2026

### Feed-Digest-Ansicht defensiv typisiert

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🛡️ security | Member/Feeds | `member/feeds.php` formatiert `last_sent_at` und `last_fetched_at` jetzt fail-closed statt implizit über `strtotime()` in scheinbar gültige Zeiten umzudeuten; ungültige Zeitstempel erzeugen keine irreführenden Datumsanzeigen mehr. |
| 🛡️ security | Member/UI | Zähler- und Badge-Werte im Feed-Abo-, Auswahl- und Zusammenfassungsbereich werden explizit als Integer ausgegeben, damit reine Statistikwerte nicht mehr implizit aus gemischten Payloads gerendert werden. |
| 🔵 docs | Audit | `DOC/THEME-AUDIT.md` ergänzt die Feed-Härtung als `SEC-15`. |
| 🔵 docs | Release | `functions.php`, `theme.json` und `update.json` auf Version `1.5.14` angehoben. |

---

## v1.5.13 — 29. März 2026

### Defensive Member-Listen für Kommentare & Favoriten

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🛡️ security | Member/Dashboard | `member/dashboard.php` baut Post-Links für die Kommentar-/Favoritenlisten jetzt über den kanonischen Post-URL-Helfer plus Public-URL-Allowlist auf und formatiert Datumswerte defensiv statt implizit über `strtotime(... ?? 'now')`. |
| 🛡️ security | Member/Kommentare | `member/comments.php` nutzt für Kommentar-Links denselben kanonischen Post-Link-Pfad, zeigt ungültige Zeitstempel jetzt als `—` statt still als aktuelle Zeit an und typisiert Zähler-/Paginierungswerte explizit als Integer. |
| 🔵 docs | Audit | `DOC/THEME-AUDIT.md` dokumentiert die Listen-Härtung als `SEC-14`. |
| 🔵 docs | Release | `functions.php`, `theme.json` und `update.json` auf Version `1.5.13` angehoben. |

---

## v1.5.12 — 29. März 2026

### Member-Dashboard-Settings mit Theme-Override synchronisiert

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🛡️ security | Member/Dashboard | `member/dashboard.php` nutzt die bereits serverseitig gehärteten Core-Settings `member_dashboard_greeting`, `member_dashboard_welcome_text` und `member_dashboard_logo` jetzt als sichere Fallbacks, sodass Admin-Konfiguration und Theme-Override nicht länger auseinanderlaufen. |
| 🛡️ security | Member/Media | Das optionale Dashboard-Logo wird im Theme nur noch über `phinit_normalize_public_media_url()` in ein `img`-Tag übernommen; schemenfremde oder ungültige Medienreferenzen bleiben damit auch im Hero-Bereich außen vor. |
| 🔵 docs | Audit | `DOC/THEME-AUDIT.md` dokumentiert die Theme-seitige Angleichung als `SEC-13`. |
| 🔵 docs | Release | `functions.php`, `theme.json` und `update.json` auf Version `1.5.12` angehoben. |

---

## v1.5.11 — 29. März 2026

### Member-Favoriten-Härtung & sichere Avatar-Previews

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🛡️ security | Member/Favoriten | `includes/theme-template-helpers.php` validiert Page-Favorites beim Lesen und Speichern jetzt gegen die zentrale Public-URL-/Media-Allowlist, sodass defekte oder schemenfremde Werte nicht in der Merkliste verbleiben. |
| 🛡️ security | Member/UI | `member/favorites.php` typisiert `favorite_storage`, `favorite_id` und `favorite_content_id` strenger, normalisiert Favoritenbilder/-URLs vor dem Rendern und zeigt ungültige Datumswerte defensiv als `—` statt implizit `now` an. |
| 🛡️ security | Member/Profil | `member/profile.php` und `member/partials/member-nav.php` rendern Avatar-Previews nur noch über `phinit_normalize_public_media_url()`, sodass rohe Meta-URLs nicht mehr direkt in `img src` landen. |
| 🟡 refactor | Favoriten-Flow | Die Page-Favorites-Toggle-Logik vermeidet einen redundanten zweiten Meta-Read, indem sie den Existenzcheck aus dem bereits geladenen Favoriten-Array ableitet. |
| 🔵 docs | Audit | `DOC/THEME-AUDIT.md` dokumentiert die neue Member-Härtung als `SEC-08` und markiert die verbleibende serverseitige Profil-URL-Speicherung im Core als nächsten Folge-Hotspot. |
| 🔵 docs | Release | `functions.php`, `theme.json` und `update.json` auf Version `1.5.11` angehoben. |

---

## v1.5.10 — 29. März 2026

### Konservative Output-Härtung & konsistente Share-Links

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🛡️ security | Customizer/Admin | `admin/customizer-alert.php` escaped Statusmeldungen jetzt explizit als Text und rendert leere Meldungen gar nicht mehr, sodass ein verbleibender HTML-/XSS-Restpfad aus Alert-Strings entfällt. |
| 🛡️ security | Archive/Author | `author.php` und `category.php` geben Pagination- und Statistikwerte jetzt explizit als Ganzzahlen aus; Autoren-Paginierung hängt `page` zudem robust mit dem passenden Query-Separator an bestehende Profil-URLs an. |
| 🟡 refactor | Share-Links | `post.php`, `post-wide.php` und `post-tech.php` bauen LinkedIn-, Twitter/X- und Mail-Share-Links jetzt konsistent per `http_build_query(..., PHP_QUERY_RFC3986)` statt über verteilte manuelle Parameter-Escapes. |
| 🔵 docs | Audit | `DOC/THEME-AUDIT.md` dokumentiert die neue Output-Kontext-Härtung als `SEC-07` und hebt die statische Sicherheitsbewertung auf `A` an. |
| 🔵 docs | Release | `functions.php`, `theme.json` und `update.json` auf Version `1.5.10` angehoben. |

---

## v1.5.9 — 29. März 2026

### HubSites-Renderfix, Frontend-Härtung & stabilerer Theme-Editor

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | HubSites/Theme | HubSites behalten im `cms-phinit`-`page.php` jetzt ihr vom Core geliefertes Struktur-Markup, statt auf dem finalen Theme-Renderpfad erneut durch den Default-Purifier zu laufen. `section`-, `article`-, `nav`- und weitere Hub-Klassen bleiben dadurch erhalten und das Layout greift wieder vollständig. |
| 🛡️ security | Frontend/Links | Öffentliche Footer-, Sidebar- und Homepage-Links aus dem Theme-Customizer laufen jetzt konsistent über `phinit_safe_public_url()`, sodass schemenfremde oder defekte Werte nicht mehr als klickbare URLs im Frontend erscheinen. |
| 🛡️ security | Homepage | Download-, Notice-, Social- und Repo-Links in den Startseiten-Widgets werden defensiv normalisiert bzw. verworfen, wenn sie nicht der öffentlichen URL-Allowlist entsprechen. |
| 🔴 fix | Theme Editor | Der eingebettete Customizer speichert, resettet und importiert wieder zuverlässig, weil der Admin-Wrapper POST-Requests inline durchreicht und denselben CSRF-Kontext nicht doppelt verbraucht. |
| 🔴 fix | Customizer-Persistenz | Globale Theme-Customizer-Werte werden NULL-sicher dedupliziert und stabil geladen, sodass alte Datenbankzeilen neue Saves nicht mehr überstimmen. |
| 🔵 docs | Audit | `DOC/THEME-AUDIT.md` dokumentiert die neuen Hardening-Maßnahmen sowie die validierten Scanner-False-Positives. |
| 🔵 docs | Release | `functions.php`, `theme.json` und `update.json` auf Version `1.5.9` angehoben. |
