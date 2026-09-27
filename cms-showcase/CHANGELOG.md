# Changelog

## 1.0.0 – 2026-09-27

Erstveröffentlichung des Produkt-Themes **365CMS Showcase** für 365CMS 3.4.00.

- Markenpalette von 365CMS: Nachtblau, Blau (#3b82f6) und der Logo-Verlauf aus Violett, Pink und Orange; Plus Jakarta Sans, Inter und JetBrains Mono.
- Ankündigungsleiste und dunkler, fixierter Header mit 365CMS-Logo aus dem Core, Navigation, Suche, GitHub-Link und Call-to-Action; mobiles Menü mit Fokusführung.
- Hero mit gezeichneter, CSS-basierter Dashboard-Vorschau (ohne Bilddateien, optional durch einen eigenen Screenshot ersetzbar), Versions-Badge direkt aus `CMS\Version`, Vertrauenspunkten und Kennzahlen.
- Neun Funktionskacheln mit Symbolauswahl und eine Liste weiterer Module aus dem echten Funktionsumfang.
- Rollen-Tabs „Redaktion“, „Administration“, „Entwicklung“ nach WAI-ARIA (Pfeiltasten, Pos1/Ende) mit Mini-Vorschauen; ohne JavaScript werden alle Inhalte untereinander gezeigt.
- Sicherheitsbereich mit den tatsächlichen Security-Headern von 365CMS (CSP mit Nonce, Trusted Types, Frame- und Referrer-Policy).
- Entwickler-Bereich mit Code-Beispiel, das serverseitig über den PHP-Tokenizer hervorgehoben wird, Kopierfunktion und Links zur Dokumentation.
- Automatische Theme-Galerie: listet alle installierten Themes mit Name, Version, Beschreibung, Schlagwörtern und einer Mini-Vorschau in den Farben aus deren `theme.json` (bzw. `screenshot.png`).
- Release Notes der letzten drei Versionen, Blog-Teaser, Installationsanleitung mit nummerierten Schritten, Terminal-Fenster und Voraussetzungen, FAQ und Abschluss-Band.
- Dokumentationsseiten mit Inhaltsverzeichnis (aktiver Abschnitt wird markiert), Blog mit Kategorie-Filter, Übersichten, Beitragsseite mit Lesezeit, Schlagwörtern und Teilen-Links, Suche mit Einstiegen, 404 und eigenständige Fehlerseite.
- 365CMS-3.4-Vertrag: Header/Footer gegen Doppel-Einbindung geschützt, `cms_csp_runtime_tags()`, CSP-Nonce (auch für die Paletten der Theme-Galerie), keine Inline-Handler oder HTML-Sinks, `body_start`/`before_footer`/`body_end`, SEO über `SEOService::renderCurrentHeadTags()`, Menüpositionen über `register_menu_locations`, lokale Schriften über `local_font_slugs`.
