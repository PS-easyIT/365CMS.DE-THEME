# Changelog

## 1.0.0 – 2026-09-27

Erstveröffentlichung des News-Themes **Rundschau** für 365CMS 3.4.00.

- Header in drei Ebenen: Service-Leiste mit Datum und Claim, Masthead mit Wortmarke (oder Logo) und Suche, fixierte Ressort-Navigation in Petrol mit Signal-Orange-Markierung; mobil horizontal scrollbar mit Verlaufskanten.
- Nachrichten-Ticker unter der Navigation: neueste Beiträge oder Beiträge eines Schlagworts (z. B. „eilmeldung“), gleichmäßige Laufgeschwindigkeit, Pause beim Überfahren/Fokussieren und per Schalter, statische Liste bei `prefers-reduced-motion`.
- Startseite komplett aus der Datenbank: Aufmacher + vier Top-Meldungen, Seitenleiste „Neueste Meldungen“ (Zeitleiste) und „Meistgelesen“ (Aufrufe im wählbaren Zeitraum), automatische Ressort-Blöcke (Auswahl und Reihenfolge per Slug-Liste steuerbar), optionales „Im Fokus“-Band für ein Schlagwort und Newsletter-Band.
- Stabile Ressortfarben je Kategorie für Kicker, Rubrikenlinien, Archivköpfe und Listen (abschaltbar).
- Artikel: Brotkrumen, Kicker, Teaser, Byline mit Datum/Uhrzeit, Aktualisierung und Lesezeit, Teilen-Leiste (Link kopieren, E-Mail, WhatsApp, LinkedIn, Drucken – ohne Fremd-Scripts), Themen-Chips, Seitenleiste „Mehr aus dem Ressort“/„Meistgelesen“ und „Mehr zum Thema“.
- Archive für alle Meldungen, Ressorts, Themen und Autor:innen inklusive Übersichten (`$isOverview`), Archivsuche und nummerierter Seitennavigation; Suche, 404 und eigenständige Fehlerseite.
- 365CMS-3.4-Vertrag: Header/Footer gegen Doppel-Einbindung geschützt, `cms_csp_runtime_tags()`, CSP-Nonce für Inline-Blöcke, `body_start`/`before_footer`/`body_end`, SEO über `SEOService::renderCurrentHeadTags()`, Menüpositionen über `register_menu_locations`, lokale Schriften über `local_font_slugs`.
- Customizer mit Farben, Typografie, Layout, Header, Ticker, Startseite, Artikel, Footer und eigenem CSS.
