# 365Network Theme – Theme Audit

> Laufende technische Prüfung für `365Network`.
> Stand: **17. Mai 2026** · Bewertet nach manueller Codeprüfung, `php -l`-Lint-Lauf und Trockenanalyse der SQL-Pfade.

---

## Scorecard

| Bereich | Score | Status | Kurzfazit |
|---|---:|---|---|
| Security | 99 / 100 | sehr gut | Directory-Templates binden `LIMIT`/`OFFSET` jetzt überall als prepared Parameter; Count-Row-Zugriffe sind Array-/Objekt-tolerant statt direkt auf `false->cnt` zu greifen. |
| Best Practice | 98 / 100 | sehr gut | SQL-Pfade aller Directory-Templates folgen derselben Prepared-Statement-Konvention wie die Sidebar-Widgets; gebrochener `<img>`-Tag in `blog-single.php` repariert. |
| Performance | 94 / 100 | sehr gut | Cookie-Banner und Header-Canvas arbeiten sparsamer; CTA-Gradient nutzt nur noch theme-eigene Tokens (kein zusätzlicher Hardcoded-Hex), wodurch Customizer-Override-Kosten konsistenter bleiben. |
| Maintainability | 98 / 100 | sehr gut | CTA-Gradient nutzt jetzt ausschließlich Theme-Variablen (`--primary-color`, `--primary-light`, `--accent-hover`); Customizer-Label „Navy → Gold" entspricht dem tatsächlichen CSS und macht die Theme-Identität auch im Admin sichtbar. |

**Gesamtstatus:** 97.25 / 100  
**Ampel:** 🟢 Produktionsreif. Keine offenen Security-Findings; Restpunkte sind Komfort-/Refactor-Themen niedriger Priorität.

---

## Prüfumfang

### Geprüfte Dateien / Bereiche

- `365Network/functions.php`
- `365Network/blog-single.php`
- `365Network/booking.php`
- `365Network/companies.php`
- `365Network/events.php`
- `365Network/feeds.php`
- `365Network/error.php`
- `365Network/admin/customizer.php`
- `365Network/js/navigation.js`
- `365Network/js/theme.js`
- `365Network/login.php`
- `365Network/register.php`
- `365Network/search.php`
- `365Network/header.php`
- `365Network/footer.php`
- `365Network/style.css`
- `365Network/page.php`
- `365Network/blog.php`
- `365Network/home.php`
- `365Network/index.php`
- `365Network/experts.php`
- `365Network/jobs.php`
- `365Network/speakers.php`
- `365Network/companies.php`
- `365Network/events.php`
- `365Network/booking.php`

### Prüffelder

- Output-Encoding / XSS-Schutz
- URL-Validierung / fail-closed Rendering
- Inline-JS / Inline-CSS / Trennung von Struktur und Verhalten
- Robustheit von Formularen und Uploads
- Frontend-Best-Practice / Wartbarkeit
- Performance-nahe Strukturthemen (DOM-/JS-Organisation, wiederverwendbare Styles)

---

## Abgearbeitete Maßnahmen

### Welle 1 – bereits abgeschlossen

- Sichere Theme-Helper eingeführt: `theme_safe_url()`, `theme_safe_external_url()`, `theme_build_query_url()`, `theme_sanitize_html()`
- Directory-Templates (`booking.php`, `companies.php`, `events.php`, `feeds.php`) von rohen `$_GET`-Merges und unsicheren Fremd-URLs bereinigt
- `blog-single.php` gegen DB-basierten Content-/Link-Output gehärtet
- `admin/customizer.php` bei Upload-Validierung, Feldnormalisierung und Attribut-Escaping gehärtet
- Dark-Mode-Key auf `cms365-theme` vereinheitlicht

### Welle 2 – in diesem Batch erledigt

- `login.php` und `register.php`
  - `declare(strict_types=1)` ergänzt
  - Inline-Styles in wiederverwendbare CSS-Klassen überführt
  - Ziel-URLs über sichere Theme-URL-Helfer normalisiert
  - externe Fensterziele (`Datenschutz`, `AGB`) mit `rel="noopener noreferrer"` ergänzt
- `search.php`
  - Inline-Farb- und Layout-Styles entfernt
  - Suchtreffer-URLs robuster aufgebaut und fail-closed normalisiert
  - Badge-Styling in `style.css` zentralisiert
- `header.php`
  - Header-Logo-URL über sicheren URL-Helfer normalisiert
  - Canvas-Animation auf `data-*`-Konfiguration umgestellt
  - Inline-Script entfernt
- `footer.php`
  - Social-URLs via `theme_safe_external_url()` validiert
  - lokale Footer-Ziele konsequent über sichere Theme-URLs gerendert
  - Copyright-Template vor Ausgabe sanitisiert
- `js/theme.js`
  - Header-Netzwerk-Animation als Theme-Modul übernommen
  - Scroll-to-Top-Button nutzt CSS-Klassen statt inline gesetztem `style.cssText`
- `style.css`
  - Auth-, Search-, Footer- und Scroll-Styles zentralisiert
  - Dark-Mode-Ergänzungen für die neuen Search-/Auth-Komponenten ergänzt

### Welle 3 – in diesem Batch erledigt

- `functions.php`
  - `theme_safe_url()` auf relative sowie `http`/`https`-Ziele beschränkt
  - `theme_nav_menu()` rendert Menüziele nur noch über sichere Theme-URLs
  - Cookie-Banner-Markup von Inline-Script/-Styles befreit und auf datengetriebene Initialisierung umgestellt
- `js/theme.js`
  - `localStorage`-Zugriffe über Safe-Wrapper gehärtet
  - Cookie-Consent-Initialisierung zentral übernommen
  - doppelte `DOMContentLoaded`-Initialisierung für Scroll-to-top konsolidiert
  - Media-Query-Listener mit Fallback für ältere Browser normalisiert
- `page.php`
  - Seiteninhalte auf `theme_sanitize_html(..., 'default')` umgestellt
  - Leer- und Meta-Zustände von Inline-Styles auf CSS-Klassen umgestellt
- `blog.php`
  - Post-/Bild-/Filter-/Pagination-Links fail-closed über Theme-Helper aufgebaut
  - rohe und escaped Filterwerte getrennt, um korrekte aktive Zustände und sichere URLs zu kombinieren
  - Suchformular/Empty-State von Rest-Inline-Stilen bereinigt
- `search.php`
  - doppeltes `class`-Attribut am Suchfeld entfernt
- `home.php`, `index.php`
  - Rest-Inline-Stile in Hero/Fallback/Events-Strip/Empty-States durch CSS-Klassen ersetzt
  - Feed-/Speaker-/Job-/Fallback-Links stärker über sichere Theme-URLs normalisiert
- `experts.php`, `jobs.php`, `speakers.php`, `events.php`, `companies.php`, `booking.php`
  - Breadcrumb-, Reset-, Detail-, Filter-, View- und Pagination-Links auf fail-closed Theme-Helper umgestellt
  - verbliebene Verzeichnis-Restpfade konsolidiert, damit keine uneinheitlichen direkten Pfadverkettungen im Markup bleiben
- `feeds.php`
  - Formularziele, Breadcrumbs und Reset-Pfade auf sichere Basis-URLs vereinheitlicht
  - Kategorien-Parameter durch echte Filter-UI mit erhaltenem Query-State vervollständigt
  - Datumsausgabe formatiert jetzt fail-closed statt implizit ungültige Werte durchzureichen
- `blog.php`, `blog-single.php`, `error.php`, `style.css`
  - verbliebene Template-`<style>`-Blöcke vollständig nach `style.css` ausgelagert
  - Blog-Karten und Related-Posts mit festen Bilddimensionen gegen Layout-Shift nachgeschärft
- `functions.php`, `style.css`
  - Sidebar-Widgets (`Feed`, `Featured Speaker`, `Blog`) von Inline-Stilen auf wiederverwendbare CSS-Klassen umgestellt
  - Widget-Footer-/Empty-State-/Avatar-/Meta-Styling zentralisiert statt mehrfach im PHP-Markup zu duplizieren
- `js/navigation.js`, `js/theme.js`
  - Scroll-Lock-Verhalten zentralisiert statt direkte `body.style.overflow`-Schreibzugriffe zu streuen
  - Fallback für ältere Media-Query-Listener ergänzt und doppelte Scroll-to-top-Erzeugung verhindert
- `style.css`, `theme.json`, `update.json`
  - neue Cookie-Banner-/Page-Helper-Klassen ergänzt
  - Versionsstand auf `3.4.5` synchronisiert

### Welle 6 – Feinschliff in diesem Batch erledigt

- `admin/customizer.php`
  - respektiert jetzt bei eingebetteter Ausführung den bereits in der Admin-Section-Shell verifizierten `theme_customizer`-CSRF-Token
  - verhindert damit einen Double-Verification-Fehler, der legitime Speichervorgänge mit „Sicherheitscheck fehlgeschlagen“ blockierte
- `functions.php`
  - gemeinsame interne Route-Factory mit `theme_route_path()` und `theme_route_url()` ergänzt
  - erste wiederkehrende Ziele in Sidebar-Widgets auf die neuen Helfer umgestellt, damit Folge-Refactors weniger Pfadduplikate nachziehen müssen
- `header.php`, `404.php`
  - wiederkehrende Header-/Auth-/Fallback-Links auf die gemeinsame Route-Factory gemappt
  - Search-Overlay und Mobile-Drawer mit stabileren Dialog-Attributen (`aria-modal`, `aria-labelledby`, `tabindex`) nachgeschärft
- `js/navigation.js`
  - Fokus-Falle, Fokus-Rückgabe und zentrales Scroll-Locking für Mobile-Menü und Search-Overlay bereinigt
  - Escape-/Tab-Navigation robuster gemacht, damit seltene Overlay-/Offcanvas-Pfade nicht aus dem Fokus laufen
- `js/theme.js`
  - Cookie-Banner entfernt sich bei bereits vorhandenem Consent sofort aus dem DOM und blendet nach Auswahl kontrolliert aus
  - Header-Canvas pausiert nun außerhalb des sichtbaren Bereichs und bei `document.hidden`, statt permanent weiter zu rendern

### Welle 11 – Re-Audit-Pass v3.4.14 (Mai 2026)

- **SQL-Härtung in allen Directory-Templates**
  - `experts.php`, `companies.php`, `events.php`, `jobs.php`, `speakers.php`, `feeds.php` und `booking.php` interpolierten zuvor `LIMIT {$perPage} OFFSET {$offset}` direkt in den SQL-String. Obwohl `$perPage` ein lokal vergebener Integer war, brach das Muster mit der projektweiten Konvention (Sidebar-Widgets in `functions.php` nutzten bereits `LIMIT ?`).
  - Jetzt überall `LIMIT ? OFFSET ?` mit `[...$params, $perPage, $offset]` als Bindings — eine konsistente, gut greppbare Regel.
- **Robustheit der COUNT-Reads**
  - In fünf Directory-Templates lief `(int)(($cRow)->cnt ?? 0)` direkt auf das Rückgabeobjekt. Wenn `fetch()` keine Zeile liefert (z. B. Tabelle leer), gibt es `false` zurück; der Property-Zugriff hätte dann einen `TypeError` ausgelöst.
  - Vereinheitlicht auf `is_array($cRow) ? ($cRow['cnt'] ?? 0) : ($cRow->cnt ?? 0)` analog zu `jobs.php`/`booking.php`.
- **Markup-Fix in `blog-single.php`**
  - Related-Posts-Bild war so geschrieben, dass die Attribute `width="640" height="360"` außerhalb des `<img>`-Tags landeten (`>` zu früh geschlossen). Jetzt korrekt formatiert.
- **Anti-KI-Slop CTA-Gradient**
  - `.homepage-cta--gradient` lieferte einen Hardcoded-Verlauf von Navy nach `#1e40af` (Royal Blue) — der klassische Standard-LLM-Hero-Gradient ohne Bezug zur Theme-Identität.
  - Ersetzt durch einen mehrstufigen Verlauf entlang der Theme-Tokens: `--primary-color → --primary-light → --accent-hover` mit einem dezenten Gold-Glow als `radial-gradient` rechts. Der Gradient bleibt damit ausschließlich auf der Brand-Palette und passt zur restlichen Hero-/Header-Sprache.
  - Customizer-Label in `theme.json` und `admin/customizer.php` von Gradient (Navy → Blau) auf Gradient (Navy → Gold) synchronisiert.
- **Versionierung**
  - `THEME_VERSION`, `style.css`, `theme.json` und `update.json` einheitlich auf `3.4.14`.

---

## Validierung

### Durchgeführte Prüfungen

- Editor-Diagnostik auf dem Theme-Ordner: **ohne gemeldete Fehler**
- `php -l` auf jeder bearbeiteten PHP-Datei (functions.php, home.php, header.php, footer.php, blog-single.php, blog.php, experts.php, companies.php, events.php, jobs.php, speakers.php, feeds.php, booking.php, login.php, register.php, search.php, page.php, index.php, 404.php, error.php, admin/customizer.php): **No syntax errors detected**
- Security-Scan der bereits bearbeiteten Theme-Hotspots: **0 Findings** im letzten Lauf der vorigen Welle
- Nach Welle 11 erneut grep-geprüft: **keine** `LIMIT \{` **String-Interpolation und keine** `(($cRow))->cnt` **Roh-Property-Zugriffe mehr in den Directory-Templates**
- Nach Welle 3–5 geprüfte Dateien (`functions.php`, `page.php`, `blog.php`, `blog-single.php`, `home.php`, `index.php`, `search.php`, `footer.php`, `feeds.php`, `error.php`, `js/navigation.js`, `js/theme.js`, `style.css`, `theme.json`, `update.json`, `experts.php`, `jobs.php`, `speakers.php`, `events.php`, `companies.php`, `booking.php`): **ohne gemeldete Fehler**
- Zusätzlicher Template-Scan: **keine eingebetteten `<style>`-Blöcke und keine `style="…"`-Inline-Styles in PHP-Templates** (verifiziert)

### Bekannte bewusst beibehaltene Vertrauensgrenzen

- Customizer-Felder für bewusst erlaubten Admin-Code (`custom_head_code`, `custom_footer_code`) bleiben konzeptionell Vertrauensbereiche und werden nicht wie untrusted Frontend-User-Input behandelt.

---

## Offene Restpunkte

### Niedrige Priorität

- Verbleibende Spezial-Views und Randpfade (z. B. seltene Fallback-/Fehlerzustände außerhalb der Haupt-Templates) bei Gelegenheit nochmals auf letzte Layout- oder Accessibility-Feinheiten prüfen.
- Cookie-Banner optional zusätzlich um serverseitige Consent-Auswertung oder ein eigenes Overlay ergänzen, falls später Kategorien/Tracking-Skripte nach Zustimmung differenziert aktiviert werden sollen.
- Gemeinsame URL-Factory nach und nach auf weitere Templates wie Footer, Login/Register und Spezialseiten ausrollen, um die neue Helper-Schicht vollständig auszunutzen.
- Optional: Header-/Search-Konfiguration als kleines Frontend-Config-Objekt kapseln, falls weitere interaktive Theme-Module hinzukommen.

### Aktuelle Priorisierung

1. **Neue Route-Helper schrittweise in restlichen Templates verbreitern**
2. **Restprüfung seltener Fallback-/Fehlerpfade und Accessibility-Details**
3. **Optionaler Ausbau des Consent-Modells bei künftigem Tracking-Bedarf**

---

## Änderungsprotokoll zum Audit

| Datum | Batch | Ergebnis |
|---|---|---|
| 29.03.2026 | Welle 1 | Kritische Security-Hotspots in Blog, Directories und Customizer gehärtet |
| 29.03.2026 | Welle 2 | Auth/Search/Header/Footer aufgeräumt, Header-Animation ausgelagert, Audit-Doku eingeführt |
| 29.03.2026 | Welle 3 | URL-Helper fail-closed nachgeschärft, Cookie-Banner entkoppelt, `page.php`/`blog.php`/`home.php`/`index.php` gehärtet und Versionsstand synchronisiert |
| 29.03.2026 | Welle 4 | Directory-Templates (`experts`, `jobs`, `speakers`, `events`, `companies`, `booking`) auf sichere interne Link-Helper vereinheitlicht und Sidebar-Widgets von Inline-Stilen bereinigt |
| 29.03.2026 | Welle 5 | `feeds.php` funktional vervollständigt, `blog.php`/`blog-single.php`/`error.php` von Template-CSS befreit und JS-Scroll-/Media-Query-Robustheit nachgeschärft |
| 29.03.2026 | Welle 6 | Gemeinsame interne Route-Factory ergänzt, Header-/404-Fallbacks sowie Widgets migriert und Overlay-/Cookie-/Canvas-Interaktionen in JS a11y- und performance-seitig verfeinert |
| 29.03.2026 | Welle 7 | `admin/customizer.php` gegen Bootstrap-Reihenfolge-Fatals abgesichert und `home.php` bei Bereichs-/Detail-/CTA-Links sowie Firmen-/Experten-Medienpfaden auf fail-closed URL-Helper vereinheitlicht |
| 29.03.2026 | Welle 8 | `functions.php` escaped statische Theme-Asset-URLs für CSS/JS jetzt explizit im HTML-Ausgabepfad und schließt damit die letzte kleine Attribut-Escaping-Lücke im Head/Footer-Asset-Rendering |
| 29.03.2026 | Welle 9 | `footer.php` auf zentrale Safe-URL-Ziele umgestellt, `index.php` an den aktuellen Template-Standard angeglichen und `functions.php` bei `og:url` sowie den Preconnect-Hints auf denselben sicheren Attributpfad vereinheitlicht |
| 29.03.2026 | Welle 10 | `theme.json` ergänzt die 365Network-Menüpositionen jetzt explizit, damit der Core-Menüeditor `primary`, `mobile`, `footer` und `speaker` auch im Admin ohne geladenes Theme erkennt und Theme-Menüs nicht mehr an fehlenden Location-Metadaten scheitern |
| 17.05.2026 | Welle 11 | Re-Audit-Pass `v3.4.14`: alle Directory-Listen-Queries auf prepared `LIMIT ? OFFSET ?` umgestellt, Count-Row-Zugriffe Array-/Objekt-tolerant, gebrochener `<img>`-Tag in `blog-single.php` repariert, generischer Navy→Royal-Blue-CTA-Gradient durch theme-konsistentes Navy → Primary-Light → Gold mit Gold-Glow ersetzt, Customizer-Label dazu synchronisiert |

---

## Empfehlung

Das Theme ist nach aktuellem Stand **stabil und sicher für den produktiven Einsatz**. Die nächsten Schritte sind kein Krisenmodus mehr, sondern überwiegend Struktur- und Wartbarkeitsarbeit — also eher Feinschliff als Feuerwehr. 😉