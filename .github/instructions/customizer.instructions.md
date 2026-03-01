---
applyTo: "**/admin/customizer.php"
---

# 365CMS Theme – Admin-Customizer-Richtlinien

## Boilerplate

```php
<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

use CMS\Auth;
use CMS\Security;

if (!Auth::instance()->isAdmin()) {
    header('Location: ' . SITE_URL);
    exit;
}

$csrfToken = Security::instance()->generateToken('theme_customizer');
```

## Admin-Integration

- `renderAdminSidebar('theme-customizer')` und `renderAdminSidebarStyles()` aufrufen
- `.admin-content` als Haupt-Container
- `.admin-page-header` mit `h2` + Emoji
- `.admin-card` für Einstellungs-Sektionen
- `admin.css` mit `?v=YYYYMMDD` Cache-Parameter

## ThemeCustomizer-API nutzen

```php
use CMS\Services\ThemeCustomizer;
$customizer = new ThemeCustomizer();

// Laden
$colors = $customizer->getCategory('colors');
$primary = $customizer->getSetting('colors', 'primary_color', '#2563eb');

// Speichern
$customizer->setSetting('colors', 'primary_color', $newColor);

// CSS generieren
$css = $customizer->generateCSS();

// Export / Import
$json = $customizer->exportSettings();
$result = $customizer->importSettings($json);
```

## CSRF-Pflicht

Jedes Formular und jeder AJAX-POST muss einen `csrf_token` enthalten:
```php
if (!Security::instance()->verifyToken($_POST['csrf_token'] ?? '', 'theme_customizer')) {
    $error = 'Sicherheitscheck fehlgeschlagen';
}
```

## Eingabe-Validierung

```php
// Farben validieren
if (!preg_match('/^#[0-9a-f]{6}$/i', $color)) {
    $color = '#2563eb'; // Fallback
}

// Zahlen validieren
$fontSize = max(12, min(24, (int)($_POST['font_size'] ?? 16)));
```

## Kein window.confirm()

Bestätigungen über eigenes Modal. Kein jQuery.
