# PTC Theme – Customizer Referenz

> Vollständige Referenz aller Customizer-Einstellungen im PTC Theme.

---

## Farben (`colors`)

| Key | Typ | Default | Beschreibung |
|---|---|---|---|
| `primary_color` | color | `#002D5D` | Primärfarbe (Navy) |
| `primary_hover` | color | `#001F42` | Hover-Zustand |
| `primary_light` | color | `#E8F0FE` | Helle Variante |
| `accent_color` | color | `#D4A017` | Akzentfarbe (Gold) |
| `accent_hover` | color | `#B8860B` | Gold Hover |
| `accent_light` | color | `#FDF5E6` | Helles Gold |
| `secondary_color` | color | `#607D8B` | Sekundärfarbe (Slate) |
| `text_color` | color | `#334155` | Fließtext |
| `heading_color` | color | `#002D5D` | Überschriften |
| `text_light` | color | `#F8F9FA` | Helle Texte |
| `muted_color` | color | `#94a3b8` | Gedämpfter Text |
| `bg_color` | color | `#F8F9FA` | Seitenhintergrund |
| `bg_secondary` | color | `#F1F5F9` | Sekundärer Hintergrund |
| `link_color` | color | `#002D5D` | Links |
| `link_hover_color` | color | `#D4A017` | Link Hover |
| `border_color` | color | `#E2E8F0` | Rahmenfarbe |
| `success_color` | color | `#22c55e` | Erfolgsfarbe |
| `error_color` | color | `#ef4444` | Fehlerfarbe |
| `cta_bg_color` | color | `#002D5D` | CTA Gradient-Start |
| `cta_bg_to` | color | `#001F42` | CTA Gradient-Ende |
| `cta_text_color` | color | `#FFFFFF` | CTA Textfarbe |
| `side_margin_color` | color | `#EDF1F5` | Seitenrand-Hintergrund |
| `side_accent_color` | color | `#002D5D` | Seitenrand-Akzent |
| `side_accent_width` | number | `0` | Seitenstreifen-Breite (px, 0 = aus) |

---

## Dienstleistungen (`services`)

### Allgemein
| Key | Typ | Default |
|---|---|---|
| `show_services` | checkbox | `true` |
| `services_tag` | text | `Unsere Leistungen` |
| `services_title` | text | `Unsere Dienstleistungen` |
| `services_subtitle` | textarea | (Beschreibung) |

### Layout & Design
| Key | Typ | Default | Optionen |
|---|---|---|---|
| `services_columns` | select | `3` | 2, 3, 4 |
| `services_bg_style` | select | `default` | default, alt, navy |
| `services_card_style` | select | `bordered` | bordered, shadow, flat, filled |
| `services_icon_style` | select | `circle` | circle, square, plain, large |
| `services_show_hover` | checkbox | `true` | |
| `services_show_icons` | checkbox | `true` | Icons ein/ausblenden |
| `services_max_items` | number | `6` | |

### CTA-Button
| Key | Typ | Default |
|---|---|---|
| `services_show_cta` | checkbox | `false` |
| `services_cta_label` | text | `Alle Leistungen entdecken` |
| `services_cta_url` | text | `/leistungen` |

### Service-Karten (1–12)
Pro Karte (`N` = 1–12):
| Key | Typ |
|---|---|
| `service_N_icon` | text (Emoji) |
| `service_N_title` | text |
| `service_N_text` | textarea |
| `service_N_url` | text |

Karte wird nur angezeigt wenn Icon oder Titel nicht leer sind.  
Karten 7–12 haben leere Defaults (optional).

---

## Termine (`events`)

### Allgemein
| Key | Typ | Default |
|---|---|---|
| `show_events` | checkbox | `true` |
| `events_source` | select | `auto` (auto/plugin/manual) |
| `events_max_items` | number | `6` |

### MS Booking
| Key | Typ | Default |
|---|---|---|
| `events_booking_url` | text | _(leer)_ |
| `events_booking_title` | text | `Online-Termin buchen` |
| `events_booking_height` | number | `600` |

---

## Footer (`footer`)

### Network Bar
| Key | Typ | Default |
|---|---|---|
| `show_network_bar` | checkbox | `true` |
| `network_bar_name` | text | `Andreas Hepp` |
| `network_bar_link1_label` | text | `PHIN IT` |
| `network_bar_link1_url` | text | `https://phinit.de` |
| `network_bar_link2_label` | text | `365CMS` |
| `network_bar_link2_url` | text | `https://365cms.de` |
| `network_bar_link3_label` | text | `365 Network` |
| `network_bar_link3_url` | text | `https://365network.de` |
