---
applyTo: "**/*"
---

# 365CMS Theme-Repository – Dokumentations-Mapping

- Zentrales DOC-Verzeichnis im Haupt-Repo: `365CMS.DE/DOC/theme/`
- Theme-Überblick und -struktur: `DOC/theme/README.md`
- Theme-Entwicklung (Anfänger-Guide): `DOC/theme/THEME-DEVELOPMENT.md`
- Ausführliche Entwickler-Referenz: `DOC/theme/DEVELOPMENT.md`
- Komponenten-Referenz (Header, Footer, Cards, Buttons, …): `DOC/theme/COMPONENTS.md`
- Design-Tokens (Farben, Typografie, Schatten, Z-Index, …): `DOC/theme/DESIGN-SYSTEM.md`
- JavaScript-Module (navigation.js): `DOC/theme/JAVASCRIPT.md`

## Vor neuen Aufgaben prüfen

1. `copilot-instructions.md` – Allgemeine Konventionen
2. `.github/instructions/` – Kontextspezifische Regeln
3. `index.json` (Root) – Theme-Registry (alle verfügbaren Themes)
4. `[theme-slug]/theme.json` – Theme-Metadaten & Customization
5. `[theme-slug]/update.json` – Versionsinformationen
