# cms-phinit – Template-System

> Version 1.0.0 · Stand 2026-03-03  
> Pfade beziehen sich auf `365CMS.DE-THEME/cms-phinit/`

---

## Übersicht

Das Theme **cms-phinit** unterstützt wählbare Templates für Beiträge (Posts) und Seiten (Pages). Damit können Inhalte mit unterschiedlichem Layout-Typ in einem einheitlichen Theme veröffentlicht werden.

Die Template-Auswahl soll im **365CMS-Editor** beim Erstellen/Bearbeiten eines Beitrags oder einer Seite als Dropdown angeboten werden. Die CMS-Implementierung liest die Registrierung aus `theme.json` → `post_templates` / `page_templates`.

---

## Template-Registrierung (theme.json)

```json
"post_templates": [
  { "id": "default", "label": "...", "file": "post.php", "features": [...] },
  ...
]
"page_templates": [
  { "id": "default", "label": "...", "file": "page.php", "features": [...] },
  ...
]
```

### Felder
| Feld | Typ | Beschreibung |
|---|---|---|
| `id` | string | Eindeutige Template-ID (wird in DB gespeichert) |
| `label` | string | Anzeigename im CMS-Dropdown |
| `description` | string | Kurzbeschreibung für den Redakteur |
| `file` | string | PHP-Template-Datei im Theme-Verzeichnis |
| `features` | string[] | Aktivierte Features (informativ) |
| `meta_fields` | object | Welche Metadaten-Felder der CMS anzeigen soll |

### CMS-Integration (PHP-Pseudocode)

```php
// Beim Rendern eines Posts/Pages:
$template = $post['template'] ?? 'default'; // aus DB
$templates = $themeConfig['post_templates'];
$tplFile = collect($templates)->firstWhere('id', $template)['file'] ?? 'post.php';
get_theme_part(str_replace('.php', '', $tplFile));

// Beim Speichern:
$_POST['template'] // wird als meta oder eigene Spalte gespeichert
```

---

## Beitrags-Templates (Posts)

### 1. `post.php` – Standard (Default)

**ID:** `default`

Das Standard-Layout für alle normalen Beiträge.

**Layout:**
```
┌─────────────────────────────────────────────────┐
│ Post-Header (Thumbnail, Kategorien, Titel, Meta) │
├────────────────────────────┬────────────────────┤
│ Artikel-Body               │ Sticky Sidebar      │
│  - Post-Content            │  - TOC              │
│  - Share-Buttons           │  - Social Icons     │
│                            │  - Kategorien       │
├────────────────────────────┴────────────────────┤
│ Prev / Next Navigation                           │
│ Kommentarbereich + Formular                      │
└─────────────────────────────────────────────────┘
```

**Features:** Sidebar, TOC (automatisch aus `<h2>`/`<h3>`), Kommentare, Teilen-Buttons, Vorherige/Nächste Navigation

**Geeignet für:** News, Anleitungen, Tutorials mit moderatem Umfang

---

### 2. `post-wide.php` – Vollbreite

**ID:** `wide`

Einzelspalte ohne Sidebar, TOC als aufklappbares `<details>`-Element vor dem Artikel.

**Layout:**
```
┌─────────────────────────────────────────────────┐
│ Post-Header                                      │
│ TOC-Inline (aufklappbar, 2-spaltig)             │
│ Artikel-Body (max. 860 px, zentriert)            │
│ Share-Buttons                                    │
│ Prev / Next Navigation                           │
│ Kommentarbereich + Formular                      │
└─────────────────────────────────────────────────┘
```

**Features:** Inline-TOC (`<details>`), Kommentare, Teilen-Buttons, Vorherige/Nächste Navigation  
**Keine:** Sidebar

**Geeignet für:** Lange Tutorials, Guides, Leseartikel ohne Ablenkung

**CSS-Klassen:** `.article-layout--wide`, `.toc-inline`, `.toc-inline__toggle`, `.toc-inline__body`

---

### 3. `post-tech.php` – Tech-Artikel und Spezial-Steckbriefe

**ID:** `tech`

Wie Standard, jedoch mit einer **Template-Meta-Card** in der Sidebar. Sie zeigt nur ausgefüllte Zusatzfelder, nutzt ein dezentes Card-Design und ordnet normale Metafelder zweispaltig mit gleicher Feldhöhe an. Array-Felder wie Voraussetzungen laufen über die volle Breite; Website-, Dokumentations- und GitHub-Links erscheinen als kompakte Icon-Links.

**Layout:**
```
┌─────────────────────────────────────────────────┐
│ Post-Header                                      │
│ ┌──────────────────────────────────────────────┐ │
│ Artikel-Body               │ Sidebar             │
│                            │  - Zusatzkarte       │
│                            │  - TOC              │
│                            │  - Kategorien       │
├────────────────────────────┴────────────────────┤
│ Share / Prev-Next / Kommentare                   │
└─────────────────────────────────────────────────┘
```

**Features:** Zusatzkarte, Sidebar, TOC, Kommentare, Metadaten-Widget in Sidebar

**Tech-Karte – Metadaten (gespeichert in `post[meta]`):**

| Schlüssel | Typ | Beschreibung | Beispiel |
|---|---|---|---|
| `os` | string | Betriebssystem | `"Windows Server 2022"` |
| `version` | string | Software-Version | `"PowerShell 7.4"` |
| `last_tested` | date (YYYY-MM-DD) | Zuletzt getestet | `"2025-11-01"` |
| `difficulty` | enum | Schwierigkeit | `"beginner"`, `"intermediate"`, `"advanced"`, `"expert"` |
| `prerequisites` | string[] | Voraussetzungen-Liste | `["Admin-Rechte", ".NET 8"]` |
| `time_needed` | string | Geschätzte Durchführungszeit | `"30 Minuten"` |
| `website_url` | url | Website oder Dokumentation | `"https://learn.microsoft.com/..."` |
| `github_url` | url | GitHub-Repository oder Script | `"https://github.com/org/repo"` |

**Schwierigkeits-Badges:**

| Wert | Label | Farbe |
|---|---|---|
| `beginner` | Einsteiger | Grün |
| `intermediate` | Fortgeschritten | Gelb |
| `advanced` | Experte | Orange |
| `expert` | Profi | Rot |

**CSS-Klassen:** `.post-template-meta-card`, `.post-template-meta-card__list`, `.post-template-meta-card__item`, `.post-template-meta-card__icon-link`, `.post-template-meta-card__chips`, `.inline-code`

**Geeignet für:** Schritt-für-Schritt-Anleitungen mit klaren Systemvoraussetzungen, PowerShell-Tutorials, Linux-Howtos, Intune-Konfigurationen

#### Spezial-Templates auf Basis von `post-tech.php`

Alle folgenden Templates verwenden dieselbe dezente Sidebar-Zusatzkarte und das `TechArticle`-Schema des `post-tech.php`-Layouts.

| Template-ID | Bereich | Logische Metafelder |
|---|---|---|
| `microsoft-365` | Microsoft 365 Workloads | `workload` + `scope`, `admin_center` + `license_plan`, `api_module` + `last_tested`, danach `prerequisites`, `website_url`, `github_url` |
| `windows` | Windows Server oder Client | `platform` + `version`, `role_feature` + `management`, `environment` + `last_tested`, danach `prerequisites`, `website_url`, `github_url` |
| `powershell` | Scripts, Module und Automatisierung | `module` + `version`, `edition` + `execution`, `target_system` + `last_tested`, danach `prerequisites`, `website_url`, `github_url` |

**Darstellungsregeln:**

- Normale Text-/Datumsfelder werden möglichst paarweise in einer Reihe angezeigt.
- Felder haben gleiche Mindesthöhe und nur kleine Zwischenräume, damit die Sidebar kompakt bleibt.
- `prerequisites` wird als Chip-Liste über volle Breite gerendert.
- `website_url` und `github_url` werden als Icon-Links gerendert; der sichtbare URL-Text wird zugunsten der ruhigen Card-Optik ausgeblendet, bleibt aber per `aria-label` zugänglich.

---

## Seiten-Templates (Pages)

### 1. `page.php` – Standard (Default)

**ID:** `default`

Standard-Seite mit Sidebar.

**Layout:**
```
┌────────────────────────────┬────────────────────┐
│ Breadcrumb + Seitentitel   │                     │
│ Seiteninhalt               │ Sidebar             │
│                            │  - Social Icons     │
│                            │  - Navigations-Links│
└────────────────────────────┴────────────────────┘
```

**Geeignet für:** Allgemeine Seiten mit Seitennavigation

---

### 2. `page-wide.php` – Vollbreite

**ID:** `wide`

Zentrierte Einzelspalte (max. 860 px) ohne Sidebar, optimiert für Lesbarkeit.

**Layout:**
```
┌─────────────────────────────────────────────────┐
│ Breadcrumb + Seitentitel (zentriert, max. 860px) │
│ Seiteninhalt (große Schrift, viel Zeilenabstand) │
└─────────────────────────────────────────────────┘
```

**Features:** Breadcrumbs, Datum letzte Aktualisierung  
**Keine:** Sidebar

**CSS-Klassen:** `.page-header-block--wide`, `.page-content--wide`, `.page-last-updated`

**Geeignet für:** Datenschutzerklärung, Impressum, AGB, FAQ, Über-mich

---

### 3. `page-landing.php` – Landing Page

**ID:** `landing`

Vollbreite-Marketing-Seite mit Hero, Feature-Cards-Grid und optionalem CTA-Banner.

**Layout:**
```
┌─────────────────────────────────────────────────┐
│ HERO: Gradient-Hintergrund                       │
│   - Titel                                       │
│   - Untertitel                                  │
│   - [CTA-Button primär]  [CTA-Button sekundär]  │
│   - (Optionales Bild rechts)                    │
├─────────────────────────────────────────────────┤
│ FEATURE-CARDS (responsive Grid, min. 260 px)    │
│   🔑 Titel          📊 Titel          ⚙️ Titel   │
│   Beschreibung      Beschreibung      Beschreibung│
│   Mehr erfahren →   Mehr erfahren →   ...        │
├─────────────────────────────────────────────────┤
│ FREIER INHALT (aus page[content], optional)     │
├─────────────────────────────────────────────────┤
│ CTA-BANNER (dunkler Hintergrund, optional)      │
│   Titel + Text + [Button]                       │
└─────────────────────────────────────────────────┘
```

**Keine:** Sidebar, Breadcrumbs

**Meta-Felder (gespeichert in `page[meta]`):**

| Schlüssel | Typ | Beschreibung |
|---|---|---|
| `hero_subtitle` | string | Untertitel im Hero |
| `hero_cta_label` | string | Primärer CTA-Button-Text |
| `hero_cta_url` | url | Primärer CTA-Button-URL |
| `hero_cta2_label` | string | (Optional) Sekundärer CTA-Button-Text |
| `hero_cta2_url` | url | (Optional) Sekundärer CTA-Button-URL |
| `features` | object[] | Feature-Cards: `{ icon, title, text, url }` |
| `cta_title` | string | (Optional) CTA-Banner-Überschrift |
| `cta_text` | string | (Optional) CTA-Banner-Text |
| `cta_button_label` | string | (Optional) CTA-Banner-Button-Text |
| `cta_button_url` | url | (Optional) CTA-Banner-Button-URL |

**Feature-Card-Objekt:**
```json
{
  "icon":  "🔐",
  "title": "Datenschutz-Templates",
  "text":  "Fertige DSGVO-konforme Vorlagen für Datenschutzerklärungen und Einwilligungen.",
  "url":   "/datenschutz/templates"
}
```

**CSS-Klassen:** `.landing-hero`, `.landing-hero__inner`, `.landing-hero__title`, `.landing-hero__sub`, `.landing-hero__ctas`, `.btn--landing-primary`, `.btn--landing-outline`, `.landing-features`, `.landing-features__grid`, `.landing-feature-card`, `.landing-cta`

**Geeignet für:** Topic-Landing-Pages (z. B. „Über mich", „PowerShell-Kurse"), Kurs-Seiten, Produkt-Seiten

---

## Öffentliche Spezialseiten außerhalb des Content-Template-Registers

Neben den klassischen Post-/Page-Templates verwendet `cms-phinit` zusätzlich dedizierte öffentliche Spezialseiten, die direkt über den Core gerendert werden.

### `login.php`

Öffentliche Login-Seite im PhinIT-Layout.

**Besonderheiten:**

- verlinkt kanonisch auf `/forgot-password` und `/register`
- nutzt lokalisierte Pfadhelfer für DE/EN
- ist als Auth-Seite für private/no-store-Cacheprofile gedacht

### `register.php`

Öffentliche Registrierungsseite im PhinIT-Layout.

**Besonderheiten:**

- verlinkt konsistent auf `/datenschutz` statt auf Legacy-Rechtstextpfade
- nutzt lokalisierte Login-/Legal-Links
- ist als Auth-Seite für private/no-store-Cacheprofile gedacht

### `forgot-password.php`

Öffentliche Recovery-Seite für Passwort-Reset-Anfragen und Token-basierte Passwort-Neuvergabe.

**Besonderheiten:**

- rendert Anfrage-, Reset- und Abschlusszustände in einem separaten Theme-Template
- verwendet CSRF-Schutz für Recovery-Aktionen
- versendet Reset-Mails über den zentralen Mail-Service des Core
- nutzt DE/EN-Varianten über lokalisierte Pfade
- gehört fachlich in dieselbe Cache-/Sicherheitsklasse wie Login/Register

---

## Zusammenfassung

| Template | Datei | Sidebar | TOC | Tech-Karte | Landing-Elemente |
|---|---|:---:|:---:|:---:|:---:|
| Post: Standard | `post.php` | ✅ | Sidebar | – | – |
| Post: Vollbreite | `post-wide.php` | – | Inline | – | – |
| Post: Tech | `post-tech.php` | ✅ | Sidebar | ✅ | – |
| Page: Standard | `page.php` | ✅ | – | – | – |
| Page: Vollbreite | `page-wide.php` | – | – | – | – |
| Page: Landing | `page-landing.php` | – | – | – | ✅ |

---

## CMS-Implementierungshinweise

### Datenbank
- Die Template-ID (`"default"`, `"wide"`, `"tech"`, `"landing"`) sollte als eigene Spalte in `cms_posts` bzw. `cms_pages` gespeichert werden: `template VARCHAR(50) DEFAULT 'default'`.
- Alternativ als JSON-Key in einem bestehenden `meta`-Feld.

### Template-Auflösung
```php
// In PostController / PageController
$templateId  = $post['template'] ?? 'default';
$tplRegistry = $theme->getPostTemplates();  // Liest post_templates aus theme.json
$tplFile     = $tplRegistry[$templateId]['file'] ?? 'post.php';
```

### Meta-Felder im CMS-Editor
Der Key `meta_fields` in `theme.json` beschreibt, welche Felder für das jeweilige Template angezeigt werden sollen:
- `type: text` → Einzeiliges Textfeld  
- `type: textarea` → Mehrzeiliges Textfeld  
- `type: date` → Datums-Picker  
- `type: select` + `options: [...]` → Dropdown  
- `type: array` → Komma-separiertes oder dynamisches Listenfeld  
- `type: array-of-objects` + `fields: [...]` → Wiederholbarer Formular-Block (z. B. Feature-Cards)
- `type: url` → URL-Feld mit Validierung

### Template-Wechsel
Wenn der Redakteur das Template wechselt, sollten vorhandene `meta`-Daten erhalten bleiben. Nur Template-spezifische Felder werden ausgeblendet – nicht gelöscht.
