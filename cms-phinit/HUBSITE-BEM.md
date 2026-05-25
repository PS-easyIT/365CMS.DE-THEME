# CMS PHINIT HubSite BEM-Struktur

Diese Beispiele zeigen die primären BEM-Klassen für alle HubSite-Varianten. Der Core-Renderer gibt zusätzlich legacy `cms-hub-site*` Klassen aus, damit bestehende Integrationen kompatibel bleiben.

## Default Card

```html
<article class="hubsite-card">
  <h3 class="hubsite-card__header">MS365 | PowerShell</h3>
  <div class="hubsite-card__body">
    <ul class="hubsite-card__list">
      <li class="hubsite-card__item"><a href="/beitrag-1">Install- & Connect-Modul</a></li>
      <li class="hubsite-card__item"><a href="/beitrag-2">Microsoft Azure</a></li>
      <li class="hubsite-card__item"><a href="/beitrag-3">Exchange Online</a></li>
    </ul>
    <a class="hubsite-card__cta" href="/powershell">… weiter Lesen</a>
  </div>
</article>
```

## Featured Card

```html
<article class="hubsite-card hubsite-card--featured">
  <div class="hubsite-card__media">
    <img class="hubsite-card__image" src="/uploads/m365-guide.jpg" alt="Microsoft 365 Datenschutz-Guide" loading="lazy">
  </div>
  <div class="hubsite-card__body">
    <h3 class="hubsite-card__title">Einstellungen der Organisation</h3>
    <div class="hubsite-card__content">
      <p>Ein kurzer Einstiegstext, der auf Tablet sauber unter das Bild bricht und auf Desktop rechts daneben steht.</p>
    </div>
    <a class="hubsite-card__cta" href="/m365/datenschutz">… weiter Lesen</a>
  </div>
</article>
```

## Hero Block

```html
<header class="hubsite-hero">
  <div class="hubsite-hero__inner">
    <span class="hubsite-hero__breadcrumb">MS | PowerShell</span>
    <h1 class="hubsite-hero__title">Microsoft PowerShell</h1>
    <div class="hubsite-hero__intro">
      <p>PowerShell wurde ursprünglich von Microsoft entwickelt und wird heute für Automatisierung, Administration und Cloud-Szenarien eingesetzt.</p>
    </div>
    <a class="hubsite-card__cta hubsite-hero__cta" href="/powershell/start">Zum Einstieg</a>
  </div>
</header>
```

## Grid

```html
<div class="hubsite-grid">
  <article class="hubsite-card">…</article>
  <article class="hubsite-card">…</article>
  <article class="hubsite-card">…</article>
</div>
```

Breakpoints werden ausschließlich in `assets/css/hubsite.css` geregelt:

- Mobile: `1fr`
- Tablet ab `600px`: `repeat(2, 1fr)`
- Desktop ab `1025px`: `repeat(3, 1fr)`
