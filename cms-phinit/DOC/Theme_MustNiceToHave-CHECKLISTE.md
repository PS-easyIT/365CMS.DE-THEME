# cms-phinit – Theme-Prüf-Checkliste
> Stand: 2026-05-10 | Basis: theme.json, README, TEMPLATES.md, PERFORMANCE-BUDGETS.md
> Status: Arbeitsdokument für Audit, Abnahme und Ausbau

## Zweck

Diese Checkliste prüft das Theme `cms-phinit` systematisch und auf demselben Niveau
wie der 365CMS-Adminbereich. Sie deckt Templates, Customizer, Member-Pfade, Assets,
Security, Performance und Doku ab.

Die Theme-Manifeste in `theme.json` und `update.json` sind für die Template- und
Versionsstruktur führend. Für Performance-Werte ist `DOC/PERFORMANCE-BUDGETS.md`
maßgeblich.

---

## Empfohlene Abarbeitung

1. Zuerst die globale Pflichtprüfung einmal vollständig durchgehen.
2. Danach die Bereiche von oben nach unten prüfen.
3. Pro Bereich dokumentieren: Status, Fehlerbild, Reproduktionsweg, abhängige Module,
   offene Must-haves, optionale Nice-to-haves.
4. Bei jeder Änderung an Templates oder Customizer zusätzlich Performance-Budgets
   und Lighthouse-Zielseiten prüfen.

---

## Globale Pflichtprüfung für jedes Template und jeden Theme-Pfad

### Architektur und Bootstrap

- [ ] `functions.php` registriert Hooks idempotent, kein doppeltes `add_action`/`add_filter`.
- [ ] Theme-Bootstrap bricht bei fehlendem Core-Kontext (`CMS\Hooks`, `CMS\Database`)
      kontrolliert ab statt mit Fatal.
- [ ] `theme.json` ist gegen das erwartete Schema valide; `post_templates` und
      `page_templates` enthalten nur tatsächlich vorhandene Dateien.
- [ ] `update.json` und `theme.json` haben dieselbe Versionsangabe wie der Header
      in `style.css` bzw. `functions.php`.
- [ ] `meridian_nav_menu()` und vergleichbare Helfer sind per `function_exists()`
      gegen Redeclare-Fatal geschützt (analog zum Core-Vertrag seit 2.9.724).
- [ ] Kein Template wird direkt aufrufbar (`ABSPATH`-Guard in jeder PHP-Datei).

### Sicherheit

- [ ] Kein Template gibt Felder unescaped aus; Kontextregeln (HTML, Attribut, URL,
      Style, JS) werden eingehalten.
- [ ] WYSIWYG-/Langtexte laufen ausschließlich über den dedizierten Purifier-Renderpfad
      für Seiten- und Landing-Content.
- [ ] Customizer-Importe laufen über den kontrollierten Temp-Staging-Flow, nicht über
      direkten Filesystem-Schreibzugriff.
- [ ] Public-URL-/Media-Allowlist in Frontend- und Member-Pfaden greift für alle
      ausgegebenen externen Links und Bildquellen.
- [ ] Customizer-POSTs nutzen CSRF-Token plus PRG-Redirect, mehrtab-tolerant analog
      zum Admin-Vertrag.
- [ ] Login-, Register- und Forgot-Password-Templates senden Cache-Header
      `private, no-store` und werden nicht in einem öffentlichen Cacheprofil abgelegt.
- [ ] Externe Quellen in Header, Footer und Hero werden nicht ohne Allowlist eingebunden
      (keine fremden Skripte, kein fremdes CSS aus Customizer-Werten).

### Daten und Konfiguration

- [ ] Customizer-Werte werden serverseitig validiert, nicht nur clientseitig.
- [ ] Farb-, URL- und Enum-Felder im Customizer arbeiten gegen feste Whitelists.
- [ ] Theme-Settings werden zentral aus DB gelesen, nicht aus mehreren Quellen
      mit Drift-Risiko.
- [ ] `template`-Spalte in `cms_posts`/`cms_pages` enthält nur registrierte IDs;
      unbekannte Werte fallen sauber auf `default` zurück.
- [ ] Template-Wechsel im Editor zerstört keine Meta-Daten, nur sichtbare Felder
      werden ausgeblendet.

### Frontend-UX

- [ ] Sticky-Header bricht bei langen Menüs nicht um.
- [ ] Dark Mode ist konsistent zwischen Header, Content, Sidebar, Footer.
- [ ] Mobile Navigation verwendet `aria-expanded`/`aria-controls` und ist tastaturbedienbar.
- [ ] Skip-Link am Anfang der Seite vorhanden, sichtbar bei Fokus.
- [ ] Inhalte ohne Sidebar (`*-wide`, `landing`) bleiben mobil und auf großen Screens
      konsistent zentriert.
- [ ] JSON-LD-Breadcrumbs werden ohne sichtbare Public-Breadcrumb-Leiste ausgegeben,
      keine doppelten oder leeren Schemata.

### Performance

- [ ] Above-the-fold-Bilder werden nicht lazy geladen; `loading="lazy"` nur für
      tieferliegende Bilder.
- [ ] Lokale Bilder besitzen `width` und `height` zur CLS-Vermeidung.
- [ ] Header-Logo hat intrinsische Dimensionen.
- [ ] Mobile-Head-Pfad bleibt entlastet: Theme-Init inline klein, UI-/Card-CSS
      asynchron auf Home-/Blog-Listings.
- [ ] Customizer-Werte erzeugen CSS-Variablen, keine großen Inline-`<style>`-Blöcke.
- [ ] Theme-CSS pro Request bleibt unter 90 KB, Theme-JS unter 180 KB.
- [ ] Kein Drittanbieter-Script im Initial-Request ohne dokumentierten Grund.

### Locale und i18n

- [ ] `/en`-Pfade liefern nur tatsächlich übersetzte Inhalte.
- [ ] Lokalisierte Pfadhelfer für Login, Register, Forgot-Password, Datenschutz
      werden konsistent in allen Templates verwendet.
- [ ] Kein `Undefined variable $_currentLocale`-Warning im Public-Frontend.
- [ ] hreflang-Tags werden korrekt für vorhandene Übersetzungen gesetzt.

### Dokumentation

- [ ] Jede neue Template- oder Customizer-Funktion ist in `DOC/TEMPLATES.md`
      ergänzt.
- [ ] `CHANGELOG.md`, `theme.json` und `update.json` sind nach jedem Release synchron.
- [ ] `DOC/THEME-AUDIT.md` enthält den aktuellen Audit-Stand.

---

## 1. Beitrags-Templates (Posts)

### Unterbereiche

| Template | Datei | Layout |
|---|---|---|
| Standard | `post.php` | Sidebar, Sidebar-TOC |
| Vollbreite | `post-wide.php` | Inline-TOC, keine Sidebar |
| Tech | `post-tech.php` | Sidebar, Tech-Karte, Umgebungs-Widget |

### Must-haves

- [ ] Alle drei Templates rendern ohne PHP-Notice/Warning bei minimal befülltem Beitrag.
- [ ] TOC wird automatisch aus `<h2>` und `<h3>` erzeugt; keine Duplikat-IDs.
- [ ] Inline-TOC im Wide-Template ist als `<details>` zugänglich (Tastatur, Screenreader).
- [ ] Tech-Karte rendert auch bei teilweise leeren Meta-Feldern korrekt.
- [ ] Schwierigkeits-Badge zeigt nur erlaubte Werte (`beginner`, `intermediate`,
      `advanced`, `expert`).
- [ ] Prev/Next-Navigation respektiert Status (`published`) und Sprache.
- [ ] Share-Buttons öffnen externe Ziele mit `rel="noopener noreferrer"`.
- [ ] Kommentarbereich nutzt Core-AntiSpam-Vertrag (Honeypot, Mindestzeit, Linklimit).
- [ ] Kategorien- und Tag-Links zeigen auf gültige Archive, nicht auf alte Slugs.

### Nice-to-haves

- [ ] Lesezeit-Anzeige basierend auf Wortzahl im Header.
- [ ] Fortschrittsbalken am oberen Rand beim Scrollen.
- [ ] Kopier-Button für Code-Blöcke im Tech-Template.
- [ ] Tech-Karte mit Print-optimiertem Stylesheet für PDF/Druck.
- [ ] Inline-Voting für Hilfreich/Nicht-hilfreich am Artikelende.

---

## 2. Seiten-Templates (Pages)

### Unterbereiche

| Template | Datei | Layout |
|---|---|---|
| Standard | `page.php` | Sidebar |
| Vollbreite | `page-wide.php` | zentriert max 860 px |
| Landing | `page-landing.php` | Hero, Features, optional CTA-Banner |

### Must-haves

- [ ] Standard- und Wide-Template rendern Breadcrumbs konsistent (nur einmal pro Seite).
- [ ] Wide-Template hält Lesetextbreite stabil bei langen Inline-Codeblöcken oder
      breiten Tabellen (Overflow-Strategie).
- [ ] Landing-Template rendert ohne Sidebar und ohne sichtbare Breadcrumb-Leiste.
- [ ] Hero-CTAs validieren `hero_cta_url` und `hero_cta2_url` als interne oder
      explizit erlaubte externe URLs.
- [ ] Feature-Cards mit fehlendem Icon/Text/URL fallen kontrolliert auf Defaults
      zurück, kein leerer Knoten.
- [ ] CTA-Banner ist optional und blendet bei fehlenden Meta-Werten komplett aus.
- [ ] „Datum letzte Aktualisierung" auf Wide-Seiten kommt aus `updated_at`,
      nicht aus `created_at`.

### Nice-to-haves

- [ ] Feature-Cards-Drag-and-Drop im Editor (Reihenfolge per UI änderbar).
- [ ] Variante mit Hintergrund-Pattern oder optionaler Hero-Hintergrundgrafik.
- [ ] FAQ-Block als wiederverwendbare Sektion auf Landing-Seiten.
- [ ] Sticky-Inhaltsverzeichnis auf langen Wide-Seiten als optionales Toggle.
- [ ] Theme-Variante des Landing-Templates für „dunkle Hero, helle Features".

---

## 3. Öffentliche Spezialseiten (Auth)

### Unterbereiche

| Seite | Datei | Zweck |
|---|---|---|
| Login | `login.php` | Anmeldung |
| Register | `register.php` | Registrierung |
| Passwort vergessen | `forgot-password.php` | Recovery, Reset, Abschluss |

### Must-haves

- [ ] Alle drei Seiten setzen `Cache-Control: private, no-store` und werden nie
      öffentlich gecacht.
- [ ] CSRF-Token vorhanden, Reset-Token-Flow gegen Replay geschützt.
- [ ] Login verlinkt kanonisch auf `/forgot-password` und `/register`.
- [ ] Register verlinkt auf `/datenschutz`, nicht auf Legacy-Pfade.
- [ ] Reset-Mails werden über den zentralen Mail-Service versendet, nicht direkt
      aus dem Template.
- [ ] Fehlertexte sind nicht so spezifisch, dass sie Benutzer enumerieren
      (kein „Benutzer existiert nicht" vs. „falsches Passwort").
- [ ] Nach erfolgreichem Login/Reset wird auf einen internen Pfad geleitet
      (kein offener Redirect).
- [ ] Forgot-Password-Anfrage gibt immer dieselbe neutrale Antwort, unabhängig
      davon, ob die Mail-Adresse existiert.
- [ ] Rate-Limit für Login und Reset-Anfragen ist aktiv (Core-Vertrag).

### Nice-to-haves

- [ ] WebAuthn/Passkey-Unterstützung im Login-Template (Core unterstützt es bereits).
- [ ] Inline-Passwort-Stärke-Anzeige am Register-Formular gegen die Core-Policy.
- [ ] Zweisprachige Fehlertexte automatisch passend zum Locale-Pfad.
- [ ] Login mit Single-Sign-On-Hinweis, falls aktiv (Hint-Card statt zusätzlicher
      Buttons).

---

## 4. Header, Navigation, Footer

### Must-haves

- [ ] Sticky-Header verschwindet nicht bei iOS-Adressleisten-Resize.
- [ ] Suche, Quicklinks und Member-Bar lassen sich einzeln ein- und ausblenden,
      Settings wirken im Frontend.
- [ ] Login-/Account-Button ist über Customizer ausblendbar; ausgeblendet wird
      auch der Platzhalter, kein leerer Container.
- [ ] Aktive Navigation wird konsistent markiert, ohne `Undefined variable`-Warnings.
- [ ] Footer-Pflichtlinks (Impressum, Datenschutz, Kontakt) sind aus Core-Settings
      vorbelegt; Theme zeigt Warnung bei Fehlen.
- [ ] Mobile-Menü ist tastaturbedienbar und schließt mit Escape.

### Nice-to-haves

- [ ] Mega-Menu-Variante für mehrspaltige Hauptmenüs.
- [ ] Optionaler Topbar-Slot über dem Header (Aktionsleiste).
- [ ] Footer mit konfigurierbarer Spaltenzahl und Sektionen.
- [ ] „Zurück nach oben"-Button ohne JS-Abhängigkeit.

---

## 5. Theme-Customizer

### Must-haves

- [ ] Customizer-Routen sind admin-geschützt (`isAdmin`), keine Schreibaktion ohne
      CSRF.
- [ ] Preview-Drawer rendert immer die echte Theme-Pipeline, keine separate
      Vorschau-Logik mit Drift-Risiko.
- [ ] Import läuft ausschließlich über den Temp-Staging-Flow.
- [ ] Export liefert nur unkritische Theme-Einstellungen, keine Geheimnisse oder
      Mail-Credentials.
- [ ] Reset auf Default ist mit Bestätigungsdialog versehen und nimmt Snapshot
      des bisherigen Zustands.
- [ ] Customizer-Werte mit ungültigen Farben, URLs oder Enums werden auf Defaults
      zurückgesetzt und nicht persistiert.

### Nice-to-haves

- [ ] Customizer-Diff vor Speichern (aktuell vs. neu) analog zum geplanten
      Core-Konfigurations-Diff.
- [ ] Versionierte Customizer-Snapshots mit Restore.
- [ ] Customizer-Profile pro Umgebung (Dev, Staging, Prod) mit Export/Import.
- [ ] Live-Token-Vorschau für Farben, Spacing und Schriftgrößen als Sticker-Galerie.

---

## 6. Member-Bereich

### Unterbereiche

Dashboard, Profil, Favoriten, Sicherheit, Feeds.

### Must-haves

- [ ] Alle Member-Pfade nutzen Auth-Guard und kontextabhängige Capabilities.
- [ ] `/member/security` zeigt nur eigene Sicherheitsereignisse, keine fremden.
- [ ] Profil-Update führt CSRF-Prüfung durch und bestätigt erfolgreiches Speichern
      über Flash-Message.
- [ ] Favoriten-Listen brechen bei gelöschten Quellinhalten nicht (kein Fatal,
      sondern grauer Eintrag „nicht mehr verfügbar").
- [ ] Feeds respektieren Sichtbarkeits-/Sprachregeln des Core.
- [ ] Member-Templates verwenden dieselben Cache-Header-Profile wie Auth-Seiten,
      kein öffentlicher Cache.

### Nice-to-haves

- [ ] Profil-Vollständigkeitsbalken mit Hinweis auf fehlende Pflichtfelder.
- [ ] Kontextuelle Tipps („Tipp: aktiviere 2FA") nicht-aufdringlich im Dashboard.
- [ ] Persönliche Favoriten als Sortier- und Filterliste (analog Admin-Dashboard).
- [ ] Feed-Aggregation mit „neu seit letztem Besuch"-Badge.

---

## 7. Locale, Routen und SEO

### Must-haves

- [ ] EN-Custom-Slugs funktionieren für `page`, `landing` und Spezialseiten;
      keine Doppelauflösung mit/ohne `/en`.
- [ ] Canonical-Tag wird pro Sprache korrekt gesetzt.
- [ ] hreflang-Cluster ist für DE und EN vollständig oder gar nicht gesetzt
      (kein halbes Cluster).
- [ ] JSON-LD-Breadcrumbs sind valide und enthalten keine Platzhalter.
- [ ] Featured-Image im OG-Tag fällt bei fehlendem Bild auf Site-Default zurück
      (Core-SEO-Vertrag).

### Nice-to-haves

- [ ] Themewechsel und Sprachwechsel landen auf der entsprechenden Übersetzung,
      nicht generisch auf Startseite.
- [ ] OG-Image-Variante pro Template (Landing-Hero als OG, Tech-Card als OG-Badge).
- [ ] Strukturierte Daten für Tech-Posts (HowTo) optional pro Template aktivierbar.

---

## 8. Performance

### Must-haves

- [ ] Lighthouse-Mehrheit der drei Läufe pro Zielseite erreicht die Werte aus
      `DOC/PERFORMANCE-BUDGETS.md`.
- [ ] Kritischer Pfad mobil bleibt unter 170 KB.
- [ ] Kein Bild im Above-the-fold ist lazy.
- [ ] Customizer-Änderungen erzeugen keine zusätzlichen globalen Inline-Styles
      jenseits der dokumentierten CSS-Variablen-Strategie.
- [ ] Theme-Assets werden requestbezogen geladen, nicht global ohne Notwendigkeit.
- [ ] Performance-Regressionen werden im Review dokumentiert (Zielseite, Tool,
      Delta).

### Nice-to-haves

- [ ] Lighthouse-CI-Workflow ist mit echter `cms-phinit-lighthouse-targets.json`
      verdrahtet (statt nur Example-Datei).
- [ ] PR-Kommentar mit kompaktem Budget-Diff aus LHCI.
- [ ] Resource Hints (`preconnect`, `preload`) für Schriften und LCP-Bild
      automatisch pro Template.
- [ ] Critical-CSS pro Templatefamilie eingebettet.

---

## 9. Accessibility

### Must-haves

- [ ] Eindeutige H1 pro Seite über alle Templates.
- [ ] Sichtbare Fokus-Ringe bei allen interaktiven Elementen.
- [ ] Kontrastverhältnis WCAG AA in Light- und Dark-Mode für Body-, Link- und
      Button-Text.
- [ ] Hero-CTAs als Buttons oder Links semantisch korrekt, nicht nur visuell.
- [ ] Schwierigkeits-Badges der Tech-Karte haben Textinhalt, nicht nur Farbe.
- [ ] TOC-Toggle und Mobile-Menü reagieren auf Enter und Space.

### Nice-to-haves

- [ ] „Reduzierte Bewegung" respektiert (`prefers-reduced-motion`) für Hero-
      und Hover-Animationen.
- [ ] Landmarks-Übersicht pro Template als Doku-Block in `DOC/TEMPLATES.md`.
- [ ] Optionaler Hochkontrast-Modus über Customizer.

---

## 10. Versionierung und Doku

### Must-haves

- [ ] `theme.json`, `update.json`, `style.css` und `CHANGELOG.md` zeigen dieselbe
      Version.
- [ ] `DOC/THEME-AUDIT.md` enthält Datum, Findings und Status des letzten Snyk-
      Code-Scans.
- [ ] `DOC/TEMPLATES.md` listet jedes Template mit Datei, Features, CSS-Klassen
      und Meta-Feldern.
- [ ] `DOC/PERFORMANCE-BUDGETS.md` ist nach jeder größeren Frontend-Änderung
      verifiziert.

### Nice-to-haves

- [ ] Automatische Konsistenzprüfung Version <-> Manifest <-> Header in CI.
- [ ] Visuelles Theme-Storybook (statisch generiert) für alle Templates und
      Block-Varianten.
- [ ] Migrationsleitfaden bei Major-Releases als eigene Datei in `DOC/`.

---

## Cross-Bereichs-Abhängigkeiten

### Templates ↔ Editor

- [ ] Editor zeigt nur tatsächlich registrierte Templates an.
- [ ] Template-Wechsel löst keine stillen Datenverluste aus.
- [ ] Meta-Feld-Schemata aus `theme.json` werden vom Editor respektiert
      (Pflichtfelder, Typen, Optionen).

### Customizer ↔ Frontend

- [ ] Jede Customizer-Option, die im UI steht, hat einen sichtbaren Effekt im
      Frontend; tote Schalter werden entfernt.
- [ ] Defaults im Customizer entsprechen den Defaults in den Template-Renderpfaden.

### Theme ↔ Core-Sicherheitsvertrag

- [ ] Theme-Templates verwenden ausschließlich Core-Helfer für CSRF, Escape,
      URL-Validierung, Sanitizer-Profile.
- [ ] Theme bringt keine eigenen Sanitizer mit, die den Core unterlaufen.

---

## Abschluss pro geprüftem Bereich

Nach jedem Bereich dokumentieren:

- [ ] Verantwortlicher / Prüfer
- [ ] Datum
- [ ] geprüfte Templates und Routen
- [ ] reproduzierte Fehler
- [ ] offene Must-haves
- [ ] offene Nice-to-haves
- [ ] Doku aktualisiert (CHANGELOG, TEMPLATES, THEME-AUDIT, PERFORMANCE-BUDGETS)
- [ ] Follow-up-Tickets angelegt

---

## Zugehörige Theme-Dokumente

- `README.md`
- `DOC/TEMPLATES.md`
- `DOC/THEME-AUDIT.md`
- `DOC/PERFORMANCE-BUDGETS.md`
- `CHANGELOG.md`
- `theme.json`
- `update.json`