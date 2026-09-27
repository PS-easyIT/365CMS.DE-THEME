# Kontor – Business-Theme für 365CMS

**Version:** 1.0.0
**Ziel:** 365CMS 3.4.00+ · PHP 8.4
**Slug:** `kontor`
**Kategorie:** Business / Unternehmenswebsite

Seriöses, modernes Theme für Mittelständler, Kanzleien, Steuerbüros, Beratungen,
Agenturen und Dienstleister. Tannengrün als Markenanker, Mint als Akzent und warme
Sand-Flächen sorgen für Vertrauen ohne Konzern-Kälte. Die komplette Startseite ist
über den Customizer pflegbar – ganz ohne Seitenbaukasten.

## Startseite

`Hero → Kundenleiste → Leistungen → Über uns → Kennzahlen → Ablauf → Referenz → FAQ → Aktuelles → Kontakt-Band`

Jede Sektion lässt sich einzeln ein- oder ausblenden. Wörter in `*Sternchen*` werden in
Überschriften farbig hervorgehoben (z. B. „Wir machen Ihr Unternehmen *zukunftssicher*.“).

## Templates

| Datei | Zweck |
| --- | --- |
| `home.php` | Startseite mit allen Sektionen |
| `page.php` | Unterseite mit Seitenkopf und Kontaktkarte (Impressum/Datenschutz ohne Seitenleiste) |
| `blog.php` | „Aktuelles“: Blog, Kategorien, Schlagwörter, Autor:innen, Übersichten |
| `category.php`, `tag.php`, `author.php` | nutzen `blog.php` |
| `blog-single.php` | Beitrag mit Seitenleiste, Teilen-Links und verwandten Beiträgen |
| `search.php` | Suche mit Leistungs-Vorschlägen |
| `404.php` | Nicht gefunden im Hero-Stil |
| `error.php` | Eigenständige Fehlerseite (Fatal-Handler) |
| `index.php` | Fallback (z. B. `/autoren`, `/sitemap`) |

## Menüpositionen

| Slug | Verwendung |
| --- | --- |
| `primary` | Hauptnavigation (Fallback: Start, Leistungen, Über uns, Aktuelles, Kontakt) |
| `footer-nav` | Footer-Spalte „Unternehmen“ |
| `footer-services` | Footer-Spalte „Leistungen“ (Fallback: Titel der Leistungen) |
| `footer-legal` | Rechtliche Links |

## Customizer

| Gruppe | Inhalt |
| --- | --- |
| `colors` | Anker, Primär-/Hover-Farbe, Akzent, Text, Flächen, Linien |
| `typography` | Überschrift-/Fließtextschrift, Grundgröße |
| `layout` | Seitenbreite, Eckenradius, Kontaktkarte in Seitenleisten |
| `header` | Logo, fixierter Header, Kontaktleiste, Header-Button |
| `kt_hero` | Hero-Texte, Buttons, Bild, Vertrauenspunkte, Bewertungs-Plakette |
| `kt_trust` | Kundenleiste |
| `kt_services` | Überschrift, Einleitung, bis zu sechs Leistungen (Titel, Text, Symbol, Link) |
| `kt_about` | Über uns: Texte, Stichpunkte, Bild, Link |
| `kt_stats` | Vier Kennzahlen |
| `kt_process` | Vier Ablaufschritte |
| `kt_testimonial` | Zitat, Name, Funktion |
| `kt_faq` | Bis zu fünf Fragen und Antworten |
| `kt_news` | Aktuelles: Überschrift, Anzahl |
| `kt_cta` | Kontakt-Band |
| `kt_contact` | Firmenname, Anschrift, Telefon, E-Mail, Öffnungszeiten, Social-Links |
| `footer` | Kurzbeschreibung, Copyright |
| `advanced` | Eigenes CSS (wird gefiltert) |

## 365CMS-3.4-Vertrag

- Header/Footer über `ThemeManager::render()`, gegen Doppel-Einbindung geschützt.
- `cms_csp_runtime_tags()` als erstes Script, CSP-Nonce für Inline-Styles, keine Inline-Handler, keine HTML-Sinks.
- SEO-Metadaten über `SEOService::renderCurrentHeadTags()`; der Titel läuft über `page_title`.
- `before_footer`/`body_end` im Footer, `body_start` nach `<body>`.
- Beitragslinks über `PermalinkService`, Archivlinks über `cms_get_archive_url()`.
- Google Fonts entfallen bei `privacy_use_local_fonts = 1`; die Schriften werden dann über `local_font_slugs` aus dem Font-Manager geladen (`manrope` …).

## Barrierearmut

Skip-Link, sichtbarer Fokus, `aria-current`, beschriftete Icon-Buttons, FAQ mit nativen
`details`/`summary`, reduzierte Bewegung, `forced-colors` und Druckansicht.
