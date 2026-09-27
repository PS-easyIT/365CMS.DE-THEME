# Feder – Blog-Theme für 365CMS

**Version:** 1.0.0
**Ziel:** 365CMS 3.4.00+ · PHP 8.4
**Slug:** `feder`
**Kategorie:** Blog

Lesefokussiertes Theme für persönliche Blogs, Autor:innen und Essay-Sammlungen.
Eine schmale Lesespalte, klassische Serifen-Typografie und eine ruhige
Papier-Palette mit Bordeaux-Akzent stellen den Text in den Mittelpunkt.
Ein Hell-/Dunkelmodus, Lesefortschritt und Autorenbox runden das Leseerlebnis ab.

## Startseite

`Autor:innen-Intro → neuester Beitrag (hervorgehoben) → Beitragsliste nach Jahren → Themen → Newsletter-/Kontaktbox`

Alle Inhalte der Startseite werden live aus den veröffentlichten Beiträgen und
Kategorien geladen; Texte der Intro- und Newsletter-Box stammen aus dem Customizer.

## Templates

| Datei | Zweck |
| --- | --- |
| `home.php` | Startseite |
| `blog.php` | Beitragsarchiv (Blog, Kategorie, Schlagwort, Autor:in, Übersichten) |
| `category.php`, `tag.php`, `author.php` | nutzen `blog.php` |
| `blog-single.php` | Beitrag mit Lesezeit, Teilen, Autorenbox, Navigation, verwandten Beiträgen |
| `page.php` | Statische Seite |
| `search.php` | Suche mit Trefferliste und Themen-Vorschlägen |
| `404.php` | Nicht gefunden – mit Suche und neuesten Beiträgen |
| `error.php` | Eigenständige Fehlerseite (Fatal-Handler) |
| `index.php` | Fallback (z. B. `/autoren`, `/sitemap`) |

## Menüpositionen

| Slug | Verwendung |
| --- | --- |
| `primary` | Hauptnavigation im Header und im mobilen Menü |
| `footer-nav` | Footer-Navigation |
| `footer-legal` | Rechtliche Links (Impressum, Datenschutz) |

Solange ein Menü nicht gepflegt ist, zeigt das Theme sinnvolle Standardlinks an
(z. B. Start, Alle Texte, Themen) – ohne dabei Einträge in die Datenbank zu schreiben.

## Customizer

Theme-Editor → **Feder – Theme-Customizer** (`admin/customizer.php`, Schema in `theme.json`):

| Gruppe | Inhalt |
| --- | --- |
| `colors` | Akzent, Tinte, Text, Papier, Flächen, Linien – jeweils für hell und dunkel |
| `typography` | Überschrift-, Lese- und UI-Schrift, Schriftgröße, Zeilenhöhe, Initiale |
| `layout` | Breite der Lesespalte und breiter Elemente, Eckenradius |
| `header` | Logo, Untertitel, fixierter Header, Suche, Hell/Dunkel-Schalter, Farbschema |
| `feder_author` | Name, Rolle, Kurzbiografie, Portrait, Profil-Links, RSS |
| `feder_home` | Intro-Texte, Hervorhebung, Anzahl Beiträge, Themen, Newsletter-Box |
| `feder_article` | Lesezeit, Lesefortschritt, Teilen, Autorenbox, Navigation, verwandte Beiträge |
| `footer` | Kurzbeschreibung, Copyright (`{year}`, `{site_title}`), „Nach oben“ |
| `advanced` | Eigenes CSS (wird gefiltert) |

## 365CMS-3.4-Vertrag

- `ThemeManager::render()` bindet Header und Footer ein; beide sind gegen Doppel-Einbindung geschützt.
- `header.php` gibt `cms_csp_runtime_tags()` als erstes Script aus; alle Inline-`<style>`/`<script>` tragen den CSP-Nonce. Keine Inline-Handler, keine HTML-Sinks im JavaScript (Trusted Types).
- `header.php` stellt `$post`/`$page` für das Core-SEO-Modul bereit; Description, Canonical, Open Graph und Schema kommen aus `SEOService::renderCurrentHeadTags()`, der Titel läuft über den Filter `page_title`.
- `footer.php` löst `before_footer` und `body_end` aus (Cookie-Consent, Web-Vitals, PhotoSwipe).
- Beitragslinks über `PermalinkService`, Archivlinks über `cms_get_archive_url()`.
- Google Fonts entfallen bei `privacy_use_local_fonts = 1`; die Schriften werden dann über den Filter `local_font_slugs` aus dem Font-Manager geladen (Slugs: `fraunces`, `literata`, `inter` …).
- `theme.json`, `update.json`, `style.css` und `FEDER_THEME_VERSION` tragen dieselbe Version.

## Barrierearmut

Skip-Link, sichtbarer Fokus (`:focus-visible`), semantische Überschriften, `aria-current`
in Menüs, `aria-expanded`/`aria-controls` an Such- und Menü-Schaltern, Escape schließt
Panels, `prefers-reduced-motion` und `forced-colors` werden berücksichtigt, Druckansicht
ohne Navigation.
