# PTC – Theme-Dokumentation

**Slug:** `PTC` · **Version:** 3.0.0

## Überblick

Generisches 365CMS-Theme für **Personalvermittlung** und **berufliche Weiterbildung**. Betreiber passen Logo, Texte und Farben im Customizer an – keine fest eingebaute Firma.

## Technik

- PHP 8.4, `declare(strict_types=1)`
- Klasse `PTC_Theme` (Singleton)
- CSS-Variablen: `--color-primary`, `--color-accent`, Layout- und Header-Tokens

## Customizer

Siehe `PTC/CUSTOMIZER.md` (falls vorhanden) oder `theme.json` → `customization`.

## Qualitätssicherung

```bash
php -l PTC/*.php
```

Prüfliste Generalisierung: keine Firmennamen/Logos/Kontaktdaten in Code-Defaults; neutrale README.
