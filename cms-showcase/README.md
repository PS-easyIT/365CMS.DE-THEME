# 365CMS Showcase – Produkt-Theme für 365CMS

**Version:** 1.0.0
**Ziel:** 365CMS 3.4.00+ · PHP 8.4
**Slug:** `cms-showcase`
**Kategorie:** Produktseite / Landingpage / Dokumentation

Ein Theme, das **365CMS selbst präsentiert** – gedacht für die Produktwebsite, Demo-Instanzen
oder Partnerseiten. Es nutzt die Markenfarben von 365CMS, zeigt das mitgelieferte Logo des
Cores, liest die aktuelle Core-Version live aus und stellt alle installierten Themes automatisch
in einer Galerie vor.

## Startseite

`Ankündigung → Hero mit Dashboard-Vorschau → Kennzahlen → Funktionen + Module → Rollen-Tabs → Sicherheit → Entwickler → Theme-Galerie → Release Notes + Blog → Installation → FAQ → Abschluss-Band`

- **Hero:** Die Dashboard-Vorschau ist reines HTML/CSS (keine Bilddateien) und bildet den
  echten Admin-Bereich nach. Im Customizer kann stattdessen ein eigener Screenshot gesetzt werden.
- **Version:** Das Badge liest `CMS\Version::CURRENT` und `STATUS` – nach einem Core-Update
  aktualisiert es sich von selbst.
- **Theme-Galerie:** Listet alle Themes aus `/themes` (außer dem Showcase selbst). Liegt eine
  `screenshot.png` im Theme-Ordner, wird sie gezeigt, sonst eine Mini-Vorschau in den
  Standardfarben aus der `theme.json` des jeweiligen Themes.
- **Code-Beispiel:** Wird serverseitig mit dem PHP-Tokenizer hervorgehoben und vollständig
  escaped; Terminal- und Code-Fenster haben eine Kopierfunktion.
- Wörter in `*Sternchen*` erhalten in Überschriften den Farbverlauf aus dem Logo.

## Templates

| Datei | Zweck |
| --- | --- |
| `home.php` | Produkt-Startseite mit allen Sektionen |
| `page.php` | Dokumentationsseite mit Inhaltsverzeichnis „Auf dieser Seite“ |
| `blog.php` | Neuigkeiten, Kategorie, Schlagwort, Autor:in, Übersichten |
| `category.php`, `tag.php`, `author.php` | nutzen `blog.php` |
| `blog-single.php` | Beitrag mit Inhaltsverzeichnis, Schlagwörtern, Teilen-Links, „Weiterlesen“ |
| `search.php` | Suche mit Einstiegen bei leeren Ergebnissen |
| `404.php` | Nicht gefunden mit Suche |
| `error.php` | Eigenständige Fehlerseite (Fatal-Handler) |
| `index.php` | Fallback (z. B. `/sitemap` als Seitenübersicht, `/autoren`) |

## Menüpositionen

| Slug | Verwendung |
| --- | --- |
| `primary` | Hauptnavigation (Fallback: Funktionen, Sicherheit, Entwickler, Themes, Neuigkeiten) |
| `footer-product` | Footer-Spalte „Produkt“ |
| `footer-resources` | Footer-Spalte „Ressourcen“ (Fallback inkl. GitHub-Link) |
| `footer-legal` | Footer-Spalte „Rechtliches“ |

## Customizer

| Gruppe | Inhalt |
| --- | --- |
| `colors` | Nachtblau, Primärfarbe (hell/dunkel), drei Verlaufsfarben, Text, Flächen, Linien |
| `typography` | Überschrift-, Fließtext- und Code-Schrift, Grundgröße |
| `layout` | Seitenbreite, Eckenradius, Inhaltsverzeichnis, Einblenden beim Scrollen |
| `header` | Logo, Markenname, Header-Button, GitHub-Repository, Suche |
| `sc_announce` | Ankündigungsleiste |
| `sc_hero` | Kicker, Überschrift, Text, zwei Buttons, Versions-Badge, Vertrauenspunkte, Screenshot |
| `sc_stats` | Vier Kennzahlen |
| `sc_features` | Neun Funktionskacheln mit Symbol, Modul-Liste |
| `sc_roles` | Drei Tabs mit Titel, Text und Stichpunkten |
| `sc_security` | Sicherheitsbereich |
| `sc_dev` | Entwickler-Bereich, Code-Beispiel, Dateiname, drei Dokumentations-Links |
| `sc_themes` | Theme-Galerie (Anzahl, Texte, Link) |
| `sc_releases` | Drei Releases mit Highlights, Blog-Teaser |
| `sc_install` | Voraussetzungen, Schritte, Terminal, Download- und Anleitungs-Link |
| `sc_faq` | Sechs Fragen und Antworten |
| `sc_cta` | Abschluss-Band |
| `footer` | Kurzbeschreibung, Versionsanzeige, Copyright |
| `advanced` | Eigenes CSS (wird gefiltert) |

## 365CMS-3.4-Vertrag

- Header/Footer über `ThemeManager::render()`, gegen Doppel-Einbindung geschützt.
- `cms_csp_runtime_tags()` als erstes Script, CSP-Nonce für alle Inline-Styles/-Scripts
  (auch für die Farbpaletten der Theme-Galerie), keine Inline-Handler, keine HTML-Sinks.
- SEO-Metadaten über `SEOService::renderCurrentHeadTags()`; der Titel läuft über `page_title`.
- `before_footer`/`body_end` im Footer, `body_start` nach `<body>`.
- Beitragslinks über `PermalinkService`, Archivlinks über `cms_get_archive_url()`.
- Das Core-Inhaltsverzeichnis wird ausgeblendet, sobald das Theme sein eigenes zeigt.
- Google Fonts entfallen bei `privacy_use_local_fonts = 1`; die Schriften werden dann über
  `local_font_slugs` aus dem Font-Manager geladen (`plus-jakarta-sans`, `inter`, `jetbrains-mono` …).

## Barrierearmut

Skip-Link, sichtbarer Fokus, Tabs nach WAI-ARIA mit Tastatursteuerung, beschriftete
Icon-Buttons, Statusmeldungen für Kopieraktionen, FAQ mit `details`/`summary`, Unterstützung
für `prefers-reduced-motion` (keine Animationen, kein Einblenden), `forced-colors` und Druck.
