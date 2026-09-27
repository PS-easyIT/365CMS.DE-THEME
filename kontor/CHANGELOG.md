# Changelog

## 1.0.0 – 2026-09-27

Erstveröffentlichung des Business-Themes **Kontor** für 365CMS 3.4.00.

- Tannengrün als Markenanker mit Mint-Akzent und Sand-Flächen, Manrope-Typografie, großzügige Abstände und klare Karten.
- Kontaktleiste (Telefon, E-Mail, Social) über einem fixierten Header mit Marke, Navigation, Suche und Beratungs-Button; mobiles Menü mit Kontaktangaben.
- Startseite komplett über den Customizer pflegbar: Hero (Kicker, Überschrift mit *Hervorhebung*, Einleitung, zwei Buttons, Vertrauenspunkte, Bild oder Leistungs-Kacheln, Bewertungs-Plakette), Kundenleiste, sechs Leistungen mit Symbolauswahl und optionalem Link, Über uns mit Bild/Monogramm und Checkliste, Kennzahlen, Ablauf, Referenz, FAQ (natives `details`/`summary`) und Aktuelles aus dem Blog.
- Kontakt-Band vor dem Footer und vierspaltiger Footer mit Unternehmens-, Leistungs- und Kontaktspalte.
- Unterseiten mit Seitenkopf, Brotkrumen und Kontaktkarte in der Seitenleiste (bei Impressum/Datenschutz automatisch ohne).
- Blog „Aktuelles“ mit Kategorie-Filter, Übersichten, Archivsuche und Seitennavigation; Beitragsseite mit Lesezeit, Schlagwörtern, Teilen-Links (LinkedIn, XING, E-Mail), Seitenleiste und verwandten Beiträgen.
- Suche mit Leistungs-Vorschlägen, 404 im Hero-Stil, eigenständige Fehlerseite.
- 365CMS-3.4-Vertrag: Header/Footer gegen Doppel-Einbindung geschützt, `cms_csp_runtime_tags()`, CSP-Nonce, `body_start`/`before_footer`/`body_end`, SEO über `SEOService::renderCurrentHeadTags()`, Menüpositionen über `register_menu_locations`, lokale Schriften über `local_font_slugs`.
