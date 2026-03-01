---
applyTo: "**/theme.json"
---

# 365CMS Theme – theme.json Richtlinien

## Pflichtfelder

```json
{
  "name": "Theme Name",
  "slug": "theme-slug",
  "version": "X.Y.Z",
  "author": "Autor-Name",
  "description": "Beschreibung des Themes",
  "templates": {
    "home": "home.php",
    "page": "page.php",
    "404":  "404.php"
  },
  "partials": {
    "header": "header.php",
    "footer": "footer.php"
  }
}
```

## Empfohlene Felder

```json
{
  "tags": ["editorial", "responsive", "dark-mode"],
  "supports": ["custom-logo", "sticky-header", "custom-css"],
  "menus": {
    "primary":   "Hauptmenü (Header)",
    "mobile":    "Mobiles Menü (Hamburger)",
    "footer":    "Footer-Navigation"
  }
}
```

## Customization-Struktur

```json
{
  "customization": {
    "header": {
      "label": "Header & Logo",
      "description": "Header-Optionen",
      "settings": {
        "setting_key": {
          "label": "Anzeigename",
          "type": "color|text|select|image|toggle|number",
          "default": "Standardwert",
          "description": "Hilfebeschreibung"
        }
      }
    }
  }
}
```

**Verfügbare Setting-Typen:** `color`, `text`, `select`, `image`, `toggle`, `number`, `font`

## Slug-Regeln

- Kleinbuchstaben + Bindestriche: `mein-theme`
- Slug muss dem Verzeichnisnamen entsprechen
- Prefix `cms-` für offizielle Themes

## Versions-Format

Semantic Versioning: `MAJOR.MINOR.PATCH` (z. B. `1.2.3`)

## index.json im Repository-Root aktualisieren

Bei jedem neuen Theme einen Eintrag in `index.json` hinzufügen:
```json
{
  "slug": "mein-theme",
  "name": "Mein Theme",
  "manifest": "THEMES/mein-theme/update.json"
}
```
