# Rundschau – News-Theme für 365CMS

**Version:** 1.0.0
**Ziel:** 365CMS 3.4.00+ · PHP 8.4
**Slug:** `rundschau`
**Kategorie:** News / Nachrichtenportal

Datengetriebenes Nachrichtenportal für Lokalzeitungen, Fachportale, Vereins- und
Verbandsnachrichten. Alle Bereiche der Startseite füllen sich automatisch aus den
veröffentlichten Beiträgen: Aufmacher, Top-Meldungen, Zeitleiste, Meistgelesen und
Ressort-Blöcke. Petrol als Markenfarbe, Signal-Orange für Live-Hinweise und die
kräftige Archivo-Grotesk geben dem Portal einen klaren Zeitungscharakter.

## Aufbau

`Service-Leiste → Masthead → Ressort-Navigation (sticky) → Ticker`

`Top-Themen (Aufmacher + 4) | Neueste Meldungen + Meistgelesen → Ressort-Blöcke → Im Fokus → Newsletter`

## Templates

| Datei | Zweck |
| --- | --- |
| `home.php` | Startseite |
| `blog.php` | Archiv für Meldungen, Ressorts, Themen, Autor:innen und Übersichten |
| `category.php`, `tag.php`, `author.php` | nutzen `blog.php` |
| `blog-single.php` | Artikel mit Byline, Teilen-Leiste, Seitenleiste, „Mehr zum Thema“ |
| `page.php` | Statische Seite |
| `search.php` | Suche mit Ressort-Seitenleiste |
| `404.php` | Nicht gefunden – mit Suche und neuesten Meldungen |
| `error.php` | Eigenständige Fehlerseite (Fatal-Handler) |
| `index.php` | Fallback (z. B. `/autoren`, `/sitemap`) |

## Menüpositionen

| Slug | Verwendung |
| --- | --- |
| `primary` | Ressort-Navigation (Fallback: Hauptkategorien mit Beiträgen) |
| `service-nav` | Service-Leiste oben (z. B. E-Paper, Newsletter, Kontakt) |
| `footer-nav` | Footer „Service“ |
| `footer-legal` | Rechtliche Links |

## Ressortfarben

Jede Kategorie erhält über `rundschau_ressort_class()` eine stabile Farbklasse
(`.rs-c-0` … `.rs-c-7`, abgeleitet vom Slug). Sie färbt Kicker, Rubrikenlinien,
Archivköpfe und Ressortlisten. Die Farben lassen sich per eigenem CSS anpassen,
z. B. `.rs-c-3 { --rs-ressort: #7c3aed; }`, oder im Customizer ganz abschalten.

## Customizer

| Gruppe | Inhalt |
| --- | --- |
| `colors` | Marke (Petrol), Signalfarbe, Links, Text, Flächen, Linien, Ressortfarben an/aus |
| `typography` | Schlagzeilen-/Artikelschrift, Artikelgröße, schmale Schlagzeilen |
| `layout` | Seitenbreite, Eckenradius, Seitenleiste |
| `header` | Logo, Claim, Service-Leiste, Datum, Suche, fixierte Navigation |
| `rs_ticker` | Ticker an/aus, Bezeichnung, Quelle (neueste/Schlagwort), Anzahl, Tempo |
| `rs_home` | Überschriften, Anzahl Meldungen, Meistgelesen-Zeitraum, Ressort-Auswahl, Fokus-Schlagwort, Newsletter |
| `rs_article` | Lesezeit, Aktualisierung, Teilen, „Mehr zum Thema“ |
| `footer` | Kurzbeschreibung, Ressortliste, Copyright |
| `advanced` | Eigenes CSS (wird gefiltert) |

## 365CMS-3.4-Vertrag

- Header/Footer über `ThemeManager::render()`, gegen Doppel-Einbindung geschützt.
- `cms_csp_runtime_tags()` als erstes Script, CSP-Nonce für alle Inline-Blöcke, keine Inline-Handler, keine HTML-Sinks (Trusted Types).
- SEO-Metadaten über `SEOService::renderCurrentHeadTags()`; `$post`/`$page` werden dafür bereitgestellt, der Titel läuft über `page_title`.
- `before_footer`/`body_end` im Footer, `body_start` nach `<body>`.
- Beitragslinks über `PermalinkService`, Archivlinks über `cms_get_archive_url()`.
- „Meistgelesen“ nutzt die Aufrufzahl (`posts.views`), die der Core beim Artikelaufruf zählt.
- Google Fonts entfallen bei `privacy_use_local_fonts = 1`; stattdessen werden die Schriften über `local_font_slugs` aus dem Font-Manager geladen (`archivo`, `source-serif-4` …).

## Barrierearmut

Skip-Link, sichtbarer Fokus, `aria-current` in Menüs und Brotkrumen, beschriftete
Icon-Buttons, Ticker pausiert bei Hover/Fokus und per Schalter, reduzierte Bewegung
wird respektiert, `forced-colors`-Anpassungen und Druckansicht ohne Navigation.
