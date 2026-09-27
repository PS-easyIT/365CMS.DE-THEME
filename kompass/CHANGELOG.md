# Changelog

## 1.0.0 – 2026-09-27

Erstveröffentlichung des Informations-Themes **Kompass** für 365CMS 3.4.00.

- Ruhiges Nordblau mit Signalgelb, Atkinson Hyperlegible für Fließtext und Public Sans für Überschriften; alle Standardfarben erfüllen WCAG 2.2 AA.
- Barrierefreiheits-Leiste: Schriftgröße A / A+ / A++ und hoher Kontrast (Schwarz/Weiß/Gelb), gespeichert im Browser und vor dem ersten Rendern angewendet; optionale Links zu Leichter Sprache und Gebärdensprache sowie Menüposition `service-nav`.
- Fokus nach GOV.UK-Muster (gelbe Fläche mit dunkler Unterkante), Skip-Link, `aria-current`, beschriftete Schalter mit `aria-pressed`.
- Hinweisbanner (Information, Wichtig, Erledigt) – wahlweise nur auf der Startseite.
- Startseite: Such-Hero mit sichtbarem Label und „Häufig gesucht“, bis zu sechs Schnellzugriffe mit Symbolen, Themenkacheln aus den Kategorien samt Unterthemen und Beitragszahl, aktuelle Informationen mit Datumsblöcken, FAQ (`details`/`summary`) und Service-Kontakt mit Sprechzeiten.
- Informationsseiten und Beiträge mit automatischem Inhaltsverzeichnis „Auf dieser Seite“ (H2/H3, aktiver Abschnitt wird markiert), Stand-Datum, Drucken, Link kopieren, PDF-Download (Seiten, sofern Dompdf verfügbar) und „War diese Seite hilfreich?“ per E-Mail.
- Archive mit Themen-Seitenleiste und Kontaktkarte, Unterthemen, Archivsuche; Übersicht „Themenbereiche“ als Themenbaum und „Themen A–Z“ mit Buchstaben-Sprungleiste.
- Suche mit Treffertyp und Datum, Suchtipps, „Häufig gesucht“ und Schnellzugriffen bei leeren Ergebnissen; 404 mit Suche und Einstiegen, Seitenübersicht für `/sitemap`, eigenständige Fehlerseite.
- 365CMS-3.4-Vertrag: Header/Footer gegen Doppel-Einbindung geschützt, `cms_csp_runtime_tags()`, CSP-Nonce, `body_start`/`before_footer`/`body_end`, SEO über `SEOService::renderCurrentHeadTags()`, Menüpositionen über `register_menu_locations`, lokale Schriften über `local_font_slugs`.
