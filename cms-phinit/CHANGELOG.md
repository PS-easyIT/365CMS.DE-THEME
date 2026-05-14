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

Noch keine unveröffentlichten Änderungen.

---

## v1.5.28 — 14. Mai 2026

### Audit-Restpunkte im Customizer und in Spezialseiten geschlossen

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🛡️ security | Theme Customizer | `admin/customizer-field-renderer.php` escaped generierte `id`-/`name`-Attribute jetzt direkt vor der Ausgabe, sodass auch schema-basierte Feldnamen keinen Raw-Attribut-Sink mehr bilden. |
| 🛡️ security | Theme Customizer | `admin/customizer-request-handler.php` begrenzt gepostete Customizer-Feldwerte defensiv auf skalare Werte, bevor die feldtypspezifische Normalisierung greift. |
| 🟡 refactor | Spezialseiten / Autoren | `includes/theme-special-pages-helpers.php` ersetzt die N+1-Schleife über `getPublicAuthorProfile()` durch eine aggregierte Batch-Abfrage für Autorenübersichten. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.28` synchronisiert. |
| 🛡️ security | Content Rendering | `includes/theme-content-helpers.php`, `post.php`, `post-wide.php` und `post-tech.php` rendern Post- und Hub-Content jetzt ausschließlich über den zentralen Purifier-Renderpfad; direkte Raw-HTML-Sinks wurden entfernt. |
| 🛡️ security | Request Handling | `includes/theme-template-helpers.php` ergänzt arraysichere Input-Helper; öffentliche Archive, Auth-/Member-Formulare, Favoriten und Customizer-Steuerfelder nutzen diese Helper für Query- und POST-Werte. |
| 🔴 fix | Post Templates | `post-wide.php` und `post-tech.php` übernehmen den Router-/Fallback-Flow des Standard-Templates: keine doppelte Content-Aufbereitung und keine doppelte View-Zählung bei router-geladenen Beiträgen. |
| 🔴 fix | Member Dashboard | `member/dashboard.php` importiert die URL-Builder-Closure korrekt in den Favoriten-Mapper, damit aktuelle Favoriten wieder kanonische Post-URLs erzeugen. |
| 🟡 refactor | Head / Performance | `includes/theme-head-trait.php` nutzt vorhandene `$GLOBALS['post']`-/`$GLOBALS['page']`-Payloads als Cache-Quelle und vermeidet redundante Head-DB-Abfragen. |
| 🎨 style | Header / Logo | `assets/css/header-navigation.css`, `assets/js/navigation.js` und `includes/theme-assets-trait.php` binden die Logo-Höhe konsequent an `header.logo_max_height`; beim Scrollen schrumpft der Logo-/Suchbereich auf 75% der normalen Höhe. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.27` synchronisiert. |
| 🔴 fix | Theme Assets / Homepage | `includes/theme-assets-trait.php` deklariert die Homepage-Lead-Image-Abfrage jetzt mit `FROM posts p` und nutzt `phinit_post_publication_where('p')`, sodass der locale-aware Filter aus `phinit_build_homepage_post_locale_condition()` nicht mehr mit `Unknown column 'p.content'` im PHP Error-Log scheitert. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json` und `README.md` wurden auf Version `1.5.26` synchronisiert. |
| 🟢 feat | Theme Editor / Header | `admin/customizer-schema.php`, `theme.json` und `header.php` ergänzen die neue Header-Option `show_login_button`, mit der sich der komplette Login-/Account-Button im Desktop-Header sowie der mobile Login-Link gezielt ausblenden lassen. |
| 🎨 style | Post/Page Detail | `assets/css/post-detail.css` und `assets/css/page-detail.css` blenden das Headerbild auf Beitrags- und Seitendetailseiten bei `≤ 768px` aus, damit der mobile Content-Header kompakter startet und Titel/Meta sofort sichtbar bleiben. |
| 🎨 style | Mobile Header/Home | `assets/css/header-navigation.css` zeigt das Theme-Logo im mobilen Header wieder links an und begrenzt Bildlogos auf maximal `30px` Höhe; `style.css` reduziert den Abstand zwischen Header und dem ersten Homepage-Band („Aktuelle Beiträge“) mobil auf `10px`. |
| 🎨 style | Post Detail / Share | `assets/css/post-detail.css` ordnet die Teilen-Buttons auf Beitragsdetailseiten in der Mobileansicht jetzt immer in einem festen 3er-Raster an, sodass sechs Share-Aktionen als zwei Reihen mit je drei Buttons erscheinen. |
| 🎨 style | Post Sidebar / Social | `partials/sidebar.php` und `assets/css/post-sidebar.css` verteilen die aktiv konfigurierten Social-Buttons in der Desktop-Sidebar jetzt über die volle Widget-Breite; die Spaltenzahl richtet sich dynamisch nach der Anzahl der eingerichteten Netzwerke. |
| 🎨 style | Post Sidebar / Tags | `assets/css/post-sidebar.css` rendert das Tags-Widget in der Desktop-Sidebar jetzt als zweispaltiges Grid mit dezenten, eckigen Badge-Links; die Tag-Namen bleiben einzeilig ohne Umbruch und werden vor dem rechten Rand zuverlässig per Ellipsis (`...`) gekürzt. |

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
