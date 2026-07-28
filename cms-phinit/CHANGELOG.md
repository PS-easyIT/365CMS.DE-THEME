# CMS PHINIT Theme – Changelog

## 📋 Legende

| Symbol | Typ | Bedeutung |
|--------|-----|-----------|
| 🟢 | `feat` | Neues Feature |
| 🔴 | `fix` | Bugfix |
| 🟡 | `refactor` | Code-Umbau ohne Funktionsänderung |
| 🟠 | `perf` | Performance-Verbesserung |
| 🎨 | `style` | Design- / UI-Änderungen |
| 🔵 | `docs` | Dokumentation |
| 🛡️ | `security` | Sicherheits- / Hardening-Maßnahme |

---

## v1.7.22 — 28. Juli 2026

### Zitat-Block: abgeschwächter Balken + 3 neue Designs, Trennstrich-Dicke-Fix

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | EditorJS / Zitat-Block | `assets/css/rich-content.css` gestaltet den Standard-Balken des Zitat-Blocks ("Balken"-Design) dünner und abgeschwächt (Farb-/Hintergrundmischung über `color-mix()` mit Theme-Variablen statt vollgesättigter Akzentfarbe auf Weiß) und bleibt dadurch auch im Dark Mode stimmig. |
| 🟢 feat | EditorJS / Zitat-Block | `assets/css/rich-content.css` ergänzt drei neue Zitat-Designs — **Karte**, **Minimal** und **Anführungszeichen** — zusätzlich zum bestehenden Balken-Design; auswählbar direkt über die Block-Einstellungen im Editor (Core: `CmsQuoteTool` in `editor-init.js`). |
| 🔴 fix | EditorJS / Trennstrich | `assets/css/rich-content.css` respektiert jetzt die im Editor gewählte Liniendicke des Trennstrich-Blocks (`--cms-editorjs-delimiter-thickness`) statt sie hart auf `1px` zu setzen. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.7.22` und Core-Teststand `3.3.76` synchronisiert. |

---

## v1.7.21 — 18. Juli 2026

### Kategorie-Badge bündig im Listcard-Vorschaubild

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | Startseite / Artikel-Listcards | `assets/css/homepage-blog.css` und `homepage-blog-critical.css` positionieren das türkise Kategorie-Badge ohne Abstand bei `top: 0; left: 0`; Rundung, weißer Rand und Schatten wurden entfernt, sodass die Kachel bündig am Vorschaubildrand anschließt. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.7.21` und Core-Teststand `3.3.71` synchronisiert. |

---

## v1.7.20 — 18. Juli 2026

### Dunkler Goldton für den Update-Badge-Text

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | Beitragsdetail / Update-Badge | `assets/css/post-detail.css` verwendet für `[UPDATE | dd.MM.YY]` den dunklen Goldton `#8a5a05`. Im Dark Mode erhält die kantige Badge-Fläche einen warmen hellen Hintergrund, damit der dunkle Text lesbar bleibt. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.7.20` und Core-Teststand `3.3.70` synchronisiert. |

---

## v1.7.19 — 18. Juli 2026

### Kantige Header-Badges direkt am Bildrand

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | Beitragsdetail / Headerbild | `assets/css/post-detail.css` setzt die Lesezeit als kantige Kachel ohne Schatten bei `top: 0; left: 0`; das goldfarbene `[UPDATE | dd.MM.YY]`-Badge liegt ohne Abstand bei `right: 0; bottom: 0`. Die Positionen gelten auch auf Tablet und Mobilgeräten. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.7.19` und Core-Teststand `3.3.69` synchronisiert. |

---

## v1.7.18 — 18. Juli 2026

### Lesezeit links, Update-Badge rechts im Beitrags-Header

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Beitragsdetail / Headerbild | `assets/css/post-detail.css` positioniert die Lesezeit wieder fest oben links. Das optionale `[UPDATE]`-Badge nutzt die rechte Seite desselben Overlays und bleibt auch ohne vorhandene Lesezeit rechts ausgerichtet. |
| 🎨 style | Beitragsdetail / Update-Badge | Der `[UPDATE]`-Text verwendet im hellen und dunklen Headerbild den Theme-Goldton `--accent-color`. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.7.18` und Core-Teststand `3.3.68` synchronisiert. |

---

## v1.7.17 — 18. Juli 2026

### Manuelles Update-Badge im Beitrags-Content-Header

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🟢 feat | Beitragsdetail / Headerbild | `partials/post-header.php` prüft das optionale Feld `content_updated_at` und zeigt bei gefülltem Aktualisierungsdatum `[UPDATE]` direkt rechts neben der Lesezeit im Headerbild. Standard-, Wide- und Tech-Beitragslayouts nutzen dieselbe Partial und erhalten die Anzeige automatisch. |
| 🎨 style | Beitragsdetail / Headerbild | `assets/css/post-detail.css` bündelt Lesezeit und Update-Badge in einer Leiste oben rechts im Content-Headerbild; das Badge bleibt bewusst dezent und besitzt passende Dark-Mode-Kontraste. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.7.17` und Core-Teststand `3.3.67` synchronisiert. |

---

## v1.7.16 — 06. Juli 2026

### Passende Icons für Website- und Social-Media-Einträge im öffentlichen Autorenprofil

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🟢 feat | Public / Autorenprofil | Neue Funktion `phinit_social_platform_icon()` in `includes/theme-template-helpers.php` erkennt die Plattform (LinkedIn, X/Twitter, Facebook, Instagram, YouTube, GitHub, GitLab, Xing, TikTok, Threads, Bluesky, Pinterest, Discord, Telegram, Mastodon) anhand des Hosts einer Social-URL und liefert ein passendes Emoji-Icon. |
| 🟢 feat | Public / Autorenprofil | `author.php` zeigt vor dem Website-Link jetzt 🌐 und vor dem Social-Link das erkannte Plattform-Icon an, statt nur reinen Linktext. |
| 🟢 feat | Public / Autoren-Übersicht | `authors.php` zeigt auf der Autoren-Karte ebenfalls das Website-Icon vor dem Link. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.7.16` synchronisiert. |

---

## v1.7.15 — 06. Juli 2026

### „Über mich“ nutzt den Block-Editor + saubere Bio-Darstellung auf Autorenseiten

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Member / Profil | `member/profile.php` rendert das Feld „Über mich“ jetzt über `\CMS\Services\EditorService::render()` (EditorJS-Block-Editor) statt eines einfachen `<textarea>` — das Theme hat den Member-Profilbereich als eigenes Template implementiert und überschrieb damit bislang die Core-Editor-Integration aus 365CMS 3.3.61. |
| 🔴 fix | Public / Autorenseite | `author.php` gibt die Biografie jetzt über `EditorService::renderContent()` als formatiertes HTML aus statt sie nur `htmlspecialchars()`-escaped als Rohtext (inkl. sichtbarem Editor.js-JSON) anzuzeigen. |
| 🔴 fix | Public / Autoren-Übersicht | `authors.php` nutzt für die Bio-Vorschau auf der Autoren-Karte jetzt `phinit_excerpt_plain_text()` und kürzt auf 160 Zeichen, statt rohes Editor.js-JSON unverarbeitet auszugeben. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.7.15` synchronisiert. |

---

## v1.7.14 — 05. Juli 2026

### Individueller Seiten-Titel für jede HubSite + Reihenfolge „Sitename – Spezifischer Titel“

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | SEO / `<title>` | `getCurrentHeadPage()` in `includes/theme-head-trait.php` prüfte bislang nur `PageManager::getPageBySlug()`. HubSites (verwaltet über `SiteTableService`) wurden dabei nie gefunden, wodurch `filterPageTitle()` für JEDE HubSite auf den generischen Fallback-Titel zurückfiel – alle HubSites zeigten denselben Browser-Titel. Neuer Fallback auf `SiteTableService::getHubPageBySlug()` behebt das. |
| 🟡 refactor | SEO / `<title>` | `filterPageTitle()` liefert die Titel-Reihenfolge jetzt konsistent als „{Sitename} – {Spezifischer Titel}“ (Beitrag, Seite, HubSite, Kategorie/Tag-Archiv, Member-Bereich, Blog, Suche) statt umgekehrt. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.7.14` synchronisiert. |

---

## v1.7.13 — 05. Juni 2026

### Kontextabhängiger „… bearbeiten“-Direktlink für Detailseiten, Beiträge und HubSites

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🟢 feat | Header / Member-Bar | `includes/theme-template-helpers.php` erweitert `phinit_get_member_edit_link()` um einen priorisierten Kontext-Fallback über die aktuell geladene Seite bzw. den aktuell geladenen Beitrag (`$GLOBALS['page']` / `$GLOBALS['post']`). Damit erscheint der Bearbeiten-Link zuverlässig direkt auf Detailseiten, Beiträgen und HubSites. |
| 🔴 fix | Routing / Edit-Resolver | Der Seiten-Fallback nutzt jetzt `\CMS\PageManager::instance()->getPageBySlug(..., $locale)` statt einer reinen `slug`-DB-Abfrage, sodass lokalisierte Seiten-Aufrufe konsistenter auf den passenden Admin-Editor aufgelöst werden. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.7.13` synchronisiert. |

---

## v1.7.12 — 05. Juni 2026

### Member-Bar visuell überarbeitet (flache Schwarz/Weiß-Icons + Trenner)

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | Header / Member-Bar | `header.php` ersetzt die bisherigen Emoji-Icons durch monochrome SVG-Line-Icons für Bearbeiten, Dashboard, Profil, Benachrichtigungen, Favoriten, Sicherheit, RSS und Logout. |
| 🎨 style | Header / Navigation | `assets/css/header-navigation.css` richtet die Member-Bar-Links ruhiger aus, setzt flache Icon-Geometrien und ergänzt dezente vertikale Trenner zwischen den Menüpunkten. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.7.12` synchronisiert. |

---

## v1.7.11 — 05. Juni 2026

### EditorJS Bild+Text vertikal bündig

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | EditorJS / Bild+Text | `assets/css/rich-content.css` übernimmt die neue Core-Vertikalausrichtung `top`, `center` und `bottom` für Text+Bild-Blöcke in das PHINIT-Grid-Layout, damit der Text oben, mittig oder unten bündig zum Bild sitzt. |
| 🎨 style | EditorJS / Mobile | Mobile Text+Bild-Ausgaben bleiben trotz gespeicherter Vertikalausrichtung einspaltig, vollbreit und sauber gestapelt. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.7.11` synchronisiert und für Core `3.3.47` freigegeben. |

---

## v1.7.10 — 05. Juni 2026

### HubSite-TOC mit stabilem Dark Mode

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | HubSites / TOC Dark Mode | `assets/css/hubsite.css` setzt für HubSite-Inhaltsverzeichnisse eigene Dark-Mode-Tokens bei `body.dark-mode` und `html.dark-mode body`, damit lokale HubSite-Profilvariablen keine hellen TOC-Flächen mehr erzwingen. |
| 🎨 style | HubSites / TOC-Kontrast | TOC-Container, Titel, Labels, Pfeilmarker, Summary-Icon, Hover- und Empty-Zustände nutzen dunkle Flächen mit hellen Texten und goldenen Akzenten. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.7.10` synchronisiert. |

---

## v1.7.9 — 05. Juni 2026

### Dark-Mode-Kontrast für Startseite, Details und HubSites

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Startseite / Dark Mode | `assets/css/homepage-blog.css` und `assets/css/homepage-blog-critical.css` wenden Featured-Banner-, Sidebar- und Hover-Kontraste jetzt auch bei `html.dark-mode` an. |
| 🔴 fix | Detailseiten / TOC & Kommentare | `assets/css/post-sidebar.css`, `assets/css/post-detail.css`, `assets/css/page-detail.css` und `assets/css/ui-chrome.css` setzen aktive Inhaltsverzeichnis-Links hell und ziehen About-Me/Autorbox sowie Kommentarbereich auf dunkle Flächen mit hellen Texten. |
| 🔴 fix | HubSites / Button-Hover | `assets/css/hubsite.css` verhindert Gold-auf-Gold im Dark-Mode-Hover und nutzt dunklen Text auf goldener Buttonfläche. |
| 🔵 docs | Release | `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.7.9` synchronisiert. |

---

## v1.7.8 — 05. Juni 2026

### EditorJS Hinweisbox-Kontrast im Titelband und Body

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | EditorJS / Hinweisboxen | `assets/css/rich-content.css` setzt den Text im dunklen Titelbalken der Info-, Warn-, Erfolgs- und Kritisch-Boxen konsequent hell. |
| 🔴 fix | EditorJS / Dark Mode | Die helle Inhaltsfläche der Hinweisboxen behält auch im Dark Mode dunkle variantenspezifische Text- und Linkfarben, statt auf helle Dark-Mode-Texttokens umzuschalten. |
| 🔵 docs | Release | `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.7.8` synchronisiert. |

---

## v1.7.7 — 05. Juni 2026

### EditorJS Public-Hinweisboxen und Spacer-Kompatibilität

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | EditorJS / Hinweisboxen | `assets/css/rich-content.css` rendert Info-, Warn-, Erfolgs- und Kritisch-Boxen im Public-Frontend mit farbigem Titelbalken und heller Inhaltsfläche passend zur Variante. |
| 🔴 fix | EditorJS / Spacer | PHINIT ist auf Core `3.3.45` getestet; die Core-Fallbacks erhalten EditorJS-Spacer mit `10px`, `100px` und `150px` im Public-Rendering auch dann, wenn Theme-/Sanitizer-Pfade Inline-Styles reduzieren. |
| 🔵 docs | Release | `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.7.7` synchronisiert. |

---

## v1.7.6 — 30. Mai 2026

### M365-Linkcollection in der Startseiten-Sidebar

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🟢 feat | Startseiten-Sidebar | PHINIT kennt jetzt den Sidebar-Widget-Key `linkcollection` und rendert bei aktivem `cms-m365linkcollection` Plugin ein rotierendes Link-Widget mit Bild oder Platzhalter. |
| 🔵 docs | Release | Theme-Metadaten und Manifest wurden auf Version `1.7.6` synchronisiert. |

---

## v1.7.5 — 30. Mai 2026

### Theme-Customizer ohne Unknown-Setting-Warnungen

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Theme-Customizer / Schema | `theme.json` enthält jetzt alle im PHINIT-Admin-Customizer sichtbaren Settings aus Farben, Header, Footer, Startseite, Seiten und Memberdashboard. Dadurch stuft der Core diese Felder beim Speichern nicht mehr als unbekannte Theme-Customizer-Keys ein. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.7.5` synchronisiert. |

---

## v1.7.4 — 25. Mai 2026

### Featured-Bildhöhe steuert jetzt wirklich das Bild

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | HubSites / Featured-Kacheln | `assets/css/hubsite.css` entkoppelt die Einstellung `--hubsite-feature-image-height` von Text- und Card-Stretch. Die Höhe liegt jetzt direkt auf Medienfeld und Bild; das Bild skaliert mit `height: 100%`, `width: auto`, `max-width: 100%` und `object-fit: contain` mittig im konfigurierten Bildbereich. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.7.4` synchronisiert. |

---

## v1.7.3 — 25. Mai 2026

### Featured-Bilder ohne Zoom mittig eingepasst

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | HubSites / Featured-Kacheln | `assets/css/hubsite.css` zieht Featured-Bildfelder nicht mehr über die komplette Card-Höhe. Das Medienfeld nutzt die eingestellte `--hubsite-feature-image-height`, sitzt vertikal mittig in der Kachel und zeigt das Bild per `object-fit: contain` mit `object-position: center center` vollständig und zentriert statt gezoomt. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.7.3` synchronisiert. |

---

## v1.7.2 — 25. Mai 2026

### Featured-Bilder mittig und mit steuerbarer Höhe

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | HubSites / Featured-Kacheln | `assets/css/hubsite.css` richtet Featured-Bilder per `object-position: center center` mittig aus und nutzt `--hubsite-feature-image-height` als Mindesthöhe der Featured-Kachel. Zusammen mit `--hubsite-feature-image-width` werden Höhe und Breite aus den HubSite-/Template-Einstellungen sichtbar umgesetzt. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.7.2` sowie Core-Teststand `3.3.40` synchronisiert. |

---

## v1.7.1 — 25. Mai 2026

### Kachel-Schema und Card-Layout sichtbar umgesetzt

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | HubSites / Kacheln pro Reihe | `assets/css/hubsite.css` wertet `hubsite-grid--cols-1/2/3` und `cms-hub-site__grid--cols-1/2/3` jetzt vollständig aus. Dadurch bleiben 1er-Layouts auch ab Tablet einspaltig, 2er-Layouts am Desktop zweispaltig und 3er-Layouts dreispaltig. |
| 🔴 fix | HubSites / Card-Layout | `assets/css/hubsite.css` macht `feature` und `compact` sichtbar: Feature-Karten nutzen bei Bildposition links/rechts eine echte Side-by-Side-Struktur mit prozentualem Bildbereich; Compact-Karten erhalten reduzierte Abstände und kleinere Meta-/Summary-Typografie. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.7.1` sowie Core-Teststand `3.3.39` synchronisiert. |

---

## v1.7.0 — 25. Mai 2026

### Featured-Kacheln mit steuerbarer Bildbreite

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | HubSites / Featured-Kacheln | `assets/css/hubsite.css` nutzt `--hubsite-feature-image-width` für die Desktop-Breite des Bildbereichs in Featured-Kacheln. Das Bildfeld streckt sich über die volle Kachelhöhe und cropt sauber per `object-fit: cover`, statt mit fixer 200px-Spalte zu arbeiten. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.7.0` sowie Core-Teststand `3.3.38` synchronisiert. |

---

## v1.6.9 — 25. Mai 2026

### Tabellen-HubSites mit höherem Titelband

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | HubSites / Tabellen-Cards | `assets/css/hubsite.css` setzt Table-Card-Titelbänder auf mindestens 60 px Höhe. Titel und verlinkte Titel werden im Band vertikal zentriert und behalten sauberen Umbruch, damit die Tabellen-HubSites optisch ruhiger und näher an den Datenschutz-Karten wirken. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.6.9` synchronisiert. |

---

## v1.6.8 — 25. Mai 2026

### Anpassbarer HubSite-Card-Reihenabstand

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | HubSites / Card-Abstände | `assets/css/hubsite.css` nutzt `--hubsite-card-row-gap` für den vertikalen Abstand zwischen HubSite-Card-Reihen. Der Fallback liegt bei 30 px und verbessert insbesondere den Abstand zwischen Full-Width-Cards und den darunterliegenden mehrspaltigen Cards. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.6.8` sowie Core-Teststand `3.3.37` synchronisiert. |

---

## v1.6.7 — 25. Mai 2026

### HubSite-Cards mit gleicher Reihenhöhe

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | HubSites / Cards | `assets/css/hubsite.css` stellt HubSite-Grids und Section-Grids auf Stretching um. Karten, Tabellenkarten, Datenschutz-Karten und Section-Cards enden dadurch innerhalb einer Reihe bündig auf Höhe der höchsten Card. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.6.7` synchronisiert. |

---

## v1.6.6 — 25. Mai 2026

### Datenschutz-HubSite Titelband unter Top-Bildern

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | HubSites / Datenschutz | `assets/css/hubsite.css` setzt bei Datenschutz-HubSite-Karten mit `image-top` das Titelband direkt unter das Bild. Das Band läuft per Full-Bleed von linkem bis rechtem Card-Rand und wird vor Badge/Content einsortiert. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.6.6` synchronisiert. |

---

## v1.6.5 — 25. Mai 2026

### HubSite-Autorenbox CTA-Position

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | HubSites / Autorenbox | `assets/css/hubsite.css` platziert den Dienstleistungsbutton der HubSite-Autorenbox in der rechten unteren Ecke der Service-Card. Der Button nutzt dafür das bestehende Flex-Layout und betrifft nur `.hubsite-author-box`. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.6.5` synchronisiert. |

---

## v1.6.4 — 25. Mai 2026

### Optionale HubSite-Autorenbox mit Dienstleistungsbereich

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| ✨ feature | HubSites / Autorenbox | `page.php` rendert unter Core-HubSites optional die bestehende PHINIT-Autorenbox, wenn `hub_show_author_box` in der jeweiligen HubSite aktiviert ist. Die Inhalte kommen aus den Beitrags-Autorenbox-Einstellungen. |
| 🎨 style | HubSites / Dienstleistungsbereich | `assets/css/hubsite.css` passt die Autorenbox optisch an HubSites an: helle Card, kompakte Autoren-Spalte und dunkler Dienstleistungsbereich mit Gold-CTA. |
| 🔵 refactor | Authorbox-Partial | `partials/post-author-box.php` und `includes/theme-template-helpers.php` unterstützen eine optionale Modifier-Klasse, ohne die bestehende Beitrags-/Seiten-Ausgabe zu verändern. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.6.4` sowie Core-Teststand `3.3.36` synchronisiert. |

---

## v1.6.3 — 25. Mai 2026

### Persistenter PHINIT Dark Mode für HubSites

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| ✨ feature | Dark Mode / Head Init | `assets/js/theme-init.js` liest den localStorage-Key `phinit_theme` bereits im `<head>` und setzt `html.dark-mode` vor dem ersten Paint. Alte Keys `cms365-theme` und `cms-phinit-theme` werden einmalig migriert. |
| ✨ feature | Dark Mode / Toggle | `header.php` und `assets/js/navigation.js` verwenden einen zustandsfähigen Button mit `data-dark-toggle`, speichern `dark`/`light` in `phinit_theme` und wechseln das Icon zwischen `☾` und `☀`. |
| 🎨 style | HubSites / Dark Tokens | `assets/css/hubsite.css` ergänzt die PHINIT Dark-Mode-Palette über einen zentralen `html.dark-mode` Token-Override, inklusive dunkler Cards, Hero-Hintergrund `#0A1020`, Card-Header `#1E2F48`, Gold-Akzent und 200ms-Farbtransitionen. |
| 🎨 style | HubSites / Inhaltsverzeichnis | `assets/css/hubsite.css` stellt das HubSite-TOC kompakt als Pfeilliste dar, ohne breite Button-Kacheln oder unnötige vertikale Abstände. |
| 🔴 fix | HubSites / Dark-Mode-Tabellen | `assets/css/hubsite.css` ergänzt eigene Stripe-/Hover-Texttokens für Tabellenzeilen, damit hellere/dunklere Dark-Mode-Streifen mit passender Textfarbe gerendert werden. |
| 🔴 fix | Dark Mode / Light Persistenz | `style.css` verhindert, dass die alte `prefers-color-scheme: dark`-Regel einen explizit gespeicherten hellen Modus übersteuert. |

---

## v1.6.2 — 25. Mai 2026

### HubSite-Table-Cards und Datenschutz-Beschreibung nachgeschärft

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | HubSites / Tabellenkarten | `assets/css/hubsite.css` entfernt die letzten linken/rechten Innen- und Zellränder aus Tabellen innerhalb von HubSite-Cards. Die Card ist damit wieder der einzige äußere Rahmen. |
| 🎨 style | HubSites / Table-Header | Table-Card-Header verwenden nur noch den Header-Container als Navy-Fläche; der verschachtelte `h3`-Titel wird zurückgesetzt. Dadurch halbiert sich die Headerhöhe ohne kleinere Schriftgröße. |
| 🔴 fix | HubSites / Datenschutz-Hero | Theme-Teststand auf Core `3.3.35` aktualisiert: Der Core rendert HubSite-Hero-Beschreibungen nun auch über die Template-Profil-Beschreibung, falls kein expliziter Hero-Text/Tabellenbeschreibung vorhanden ist. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.6.2` sowie Core-Teststand `3.3.35` synchronisiert. |

---

## v1.6.1 — 25. Mai 2026

### HubSite-Designsystem neu und kontrastfest ausgerichtet

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🟡 refactor | HubSites / BEM-CSS | `assets/css/hubsite.css` ist die neue gemeinsame HubSite-CSS-Datei für alle Varianten. Sie nutzt die BEM-Klassen `hubsite`, `hubsite-grid`, `hubsite-card`, `hubsite-card__header`, `hubsite-card__cta`, `hubsite-card--featured` und `hubsite-hero`. |
| 🎨 style | HubSites / Designsystem | Das gewünschte Designsystem wird strikt umgesetzt: Navy-Header, weißer Card-Body, Gold-Akzent links, Poppins-Headings, System-Body-Font, `8px` Cards, `4px` Buttons und dezente Card-Shadows. |
| 🔴 fix | HubSites / Lesbarkeit | Ein finaler Designsystem-Lock verhindert Dark-on-Dark-Zustände. Cards, Listen, Tabellen, Beschreibungen und Pagination bleiben auch im Dark Mode hell mit `#1B2A4A`/`#5A6A84`; nur Hero und Card-Header sind dunkel mit weißem Text. |
| 🎨 style | HubSites / Layout | Grid-Breakpoints folgen dem Vertrag: Mobile `1fr`, Tablet ab `600px` zwei Spalten, Desktop ab `1025px` drei Spalten. Featured Cards brechen unter `900px` sauber von Bild-links auf Bild-oben. |
| 🎨 style | HubSites / Tabellen & Pagination | Eingebettete SiteTables in Cards verlieren doppelte Außenränder, bekommen lesbare Table-Meta-Bereiche, saubere Link-Hover-Zustände und gestaltete Pagination-Buttons. |
| 🟡 refactor | HubSites / Legacy-CSS | `assets/css/hub-sites.css` enthält keine alten widersprüchlichen Layer mehr, sondern verweist als Kompatibilitäts-Shim auf `assets/css/hubsite.css`. |
| 🔵 docs | HubSites / Beispiele | `HUBSITE-BEM.md` dokumentiert die PHP-kompatible HTML-Struktur für Default Card, Featured Card, Hero und Grid. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.6.1` sowie Core-Teststand `3.3.34` synchronisiert. |

---

## v1.6.0 — 25. Mai 2026

### HubSite-Templates vollständig auf PHINIT-UI normalisiert

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | HubSites / alle Templates | `assets/css/hub-sites.css` ergänzt einen finalen Template-QA-Layer für `general-it`, `services`, `general-table`, `microsoft-365`, `m365-table`, `powershell-table`, `datenschutz`, `compliance`, `datenschutz-compliance-table`, `linux` und `linux-table`. |
| 🎨 style | HubSites / Typografie | Hero-, Section-, Card-, Feature-Card- und Table-Texte wurden auf PHINIT-kompatible Größen, Zeilenhöhen und Textbreiten normalisiert. |
| 🐞 fix | HubSites / Beschreibungen | Beschreibungen in Cards, Sections, Feature-Cards und eingebetteten SiteTables werden wieder sichtbar und erhalten korrekte Light-/Dark-Mode-Farben. |
| 🎨 style | HubSites / Tabellen | Tabellen in Table-Cards nutzen die umgebende Card als Rahmen, entfernen doppelte linke/rechte Innenränder und erhalten kompakte Zellen, dezente Header und saubere Meta-Beschreibungen. |
| 🎨 style | HubSites / Layout | Grids, Sections und Cards wurden gegen künstliche Höhen, gestreckte Karten und unpassende Profil-Spezialstyles abgesichert. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.6.0` synchronisiert. |

---

## v1.5.99 — 25. Mai 2026

### HubSite-Buttons und Tabellen kompakter korrigiert

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | HubSites / Buttons | `assets/css/hub-sites.css` setzt CTA- und Card-Buttons im Light- und Dark-Mode auf kontraststarken weißen Text mit PHINIT-Navy-Hintergrund. |
| 🎨 style | HubSites / Tabellen | Tabellenzellen, Tabellenkarten und Footer-Bereiche wurden verdichtet, damit Listen und SiteTables nicht mehr unnötig viel Fläche einnehmen. |
| 🎨 style | HubSites / Grid-Höhen | HubSite-Grids, Sections und Cards verwenden nun `align-items: start` und keine künstliche volle Kartenhöhe mehr; dadurch verschwinden große Leerflächen in kurzen Karten neben längeren Tabellenlisten. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.99` synchronisiert. |

---

## v1.5.98 — 25. Mai 2026

### HubSite-UX auf professionelle Proportionen korrigiert

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | HubSites / UX-Proportionen | `assets/css/hub-sites.css` entschärft den PHINIT-HubSite-Design-Layer: Hero-Titel, Header-Flächen, Abstände, Cards, Feature-Cards und Tabellenköpfe skalieren jetzt kompakt statt übergroß. |
| 🎨 style | HubSites / Profi-UI | Meta-Chips, Quicklinks, TOC, Sections und Card-Inhalte verwenden ruhigere Typografie, kleinere Paddings, dezente Schatten und minimalere Hover-Zustände für ein professionelles Dashboard-/HubSite-Gefühl. |
| 🎨 style | HubSites / Mobile | Mobile HubSites wurden auf kleinere Header-Titel und kompakte Inhaltsflächen korrigiert, damit keine riesigen Landingpage-Texte mehr entstehen. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.98` synchronisiert. |

---

## v1.5.97 — 25. Mai 2026

### HubSite-Design vollständig auf CMS-PHINIT gezogen

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | HubSites / PHINIT-Design-Layer | `assets/css/hub-sites.css` enthält am Dateiende einen dominanten HubSite-Design-Layer, der Core-/Profil-Fallbacks gezielt nur innerhalb von `.page-content--hub .cms-hub-site` überstimmt. |
| 🎨 style | HubSites / Content Header | HubSite-Hero, Content-Header, Meta-Chips, CTA, Quicklinks und TOC verwenden jetzt durchgehend PHINIT-Navy, Gold-/Teal-Akzente, Theme-Radien, dezente Schatten und PHINIT-Typografie. |
| 🎨 style | HubSites / Cards & Tabellen | Sections, Cards, Feature-Cards, Card-Medien, Footer-Meta und SiteTable-Content-Header wurden auf PHINIT-Flächen, Borders, Hover-Zustände, Dark Mode und responsive Layouts nachgezogen. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.97` synchronisiert. |

---

## v1.5.96 — 25. Mai 2026

### HubSite-Assets und M365-/Datenschutz-Design stabilisiert

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | HubSites / Asset Loading | `includes/theme-assets-trait.php` erkennt HubSite-Anfragen jetzt über den Core-Hub-Router inklusive lokalisierter Pfade und Hub-Domain-Startseite. Dadurch laden `hub-sites.css`, `rich-content.css` und die Template-Styles auch dann, wenn die Seite im Head noch nicht als `$GLOBALS['page']` verfügbar ist. |
| 🔴 fix | HubSites / Body Class | `includes/theme-head-trait.php` nutzt dieselbe robuste HubSite-Erkennung, sodass `is-hub-site` und Head-Kontext mit dem tatsächlich gerenderten Hub übereinstimmen. |
| 🎨 style | HubSites / M365 & Datenschutz | `assets/css/hub-sites.css` zieht Microsoft-365-, `m365-table`-, Datenschutz- und Datenschutz-Compliance-Templates auf PHINIT-/Customizer-nahe Flächen, Akzente, Tabellen und Karten nach, statt auf Core- oder alte Profil-Fallbacks zu fallen. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.96` sowie Core-Teststand `3.3.33` synchronisiert. |

---

## v1.5.95 — 25. Mai 2026

### EditorJS-H4 und unnummerierte TOC-Marker

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | EditorJS / Text+Bild | `assets/css/rich-content.css` behandelt `.editorjs-media-text__heading` im PHINIT-Frontend jetzt wie eine H4-Überschrift und überschreibt ältere Core-Inline-Reste robust auf den H4-Maßstab. |
| 🎨 style | Inhaltsverzeichnis | `partials/page-inline-toc.php`, `assets/css/page-detail.css` und `assets/css/templates.css` rendern Seiten- und Inline-TOCs als ungeordnete Listen mit Punkten, Pfeilen und dezenteren Markern je Einrückungsebene statt nummerierter Listen. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.95` sowie Core-Teststand `3.3.32` synchronisiert. |

---

## v1.5.94 — 25. Mai 2026

### HubSites an PHINIT- und Customizer-Design angeglichen

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | HubSites / Customizer | `assets/css/hub-sites.css` führt Hero, Quicklinks, TOC, Karten, Feature-Cards, Buttons und SiteTables auf PHINIT-Design-Tokens und Theme-Customizer-Variablen zurück. Harte Profilfarben wurden durch Akzent-Variablen ersetzt; Sonderprofile wie Services, Microsoft 365, Datenschutz, PowerShell und Linux setzen nur noch Akzente statt eigene Flächenwelten zu erzwingen. |
| 🎨 style | HubSites / Tabellen & Responsive | HubSite-SiteTables nutzen jetzt PHINIT-Kartenradius, dezente Schatten, Customizer-konforme Tabellenköpfe, konsistente Zeilen-/Pagination-Flächen und horizontales Overflow statt abgeschnittener breiter Tabellen. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.94` sowie Core-Teststand `3.3.31` synchronisiert. |

---

## v1.5.93 — 25. Mai 2026

### EditorJS Text+Bild-Titel an H3 angeglichen

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | EditorJS / Text+Bild | `assets/css/rich-content.css` behandelt `.editorjs-media-text__heading` im PHINIT-Frontend wie eine H3-Überschrift und überschreibt alte Core-Inline-Reste robust auf den H3-Maßstab. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.93` sowie Core-Teststand `3.3.29` synchronisiert. |

---

## v1.5.92 — 25. Mai 2026

### Core-Header-TOC für Seiten kompatibel gemacht

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🟢 feat | Seiten / Header-TOC | `page.php` respektiert die neue Core-Seitenoption `show_title_toc` für ein eingeklapptes Inhaltsverzeichnis direkt unter dem Seitentitel. |
| 🔴 fix | Seiten / TOC-Duplikate & Anker | Ist das Core-Header-TOC aktiv, erkennt PHINIT vorhandene `cms-page-title-toc`-Ausgaben zuverlässig, baut das TOC nach der finalen Heading-ID-Vergabe neu auf und unterdrückt das separate Customizer-gesteuerte Seiten-TOC. Dadurch entstehen keine doppelten TOCs und die Links springen wieder zu den passenden Seitenabschnitten. |
| 🔴 fix | Seiten / TOC-Scroll | `assets/js/navigation.js` lädt die Content-Interaktionen jetzt auch bei `.cms-page-title-toc`; `assets/js/content-interactions.js` behandelt `.cms-page-title-toc__link` mit Sticky-Header-Offset wie die bestehenden TOC-Links. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.92` synchronisiert. |

---

## v1.5.91 — 25. Mai 2026

### Metainfos im Seiten-Content-Header ergänzt

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🟢 feat | Seiten / Header | `partials/page-header-block.php` zeigt unterhalb des Seitentitels jetzt eine beitragsähnliche Meta-Zeile mit Aktualisierungs-/Veröffentlichungsdatum, Seitenautor und optionaler Lesedauer. |
| 🎨 style | Seiten / Meta | `assets/css/page-detail.css` gestaltet `.page-meta` analog zur Beitrags-Meta mit Trennlinie, Icons, Labels, Links, Dark-Mode-Kontrast und responsivem Umbruch. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.91` synchronisiert. |

---

## v1.5.90 — 25. Mai 2026

### Seiten-Autorenbox an Beitragsdesign angeglichen

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Seiten / Autorenbox | `page.php` baut die Autorenbox auf normalen Seiten jetzt mit den gleichen Customizer-Werten aus `Beiträge → Autorenbox & Navigation` wie Beitragsseiten auf. Separate Seiten-Autorenbox-Felder werden nicht mehr verwendet. |
| 🎨 style | Publicsite / Authorbox | `assets/css/ui-chrome.css` enthält die Authorbox als gemeinsame Cross-Route-Komponente, sodass das Card-Design inklusive Dienstleistungsbereich, Dark Mode und Responsive-Verhalten auch unter Seiteninhalten geladen wird. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.90` synchronisiert. |

---

## v1.5.89 — 25. Mai 2026

### Text+Bild-30px-Abstände gegen Core-Critical-CSS abgesichert

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Publicsite / Text+Bild | `assets/css/rich-content.css` setzt die gespeicherten Text+Bild-Abstände jetzt direkt als `margin-block-start/end` und klassische `margin-top/bottom` für `data-spacing-*` sowie die neuen Core-Klassen `editorjs-media-text--spacing-top-*` und `editorjs-media-text--spacing-bottom-*`. |
| 🔴 fix | Publicsite / Abstände | Dadurch überschreibt PHINIT das globale EditorJS-Critical-CSS (`.editorjs-block + .editorjs-block` / `:first-child`) zuverlässig, sodass ein gespeichertes `30px`-Preset nicht mehr als `0px` im Frontend endet. |
| 🔵 docs | Release | `assets/css/rich-content.css`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.89` und Core-Teststand `3.3.26` synchronisiert. |

---

## v1.5.88 — 25. Mai 2026

### Text+Bild-Abstände im Public-Frontend respektiert

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Publicsite / Text+Bild | `assets/css/rich-content.css` übernimmt `spacingTop` und `spacingBottom` für `.editorjs-media-text` jetzt über `--cms-editorjs-space-before`, `--cms-editorjs-space-after` und eigene PHINIT-Fallbackvariablen als `margin-block-start/end`. |
| 🔴 fix | Publicsite / Abstände | `data-spacing-top` und `data-spacing-bottom` setzen sichere Pixel-Fallbacks für alle EditorJS-Presets von `0` bis `100`, damit die gespeicherten Text+Bild-Abstände auch ohne erhaltene Inline-Styles greifen. |
| 🔵 docs | Release | `assets/css/rich-content.css`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.88` synchronisiert. |

---

## v1.5.87 — 25. Mai 2026

### Doppelte EditorJS-Normalisierung auf Seiten verhindert

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Publicsite / Seiten | `includes/theme-content-helpers.php` macht `phinit_prepare_renderable_content()` idempotent: Bereits vom Core-Router vorbereitetes EditorJS-Public-HTML wird nicht noch einmal durch `EditorService::renderContent()` geschickt. Dadurch werden fertige `.editorjs-media-text`-Blöcke auf Seiten nicht mehr in einzelne Bild- und Textblöcke zerlegt. |
| 🔴 fix | Publicsite / Text+Bild | Text+Bild-Blöcke bleiben auf Seiten im Live/Public-Bereich als Bild/Text-Layout erhalten, statt nach einer zweiten Normalisierung Bild oben und Text unten zu erscheinen. |
| 🔴 fix | Publicsite / Bildunterschriften | `includes/theme-content-helpers.php` entfernt dateinamenartige `<figcaption>`-Ausgaben im finalen Theme-HTML, damit Grafik-/Dateinamen auch bei bereits gerendertem oder importiertem Seiten-HTML nicht sichtbar bleiben. |
| 🔵 docs | Release | `includes/theme-content-helpers.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.87` synchronisiert. |

---

## v1.5.86 — 25. Mai 2026

### EditorJS-Seiteninhalte in Wide-/Landing-Templates korrigiert

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Publicsite / Seiten | `page-wide.php` und `page-landing.php` schicken Router-gelieferte Seiteninhalte jetzt ebenfalls durch `phinit_prepare_renderable_content()`. Damit werden EditorJS-Blöcke auf Vollbreite- und Landing-Seiten genauso gerendert wie in `page.php`, statt als schlecht formatierter Roh-/Fallback-Inhalt zu erscheinen. |
| 🔴 fix | Publicsite / Text+Bild | Text+Bild-Blöcke, Listen, Absätze und Inline-Formatierungen bleiben dadurch auch in speziellen Seitentemplates im Live/Public-Bereich erhalten. |
| 🔵 docs | Release | `page-wide.php`, `page-landing.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.86` synchronisiert. |

---

## v1.5.85 — 25. Mai 2026

### EditorJS-Text+Bild nach Sanitizer-Stufe stabil gerendert

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Publicsite / Text+Bild | `includes/theme-content-helpers.php` erkennt gerendertes EditorJS-/Rich-Content-Markup und nutzt dafür das strukturerhaltende Sanitizer-Profil. Dadurch bleibt die zentrale `.editorjs-media-text`-Klasse nach der finalen PHINIT-Sanitizer-Runde erhalten und Text+Bild-Blöcke werden im Live/Public-Bereich wieder als Desktop-Grid statt Bild-oben/Text-unten gerendert. |
| 🔴 fix | Publicsite / Bildunterschriften | In Kombination mit Core `3.3.24` werden Dateinamen aus der Mediathek nicht mehr als sichtbare Bildunterschriften im Public-Bereich ausgegeben. |
| 🎨 style | Publicsite / Rich Content | `assets/css/rich-content.css` gibt `.post-body` dieselben Basisabstände für Überschriften, Absätze, Listen, Tabellen und Blockquotes wie `.page-content`; außerdem steuern die Text+Bild-Klassen `image-width-*` und `image-fit-*` die Darstellung auch dann, wenn inline Styles durch Sanitizer entfernt wurden. |
| 🔵 docs | Release | `includes/theme-content-helpers.php`, `assets/css/rich-content.css`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.85` und Core-Teststand `3.3.24` synchronisiert. |

---

## v1.5.84 — 25. Mai 2026

### EditorJS-Rich-Content im Live/Public-Bereich zuverlässig geladen

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Publicsite / Asset Loading | `includes/theme-assets-trait.php` lädt `assets/css/rich-content.css` jetzt auch für HubSites und nicht eindeutig erkannte dynamische Detailrouten. Dadurch fallen EditorJS-Blöcke im Live/Public-Bereich nicht mehr auf unformatierte Core-Inline-Styles zurück. |
| 🔴 fix | Publicsite / Text+Bild | `assets/css/rich-content.css` enthält zusätzlich wrapper-unabhängige `.editorjs-media-text`-Regeln. Bild und Text bleiben damit auch außerhalb klassischer `.page-content`-/`.post-body`-Wrapper auf Desktop nebeneinander; Listen, Absätze und Inline-Formatierungen im Textbereich erhalten sichtbare Abstände und Listenmarker. |
| 🔵 docs | Release | `includes/theme-assets-trait.php`, `assets/css/rich-content.css`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.84` synchronisiert. |

---

## v1.5.83 — 25. Mai 2026

### EditorJS-Text+Bild-Blöcke im Public-Layout korrigiert

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Publicsite / Text+Bild | `assets/css/rich-content.css` rendert `.editorjs-media-text` im PHINIT-Frontend per Grid statt sich auf den inline gesetzten Flex-Wrap des Core-Renderers zu verlassen. Bild und Text bleiben dadurch auf Desktop nebeneinander; `image-right` und optionale Überschriften werden sauber abgebildet. |
| 🔴 fix | Publicsite / Bildskalierung | Text+Bild-Bilder nutzen jetzt `--cms-editorjs-media-text-image-fit` und respektieren damit die im Editor gespeicherte Skalierung wie `cover`, `contain`, `fill`, `scale-down` oder `none`. |
| 🔵 docs | Release | `assets/css/rich-content.css`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.83` und Core-Teststand `3.3.22` synchronisiert. |

---

## v1.5.82 — 24. Mai 2026

### Spezial-Templates für Microsoft 365, Windows und PowerShell

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🟢 feat | Beiträge / Templates | `theme.json` ergänzt die neuen Beitragstemplates `microsoft-365`, `windows` und `powershell`. Die Metafelder sind logisch als Zweiergruppen angelegt: Workload/Scope, Plattform/Version, Modul/Edition, Verwaltung/Ausführung, Zielsystem/Teststand sowie Voraussetzungen. |
| 🎨 style | Template-Meta-Card | `assets/css/templates.css` gestaltet die Zusatzkarte dezent mit hellem beziehungsweise dunklem Card-Hintergrund, zweispaltigem Grid, gleicher Feldhöhe und kleinen Abständen statt kräftigem Navy-Block. |
| 🟢 feat | Website- und GitHub-Links | `partials/post-template-meta-card.php` rendert URL-Felder wie Website, Dokumentation und GitHub als kompakte Icon-Links mit zugänglichem `aria-label`; leere oder ungültige URLs bleiben weiterhin ausgeblendet. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md`, `CHANGELOG.md` und `DOC/TEMPLATES.md` wurden auf Version `1.5.82` synchronisiert. |

---

## v1.5.81 — 24. Mai 2026

### EditorJS-Bilder und Bild-Text-Blöcke im Public-Layout stabilisiert

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Publicsite / EditorJS-Bilder | `assets/css/rich-content.css` übernimmt die Core-Deckelung für normale und breite EditorJS-Bildblöcke, damit Bilder im PHINIT-Frontend nicht größer als in der Editor-Vorschau erscheinen. |
| 🔴 fix | Publicsite / Bild + Text | `assets/css/rich-content.css` schirmt `.editorjs-media-text` gegen globale PHINIT-Regeln für `figure`, `figure > img` und `.post-body img` ab. Bild und Text bleiben dadurch auf Desktop sauber nebeneinander und stapeln mobil kontrolliert. |
| 🔵 docs | Release | `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.81` und Core-Teststand `3.3.5` synchronisiert. |

---

## v1.5.80 — 24. Mai 2026

### Seiten- und Beitragsinhalte serverseitig sichtbar

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Publicsite / Importseiten | `page.php`, `page-wide.php` und `page-landing.php` markieren die eigentlichen Content-Wrapper direkt mit `is-visible`. Dadurch bleiben importierte WordPress-Seiten wie `/organisationsprofil` auch dann lesbar, wenn das Scroll-Reveal-JavaScript nicht läuft oder alte CSS/JS-Assets im Browsercache liegen. |
| 🔴 fix | Publicsite / Beiträge | `post-wide.php` und `post-tech.php` erhalten denselben Fallback für verzögert animierte Artikelkörper, damit auch Template-Beiträge nicht transparent bleiben. |
| 🔵 docs | Release | `page.php`, `page-wide.php`, `page-landing.php`, `post-wide.php`, `post-tech.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.80` synchronisiert. |

---

## v1.5.79 — 24. Mai 2026

### Seiteninhalte ohne Reveal-JS sichtbar

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Publicsite / Seiteninhalt | `style.css` lässt zentrale `.page-content`-Bereiche sichtbar, selbst wenn die Scroll-Reveal-Initialisierung durch zwischengespeicherte oder fehlerhafte Assets nicht ausgeführt wird. Alte importierte WordPress-Seiten bleiben dadurch unterhalb des Content-Headers lesbar statt transparent zu bleiben. |
| 🔵 docs | Release | `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.79` synchronisiert. |

---

## v1.5.78 — 24. Mai 2026

### Site-Identity als Dienstleistungscard

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🟢 feat | Startseite / Sidebar | `theme.json`, `admin/customizer-schema.php` und `includes/theme-home-helpers.php` ergänzen die Site-Identity um konfigurierbare Dienstleistungsfelder für Kicker, Titel, Text, Logo/Bild, Alt-Text, Button und Ziel-URL. |
| 🟢 feat | Site-Identity / Publicsite | `partials/home-article-list.php` rendert unter Logo, Badge und Tagline optional eine Dienstleistungscard; Medien- und Link-Ziele laufen weiterhin über die PHINIT-Safe-URL- und Media-Helper. |
| 🎨 style | Sidebar / PHINIT-Design | `assets/css/homepage-blog.css` und `assets/css/homepage-blog-critical.css` gestalten die Dienstleistungscard kompakt, lesbar und mit dezenter Vollbreiten-CTA inklusive Dark Mode. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.78` synchronisiert. |

---

## v1.5.77 — 24. Mai 2026

### Beitrags-Templates mit Zusatzkarte

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🟢 feat | Beitragseditor / Templates | Der Core-Beitragseditor kann das aktive PHINIT-`Beitrags-Template` speichern und zeigt templateabhängige Zusatzfelder aus `theme.json`; beim Tech-Template sind Tool, Version, Autor, Website, GitHub-Repo, Testdatum und Voraussetzungen verfügbar. |
| 🟢 feat | Publicsite / Zusatzkarte | `blog-single.php` delegiert an das gespeicherte Post-Template; PHINIT rendert ausgefüllte Zusatzfelder als Karte oberhalb des TOC, leere Felder werden nicht ausgegeben. |
| 🎨 style | Zusatzkarte / PHINIT-Design | `assets/css/templates.css` gestaltet die neue Zusatzkarte im vorhandenen Navy-/Teal-Card-Look inklusive Link- und Chip-Darstellung. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.77` synchronisiert. |

---

## v1.5.76 — 24. Mai 2026

### Erweiterte Autorbox mit Dienstleistungs-HubSite

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🟢 feat | Autorbox / Beiträge | `post.php`, `post-wide.php`, `post-tech.php` und `partials/post-author-box.php` zeigen eine erweiterte Autorbox mit konfigurierbarem Kicker, Name, Über-mich-Label, Bio, Avatar und CTA zur Dienstleistungs-HubSite; der Customizer-Name überschreibt den technischen Beitragsautor. |
| 🟢 feat | Autorbox / Seiten | `page.php` rendert die Autorbox jetzt auch unter normalen Seitendetails; HubSites, Cookie-Consent- und Bildarchivseiten bleiben bewusst ausgenommen. |
| 🎨 style | Autorbox / PHINIT-Design | `assets/css/post-detail.css` gestaltet die Autorbox als echtes Grid-Layout: links Autorinfos und rechts der Dienstleistungsbereich mit Hinweistext oberhalb des optionalen Buttons; die linke Spalte wird beim Speichern robust auf 50 %, 60 % oder 75 % normalisiert. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.76` synchronisiert. |

---

## v1.5.75 — 24. Mai 2026

### Dienstleistungs-HubSite im Admin sichtbar und seed-stabil

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | HubSites / Services-Seed | `includes/theme-services-hub-seed.php` prüft `table_slug` nur noch, wenn die Spalte tatsächlich existiert. Dadurch kann die PHINIT-Dienstleistungs-HubSite auch auf älteren Installationen ohne `table_slug` angelegt werden. |
| 🔴 fix | HubSites / Admin-Editor | `CMS/admin/views/hub/edit.php`, `CMS/assets/js/admin-hub-site-edit.js` und `CMS/assets/js/admin-hub-template-editor.js` machen das `services`-/Dienstleistungen-Profil im HubSite-Admin sichtbar, aktivieren Feature-Kacheln für services-basierte Templates und zeigen eine passende Dienstleistungs-Vorschau. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.75` synchronisiert. |

---

## v1.5.74 — 23. Mai 2026

### Dienstleistungs-HubSite als PHINIT-Landingpage

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🟢 feat | HubSites / Services | `includes/theme-services-hub-seed.php` und `functions.php` legen beim `cms_init` einmalig eine Dienstleistungs-HubSite mit Slug `it-dienstleistungen`, Hero, Kontakt-CTA, zweisprachigen Texten und sechs Service-Kacheln an, sofern der Seed noch nicht existiert. |
| 🟢 feat | HubSites / Template-Profil | `CMS/admin/modules/hub/HubTemplateProfileCatalog.php` und `CMS/core/Services/SiteTable/SiteTableTemplateRegistry.php` ergänzen das neue HubSite-Profil `services` für Dienstleistungs-/Landing-Hubs. |
| 🎨 style | HubSites / PHINIT-Design | `assets/css/hub-sites.css` gestaltet `.cms-hub-site--services` im PHINIT-Look mit Navy/Gold-Hero, Service-Karten, CTA-Buttons und Dark-Mode-Kontrast. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.74` synchronisiert. |

---

## v1.5.73 — 23. Mai 2026

### SiteTable-Pagination erst nach 20 sichtbaren Zeilen

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Site Tables / Pagination | `functions.php` und `CMS/core/Services/SiteTable/SiteTableTableRenderer.php` erzwingen für PHINIT-SiteTables mindestens 20 Zeilen pro Seite. Kleinere Tabellen-Seitengrößen wie `15` lösen dadurch keine frühe Pagination mehr aus; bei exakt 20 Zeilen bleibt Pagination aus. |
| 🔴 fix | Site Tables / Meta-Infos | `assets/css/rich-content.css` und `assets/css/hub-sites.css` blenden die SiteTable-Toolbar im PHINIT-Frontend als zusätzliche Sichtbarkeitsabsicherung aus, sodass keine Zeilen-/Seitenstatus-Metainfo unter dem Tabellentitel sichtbar bleibt. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.73` synchronisiert. |

---

## v1.5.72 — 23. Mai 2026

### SiteTable-Pagination im PHINIT-Stil ohne Meta-Zeile

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | Site Tables / Pagination | `assets/css/rich-content.css` und `assets/css/hub-sites.css` gestalten die SiteTable-Pagination passend zum PHINIT-Design für normale Inhalte und HubSites inklusive Dark-Mode-Zuständen. |
| 🔴 fix | Site Tables / Toolbar | `CMS/core/Services/SiteTable/SiteTableTableRenderer.php` rendert den Toolbar-Block nur noch bei aktiver Suche. Reine Pagination erzeugt dadurch keine Zeilen-/Seitenstatus-Metainfo mehr direkt unterhalb des Tabellentitels. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.72` synchronisiert. |

---

## v1.5.71 — 23. Mai 2026

### Ruhigere SiteTables ohne Suche und frühe Pagination

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Site Tables / PHINIT-Frontend | `functions.php` deaktiviert die SiteTable-Suchleiste für PHINIT über den neuen Core-Filter `site_table_interactive_config`, sodass über öffentlichen Tabellen keine Suche mehr erscheint. |
| 🔴 fix | Site Tables / Pagination | `CMS/core/Services/SiteTable/SiteTableTableRenderer.php` aktiviert Pagination erst ab mindestens 20 Tabellenzeilen und nur, wenn die Zeilenzahl größer als die konfigurierte Seitengröße ist. Kleine Tabellen bleiben dadurch ohne unnötige Toolbar/Pagination. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.71` synchronisiert. |

---

## v1.5.70 — 23. Mai 2026

### HubSite-Tabellentitel sichtbar gehalten

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | HubSites / Site Tables | `page.php` gibt echte Core-HubSites (`content_type = hub`) nun über `phinit_render_prepared_content()` aus. Dadurch bleibt das bereits vom Core vorbereitete und sanitisiert aufgebaute HubSite-Markup inklusive sicherer Template-CSS-Variablen erhalten; Tabellen-Titel sind nicht mehr nur im Inhaltsverzeichnis vorhanden, sondern auch oberhalb der Tabelle sichtbar. Seiten, die nur zufällig `cms-hub-site` im Inhalt enthalten, laufen weiterhin durch das Hub-Sanitizer-Profil. |
| 🎨 style | HubSites / Tabellenkopf | `assets/css/hub-sites.css` ergänzt Fallback-Variablen für `--cms-hub-table-head-start` und `--cms-hub-table-head-end`, damit Tabellenkopf und Meta-Titel auch ohne Template-Variable kontrastreich bleiben. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.70` synchronisiert. |

---

## v1.5.69 — 23. Mai 2026

### Tabellen-Captions in HubSites wieder sichtbar

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Site Tables / Sanitizer | `CMS/core/Services/PurifierService.php` erlaubt `<caption>` nun in Default- und Hub-Profilen; `includes/theme-content-helpers.php` übernimmt denselben Fallback, damit zentral aktivierte Tabellen-Captions nicht mehr nach dem Rendern entfernt werden. |
| 🎨 style | Site Tables / PHINIT-Theme | `assets/css/rich-content.css` und `assets/css/hub-sites.css` gestalten Tabellen-Captions sichtbar oberhalb der Tabelle, inklusive Dark-Mode-Kontrast für normale Seiten/Beiträge und HubSites. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.69` synchronisiert. |

---

## v1.5.68 — 22. Mai 2026

### Startseiten-Buttons im Dark Mode aufgehellt

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Startseite / Button-Kontrast | `assets/css/homepage-blog.css` setzt Dark-Mode-Textfarben für Featured-Banner-CTA, Artikel-„Weiter lesen“, Sidebar-Quicklinks, Carousel-Pfeile, Widget-Buttons, Info-Card-CTAs und Suchbutton auf helle Blau-/Weißtöne. |
| 🔴 fix | Startseite / Critical CSS | `assets/css/homepage-blog-critical.css` übernimmt die hellen Dark-Mode-Textfarben für Above-the-fold-Buttons, damit die Startseite schon vor dem nachgeladenen Haupt-CSS lesbar bleibt. |
| 🔴 fix | Global / Buttons & Member | `style.css` ergänzt lesbare Dark-Mode-Zustände für globale `.btn-outline`-/`.btn-ghost`-Buttons; `assets/css/member-auth.css` nutzt sichere helle Fallbacks für Member-Navigation und Analytics-Link-Hover. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.68` synchronisiert. |

---

## v1.5.67 — 22. Mai 2026

### Dark-Mode-Kontrast für Links und Favoriten korrigiert

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Dark Mode / Inhalts-Weblinks | `style.css` und `assets/css/rich-content.css` ergänzen kontrastreiche Link-Tokens und nutzen im Dark Mode helles Blau (`#93c5fd`/`#bfdbfe`) für normale Weblinks in Seiten- und Beitragsinhalten inklusive sichtbarer Unterstreichung. |
| 🔴 fix | Dark Mode / Favoriten-Button | `assets/css/post-detail.css` und `assets/css/page-detail.css` geben dem Favoriten-Button im Seiten-/Beitragskopf dunkle Hintergründe und lesbare Hover-/Aktivzustände, sodass der Text nicht mehr weiß auf weißem Hintergrund steht. |
| 🔴 fix | Dark Mode / Member-Favoriten | `assets/css/member-auth.css` korrigiert Favoritenkarten, Badges, Metatexte, Öffnen-Links und Entfernen-Buttons im Member-Bereich auf dunkle Karten mit ausreichendem Textkontrast. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.67` synchronisiert. |

---

## v1.5.66 — 22. Mai 2026

### Public-TOC-Sprünge stabilisiert

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Inhaltsverzeichnis / Public-Anker | `includes/theme-content-helpers.php`, `post.php`, `post-wide.php`, `post-tech.php`, `page.php`, `page-wide.php` und `page-landing.php` geben bei TOC-fähigen Inhalten genau das vorbereitete HTML aus, aus dem zuvor die TOC-Einträge erzeugt wurden. Dadurch laufen Sidebar-, Inline- und Seiten-TOCs nicht mehr in IDs, die durch eine zweite Sanitizer-/Heading-ID-Runde abweichen. |
| 🔴 fix | Inhaltsverzeichnis / Scroll-Verhalten | `assets/js/navigation.js` und `assets/js/content-interactions.js` laden die Content-Interaktionen auch bei rein Core-generierten TOCs, fangen PHINIT- und Core-TOC-Links ab, scrollen mit Sticky-Header-/Quicklink-/Memberbar-Offset zum Abschnitt, aktualisieren den Hash und setzen den aktiven TOC-Link. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.66` synchronisiert. |

---

## v1.5.65 — 20. Mai 2026

### TOC-Anker und EditorJS-Abstände stabilisiert

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Inhaltsverzeichnis / Überschriften-Anker | `includes/theme-content-helpers.php` stabilisiert Heading-IDs nach der finalen Sanitizer-Stufe, normalisiert leere oder ungültige IDs und macht Duplikate eindeutig (`-2`, `-3`, …), damit Klicks im 365CMS-Inhaltsverzeichnis wieder zuverlässig zu `h2`–`h6` springen. |
| 🔴 fix | EditorJS / Public-Abstände | `assets/css/rich-content.css` nimmt `.editorjs-spacer` aus generischen Rich-Content-Margins heraus und rendert gespeicherte Spacer-Höhen über robuste `[data-height]`-Fallbacks, sodass Werte wie `25px`, `75px` oder `160px` im Frontend sichtbar bleiben. |
| 🛡️ security | Sanitizer / EditorJS-Attribute | `CMS/core/Services/PurifierService.php` erlaubt sichere EditorJS-Attribute wie `data-height`, `role`, `aria-hidden` und CMS-Spacing-Datenattribute in den relevanten Purifier-Profilen; die HTML-Definition wurde revisioniert, damit alte Caches die neuen Regeln sauber übernehmen. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.65` synchronisiert. |

---

## v1.5.64 — 17. Mai 2026

### Dropdown-Pfeile in der Hauptnavigation korrigiert

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Header / Dropdown-Menü | `header.php` dekodiert Menü- und Quicklink-Labels vor dem finalen Escaping sicher mit `html_entity_decode()`, sodass gespeicherte Entities wie `&rsaquo;` als Zeichen statt als sichtbarer Entity-Text erscheinen. |
| 🔴 fix | Header / Dropdown-CSS | `assets/css/header-navigation.css` nutzt für dekorative Untermenü-Pfeile den Unicode-Escape `\203A`, damit der Browser zuverlässig `›` rendert. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.64` synchronisiert. |

---

## v1.5.63 — 16. Mai 2026

### Desktop Grid-Abstand und Inline-CSS-Minifizierung

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | Desktop Gridcards | `assets/css/content-cards.css` reduziert den Abstand zwischen Titel und Meta-Zeile nochmals um `4px` (`margin-top: -31px`); Listcards und Mobile-Regeln bleiben unverändert. |
| 🟠 perf | Inline CSS | `includes/theme-assets-trait.php` minifiziert inline ausgelieferte CSS-Dateien zur Renderzeit, damit PageSpeed die nicht zuordenbare unminifizierte CSS-Nutzlast deutlich kleiner bewertet. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.63` synchronisiert. |

---

## v1.5.62 — 16. Mai 2026

### Mobile Kartenanzahl hart begrenzt

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🟠 perf | Mobile Homepage Counts | `includes/theme-home-helpers.php` begrenzt Mobile-Requests auf maximal vier Listcards und maximal vier Gridcards, auch wenn die List-Sektion deaktiviert ist. |
| 🎨 style | Mobile Viewport-Fallback | `assets/css/homepage-blog.css` und `assets/css/content-cards.css` blenden in der mobilen Ansicht Listcards und Gridcards ab dem fünften Eintrag aus, damit auch Desktop-Responsive-Tests maximal vier sichtbare Karten zeigen. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.62` synchronisiert. |

---

## v1.5.61 — 16. Mai 2026

### Desktop Gridcard-Meta-Abstand

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | Desktop Gridcards | `assets/css/content-cards.css` zieht die Meta-Zeile in der Desktopansicht nochmals `16px` näher an den Titel (`margin-top: -27px`); Listcards und Mobile-Regeln bleiben unverändert. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.61` synchronisiert. |

---

## v1.5.60 — 16. Mai 2026

### Mobile Kartenhöhe und Burger-Menü

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | Desktop Gridcards | `assets/css/content-cards.css` reduziert in der Desktopansicht ausschließlich bei Gridcards den Abstand zwischen Titel und Meta-Zeile um `8px`; Listcards bleiben unverändert. |
| 🎨 style | Mobile Navigation | `assets/css/header-navigation.css` entfernt den eigenen Scrollbereich des Burger-Menüs, sodass die geöffnete Navigation in der normalen Seitenhöhe liegt. |
| 🎨 style | Mobile Cards | `assets/css/homepage-blog.css` und `assets/css/content-cards.css` kürzen mobile List- und Gridcards inklusive Bildbereich um `30px` auf `165px`. |
| 🟠 perf | Mobile Homepage Grid | `includes/theme-home-helpers.php` zeigt in der mobilen Sektion „Alle Beiträge“ wieder vier Gridcards statt zwei, während die vier Listcards erhalten bleiben. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.60` synchronisiert. |

---

## v1.5.59 — 16. Mai 2026

### Mobile Scanbarkeit und Footer-Korrektur

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | Startseite / Mobile Cards | `assets/css/homepage-blog.css` und `assets/css/content-cards.css` vergrößern mobil den Abstand zwischen Titel und Meta-Zeile bei List- und Gridcards um `4px`, begrenzen Teaser mobil auf zwei Zeilen und ergänzen dezente `:active`-States für Karten und Buttons. |
| 🟠 perf | Mobile Pagination | `includes/theme-home-helpers.php` und `blog.php` reduzieren Mobile-Requests serverseitig auf sechs Artikel pro Seite, damit lange Artikellisten kürzer werden und Pagination früher sichtbar ist. |
| 🎨 style | Footer / Mobile | `assets/css/footer-consent.css` zeigt die Footer-About-Spalte mobil wieder als ersten Footer-Block, hält den Kontaktbutton sichtbar und richtet Social-Media-Icons nebeneinander aus. |
| 🎨 style | Mobile Bedienung | `assets/css/homepage-blog.css` gibt den Pfeilen im Widget `Empfohlene Artikel` mindestens `44×44px` Touch-Fläche; `assets/js/navigation.js` zeigt Back-to-top mobil erst ab zwei Bildschirmhöhen Scroll. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.59` synchronisiert. |

---

## v1.5.58 — 16. Mai 2026

### Mobile Footer: nur Social Icons

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | Footer / Mobile | `assets/css/footer-consent.css` blendet in der mobilen Footer-About-Sektion Avatar, Heading, About-Text und Kontaktbutton aus; sichtbar bleibt nur eine horizontale Social-Media-Icon-Leiste. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.58` synchronisiert. |

---

## v1.5.57 — 16. Mai 2026

### Empfohlene Artikel: 14px niedrigere Widget-Höhe

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | Startseite / Sidebar Featured | `assets/css/homepage-blog.css` reduziert ausschließlich die Höhe des Widgets `Empfohlene Artikel` um `14px`; Breite, Overlay-Badge, Typografie und Rotator-Verhalten bleiben unverändert. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.57` synchronisiert. |

---

## v1.5.56 — 16. Mai 2026

### Startseiten-Listcards wieder vier Artikel

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Startseite / Listcards | `theme.json` setzt den Default von `homepage.article_list_count` wieder auf `4` und nutzt denselben Label-Text wie der Theme-Customizer, damit ohne bewusst gesetzten Customizer-Wert wieder vier Listcards erscheinen statt drei. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.56` synchronisiert. |

---

## v1.5.55 — 16. Mai 2026

### Empfohlene Artikel: ein Artikel pro Rotation

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Startseite / Sidebar Featured | `partials/home-article-list.php` aktiviert die Rotator-Darstellung für `Empfohlene Artikel` bereits ab zwei ausgewählten Artikeln, damit das Widget wieder genau einen vollflächigen Artikel anzeigt und alle `6s` weiterwechselt statt zwei Beiträge untereinander zu rendern. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.55` synchronisiert. |

---

## v1.5.54 — 16. Mai 2026

### Customizer-Kompatibilität und PageSpeed-Pfad

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Theme Customizer / Homepage | `admin/customizer-schema.php`, `admin/customizer-request-handler.php`, `theme.json` und `includes/theme-home-helpers.php` synchronisieren die Homepage-Feldnamen auf die kanonischen Keys `article_list_label`, `article_list_count`, `show_info_grid`, `tile_grid_label` und `tile_grid_count`; alte Alias-Werte werden weiter migriert und lösen beim Speichern keine Unknown-Setting-Warnungen mehr aus. |
| 🟠 perf | Startseite / Kritischer CSS-Pfad | `includes/theme-assets-trait.php` gibt auf der Homepage Basis-, Header- und Critical-Homepage-CSS inline aus, damit die render-blockierende CSS-Kette oberhalb der Falz verkürzt wird. |
| 🟠 perf | Navigation & Animationen | `assets/js/navigation.js`, `style.css`, `assets/css/header-navigation.css`, `assets/css/homepage-blog.css`, `assets/css/content-cards.css` und `assets/css/ui-chrome.css` vermeiden unnötige Panel-Layoutreads in Dropdowns und entfernen `all`-/`box-shadow`-/`width`-Transitions aus den gemeldeten Hover- und Progress-Pfaden. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.54` synchronisiert. |

---

## v1.5.53 — 16. Mai 2026

### Listcard-Auszüge: maximal 3 Zeilen

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | Startseite / Mobile Listcards | `assets/css/homepage-blog.css` und `assets/css/homepage-blog-critical.css` begrenzen Textauszüge der Startseiten-Listcards zusätzlich zu `line-clamp` per echter `max-height` auf maximal drei Zeilen, damit Browser-Kantenfälle keine vierte Zeile anzeigen. |
| 🎨 style | Sidebar / Empfohlene Artikel | `assets/css/homepage-blog.css` stellt rotierende Sidebar-Empfehlungen wieder als vollflächiges Cover-Bild mit Titel-Badge oben dar, auch wenn Custom-/Side-Media-Klassen aktiv sind. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.53` synchronisiert. |

---

## v1.5.52 — 16. Mai 2026

### Originalbilder und mobiler Featured-Banner

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Medien / Originalauslieferung | `partials/post-card.php`, `partials/home-featured-banner.php`, `partials/home-post-grid.php` und `partials/home-article-list.php` liefern Beitrags- und Featured-Artikelbilder direkt über die normalisierte Original-Upload-URL mit `filemtime`-Cachebuster aus, ohne AVIF-/WebP-/Thumbnail-`srcset`, damit nicht versehentlich komprimierte Derivate oder alte Browsercache-Versionen gewählt werden. |
| 🎨 style | Startseite / Mobile Featured-Banner | `assets/css/homepage-blog.css`, `assets/css/homepage-blog-critical.css` und `partials/home-article-list.php` halten Featured-Artikelbilder mobil links neben Titel, Meta/Footer und Teaser statt oberhalb der Karte; die Sidebar-Featured-Box gibt ihr berechnetes Side-/Below-Layout jetzt wieder als Klasse aus. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.52` synchronisiert. |

---

## v1.5.51 — 16. Mai 2026

### Listcard-Bilder: Medienersatz erneuert Derivate

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Startseite / Listcards | `includes/theme-template-helpers.php` erkennt veraltete AVIF-, WebP- und Thumbnail-Derivate, wenn das Originalbild nach einem Medienersatz neuer ist, und erzeugt die Varianten neu. |
| 🔴 fix | Medien / Browsercache | Lokal ausgelieferte Picture-/Thumbnail-URLs erhalten einen `filemtime`-Cachebuster, damit Listcards nach dem Ersetzen nicht weiter alte Derivatdateien aus dem Cache anzeigen. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.51` synchronisiert. |

---

## v1.5.50 — 16. Mai 2026

### Mobile Listcards: gleiche Struktur wie Gridcards

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | Startseite / Mobile Artikelcards | `assets/css/homepage-blog.css` und `assets/css/homepage-blog-critical.css` setzen die Listcards mobil final auf ein Gridcard-artiges Bild-links-Layout mit fixer 96px/80px-Bildspalte, Titel, Meta, Auszug und Footer rechts daneben. |
| 🔴 fix | Startseite / Mobile Cascade | Der späte Mobile-Override, der Listcards wieder als Bild-oben-Spaltenlayout darstellen konnte, wird durch scoped `.home-section--list`-Regeln zuverlässig überstimmt. |
| 🟠 perf | Startseite / Responsive Images | `partials/post-card.php` meldet für mobile Listcard-Bilder passende `sizes`-Werte (`96px` bzw. `80px`) statt Full-Width, damit Browser kleinere Bildkandidaten wählen können. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.50` synchronisiert. |

---

## v1.5.49 — 16. Mai 2026

### Mobile Buttons: kompakter und konsistent

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | Startseite / Mobile Buttons | `assets/css/content-cards.css`, `assets/css/homepage-blog.css` und `assets/css/homepage-blog-critical.css` machen die mobilen „Weiter lesen“-Buttons schmaler, ohne die Schriftgröße zu reduzieren. |
| 🔴 fix | Startseite / Gridcards | `partials/home-post-grid.php` verwendet mobil denselben Buttontext wie die Listcards, sodass Grid- und Listcards einheitlich „Weiter lesen“ anzeigen. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.49` synchronisiert. |

---

## v1.5.48 — 16. Mai 2026

### Mobile Startseite: Listen-Cards und Sidebar reduziert

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | Startseite / Mobile Artikelcards | `assets/css/homepage-blog.css` hält die Artikelcards der Listenansicht mobil im kompakten Bild-links-Layout wie die Gridcards, inklusive fixer Bildspalte und gekürztem Textbereich. |
| 🔴 fix | Startseite / Mobile Sidebar | `assets/css/homepage-blog.css` blendet mobil alle Sidebar-Widgets außer `Empfohlene Artikel` aus; wenn keine empfohlenen Artikel aktiv sind, bleibt die Sidebar auf Mobile vollständig verborgen. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.48` synchronisiert. |

---

## v1.5.47 — 16. Mai 2026

### Content-Bilder: direkte Auslieferung und Breiten-Skalierung

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🟠 perf | Medien / Direct Delivery | `includes/theme-template-helpers.php` korrigiert öffentliche Upload-Bilder bei Bedarf auf webserverlesbare Rechte und liefert sie danach direkt über `/uploads/...` aus. Geschützte Member-/Hidden-Pfade und Nicht-Bilder bleiben weiterhin ausgeschlossen. |
| 🔴 fix | Seiten-/Beitragscontent | `includes/theme-content-helpers.php` normalisiert `src` und `srcset` aller Content-`img`-Tags über den zentralen PHINIT-Mediennormalizer, sodass auch Bilder in Seiten- und Beitragsdetailseiten bevorzugt direkte Upload-URLs nutzen. |
| 🎨 style | Rich Content / Bilder | `assets/css/rich-content.css` skaliert Contentbilder, Figures und Picture-Elemente auf die verfügbare Inhaltsbreite und hält die Höhe automatisch proportional. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.47` synchronisiert. |

---

## v1.5.46 — 16. Mai 2026

### Startseiten-Grid: Bilder sofort und mit robustem Delivery-Fallback

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Medien / Direct Delivery | `includes/theme-template-helpers.php` nutzt direkte `/uploads/...`-URLs nur noch für tatsächlich öffentlich lesbare Bilddateien. Uploads mit restriktiven Rechten wie `0640` fallen wieder zuverlässig auf `/media-file` zurück, statt als direkte, aber vom Webserver nicht auslieferbare URL im Frontend zu landen. |
| 🔴 fix | Medien / Relative Pfade | Relative Upload-Referenzen werden im PHINIT-Normalizer als Upload-Kandidaten erkannt und bei fehlender direkter Lesbarkeit explizit über `/media-file` ausgeliefert. |
| 🟠 perf | Startseite / Grid | `partials/home-post-grid.php` lädt Grid-Bilder nicht mehr lazy und entfernt die `data-anim`-Trigger vom Grid, damit die Kachelbilder auf der Startseite sofort im initialen Renderpfad stehen. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.46` synchronisiert. |

---

## v1.5.45 — 16. Mai 2026

### Medien: direkte Upload-Auslieferung für öffentliche Theme-Bilder

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🟠 perf | Medien / Public Images | `includes/theme-template-helpers.php` bevorzugt im zentralen `phinit_normalize_public_media_url()` für öffentliche Upload-Bilder jetzt direkte `/uploads/...`-URLs. Dadurch vermeiden PHINIT-Templates für öffentliche Bilder den zusätzlichen PHP-Hop über `/media-file`. |
| 🛡️ security | Medien / Fallback-Regeln | Private Member-Dateien, versteckte Pfade, Nicht-Bilddateien und lokal nicht sicher direkt lesbare Uploads bleiben weiterhin bei der kontrollierten `/media-file`-Auslieferung. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.45` synchronisiert. |

---

## v1.5.44 — 16. Mai 2026

### Empfohlene Artikel: Original-Bildquelle statt gestreckter Miniatur

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Startseite / Sidebar / Empfohlene Artikel | `partials/home-article-list.php` nutzt für das Widget „Empfohlene Artikel“ wieder die normalisierte Original-Bildquelle statt generierter `64x48`-Thumbnail-Derivate. Dadurch werden Rotator- und Sidebar-Bilder nicht mehr niedrig aufgelöst auf große Flächen gestreckt. |
| 🟠 perf | Startseite / Sidebar / Bildpriorisierung | Sichtbare Empfehlungsbilder laden jetzt eager ohne `fetchpriority="high"`, damit sie nicht verzögert erscheinen, aber auch nicht mit dem LCP-Bild konkurrieren. Versteckte Rotator-Slides bleiben weiterhin `fetchpriority="low"`. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.44` synchronisiert. |

---

## v1.5.43 — 15. Mai 2026

### Nicht-Bild-Performance: weniger Main-Thread-, Timer- und Third-Party-Druck

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🟠 perf | JS / Scroll & Layout | `assets/js/navigation.js` cached die maximale Scrollstrecke für die Fortschrittsleiste und aktualisiert sie nur noch bei `resize`, `load` und `pageshow`, statt auf jedem Scroll-Frame `scrollHeight` zu lesen. |
| 🟠 perf | Startseite / Rotatoren | `assets/js/homepage-widgets.js` bündelt die bisher mehrfach registrierten Sichtbarkeits-Listener in einem zentralen Lifecycle-Handler und pausiert Carousel-/Featured-Timer zusätzlich über `pagehide`/`pageshow` BFCache-freundlich. |
| 🟠 perf | Third-Party / Analytics | `assets/js/analytics-loader.js` initialisiert die Consent-Queue direkt, verschiebt den externen `gtag.js`-Download aber auf Load/Idle, damit Analytics nicht mit frühem Rendering und LCP konkurriert. |
| 🟠 perf | CSS / Daueranimationen | `assets/css/header-navigation.css` entfernt die permanente dekorative Header-Hintergrundanimation; `assets/css/homepage-blog.css` ersetzt animierte `box-shadow`-Puls-Paints durch eine `transform`/`opacity`-Animation mit Reduced-Motion-Fallback. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.43` synchronisiert. |

---

## v1.5.42 — 15. Mai 2026

### Startseite: versteckte Rotator-Bilder aus dem LCP-Wettbewerb nehmen

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🟠 perf | Startseite / Featured-Banner | `partials/home-featured-banner.php` priorisiert bei mehreren Featured-Banner-Slides nur noch den aktiven ersten Slide mit `loading="eager" fetchpriority="high"`; versteckte Slides laufen mit `fetchpriority="low"` im Hintergrund an, damit sie das LCP-Bild nicht ausbremsen und beim Rotieren nicht leer erscheinen. |
| 🟠 perf | Startseite / Sidebar-Rotatoren | `includes/theme-template-helpers.php` und `partials/home-article-list.php` unterstützen Low-Priority-Attribute für inaktive Carousel-/Featured-Rotator-Bilder, während sichtnahe Artikelbilder unverändert eager bzw. high-priority bleiben. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.42` synchronisiert. |

---

## v1.5.41 — 15. Mai 2026

### Startseite: stabile Beitrags-Vorschaubilder und sauberere Bildpriorisierung

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🟠 perf | Startseite / Artikelbilder | `partials/home-article-list.php` und `partials/post-card.php` behandeln die ersten zwei Artikelkarten als sichtnah: nur das erste Bild erhält `fetchpriority="high"`, das zweite lädt eager ohne zusätzliche High-Priority-Konkurrenz, und die übrigen Karten bleiben lazy mit kleinen Desktop-Kandidaten. |
| 🎨 style | Startseite / Bild-UX | `partials/post-card.php`, `assets/css/homepage-blog-critical.css`, `assets/css/homepage-blog.css` und `assets/js/navigation.js` ergänzen einen stabilen Skeleton-Placeholder für vorhandene Beitragsbilder, der den reservierten Bildbereich sichtbar füllt und erst nach erfolgreichem Load ausgeblendet wird. |
| 🟠 perf | Startseite / LCP-Preload | `includes/theme-assets-trait.php` bevorzugt beim Homepage-Lead-Preload AVIF vor WebP und nutzt für Banner- bzw. Artikelbilder passendere Fallback-Dimensionen, damit der Preload näher an der tatsächlich gerenderten `<picture>`-Quelle liegt. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.41` synchronisiert. |

---

## v1.5.40 — 15. Mai 2026

### PSI-Nacharbeit: Navigation-Reflow und responsive Artikelbilder

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🟠 perf | Navigation / Mobile | `assets/js/navigation.js` überspringt Desktop-Dropdown-Initialisierung auf Touch-/Mobile-Viewports und liest den Sticky-Header-Scrollstatus nicht mehr synchron beim `DOMContentLoaded`, um die im PSI-Report verbliebenen Forced-Reflow-Spitzen weiter zu reduzieren. |
| 🟠 perf | Startseite / Artikelbilder | `partials/post-card.php` liefert für nicht priorisierte Artikelkarten zusätzliche kleine `108x81`-Desktop-Kandidaten per `srcset`/`sizes`, während Above-the-fold-/Mobile-Darstellung größere Quellen behält. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.40` synchronisiert. |

---

## v1.5.39 — 15. Mai 2026

### Homepage: PSI-Nacharbeit für Home-CSS, Sidebar-A11y und Rotator

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Startseite / Critical CSS | `includes/theme-assets-trait.php` und `includes/theme-head-trait.php` behandeln normale Blog-Listings nicht länger fälschlich als HubSites; dadurch entfällt `assets/css/hub-sites.css` auf der Homepage aus dem kritischen Renderpfad. |
| 🔴 fix | Startseite / Sidebar / A11y | `partials/home-article-list.php` rendert die Thumbnails im Widget `Empfohlene Artikel` mit leerem `alt=""`, weil der Beitragstitel im selben Link bereits sichtbar und zugänglich vorhanden ist. |
| 🟡 refactor | Startseite / Sidebar-Rotator | `assets/css/homepage-blog.css` entfernt `visibility`-Timing und überflüssige `box-shadow`-Transitions aus den zentralen Featured-Rotator-Animationen, um unnötige PSI-Warnungen zu reduzieren. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.39` synchronisiert. |

---

## v1.5.38 — 15. Mai 2026

### Sidebar: Empfohlene Artikel höher und Pfeile robuster

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | Startseite / Sidebar / Empfohlene Artikel | `assets/css/homepage-blog.css` erhöht das Widget `Empfohlene Artikel` insgesamt um `35px` und vergrößert die sichtbare Rotator-Fläche auf Desktop und Mobile. |
| 🔴 fix | Startseite / Sidebar / Pfeilnavigation | `assets/css/homepage-blog.css` und `assets/js/homepage-widgets.js` geben den eingebetteten Pfeilbuttons ein robusteres Z-Index-/Overlay-Verhalten; Klicks werden per `preventDefault()` und `stopPropagation()` sauber vom Slide-Link entkoppelt. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.38` synchronisiert. |

---

## v1.5.37 — 15. Mai 2026

### Sidebar: Empfohlene Artikel mit Pfeilnavigation

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | Startseite / Sidebar / Empfohlene Artikel | `partials/home-article-list.php` entfernt die bisherige Dot-/Pagination-Leiste aus dem Widget `Empfohlene Artikel` und ersetzt sie durch zwei direkt im Rotator eingebettete Pfeile links und rechts. |
| 🔴 fix | Startseite / Sidebar-Rotator | `assets/js/homepage-widgets.js` unterstützt die manuelle Vor-/Zurück-Navigation des Featured-Rotators jetzt per Pfeilbuttons; `assets/css/homepage-blog.css` liefert das passende Overlay- und Fokus-Styling. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.37` synchronisiert. |

---

## v1.5.36 — 15. Mai 2026

### Startseite: weniger render-blocking CSS

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Startseite / Critical CSS | `assets/css/homepage-blog-critical.css` bündelt die sichtbaren Above-the-fold-Stile für Featured-Banner, Artikelliste und obere Sidebar-Widgets in einem kompakten synchronen Stylesheet. |
| 🔴 fix | Startseite / Asset-Ladepfad | `includes/theme-assets-trait.php` lädt auf der Startseite zuerst `homepage-blog-critical.css` synchron und zieht das große `homepage-blog.css` erst anschließend asynchron nach; Blog- und Archivseiten bleiben bewusst beim konservativen synchronen Pfad. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.36` synchronisiert. |

---

## v1.5.35 — 15. Mai 2026

### Homepage-Performance und Accessibility geschärft

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Startseite / LCP | `includes/theme-assets-trait.php` preloaded das tatsächlich gerenderte Homepage-Lead-Bild (Featured-Banner vor Artikelliste), bevorzugt vorhandene WebP-Varianten und läuft nur noch auf echten Blog-Listing-Routen. |
| 🔴 fix | Startseite / JS | `includes/theme-assets-trait.php`, `assets/js/navigation.js` und `assets/js/homepage-widgets.js` laden Homepage-Widgets nicht mehr doppelt; das Modul wird bedarfsorientiert nachgeladen und vermeidet zusätzlichen Initial-JS-Overhead sowie erzwungene Layoutmessungen. |
| 🎨 style | Startseite / Bilder & Touch Targets | `partials/home-featured-banner.php`, `partials/home-article-list.php` und `assets/css/homepage-blog.css` nutzen für Banner und Sidebar-Identity modernes `<picture>`-Markup, größere Rotator-Dots und dekorative Logo-Alt-Texte ohne redundante Screenreader-Ausgabe. |
| 🔴 fix | Startseite / Semantik | `partials/post-card.php`, `partials/home-article-list.php`, `assets/css/homepage-blog.css` und `includes/theme-assets-trait.php` korrigieren die Heading-Hierarchie der Artikelliste von `h4` auf `h3` samt passender Styles. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.35` synchronisiert. |

---

## v1.5.34 — 15. Mai 2026

### Öffentliche Medienauslieferung für ersetzte Bilder stabilisiert

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Medien / Featured Images | `includes/theme-template-helpers.php` stellt `phinit_normalize_public_media_url()` für verwaltete Uploads konsequent auf die `/media-file`-Delivery-Route um. Dadurch bleiben ersetzte Featured Images, Logos und Avatare im Public-Frontend sichtbar, auch wenn der Server direkte `/uploads`-Zugriffe wegen restriktiver Dateirechte mit `403 Forbidden` blockiert. |
| 🔵 docs | Release | `functions.php`, `style.css`, `theme.json`, `update.json`, `README.md` und `CHANGELOG.md` wurden auf Version `1.5.34` synchronisiert. |

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
