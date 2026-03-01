---
applyTo: "**/*.php"
---

# 365CMS Theme – PHP-Entwicklungs-Regeln

## Pflicht in jeder Datei

```php
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}
```

## Singleton-Pattern für Theme-Hauptklasse

```php
final class MeinTheme
{
    private static ?self $instance = null;

    public static function instance(): self
    {
        return self::$instance ??= new self();
    }

    private function __construct()
    {
        $this->init_hooks();
    }
}
MeinTheme::instance();
```

## Ausgabe-Escaping

- Text-Nodes: `htmlspecialchars($value)`
- Attribute: `htmlspecialchars($value, ENT_QUOTES)`
- URLs: `htmlspecialchars($url)`
- Ganzzahlen: `(int)$id`
- **Niemals** rohe `$_POST`/`$_GET`-Werte direkt ausgeben

## CSRF-Token in Formularen

```php
<input type="hidden" name="csrf_token"
       value="<?php echo CMS\Security::instance()->generateToken('action_slug'); ?>">
```

## Hooks-System (CMS\Hooks)

```php
// Head-Assets
\CMS\Hooks::addAction('head', [$this, 'enqueueStyles'], 10);

// Footer-Scripts
\CMS\Hooks::addAction('before_footer', [$this, 'enqueueScripts'], 10);

// Pflicht-Hooks in Templates aufrufen:
CMS\Hooks::doAction('head');           // Im <head>
CMS\Hooks::doAction('body_start');     // Nach <body>
CMS\Hooks::doAction('after_header');   // Nach Header
CMS\Hooks::doAction('before_footer');  // Vor Footer
CMS\Hooks::doAction('body_end');       // Vor </body>
```

## Theme-Manager-API

```php
$theme = CMS\ThemeManager::instance();
$path  = $theme->getThemePath();     // Dateisystem-Pfad
$url   = $theme->getThemeUrl();      // Asset-URL
$theme->render('home', ['key' => 'value']);
```

## ThemeCustomizer-API

```php
use CMS\Services\ThemeCustomizer;
$customizer = new ThemeCustomizer();
$color = $customizer->getSetting('colors', 'primary_color', '#2563eb');
$colors = $customizer->getCategory('colors');
```

## Kein jQuery

Ausschließlich Vanilla JavaScript (ES2020+). Kein externer CDN-Import.
