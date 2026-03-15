# CMS Phinit – Performance-Budgets

Stand: 2026-03-15

Dieses Dokument definiert **Zielwerte** und einen **wiederholbaren Prüfablauf** für das Theme `cms-phinit`. Es ersetzt keine echten Messläufe, sorgt aber dafür, dass Performance nicht nur „gefühlte Qualität“, sondern ein überprüfbarer Bestandteil jeder Weiterentwicklung bleibt.

## Zielseiten für Messungen

Jede Performance-Prüfung deckt mindestens diese vier Request-Typen ab:

1. `/` — Startseite
2. `/blog` — Blog-/Archivansicht
3. ein typischer Einzelbeitrag `/blog/{slug}`
4. `/member/security` — interaktive Member-Seite

Wenn ein Change nur einen Spezialpfad betrifft (z. B. Autorenarchiv, Sitemap, Suche), wird zusätzlich genau dieser Pfad geprüft.

## Zielwerte

### Nutzerorientierte Ziele

| Seitentyp | LCP | CLS | INP | Lighthouse Performance |
|---|---:|---:|---:|---:|
| Startseite | ≤ 2.8 s | ≤ 0.10 | ≤ 200 ms | ≥ 85 |
| Blog/Archiv | ≤ 2.8 s | ≤ 0.10 | ≤ 200 ms | ≥ 85 |
| Einzelbeitrag | ≤ 2.8 s | ≤ 0.10 | ≤ 200 ms | ≥ 88 |
| Member-Security | ≤ 3.0 s | ≤ 0.10 | ≤ 200 ms | ≥ 80 |

### Gewichts- und Struktur-Budgets

Die folgenden Limits sind **Arbeitsbudgets**, keine Ist-Werte aus Messläufen:

| Bereich | Ziel |
|---|---:|
| Kritischer Pfad mobil (komprimiert) | ≤ 170 KB |
| Theme-CSS pro Request | ≤ 90 KB |
| Theme-JavaScript pro Request | ≤ 180 KB |
| Drittanbieter-Scripts im Initial-Request | möglichst 0, sonst begründen |
| Große Above-the-fold-Bilder ohne Dimensionen | 0 |
| Lazy-geladene Bilder ohne `width` + `height` | 0 bei lokal auflösbaren Assets |

## Prüfablauf pro Änderung

### Bei UI-/Template-Änderungen

- Startseite und betroffene Detailseite in Chrome Lighthouse prüfen
- jeweils mindestens 3 Läufe im privaten/sauberen Browser-Kontext betrachten
- Ausreißer nicht einzeln bewerten, sondern Median/Mehrheit der Läufe beachten

### Bei Asset-/JS-/CSS-Änderungen

- prüfen, ob zusätzliche CSS- oder JS-Dateien global statt requestbezogen geladen werden
- prüfen, ob neue Bilder `alt`, `loading` und nach Möglichkeit `width`/`height` besitzen
- prüfen, ob Above-the-fold-Bilder weiterhin **nicht** lazy geladen werden

### Bei Customizer-/Runtime-Änderungen

- eine Seite mit maximal aktivierten Features gegen eine Minimal-Konfiguration vergleichen
- sicherstellen, dass neue dynamische Styles bevorzugt über CSS-Variablen statt über große Inline-`<style>`-Blöcke laufen

## Freigaberegeln

Ein Change braucht eine Performance-Nachschärfung, wenn mindestens einer dieser Punkte eintritt:

- Lighthouse Performance fällt auf einer Zielseite um mehr als 5 Punkte
- CLS steigt über `0.10`
- ein LCP-relevantes Bild wird wieder lazy geladen
- neue requestweite Assets werden ohne zwingenden Grund global eingebunden
- neue Template-Bilder kommen ohne sinnvolle Dimensionsstrategie hinzu

## Dokumentationspflicht in Reviews

Bei größeren Frontend-Änderungen soll im Review oder Commit-Kontext kurz festgehalten werden:

- welche Zielseite geprüft wurde
- ob Lighthouse/DevTools genutzt wurden
- ob sich CSS-/JS-Last oder Bildverhalten verändert hat
- ob ein Budget bewusst überschritten wurde und warum

## Automatisierungspfad

Für das Repository existiert jetzt bereits eine **manuell startbare Lighthouse-CI-Schablone** über `.github/workflows/cms-phinit-lighthouse.yml` plus `cms-phinit/lighthouserc.js`.

### Aktueller Workflow-Stand

- Start per **GitHub Actions → workflow_dispatch**
- benötigt eine echte `baseUrl` auf Preview-/Staging-Umgebung
- erwartet zusätzlich einen real existierenden `postPath`, damit Einzelbeiträge reproduzierbar gemessen werden
- führt pro Zielseite **3 Lighthouse-Läufe** aus
- lädt den LHCI-Ordner als Artefakt hoch und veröffentlicht zusätzlich temporäre Reports

### Pflicht-Inputs für manuelle Läufe

- `baseUrl` – Basis-Domain der Preview/Staging-Site
- `postPath` – konkreter Pfad eines veröffentlichten Einzelbeitrags

Optionale Inputs:

- `homePath` – Default: `/`
- `blogPath` – Default: `/blog`
- `memberSecurityPath` – Default: `/member/security`

### Nächster Ausbauschritt

Sobald für das Repository ein reproduzierbarer lokaler Start in CI oder eine stabile Preview-URL pro Pull Request vorliegt, wird die manuelle Prüfung weiter ausgebaut durch:

1. eine `lighthouserc`-Konfiguration mit den vier Ziel-URLs
2. wiederholte Läufe mit Median-Auswertung
3. Budget-Assertions für Performance und ggf. Accessibility
4. PR-Sichtbarkeit über CI-Berichte

Der erste Schritt ist mit der neuen Workflow-/Config-Schablone vorbereitet; offen bleibt vor allem die automatische PR-Anbindung an eine verlässliche Preview-Quelle.
