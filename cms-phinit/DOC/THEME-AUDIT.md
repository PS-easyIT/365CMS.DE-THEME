# CMS Phinit – Customizer-, Sicherheits- und Performance-Audit

Stand: 2026-03-14  
Scope: statischer Code-Audit des Themes `cms-phinit` in `365CMS.DE-THEME` inklusive Dateiinventar.  
Nicht enthalten: echte Lighthouse-/WebPageTest-Messläufe, Lasttests, Browser-Matrix-Tests, visuelle Regressionstests.

## Fortschritt seit Audit-Erstellung

Die folgenden Punkte aus Phase 1 wurden bereits umgesetzt:

- ✅ **CUS-01 / SEC-04**: Der Customizer speichert und importiert Werte jetzt schema-aware; bekannte Feldtypen werden serverseitig normalisiert und unbekannte Import-Keys verworfen.
- ✅ **SEC-01**: Öffentliche Author-/Profil-Links werden per Scheme-Allowlist abgesichert und bei ungültigen Schemes nicht mehr als anklickbare Links gerendert.
- ✅ **CUS-02**: Der Key-Drift zwischen `custom_header_code` und `custom_head_code` wurde bereinigt; die Runtime akzeptiert Legacy-Daten weiterhin als Fallback.
- ✅ **SEC-03**: Die redundanten lokalen Kommentar-POST-Handler in den Post-Templates wurden entfernt; der gemeinsame Flow über `/comments/post` ist jetzt die einzige aktive Wahrheit.
- ✅ **CUS-03 (teilweise)**: Fehlende Share-Optionen wurden ergänzt; Homepage-Views zeigen jetzt die kanonischen `theme.json`-Felder (`featured_section_title`, `featured_posts_count`, `show_info_cards`, `grid_section_title`, `grid_posts_per_page`), die Runtime liest diese Keys bevorzugt mit Fallback, und Post-Optionen wie `show_post_nav` / `show_author_box` sind nicht länger reine Karteileichen.
- ✅ **SEC-02**: Der Advanced-Tab verlangt bei Raw-Code-Änderungen jetzt eine explizite Bestätigung; Save/Import protokollieren Feldänderungen revisionsfähig über das Core-Audit-Log, ohne den eigentlichen Code im Log abzulegen.
- ✅ **SEC-04**: Der Customizer-Import prüft Upload-Herkunft, Dateiendung, MIME-Typ und die erwartete Root-Struktur (`theme`, `exported_at`, `customizations`) jetzt deutlich strenger; blockierte Versuche landen im Audit-Log.
- ✅ **CUS-04 (weitgehend)**: `theme.json` trägt jetzt auch die bisher nur im PHP-Schema beschriebenen Homepage-/Sidebar-, SEO-, Layout-, Header-, Footer-, Typografie-, Farb- sowie Zusatzfelder für Posts/Pages; im Legacy-Schema bleiben nur noch fünf veraltete Homepage-Alias-Keys (`article_list_label`, `article_list_count`, `show_info_grid`, `tile_grid_label`, `tile_grid_count`) als bewusste Fallback-Schicht zurück.
- ✅ **MAINT-01 (teilweise)**: `index.php` nutzt keine `extract()`-Aufrufe mehr; `get_theme_part()` rendert Partials jetzt über einen kontrollierten Scope mit validierten Variablennamen statt per pauschalem `extract()`.
- ✅ **PERF-04 (teilweise)**: Das erste Inhaltsbild wird nicht mehr pauschal lazy geladen, sondern standardmäßig mit `loading="eager"` und `fetchpriority="high"` bevorzugt behandelt.
- ✅ **PERF-04 (weitgehend)**: Content-Bilder ergänzen bei lokalen Assets jetzt zusätzlich fehlende `width`-/`height`-Attribute automatisch; zentrale Karten-/Hero-Templates schreiben Dimensionsattribute ebenfalls mit, wodurch Lazy Loading weniger CLS-Risiko erzeugt.
- ✅ **PERF-01 (teilweise)**: Die Request-Klassifizierung für den Asset-Pfad nutzt jetzt einen zentralen, pro Request gecachten Kontext statt wiederholt dieselbe Post-/Hub-/Page-Erkennung aufzurufen.
- ✅ **PERF-02 (teilweise)**: Meta-Tags, Schema.org, Breadcrumb und Seitentitel teilen sich jetzt gecachte Post-/Page-Daten, sodass dieselben Post-/Page-Abfragen im Head-Bereich nicht mehrfach ausgeführt werden.
- ✅ **PERF-03 (teilweise)**: `navigation.js` wurde auf ein schlankes Core-Bundle reduziert; Content-, Homepage- und Member-Security-Interaktionen werden jetzt über separate Dateien nur auf passenden Requests geladen.
- ✅ **PERF-05 (weitgehend)**: Die Startseite injiziert ihre dynamischen Abstände/Projektlogos nicht mehr über einen Template-`<style>`-Block, sondern über CSS-Variablen direkt am Container bzw. am jeweiligen Karten-Element.
- ✅ **PERF-05 (teilweise)**: Mit `DOC/PERFORMANCE-BUDGETS.md` existiert jetzt ein verbindlicher Minimalprozess für Zielseiten, Lighthouse-Zielwerte, Asset-Budgets und Review-Dokumentation, bis eine vollautomatische CI-Messung praktikabel ist.

Noch offen bleiben vor allem die Performance-Punkte aus Phase 3 sowie die späteren Mess-/Budget-Schritte aus Phase 4.

## Zielbild

Dieses Dokument bündelt drei Prüfbereiche in einem Arbeitsdokument:

- **Customizer-Funktions-Audit**
- **Sicherheits-Audit des Themes**
- **Performance-/Geschwindigkeits-Audit des gesamten Themes**
- **Dateiinventar mit Zweck jeder Theme-Datei**
- **priorisierte Abarbeitungsreihenfolge für die nächsten Schritte**

## Verwendete Quellen

### Lokal geprüfte Theme-Dateien

Schwerpunktmäßig geprüft wurden unter anderem:

- `functions.php`
- `theme.json`
- `header.php`
- `footer.php`
- `page.php`
- `post.php`
- `author.php`
- `admin/customizer.php`
- `admin/customizer-schema.php`
- `admin/customizer-config-builder.php`
- `admin/customizer-request-handler.php`
- `includes/theme-assets-trait.php`
- `includes/theme-head-trait.php`
- `includes/theme-template-helpers.php`
- `includes/theme-content-helpers.php`
- `partials/post-comments.php`
- `assets/js/navigation.js`

### Externe Best-Practice-Referenzen

- OWASP Cross Site Scripting Prevention Cheat Sheet  
  `https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html`
- OWASP Input Validation Cheat Sheet  
  `https://cheatsheetseries.owasp.org/cheatsheets/Input_Validation_Cheat_Sheet.html`
- MDN: Lazy loading  
  `https://developer.mozilla.org/en-US/docs/Web/Performance/Guides/Lazy_loading`
- web.dev: Fast / Performance overview  
  `https://web.dev/articles/fast`
- web.dev: Preconnect and DNS Prefetch  
  `https://web.dev/articles/preconnect-and-dns-prefetch?hl=de`
- web.dev: Browser-level image lazy loading  
  `https://web.dev/articles/browser-level-image-lazy-loading?hl=de`

## Kurzfazit

`cms-phinit` ist bereits **solide strukturiert** und bringt für ein 365CMS-Theme einige starke Grundlagen mit:

- klare Bootstrap-Struktur über `functions.php`
- sinnvolle Trennung von Templates, Partials, Includes, Admin-Customizer und Assets
- bedingt geladenes CSS statt pauschaler Komplettladung
- moderne Frontend-Bausteine wie `loading="lazy"`, `decoding="async"`, `fetchpriority="high"` für Above-the-fold-Helfer
- vielerorts korrektes Escaping mit `htmlspecialchars()`
- Admin-Schutz für den Customizer
- keine aktuellen Diagnosefehler im Theme-Ordner

Gleichzeitig gibt es **mehrere echte Audit-Befunde**, vor allem in drei Clustern:

1. **Customizer-Drift und Validierungs-Lücken**  
   `theme.json`, PHP-Schema, Request-Handler und Runtime-Code sind nicht mehr vollständig synchron.
2. **Sicherheitsrelevante Escape-Hatches**  
   Besonders kritisch sind unvalidierte Werte, die direkt in `<style>` oder als `href`/öffentliche URLs landen.
3. **vermeidbare Laufzeitkosten im Header-/Asset-Pfad**  
   Mehrere Request-Klassifizierungen und Meta-/Schema-/Breadcrumb-Abfragen wiederholen Datenbankzugriffe pro Request.

## Audit-Score auf Code-Ebene

> Kein Laborwert, sondern eine statische Einschätzung aus dem Code-Review.

| Bereich | Einschätzung | Kurzbegründung |
|---|---|---|
| Customizer | B- | mächtig und flexibel, aber mit Drift zwischen Schema, Runtime und Validierung |
| Sicherheit | B- | viele korrekte Escapes, aber mehrere unvalidierte Ausgabewege |
| Performance | B | gute Asset-Aufteilung, aber redundante DB-Checks und ein globales JS-Bundle |
| Wartbarkeit | B | gute Modulstruktur, aber verteilte Verantwortlichkeiten und einige Legacy-Spuren |

## Wichtigste Befunde

### Kritisch / hoch priorisiert

| ID | Bereich | Priorität | Befund | Betroffene Dateien |
|---|---|---:|---|---|
| CUS-01 | Customizer / Security | Hoch | **fehlende serverseitige Validierung** für viele Customizer-Felder; Werte werden später direkt in CSS-Variablen und Runtime-Ausgabe eingebettet | `admin/customizer-request-handler.php`, `includes/theme-assets-trait.php` |
| SEC-01 | Security | Hoch | **öffentliche URL-Felder werden ohne Scheme-Allowlist ausgegeben**; insbesondere Autoren-Profilfelder mit Typ `url` werden als `href` gerendert | `author.php`, `authors.php` |
| CUS-02 | Customizer | Hoch | **Key-Mismatch `custom_header_code` vs. `custom_head_code`** zwischen `theme.json`, PHP-Schema und Runtime-Ausgabe | `theme.json`, `admin/customizer-schema.php`, `includes/theme-assets-trait.php` |

### Mittel priorisiert

| ID | Bereich | Priorität | Befund | Betroffene Dateien |
|---|---|---:|---|---|
| SEC-02 | Security | Mittel | **Advanced-Customizer erlaubt absichtlich rohen Head-/Footer-Code**; das ist ein Admin-Escape-Hatch und muss als privilegierte Funktion behandelt werden | `admin/customizer-request-handler.php`, `includes/theme-assets-trait.php` |
| CUS-03 | Customizer | Mittel | neue/aktuelle Felder sind nicht überall sauber in die UI-Gruppen integriert (z. B. Share-Optionen) | `theme.json`, `admin/customizer-schema.php` |
| PERF-01 | Performance | Mittel | Request-Klassifizierung für Assets nutzt wiederholt DB-Zugriffe im Head-Pfad | `includes/theme-assets-trait.php` |
| PERF-02 | Performance | Mittel | Meta-Tags, Schema.org, Breadcrumb und Seitentitel führen auf Post-/Page-Requests mehrfach ähnliche Datenbankabfragen aus | `includes/theme-head-trait.php` |
| PERF-03 | Performance | Mittel | globales `navigation.js` wird themeweit geladen, obwohl Teile davon nur auf Spezialseiten gebraucht werden | `includes/theme-assets-trait.php`, `assets/js/navigation.js` |
| PERF-04 | Performance / UX | Mittel | `phinit_enhance_content_images()` setzt pauschal `loading="lazy"` für Content-Bilder, ohne First-Viewport-/LCP-Ausnahme oder Dimensions-Absicherung | `includes/theme-content-helpers.php` |
| SEC-03 | Security / Maintainability | Mittel | Legacy-Kommentarlogik in `post.php` ist redundant und verwendet einen anderen Token-Namen als das aktuelle Formularziel `/comments/post` | `post.php`, `partials/post-comments.php` |

### Niedriger priorisiert

| ID | Bereich | Priorität | Befund | Betroffene Dateien |
|---|---|---:|---|---|
| MAINT-01 | Wartbarkeit | Niedrig | `extract()` wird mehrfach genutzt; aktuell intern kontrolliert, aber fehleranfällig bei späteren Refactorings | `index.php`, `includes/theme-template-helpers.php`, `blog-single.php` |
| PERF-05 | Performance | Niedrig | Inline-Styles auf der Startseite sind klein, aber bündeln dynamische Bild-URLs in das HTML statt in CSS/Token-Logik | `index.php` |
| SEC-04 | Security | Niedrig | Import-Funktion prüft JSON-Größe, aber nicht MIME-Typ, Struktur-Schema oder Feld-Allowlist streng genug | `admin/customizer-request-handler.php` |

## Detailbewertung nach Audit-Bereich

### 1. Customizer-Funktions-Audit

#### Positiv

- `admin/customizer.php` ist sauber als Admin-Fragment aufgebaut.
- Der Customizer zieht Basis-Konfiguration aus `theme.json` und ergänzt Legacy-Felder per PHP-Schema.
- CSRF-Prüfung ist vorhanden.
- Export/Import ist bereits integriert.
- Der Feldrenderer unterstützt mehrere Typen, inklusive Select, Number und Post-Picker.

#### Befunde

##### CUS-01 – fehlende serverseitige Feldvalidierung

Der Request-Handler speichert Werte weitgehend roh:

- Checkboxen werden korrekt als `1`/`0` behandelt.
- Nicht-Advanced-Textareas werden mit `strip_tags()` bereinigt.
- **Zahlen, Farben, URLs, Selects und Texte werden nicht gegen das Schema validiert.**

Das ist problematisch, weil `generatePhinitCSS()` Werte später direkt in CSS schreibt:

- Farben werden ohne Regex-Allowlist eingebettet
- Zahlen werden ohne Min/Max-Validierung eingebettet
- Select-Werte werden nicht auf erlaubte Optionswerte geprüft

**Folge:** Ein manipuliertes POST kann ungültige oder schädliche Werte dauerhaft speichern.

**Status:** weitgehend umgesetzt. Der Request-Handler normalisiert jetzt Farben, Zahlen, Selects, Post-Picker und URLs schema-aware; zusätzlich rendern `theme.json`-URL-/Bildfelder im Customizer nicht mehr als generische Textfelder, sondern wieder als echte URL-Inputs, sodass UI und Validierung konsistent zusammenspielen.

##### CUS-02 – Key-Mismatch im Advanced-Bereich

Es existiert ein Drift zwischen Datenquellen:

- `theme.json` verwendet `custom_header_code`
- `admin/customizer-schema.php` verwendet `custom_head_code`
- die Runtime-Ausgabe in `includes/theme-assets-trait.php` liest `custom_head_code`

**Folge:**

- ein Teil der Konfiguration kann im falschen Key landen
- `theme.json` und tatsächliche Ausgabe verhalten sich nicht deckungsgleich
- Export/Import und spätere Automatisierung werden unnötig fragil

##### CUS-03 – UI-Drift bei neueren Settings

Die Konfiguration lebt an mehreren Stellen:

- kanonisch in `theme.json`
- ergänzend in `admin/customizer-schema.php`
- gruppiert über `tabGroups` / `tabViews`

Neue Settings können dadurch technisch existieren, aber in der UI unvollständig gruppiert oder schlechter auffindbar sein. Das betrifft insbesondere die Weiterentwicklung des Post-/Share-Bereichs.

**Status:** weitgehend umgesetzt. Share-Optionen, Post-Navigation und Autorenbox-Einstellungen sind in der UI sichtbar; zusätzlich wurden die Homepage-Gruppen/Views auf die kanonischen `theme.json`-Keys ausgerichtet. `theme.json` deckt inzwischen nahezu alle bislang nur im PHP-Schema definierten Customizer-Felder ab; lediglich fünf alte Homepage-Alias-Keys bleiben im Legacy-Schema als Fallback für bestehende Installationen erhalten.

#### Empfehlung

1. **`theme.json` als einzige fachliche Quelle definieren**  
   PHP-Schema nur noch für reine UI-Erweiterungen.
2. **Request-Handler schema-aware machen**  
   - Farbe: Regex-Allowlist (`^#[0-9a-fA-F]{6}$` etc.)
   - Zahl: Cast + Min/Max/Step
   - Select: Wert muss in `options` enthalten sein
   - URL: absolute URL-Allowlist oder interne Pfadvalidierung
3. **Key-Mismatch bereinigen**  
   überall einheitlich `custom_head_code` oder `custom_header_code`, nicht beides.
4. **Import validieren**  
   nur bekannte Kategorien/Felder übernehmen, unbekannte Keys verwerfen.

**Aktueller Rest für die vollständige Kanonisierung:** Die letzten Legacy-Alias-Felder auf der Startseite (`article_list_label`, `article_list_count`, `show_info_grid`, `tile_grid_label`, `tile_grid_count`) können perspektivisch entfernt werden, sobald ein gezielter Migrationspfad für Alt-Installationen vorhanden ist.

### 2. Sicherheits-Audit

#### Positiv

- sehr viele Ausgaben sind korrekt mit `htmlspecialchars()` escaped
- Admin-Zugangsschutz für `admin/customizer.php` vorhanden
- Kommentarformular postet auf `/comments/post` statt auf die Content-URL
- Favoriten-Flow nutzt einen separaten Token-Mechanismus
- `target="_blank"` ist an vielen Stellen sauber mit `rel="noopener noreferrer"` kombiniert

#### Befunde

##### SEC-01 – öffentliche URL-Felder ohne Scheme-Allowlist

In `author.php` und `authors.php` werden öffentliche Detailwerte vom Typ `url` direkt als `href` ausgegeben. Das Escaping schützt gegen HTML-Bruch, **aber nicht gegen gefährliche Schemes** wie `javascript:`.

**Warum wichtig:** Diese Felder wirken nicht wie ein reiner Admin-Kanal, sondern wie öffentliche Profil-/Detaildaten. Dadurch ist das Risiko höher als bei einem absichtlich rohen Admin-Escape-Hatch.

**Empfehlung:**

- nur `http`, `https`, optional `mailto` erlauben
- alles andere als Klartext statt als Link rendern

##### SEC-02 – roher Head-/Footer-Code als Admin-Escape-Hatch

`outputCustomHeaderCode()` und `outputCustomFooterCode()` geben absichtlich rohen Code aus. Das ist funktional legitim, aber sicherheitlich ein bewusstes Loch in die Sandbox.

**Bewertung:** kein Bug im engeren Sinn, aber ein **hoch privilegierter Escape-Hatch**.

**Empfehlung:**

- in der Doku explizit als Admin-only-Superfeature kennzeichnen
- optional Capability enger ziehen als nur generisches `isAdmin()`
- Änderungen auditierbar machen (wer, wann, was)

**Status:** umgesetzt. Der Save-/Import-Flow verlangt jetzt eine explizite Bestätigung für Raw-Code-Änderungen, prüft zusätzlich Capability-basierte Berechtigung und schreibt Hash-/Längen-Fingerprints der betroffenen Felder ins Core-Audit-Log statt den Klartext-Code zu protokollieren.

##### SEC-03 – redundante Kommentar-POST-Logik in `post.php`

Das aktuelle Formular in `partials/post-comments.php` postet auf `/comments/post`. In `post.php` existiert zusätzlich noch ein eigener POST-Handler mit anderer Token-Prüfung (`comment_post_{id}` vs. aktuelles Formular-Token `comment_{id}`).

**Folge:**

- Verwirrung beim Review
- erhöhtes Risiko bei späterem Refactoring
- zwei konkurrierende Wahrheiten für denselben Flow

##### SEC-04 – Import-Validierung zu großzügig

Der JSON-Import prüft im Wesentlichen nur:

- Upload erfolgreich
- Größe unter 512 KB
- `json_decode()` erfolgreich

Es fehlt eine strikte Feld-/Struktur-Allowlist.

**Status:** umgesetzt. Der Import akzeptiert jetzt nur noch verifizierte JSON-Uploads unter 512 KB, prüft Dateiendung plus MIME-Typ, verlangt die erwartete Root-Struktur des Theme-Customizer-Exports und protokolliert abgelehnte Upload-/Payload-Fälle im Core-Audit-Log.

##### SEC-05 – `extract()` als stilles Risiko

`extract()` ist hier derzeit intern kontrolliert, aber als Muster fehleranfällig. Es erschwert Nachvollziehbarkeit und erhöht die Chance auf unbeabsichtigtes Variablen-Shadowing.

**Status:** teilweise umgesetzt. Die Startseite arbeitet jetzt mit expliziten Zuweisungen statt `extract()`, und der Partial-Loader verwendet einen validierten lokalen Scope statt `extract($vars)`. Der verbleibende Altpfad betrifft vor allem den Upstream-Render-Handoff in `blog-single.php`/`ThemeManager`.

#### Empfehlung

1. URL-Allowlist für öffentliche Linkfelder sofort nachziehen.  
2. Kommentar-Altlogik aus `post.php` entfernen oder klar als Legacy-Fallback isolieren.  
3. Customizer-Import strikt auf bekannte Kategorien/Felder begrenzen.  
4. `extract()` schrittweise durch explizite View-Model-Zuweisungen ersetzen.

### 3. Performance-/Geschwindigkeits-Audit

#### Positiv

- CSS wird bereits requestabhängig geladen
- zusätzliche Spezial-CSS-Dateien werden nicht pauschal für alle Seiten ausgegeben
- Preconnect/DNS-Prefetch ist prinzipiell vorhanden
- Above-the-fold-Helfer `phinit_image_loading_attributes(true)` nutzt `fetchpriority="high"`
- `loading="lazy"` und `decoding="async"` werden systematisch ergänzt

#### Befunde

##### PERF-01 – Request-Klassifizierung erzeugt Laufzeitkosten im Asset-Pfad

`includes/theme-assets-trait.php` entscheidet CSS-Ladung u. a. über:

- `isPostRequest()`
- `isHubSiteRequest()`
- `isPageDetailRequest()`
- `isRichContentRequest()`

Dabei werden teils Datenbankabfragen zur Route-Erkennung verwendet. Das ist funktional verständlich, aber teuer, weil der Code im Head-Pfad liegt.

**Empfehlung:**

- Routing-Kontext einmal zentral ermitteln und cachen
- Asset-Entscheidung auf bereits bekannte Request-Metadaten stützen

##### PERF-02 – mehrfach ähnliche Header-Abfragen

`includes/theme-head-trait.php` lädt für einen Request mehrfach ähnliche Informationen:

- Meta-Tags
- Schema.org
- Breadcrumb
- Seitentitel

Vor allem auf Post-/Page-Requests entstehen dadurch mehrere einzelne Abfragen für verwandte Daten.

**Empfehlung:**

- Header-ViewModel pro Request aufbauen
- Post/Page-Datensatz einmal laden und wiederverwenden

##### PERF-03 – globales JavaScript-Bundle für alle Seiten

`assets/js/navigation.js` enthält:

- Navigation
- Dark Mode
- TOC-Interaktion
- Consent
- Code-Copy
- Featured-Rotator
- Passkey-Handling
- Backup-Code-Copy

Das ist praktisch, aber nicht jede Seite braucht alle Features.

**Umgesetzt:**

- `assets/js/navigation.js` enthält jetzt nur noch globale UI-Bausteine wie Navigation, Dark Mode, Progressbar, Scroll-Animationen, Flash-Messages, Consent und Back-to-top.
- `assets/js/content-interactions.js` lädt TOC-, Share- und Code-Copy-Logik nur für Rich-Content-Requests.
- `assets/js/homepage-widgets.js` lädt Featured-Rotator-/Badge-Logik nur für Blog-/Homepage-Requests.
- `assets/js/member-security.js` lädt Passkey-/Backup-Code-Interaktionen nur für `/member/security`.
- `includes/theme-assets-trait.php` verdrahtet die Dateien jetzt requestabhängig statt das frühere Komplettpaket global auszugeben.

**Empfehlung:**

- mindestens in 2–3 Bündel schneiden, z. B. `core`, `content`, `member`
- oder per data-attributgestütztem on-demand Import weiterentwickeln

##### PERF-04 – pauschales Lazy Loading aller Content-Bilder

`phinit_enhance_content_images()` ergänzt allen Inhaltsbildern `loading="lazy"`, sofern das Attribut fehlt. Das spart Daten, kann aber erste sichtbare Content-Bilder verlangsamen und ohne `width`/`height` Layoutverschiebungen begünstigen.

**Status:** weitgehend umgesetzt. Das erste Inhaltsbild bleibt eager/high-priority; zusätzlich ergänzt die Runtime bei lokal auflösbaren Bildern jetzt fehlende `width`-/`height`-Attribute automatisch. Relevante Karten- und Hero-Templates schreiben Dimensionsattribute ebenfalls mit, sodass Lazy-Loading-Bilder weniger häufig Layoutverschiebungen verursachen.

**Empfehlung:**

- erste sichtbare Editor-/Artikelbilder nicht automatisch lazy laden
- Bilddimensionen, wenn möglich, mitschreiben oder serverseitig anreichern

##### PERF-05 – keine Messwerte im Theme selbst verankert

Der ursprüngliche Inline-Style-Befund auf der Startseite ist weitgehend bereinigt: Die Homepage setzt ihre dynamischen Spacing-Werte und Projekt-Logo-Hintergründe jetzt über CSS-Variablen am jeweiligen Element statt über einen Template-`<style>`-Block. Zusätzlich definiert `DOC/PERFORMANCE-BUDGETS.md` jetzt einen verbindlichen Minimalprozess für Zielseiten, Grenzwerte und Review-Dokumentation. Offen bleibt vor allem eine spätere CI-Automatisierung.

Es gibt derzeit keinen sichtbaren Prozess für:

- Performance-Budgets
- CWV-Grenzen
- regelmäßige Lighthouse-Prüfung

**Hinweis:** In diesem Audit wurden keine echten Lab-/Field-Metriken gemessen. Das ist ein separater nächster Schritt.

## Empfohlene Abarbeitungsreihenfolge

### Phase 1 – Sicherheits- und Datenintegritäts-Fixes

1. URL-Allowlist für öffentliche Author-/Profil-Links einbauen  
2. Customizer-Request-Validierung gegen Schema härten  
3. Key-Mismatch `custom_header_code` / `custom_head_code` bereinigen  
4. Kommentar-Legacy-Pfad in `post.php` entschlacken

### Phase 2 – Customizer konsolidieren

1. `theme.json` als Primärquelle festschreiben  
2. PHP-Schema nur für UI/Legacy behalten  
3. Import/Export strikt auf bekannte Keys begrenzen  
4. fehlende neue Settings sauber in Tabs/Gruppen einhängen

### Phase 3 – Performance-Refactoring

1. Routing-/Request-Kontext einmalig cachen  
2. Header-Metadaten in ein Request-ViewModel bündeln  
3. JS in kleinere Verantwortungsbereiche schneiden  
4. Bildstrategie für LCP/CLS schärfen

### Phase 4 – Messen statt schätzen

1. Lighthouse auf Startseite, Blog-Archiv, Post, Member-Seite  
2. WebPageTest oder Chrome Performance für LCP/CLS/INP  
3. Budgets definieren, z. B. CSS/JS-Größe und Request-Anzahl

## Dateiinventar des Themes

## Root-Dateien

- `404.php` — 404-Template mit Such-/Weiterleitungs- und Empfehlungsausgabe.
- `author.php` — öffentliche Einzel-Autorenseite mit Profilblock und Beitragsliste.
- `authors.php` — Übersichtsseite „Alle Autoren“.
- `blog.php` — Blog-Archiv mit Suche, Listing und Pagination.
- `blog-single.php` — Adapter-/Wrapper-Template für einzelne Blogposts.
- `category.php` — Kategorie-Archivtemplate.
- `error.php` — generisches Fehler-Template.
- `footer.php` — globaler Footer, Consent-Banner, Footer-Menüs und Back-to-top.
- `functions.php` — Theme-Bootstrap, Hook-Registrierung, Includes und Singleton-Start.
- `header.php` — globaler Head-/Header-Bereich mit Navigation, Search, Quicklinks und Member-Bar.
- `index.html` — statische Platzhalter-/Artefakt-Datei ohne Theme-Logik.
- `index.php` — Startseiten-Template mit Repo-Card, Listen, Kacheln und Feeds.
- `login.php` — Login-Seitentemplate im Theme-Stil.
- `page.php` — Standard-Seitentemplate mit Layoutumschaltung, TOC und Spezialseiten-Fallbacks.
- `page-landing.php` — Landingpage-Template mit Hero-/CTA-/Feature-Logik.
- `page-wide.php` — breite Seitenvariante ohne Standard-Content-Spaltenlogik.
- `post-tech.php` — Einzelpost-Variante mit zusätzlicher Tech-Info-Karte.
- `post-wide.php` — Einzelpost-Variante ohne Sidebar, mit Inline-TOC.
- `post.php` — Standard-Einzelpost mit Sidebar, Share, TOC, Navigation und Kommentaren.
- `register.php` — Registrierungsseite im Theme-Stil.
- `search.php` — Suchergebnisseite.
- `sitemap.php` — HTML-Sitemap für Leser.
- `style.css` — globale Basis-Styles und Theme-Header.
- `tag.php` — Tag-Archivtemplate.
- `theme.json` — kanonische Theme-Metadaten, Templates, Supports und Customization-Definition.
- `update.json` — Versions- und Update-Metadaten.

## Dokumentation

- `DOC/PERFORMANCE-BUDGETS.md` — Zielwerte, Prüfablauf und Review-Regeln für Lighthouse-/CWV-nahe Performance-Prüfungen.
- `DOC/TEMPLATES.md` — interne Theme-Dokumentation zu Templates und Zuordnungen.

## Includes

- `includes/theme-assets-trait.php` — CSS-/JS-Ladung, Font-Handling, Customizer-CSS und Custom-Code-Ausgabe.
- `includes/theme-content-helpers.php` — Content-Rendering, Bildanreicherung, TOC-/Heading-Helfer und Text-Utilities.
- `includes/theme-head-trait.php` — Meta-Tags, Schema.org, Breadcrumbs, Body-Class und Seitentitel.
- `includes/theme-home-helpers.php` — Datenaufbereitung für die Startseite.
- `includes/theme-media-archive-helpers.php` — Hilfen für Bild-/Medienarchivseiten.
- `includes/theme-navigation-trait.php` — Menüregistrierung und Navigationsnahe Theme-Logik.
- `includes/theme-special-pages-helpers.php` — Hilfen für Sitemap, Autorenübersicht, Tags und Spezialseiten.
- `includes/theme-template-helpers.php` — allgemeine Template-Helfer, Übersetzungen, Favoriten-Utilities und kleine View-Helper.

## Admin / Customizer

- `admin/customizer.php` — zentraler Einstiegspunkt des Theme-Customizers.
- `admin/customizer-action-bar.php` — obere Aktionsleiste des Customizers.
- `admin/customizer-alert.php` — Status-/Hinweisboxen des Customizers.
- `admin/customizer-color-presets.php` — Verwaltung/Anzeige von Farb-Presets im UI.
- `admin/customizer-config-builder.php` — baut die UI-Basisconfig aus `theme.json` und merged Legacy-Schema.
- `admin/customizer-content-column.php` — Hauptspalte mit Feldlisten und Tab-Inhalt.
- `admin/customizer-field-renderer.php` — rendert einzelne Feldtypen inkl. Number, Select, Textarea, Post-Picker.
- `admin/customizer-form-hidden-fields.php` — versteckte Formularfelder für Tab-/Action-Kontext.
- `admin/customizer-form.php` — Formular-Wrapper für Save/Reset/Import/Export.
- `admin/customizer-header-menu-note.php` — Hinweisbereich für Menü-/Headernahe Einstellungen.
- `admin/customizer-page-body.php` — zusammengesetzter Seitenkörper des Customizers.
- `admin/customizer-page-header.php` — Seitenkopf des Customizers.
- `admin/customizer-preview-drawer.php` — Preview-/Drawer-Komponente.
- `admin/customizer-request-handler.php` — POST-Verarbeitung für Save, Reset, Export und Import.
- `admin/customizer-schema.php` — ergänzendes UI-/Legacy-Schema mit Gruppen- und Tab-Definition.
- `admin/customizer-scripts.php` — Customizer-JavaScript für Admin-Interaktionen.
- `admin/customizer-sidebar.php` — Navigations-/Sidebar-Bereich der Customizer-Ansicht.
- `admin/customizer-styles.php` — Inline- oder fragmentbasierte Admin-Customizer-Styles.
- `admin/customizer-tab-extras.php` — Zusatzinhalte für bestimmte Tabs.
- `admin/customizer-tab-groups.php` — Gruppierungslogik/Markup für Tab-Abschnitte.

## Assets / JavaScript

- `assets/js/navigation.js` — schlankes Core-Frontend-Skript für globale Navigation, Dark Mode, Scroll-/UI-Chrome, Consent und Basis-Interaktionen.
- `assets/js/content-interactions.js` — bedarfsgeladenes Frontend-Skript für TOC, Share-Aktionen und Code-Copy auf Rich-Content-Seiten.
- `assets/js/homepage-widgets.js` — bedarfsgeladenes Frontend-Skript für Featured-Badges und Rotatoren auf Blog-/Homepage-Modulen.
- `assets/js/member-security.js` — bedarfsgeladenes Frontend-Skript für Passkey-Registrierung und Backup-Code-Copy im Sicherheitsbereich.

## Assets / CSS

- `assets/css/content-cards.css` — Karten-/Grid-Styles für Listen- und Archivkarten.
- `assets/css/footer-consent.css` — Footer- und Consent-Banner-spezifische Styles.
- `assets/css/header-navigation.css` — Header-, Hauptnavigation-, Burger- und Dropdown-Styles.
- `assets/css/homepage-blog.css` — Startseiten-Layout für Blog-/Homepage-Module.
- `assets/css/hub-sites.css` — Styles für Hub-/SiteTable-basierte Hub-Seiten.
- `assets/css/member-auth.css` — Auth-/Member-bezogene Oberflächenstyles.
- `assets/css/page-cookie-consent.css` — Styles für die Cookie-Consent-Inhaltsseite.
- `assets/css/page-detail.css` — Styles für statische Seiten-Layouts.
- `assets/css/page-extras.css` — Styles für Sonderseiten wie Suche/404/Fehler.
- `assets/css/page-image-archive.css` — Styles für Bild-/Medienarchivseiten.
- `assets/css/page-special-pages.css` — Styles für Sitemap, Autorenübersicht und Archiv-Spezialseiten.
- `assets/css/post-detail.css` — Styles für Einzelartikel inklusive Share-Elemente.
- `assets/css/post-sidebar.css` — Styles für Post-Sidebar, TOC und Nebenmodule.
- `assets/css/rich-content.css` — Editor-/Rich-Content-Regeln für Fließtext, Heading-Spacings und Medien.
- `assets/css/templates.css` — templateübergreifende Layout-/Komponentenstyles.
- `assets/css/ui-chrome.css` — globale UI-Chrome-Elemente wie Progressbar, Buttons und Utility-Komponenten.

## Partials

- `partials/blog-archive-pagination.php` — Pagination-Baustein für Blog-, Kategorie- und Tagarchive.
- `partials/blog-archive-toolbar.php` — Toolbar mit Suche/Navigation für Archive.
- `partials/home-article-list.php` — Startseitenmodul für die horizontale Artikelliste.
- `partials/home-feed-sections.php` — Startseitenmodul für Feed-Sektionen.
- `partials/home-info-grid.php` — Startseitenmodul für Themen-/Info-Karten.
- `partials/home-post-grid.php` — Startseitenmodul für das paginierte Post-Kachelgrid.
- `partials/home-repo-card.php` — Startseitenmodul für die Repo-/Projekt-Karte.
- `partials/page-cookie-consent.php` — Content-Partial für die Cookie-Consent-Seite.
- `partials/page-header-block.php` — standardisierter Seitenkopf für statische Seiten.
- `partials/page-image-archive.php` — Rendering-Partial für Bild-/Medienarchive.
- `partials/page-inline-toc.php` — Inline-Inhaltsverzeichnis für Seiten.
- `partials/page-sidebar-nav.php` — Seitenleiste mit Navigation für zweispaltige Seiten.
- `partials/post-card.php` — wiederverwendbare Beitragskarte für Archive und Listen.
- `partials/post-author-box.php` — optionale Autorenbox unter Einzelartikeln auf Basis der Posts-Customizer-Einstellungen.
- `partials/post-comments.php` — Kommentarlisting und Kommentarformular.
- `partials/post-header.php` — Kopfbereich eines Einzelposts mit Meta und Hero.
- `partials/post-inline-toc.php` — Inline-TOC für breite Postvariante.
- `partials/post-navigation.php` — Vor-/Zurück-Navigation zwischen Artikeln.
- `partials/post-sidebar-social.php` — Social-/Follow-Modul für die Post-Sidebar.
- `partials/post-sidebar-toc.php` — TOC-Modul für die Post-Sidebar.
- `partials/post-tags.php` — Tag-Ausgabe für Beiträge.
- `partials/post-tech-card.php` — Tech-Informationskarte für `post-tech.php`.
- `partials/post-tech-sidebar-info.php` — ergänzendes Sidebar-Modul für Tech-Artikel.
- `partials/search-archive-panel.php` — Such-/Archiv-Panel für die Suchseite.
- `partials/search-empty-state.php` — Empty-State für erfolglose Suchen.
- `partials/search-result-row.php` — einzelne Trefferzeile in Suchergebnissen.
- `partials/sidebar.php` — zentrale Post-Sidebar-Komposition.

## Member-Bereich

- `member/analytics.php` — Member-/Admin-Analytics-Ansicht im Theme-Stil.
- `member/comments.php` — Member-Ansicht für eigene Kommentare.
- `member/dashboard.php` — zentrales Member-Dashboard.
- `member/favorites.php` — Liste gespeicherter Favoriten.
- `member/feeds.php` — Verwaltung der Feed-Abos im Member-Bereich.
- `member/forum.php` — Forumseinstieg bzw. Forumsteil im Member-Layout.
- `member/messages.php` — Member-Nachrichtenansicht.
- `member/newsletter.php` — Newsletter-Einstellungen im Member-Bereich.
- `member/notifications.php` — Benachrichtigungsübersicht.
- `member/posts.php` — Beitragsübersicht für das Mitglied/den Autor.
- `member/privacy.php` — Datenschutz-/Privatsphäre-Einstellungen.
- `member/profile.php` — Profilbearbeitung und Profildaten.
- `member/security.php` — Sicherheitsseite inkl. Passkeys/2FA-naher Oberfläche.
- `member/partials/member-nav.php` — Member-Navigationspartial.

## Stärken, die erhalten bleiben sollten

- modulare Trennung von Root-Templates, Partials, Includes, Admin und Assets
- requestabhängige CSS-Ausgabe statt pauschaler Komplettladung
- lokalisierungsfähige Pfad-/URL-Helfer
- konsequente Verwendung von Theme-Helfern für Content-Aufbereitung
- insgesamt gute Lesbarkeit trotz Funktionsbreite

## Konkrete nächste Datei-Pakete für die Abarbeitung

### Paket A – zuerst anfassen

- `admin/customizer-request-handler.php`
- `admin/customizer-schema.php`
- `theme.json`
- `includes/theme-assets-trait.php`
- `author.php`
- `authors.php`

### Paket B – danach

- `includes/theme-head-trait.php`
- `post.php`
- `partials/post-comments.php`
- `assets/js/navigation.js`
- `assets/js/content-interactions.js`
- `assets/js/homepage-widgets.js`
- `assets/js/member-security.js`

### Paket C – zuletzt / strukturell

- `index.php`
- `includes/theme-template-helpers.php`
- übrige Member- und Partial-Dateien nach Bedarf

## Abschlussbewertung

`cms-phinit` ist **kein chaotisches Theme**, sondern bereits eine tragfähige, moderne Basis mit klarer Ausbau-Richtung. Die größten Baustellen sind aktuell **nicht das sichtbare Frontend**, sondern die **Verlässlichkeit zwischen Customizer-Schema, gespeicherten Werten und sicherer Runtime-Ausgabe**.

Wenn die Phasen 1 und 2 umgesetzt werden, steigt das Theme deutlich in drei Dimensionen:

- weniger Drift zwischen Definition und Wirkung
- bessere Sicherheitslage bei öffentlichen und adminseitigen Eingabekanälen
- bessere Grundlage für echte Performance-Messläufe
