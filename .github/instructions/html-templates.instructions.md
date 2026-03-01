---
applyTo: "**/header.php,**/footer.php,**/home.php,**/page.php,**/login.php,**/register.php,**/404.php,**/error.php,**/index.php,**/search.php,**/blog*.php,**/archive.php,**/category.php,**/tag.php,**/author.php,**/contact.php,**/partials/*.php"
---

# 365CMS Theme – HTML-Template-Richtlinien

## Semantische Elemente (Pflicht)

| Kontext | Pflicht-Element | Falsch |
|---------|----------------|--------|
| Seiten-Header | `<header class="site-header">` | `<div class="header">` |
| Navigation | `<nav aria-label="...">` | `<div class="nav">` |
| Haupt-Inhalt | `<main>` | `<div class="main-content">` |
| Einzelner Inhalt (Card) | `<article>` | `<div class="card">` |
| Seitenspalte | `<aside>` | `<div class="sidebar">` |
| Seiten-Footer | `<footer class="site-footer">` | `<div class="footer">` |
| Inhaltsblock | `<section>` | `<div class="section">` |
| Zeitangabe | `<time datetime="ISO-8601">` | `<span class="date">` |

## Pflicht-Hooks in Templates

### header.php
```php
<head>
    <?php CMS\Hooks::doAction('head'); ?>
</head>
<body>
    <?php CMS\Hooks::doAction('body_start'); ?>
    <header>...</header>
    <?php CMS\Hooks::doAction('after_header'); ?>
```

### footer.php
```php
    <?php CMS\Hooks::doAction('before_footer'); ?>
    <footer>...</footer>
    <?php CMS\Hooks::doAction('footer'); ?>
    <?php CMS\Hooks::doAction('body_end'); ?>
</body>
</html>
```

## Bilder

```html
<!-- ✅ PFLICHT -->
<img src="<?= htmlspecialchars($url) ?>"
     alt="<?= htmlspecialchars($alt_text) ?>"
     loading="lazy"
     width="400" height="300">
```

- `alt`-Attribut ist **Pflicht** (leer `alt=""` nur bei dekorativen Bildern)
- `loading="lazy"` auf allen Bildern (außer above-the-fold Hero)
- `width` + `height` angeben um Layout-Shift zu vermeiden
- Kein `style="width:...;height:..."` – per CSS-Klasse dimensionieren

## Links

```html
<!-- Externer Link -->
<a href="<?= htmlspecialchars($url) ?>"
   target="_blank"
   rel="noopener noreferrer">
   Externer Link
</a>

<!-- ❌ Verboten -->
<a href="javascript:void(0)" onclick="doAction()">Klick</a>
```

- `<a>` nur für echte Navigation (URL-Änderung)
- `<button>` für JavaScript-Aktionen
- Externe Links: immer `target="_blank" rel="noopener noreferrer"`

## Accessibility-Pflicht

- `aria-label` auf Icon-only-Buttons
- `aria-expanded` / `aria-hidden` auf Burger-Menü
- `aria-current="page"` auf aktiver Navigation
- Fokus-Management bei Menü-Toggle (Escape → Fokus zurück)

## Inline-Styles

- **Verboten:** Statische Design-Werte inline
- **Erlaubt:** Dynamische Werte aus DB als CSS-Variable:
  ```php
  <div class="hero" style="--hero-bg: <?= htmlspecialchars($bg_color) ?>;">
  ```

## `<style>` Blöcke in Templates

- **Erlaubt:** `:root` CSS-Variable Injection aus PHP:
  ```php
  <style>:root { --primary: <?= htmlspecialchars($primary) ?>; }</style>
  ```
- **Verboten:** Vollständige CSS-Klassen-Definitionen in Templates

## PHP-Ausgabe-Escaping

```php
<?= htmlspecialchars($value) ?>               <!-- Text -->
<?= htmlspecialchars($value, ENT_QUOTES) ?>   <!-- Attribute -->
<?= (int)$count ?>                            <!-- Ganzzahlen -->
<?= $sanitized_html ?>                        <!-- Nur nach Sanitierung! -->
```
