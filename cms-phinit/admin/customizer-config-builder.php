<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

use CMS\Services\ThemeCustomizer;

/**
 * Immutable runtime view of the Customizer configuration.
 *
 * PHP 8.4 asymmetric visibility keeps templates readable while preventing
 * accidental runtime replacement of the normalized config properties.
 */
final class CMS_Phinit_Customizer_Config_Snapshot
{
    /**
     * @var array<string, array<string, mixed>>
     */
    public private(set) array $categories {
        set => self::normalizeCategories($value);
    }

    /**
     * @var array<string, mixed>
     */
    public private(set) array $tabGroups {
        set => self::normalizeTabGroups($value);
    }

    /**
     * @param array<string, array<string, mixed>> $categories
     * @param array<string, mixed> $tabGroups
     */
    public function __construct(array $categories, array $tabGroups = [])
    {
        $this->categories = $categories;
        $this->tabGroups = $tabGroups;
    }

    public function hasTab(string $tab): bool
    {
        return isset($this->categories[$tab]);
    }

    public function storageTab(string $tab, ?string $fallback = null): string
    {
        $category = $this->categories[$tab] ?? [];
        $storageTab = $category['storageTab'] ?? null;

        return is_string($storageTab) && $storageTab !== '' ? $storageTab : ($fallback ?? $tab);
    }

    /**
     * @param array<string, array<string, mixed>> $categories
     * @return array<string, array<string, mixed>>
     */
    private static function normalizeCategories(array $categories): array
    {
        $normalized = [];

        foreach ($categories as $categoryKey => $categoryConfig) {
            if (!is_array($categoryConfig)) {
                continue;
            }

            $key = trim((string) $categoryKey);
            if ($key === '') {
                continue;
            }

            $sections = $categoryConfig['sections'] ?? [];
            if (!is_array($sections)) {
                $sections = [];
            }

            $normalizedSections = [];
            foreach ($sections as $fieldKey => $fieldConfig) {
                if (!is_array($fieldConfig)) {
                    continue;
                }

                $normalizedSections[(string) $fieldKey] = $fieldConfig;
            }

            if ($normalizedSections === []) {
                continue;
            }

            $category = $categoryConfig;
            $title = trim((string) ($category['title'] ?? $key));
            $category['title'] = $title !== '' ? $title : $key;
            $category['sections'] = $normalizedSections;

            if (isset($category['storageTab'])) {
                $storageTab = trim((string) $category['storageTab']);
                if ($storageTab === '') {
                    unset($category['storageTab']);
                } else {
                    $category['storageTab'] = $storageTab;
                }
            }

            $normalized[$key] = $category;
        }

        return $normalized;
    }

    /**
     * @param array<string, mixed> $tabGroups
     * @return array<string, mixed>
     */
    private static function normalizeTabGroups(array $tabGroups): array
    {
        $normalized = [];

        foreach ($tabGroups as $tab => $groups) {
            $tab = trim((string) $tab);
            if ($tab === '' || !is_array($groups)) {
                continue;
            }

            $normalized[$tab] = $groups;
        }

        return $normalized;
    }
}

/**
 * Baut die Basis-Config des Theme-Customizers direkt aus theme.json auf.
 *
 * @return array<string, array{title: string, sections: array<string, array<string, mixed>>}>
 */
function phinit_build_customizer_base_config(ThemeCustomizer $customizer): array
{
    $options = $customizer->getCustomizationOptions();
    $config = [];

    foreach ($options as $categoryKey => $categoryConfig) {
        if (!is_array($categoryConfig)) {
            continue;
        }

        $settings = $categoryConfig['settings'] ?? null;
        if (!is_array($settings) || $settings === []) {
            continue;
        }

        $sections = [];
        foreach ($settings as $settingKey => $settingConfig) {
            if (!is_array($settingConfig)) {
                continue;
            }

            $sections[(string) $settingKey] = phinit_normalize_customizer_field($settingConfig);
        }

        if ($sections === []) {
            continue;
        }

        $config[(string) $categoryKey] = [
            'title' => (string) ($categoryConfig['label'] ?? $categoryConfig['title'] ?? $categoryKey),
            'sections' => $sections,
        ];
    }

    return (new CMS_Phinit_Customizer_Config_Snapshot($config))->categories;
}

/**
 * Führt die kanonische theme.json-Config mit verbleibenden Legacy-/UI-Ergänzungen zusammen.
 *
 * Theme.json gewinnt bei vorhandenen Kategorien/Feldern; die PHP-Schema-Datei ergänzt nur,
 * was dort noch nicht beschrieben ist.
 *
 * @param array<string, array<string, mixed>> $primaryConfig
 * @param array<string, array<string, mixed>> $legacyConfig
 * @return array<string, array<string, mixed>>
 */
function phinit_merge_customizer_config(array $primaryConfig, array $legacyConfig): array
{
    $merged = $primaryConfig;

    foreach ($legacyConfig as $categoryKey => $legacyCategory) {
        if (!is_array($legacyCategory)) {
            continue;
        }

        $categoryKey = (string) $categoryKey;
        if (!isset($merged[$categoryKey])) {
            $merged[$categoryKey] = $legacyCategory;
            continue;
        }

        if (!empty($legacyCategory['title'])) {
            $merged[$categoryKey]['title'] = (string) $legacyCategory['title'];
        }

        $mergedSections = $merged[$categoryKey]['sections'] ?? [];
        if (!is_array($mergedSections)) {
            $mergedSections = [];
        }

        foreach (($legacyCategory['sections'] ?? []) as $fieldKey => $fieldConfig) {
            if (!is_array($fieldConfig)) {
                continue;
            }

            $fieldKey = (string) $fieldKey;
            if (!isset($mergedSections[$fieldKey])) {
                $mergedSections[$fieldKey] = $fieldConfig;
            }
        }

        $merged[$categoryKey]['sections'] = $mergedSections;
    }

    return (new CMS_Phinit_Customizer_Config_Snapshot($merged))->categories;
}

/**
 * @param array<string, mixed> $fieldConfig
 * @return array<string, mixed>
 */
function phinit_normalize_customizer_field(array $fieldConfig): array
{
    $normalized = $fieldConfig;
    $type = (string) ($normalized['type'] ?? 'text');

    switch ($type) {
        case 'toggle':
            $type = 'checkbox';
            break;

        case 'image':
            $type = 'url';
            break;

        case 'font':
            $type = isset($normalized['options']) ? 'select' : 'text';
            break;
    }

    $normalized['type'] = $type;

    if (isset($normalized['options'])) {
        $normalized['options'] = phinit_normalize_customizer_options($normalized['options']);
    }

    return $normalized;
}

/**
 * @param mixed $options
 * @return array<string, string>
 */
function phinit_normalize_customizer_options(mixed $options): array
{
    if (!is_array($options)) {
        return [];
    }

    $normalized = [];
    foreach ($options as $optionKey => $optionValue) {
        if (is_array($optionValue)) {
            $value = (string) ($optionValue['value'] ?? $optionKey);
            $label = (string) ($optionValue['label'] ?? $value);
            $normalized[$value] = $label;
            continue;
        }

        if (is_int($optionKey)) {
            $value = (string) $optionValue;
            $normalized[$value] = $value;
            continue;
        }

        $normalized[(string) $optionKey] = (string) $optionValue;
    }

    return $normalized;
}
