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
- automatische Läufe für **Pull Requests** auf Theme-/Workflow-Änderungen
- geplanter **Wochenlauf** per `schedule`
- nutzt Workflow-Inputs **oder** eine optionale Repo-Konfigdatei `.github/cms-phinit-lighthouse-targets.json` als Zielkonfiguration
- führt pro Zielseite **3 Lighthouse-Läufe** aus
- lädt den LHCI-Ordner als Artefakt hoch und veröffentlicht zusätzlich temporäre Reports

### Zielkonfiguration für manuelle und automatische Läufe

Pflichtwerte für reproduzierbare LHCI-Läufe:

- `baseUrl` – Basis-Domain der Preview/Staging-Site
- `postPath` – konkreter Pfad eines veröffentlichten Einzelbeitrags

Diese Werte können auf zwei Wegen geliefert werden:

1. **manuell pro Run** über `workflow_dispatch`
2. **dauerhaft im Repository** über `.github/cms-phinit-lighthouse-targets.json`

Als Vorlage liegt `.github/cms-phinit-lighthouse-targets.example.json` im Repository. Für automatische PR-/Schedule-Läufe wird die Datei ohne `.example` mit echten Zielen erwartet.

Optionale Pfade mit Default-Werten:

- `homePath` – Default: `/`
- `blogPath` – Default: `/blog`
- `memberSecurityPath` – Default: `/member/security`

Wenn Pflichtwerte für automatische PR-/Schedule-Läufe fehlen, wird der Workflow bewusst mit Hinweis **übersprungen** statt fehlerhaft zu starten.

### Nächster Ausbauschritt

Die Grundautomatisierung ist jetzt verankert. Sinnvolle nächste Vertiefungen sind künftig vor allem:

1. Preview-URLs pro Pull Request dynamisch aus einer Hosting-Plattform einspeisen
2. zusätzliche Assertions, z. B. für Accessibility oder Ressourcenbudgets
3. optionales PR-Kommentar-/Check-Reporting mit kompaktem Budget-Diff

Der zentrale Engpass ist damit nicht mehr die Workflow-Integration im Theme-Repo, sondern nur noch die Qualität bzw. Herkunft der bereitgestellten Preview-Ziele.
