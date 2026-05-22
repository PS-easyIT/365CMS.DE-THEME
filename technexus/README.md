# TechNexus Theme

**Version:** 1.0.1  
**Ziel:** 365CMS v3.x.x · PHP 8.4  
**Slug:** `technexus`  
**Text-Domain:** `technexus`

IT- & Tech-Hub-Theme für Dienstleister, Softwarehäuser und Expert-Netzwerke.
Charcoal-Oberflächen mit elektrischem Cyan-Akzent, dezentes Node-Grid im Hero,
JetBrains Mono nur für Code, Badges und Fehlercodes.

## Startseite

`Hero (Netz-Mesh + KPIs) → Landing-Features → Experten-Teaser → CTA`

Sticky Header mit optionaler Blur-Leiste, Dark-Mode-Toggle, Suchpanel mit
Focus-Restore, Mobile-Menü mit `aria-expanded` / `aria-controls`.

## Bootstrap & Hooks

- `\CMS\Hooks::addAction('head', …)` – Preload + Stylesheet, Google Fonts,
  Meta-Tags, Customizer-CSS (`#tn-custom-vars`).
- `\CMS\Hooks::addAction('before_footer', …)` – `js/navigation.js` mit `defer`.
- Menüs auf `init` und `cms_init`; Positionen über `register_menu_locations`.
- Konstante `TECHNEXUS_THEME_VERSION` synchron mit `theme.json`, `style.css`,
  `update.json`.

## Customizer (`theme.json` → `customization`)

| Gruppe             | Inhalt                                              |
| ------------------ | --------------------------------------------------- |
| `colors`           | Primär/Sekundär, Cyan-Akzent, Code-Grün, Dark-Surfaces |
| `typography`       | Basis/Heading/Code, Größe, Zeilenhöhe, Gewicht     |
| `layout`           | Container, Padding, Grid-Hintergrund, Card-Spalten   |
| `header`           | Logo, Höhe, Blur, Status-Punkt                     |
| `footer`           | Texte, Tech-Links, Copyright-Template              |
| `buttons`          | Radius, Padding, Gewicht, Transform                 |
| `tech_hero`        | Badge, Headline, CTAs, Gradient, Stats              |
| `tech_expert_cards`| Tech-Stack, Skill-Meter, Hover-Effekt               |
| `tech_content`     | Sektionsüberschriften, Registrierungs-CTA          |
| `advanced`         | `custom_css`, Dark-Mode-Toggle, Standard-Dark       |

Menü-Positionen: `primary`, `footer-nav`, `footer-legal`, `tech-cats`.

## Helper

`theme_is_logged_in`, `theme_route_url`, `theme_nav_menu`, `get_header`,
`get_footer`, `tn_get_setting` / `technexus_get_setting`, `tn_get_flash`,
`tn_set_flash`, `tn_href`, `tn_site_title`, `tn_body_class`, `tn_safe_headline`,
`tn_html_attr`.

## Sicherheit

- Asset-URLs HTML-escaped, Cache-Bust via `TECHNEXUS_THEME_VERSION`.
- `custom_css` filtert `</style>`-Injection.
- Booleans via `filter_var(…, FILTER_VALIDATE_BOOLEAN)`.
- Google Fonts nur für gewählte Familien; bei `system`/`monospace` kein Request.

## Accessibility

- Skip-Link, sichtbarer `:focus-visible`-Ring (`--focus-ring`).
- Touch-Targets min. 44px für Toggles und primäre Buttons.
- `prefers-reduced-motion` deaktiviert Scroll-Reveal und Hover-Transforms.
- Dekorative SVGs: `focusable="false"` `aria-hidden="true"`.
