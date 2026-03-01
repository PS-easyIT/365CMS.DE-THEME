---
applyTo: "**/functions.php"
---

# 365CMS Theme – functions.php Richtlinien

## Pflicht-Boilerplate

```php
<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

define('MEIN_THEME_VERSION', '1.0.0');
define('MEIN_THEME_DIR',     THEME_PATH . 'mein-theme/');
define('MEIN_THEME_URL',     \CMS\ThemeManager::instance()->getThemeUrl());
```

## Singleton-Pattern (Pflicht)

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
        // Head-Assets
        \CMS\Hooks::addAction('head', [$this, 'outputPreconnect'],       1);
        \CMS\Hooks::addAction('head', [$this, 'enqueueStyles'],         10);
        \CMS\Hooks::addAction('head', [$this, 'outputCustomStyles'],    20);

        // Footer-Scripts
        \CMS\Hooks::addAction('before_footer', [$this, 'enqueueScripts'], 10);

        // Menüpositionen
        \CMS\Hooks::addFilter('register_menu_locations', [$this, 'registerMenuLocations']);
    }
}
MeinTheme::instance();
```

## Pflicht-Methoden

### Styles einbinden
```php
public function enqueueStyles(): void
{
    $css = MEIN_THEME_DIR . 'style.css';
    if (file_exists($css)) {
        echo '<link rel="stylesheet" href="' . MEIN_THEME_URL . '/style.css?v=' . filemtime($css) . '">' . "\n";
    }
}
```

### Scripts einbinden
```php
public function enqueueScripts(): void
{
    $js = MEIN_THEME_DIR . 'js/navigation.js';
    if (file_exists($js)) {
        echo '<script src="' . MEIN_THEME_URL . '/js/navigation.js?v=' . filemtime($js) . '" defer></script>' . "\n";
    }
}
```

### Custom Styles (Customizer-Werte)
```php
public function outputCustomStyles(): void
{
    $customizer = new \CMS\Services\ThemeCustomizer();
    $primary = $customizer->getSetting('colors', 'primary_color', '#2563eb');
    echo "<style>:root { --primary-color: " . htmlspecialchars($primary) . "; }</style>\n";
}
```

### Menüpositionen
```php
public function registerMenuLocations(array $locations): array
{
    return array_merge($locations, [
        'primary' => 'Hauptmenü (Header)',
        'mobile'  => 'Mobiles Menü',
        'footer'  => 'Footer-Navigation',
    ]);
}
```

## Konventionen

- Methoden-Stil: `camelCase`
- Cache-Busting über `filemtime()` statt statischer Query-Parameter
- Keine direkten DB-Zugriffe – stattdessen `ThemeManager` oder `ThemeCustomizer` API
- Kein Inline-CSS für statische Werte – nur `:root` Variablen-Injection
