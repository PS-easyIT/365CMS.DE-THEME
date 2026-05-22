# PTC – Personalvermittlung & Schulungen

**Slug:** `PTC` · **Version:** 3.0.0 · **PHP:** 8.4 · **365CMS:** v3.x.x

Branchentheme für Anbieter von **Personalvermittlung**, **Arbeitnehmerüberlassung** und **beruflicher Weiterbildung**. Es richtet sich an Vermittler und Bildungsträger – nicht an ein einzelnes Unternehmen.

## Branche & Zielgruppe

- Personalvermittlung und Zeitarbeit
- Akademien, Qualifizierung, Kurse und Termine
- Dualer Fokus: **Kandidaten** und **Arbeitgeber**

## Design

- **Primärfarbe (Tiefblau-Teal):** Vertrauen, Stabilität
- **Akzent (Wachstumsgrün):** Kompetenz, Entwicklung
- Zentrale CSS Custom Properties (`--color-primary`, `--color-accent`, …) – ein Token-Block im Customizer

## Homepage-Sektionen

1. Hero mit zwei CTAs (Kandidaten / Arbeitgeber)
2. Vermittlungsprozess (Pipeline: Bewerbung → Vorauswahl → Vermittlung → Einstellung)
3. Leistungen (bis zu 12 Karten)
4. Termine / Kurse (Plugin oder manuell)
5. FAQ
6. Kontakt-CTA

## Wichtige Dateien

| Datei | Zweck |
|-------|--------|
| `functions.php` | Singleton, Assets, `outputCustomStyles()`, Hilfsfunktionen |
| `theme.json` | Metadaten + Customizer-Schema |
| `style.css` | Layout & Komponenten |
| `home.php` | Startseite |
| `admin/customizer.php` | Admin-Customizer |

## Anpassung für einen neuen Betreiber

1. Site-Titel und Logo im Customizer setzen
2. Farben unter **Farben** anpassen (Primär + Akzent reichen oft)
3. Texte unter **Startseite**, **Dienstleistungen**, **Footer**
4. Menüs: Hauptnavigation, Footer-Leistungen, Rechtliches

Keine firmenspezifischen Daten sind im Code hinterlegt.
