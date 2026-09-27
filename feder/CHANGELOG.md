# Changelog

## 1.0.0 – 2026-09-27

Erstveröffentlichung des Blog-Themes **Feder** für 365CMS 3.4.00.

- Lesefokussiertes Layout mit schmaler Lesespalte, Serifen-Typografie (Fraunces für Überschriften, Literata für den Lesetext, Inter für Metadaten) und Papier-Palette mit Bordeaux-Akzent.
- Hell-/Dunkelmodus: automatisch nach Systemeinstellung, erzwungen hell/dunkel oder per Schalter im Header (Auswahl wird im Browser gespeichert, ohne Aufblitzen beim Laden).
- Startseite: Autor:innen-Intro mit Portrait und Profil-Links, hervorgehobener neuester Beitrag, Beitragsliste gruppiert nach Jahren, Themen-Chips mit Beitragszahl, Newsletter-/Kontaktbox.
- Beiträge: Lesezeit, Lesefortschritt, Initiale, Aktualisierungsdatum, Schlagwörter, Teilen-Links (Link kopieren, E-Mail, LinkedIn, Bluesky – ohne Fremd-Scripts), Autorenbox, vorheriger/nächster Beitrag und verwandte Beiträge.
- Archive: Blog, Kategorie, Schlagwort und Autor:in in einem gemeinsamen Template inklusive Kategorie-/Schlagwort-Übersicht (`$isOverview`), Archivsuche und nummerierter Seitennavigation.
- Suche, 404 mit Suchfeld und neuesten Beiträgen, eigenständige Fehlerseite für den Fatal-Handler.
- 365CMS-3.4-Vertrag: Header/Footer gegen Doppel-Einbindung geschützt, `cms_csp_runtime_tags()` als erstes Script, CSP-Nonce für alle Inline-Blöcke, `body_start`/`before_footer`/`body_end`, SEO-Metadaten über `SEOService::renderCurrentHeadTags()`, Menüpositionen über `register_menu_locations`.
- Datenschutz: Google Fonts entfallen bei `privacy_use_local_fonts = 1`; die gewählten Schriften werden dann über `local_font_slugs` aus dem Font-Manager geladen. Teilen-Links sind reine Links.
- Customizer (`theme.json` → `customization`) mit eigener Admin-Oberfläche: Farben (hell/dunkel), Typografie, Layout, Header, Autor:in, Startseite, Beiträge, Footer und eigenes CSS; Bild-Upload pro Bildfeld.
