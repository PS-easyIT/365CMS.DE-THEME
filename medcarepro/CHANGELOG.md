# MedCare Pro – Changelog

## 1.0.3 – 2026-05-19 Nachtrag

### Abschlussdokumentation v3/PHP 8.4

- `README.md`, `CHANGELOG.md`, `functions.php`, Templates, `partials/doctor-card.php`, `style.css`, `theme.json`, `update.json` und `js/navigation.js` sind als finaler Healthcare-Re-Audit-Stand dokumentiert.
- Nachgetragen sind die Detailänderungen an Arztkarte, Blog-/Such-/Login-/Register-Templates, Notfallbanner, Search-/Mobile-Drawer-A11y, Font-Size-/Kontrast-Toggles und vollständigem Customizer-Mapping.
- Der Update-Feed trägt das Nachtragsdatum vom 19.05.2026, ohne die Version `1.0.3` erneut zu erhöhen.

## 1.0.3 – 2026-05-18

### Re-Audit und medizinische Trust-Identität

- Klinik-Anker-Teal (`#0e5b6b` / `#082f38`) ersetzt generisches SaaS-Sky-Blue; medizinisches Rot (`#b91c1c`) bleibt ausschließlich Notfall- und Warnzuständen vorbehalten.
- Spezialgebiet-Badges besitzen eigene, WCAG-AA-geprüfte Vordergrund-/Hintergrund-Paare.
- `enqueueStyles()` und `enqueueScripts()` liefern versionierte Assets mit Preload/defer; hartcodierte Stylesheet-Links wurden entfernt.
- Customizer-Verkabelung für Header, Footer, Buttons, Layout, Typografie, Accessibility und Spezialgebiet-Farben vollständig nachgezogen.
- Google-Fonts-URL lädt nur tatsächlich gewählte Familien; bei System-Stacks entfällt der externe Request.
- Inline-Styles und Inline-`<style>`-Hacks wurden durch semantische CSS-Klassen und Body-Klassen ersetzt.
- Mobile-Menü und Suchpanel nutzen `aria-controls`, `aria-hidden`, Escape-/Outside-Click-Close und Focus-Restore.
- `mc_tel_sanitize()`, `mc_safe_headline()`, `mc_href()`, `mc_body_class()` und weitere Helper sichern URL-, Headline-, Telefon- und Body-Class-Ausgaben ab.
- `theme.json`, `style.css`, `update.json` und `MEDCAREPRO_THEME_VERSION` sind auf `1.0.3` synchronisiert.

## 1.0.2 – 2026-05-17

- v3-Kompatibilität: Menü-Registrierung zusätzlich über `cms_init` abgesichert.
- Customizer-Key-Mapping in `functions.php` auf die tatsächlichen `theme.json`-Keys korrigiert.
- Google-Fonts-URL, Header-/CTA-Links und Suchpanel-ARIA nachgezogen.
- Sichtbare `:focus-visible`-States und Reduced-Motion-Fallbacks ergänzt.

## 1.0.1 – 2026-03-01

- Helper-Funktionen implementiert, optionaler Notfall-Banner, vier-spaltiger Footer und zusätzliche Healthcare-Templates ergänzt.

## 1.0.0 – 2026-02-21

- Erstveröffentlichung: Healthcare- und Praxis-Theme.
