<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

trait CMS_Phinit_Theme_Navigation_Trait
{
    private bool $defaultMenusSeeded = false;

    /**
     * @param mixed $menu
     */
    private function hasMenuEntries(mixed $menu): bool
    {
        if (!is_array($menu) || $menu === []) {
            return false;
        }

        foreach ($menu as $item) {
            if (!is_array($item)) {
                continue;
            }

            $label = trim((string) ($item['label'] ?? ''));
            $url = trim((string) ($item['url'] ?? ''));
            if ($label !== '' || $url !== '') {
                return true;
            }

            if (!empty($item['children']) && $this->hasMenuEntries($item['children'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<int,mixed> $items
     * @return array<int,mixed>
     */
    private function translateMenuTreeToEnglishCaps(array $items): array
    {
        $translated = [];
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $entry = $item;
            $entry['label'] = $this->translateMenuLabelToEnglishCaps((string) ($item['label'] ?? ''));

            if (isset($item['children']) && is_array($item['children'])) {
                $entry['children'] = $this->translateMenuTreeToEnglishCaps($item['children']);
            }

            $translated[] = $entry;
        }

        return $translated;
    }

    private function translateMenuLabelToEnglishCaps(string $label): string
    {
        $normalized = trim($label);
        if ($normalized === '') {
            return '';
        }

        $dictionary = [
            'Startseite' => 'Home',
            'Grundlagen' => 'Basics',
            'Glossar' => 'Glossary',
            'Datenschutz' => 'Privacy',
            'Datenschutz & DSGVO' => 'Privacy & GDPR',
            'IT-News' => 'IT News',
            'Über mich' => 'About Me',
            'Kontakt' => 'Contact',
            'Impressum' => 'Imprint',
            'AGB' => 'Terms',
            'Rechtliches' => 'Legal',
            'Seiten' => 'Pages',
            'Themen' => 'Topics',
            'Schnelllinks' => 'Quick Links',
            'RSS-Feed' => 'RSS Feed',
            'Hilfe' => 'Help',
            'Anleitungen' => 'Guides',
            'Anleitung' => 'Guide',
            'Downloads' => 'Downloads',
            'Blog' => 'Blog',
            'Newsroom' => 'Newsroom',
            'Karriere' => 'Careers',
            'Über uns' => 'About Us',
        ];

        if (isset($dictionary[$normalized])) {
            $translated = $dictionary[$normalized];
        } elseif (function_exists('phinit_localize_menu_label')) {
            $translated = phinit_localize_menu_label($normalized, 'en');
        } else {
            $translated = $normalized;
        }

        $upper = function_exists('mb_strtoupper')
            ? mb_strtoupper((string) $translated, 'UTF-8')
            : strtoupper((string) $translated);

        return trim($upper);
    }

    /**
     * @param array<int,mixed> $fallbackItems
     */
    private function seedEnglishMenuFromGerman(
        \CMS\ThemeManager $tm,
        string $baseSlug,
        array $fallbackItems = []
    ): void {
        $englishSlug = $baseSlug . '_en';

        $englishMenu = $tm->getMenu($englishSlug);
        if ($this->hasMenuEntries($englishMenu)) {
            return;
        }

        $germanMenu = $tm->getMenu($baseSlug);
        if ($this->hasMenuEntries($germanMenu)) {
            $tm->saveMenu($englishSlug, $this->translateMenuTreeToEnglishCaps($germanMenu));
            return;
        }

        if ($fallbackItems !== []) {
            $tm->saveMenu($englishSlug, $fallbackItems);
        }
    }

    /**
     * @return array<string,string>
     */
    private function getMenuLocationLabels(): array
    {
        return [
            'primary' => phinit_t('menu_location_primary'),
            'primary_en' => phinit_t('menu_location_primary_en'),
            'quicklinks' => phinit_t('menu_location_quicklinks'),
            'quicklinks_en' => phinit_t('menu_location_quicklinks_en'),
            'footer-topics' => phinit_t('menu_location_footer_topics'),
            'footer-topics_en' => phinit_t('menu_location_footer_topics_en'),
            'footer-pages' => phinit_t('menu_location_footer_pages'),
            'footer-pages_en' => phinit_t('menu_location_footer_pages_en'),
            'footer' => phinit_t('menu_location_footer_legal'),
            'footer_en' => phinit_t('menu_location_footer_legal_en'),
        ];
    }

    public function registerNavMenus(): void
    {
        $tm = \CMS\ThemeManager::instance();
        if (!method_exists($tm, 'registerMenuLocation')) {
            return;
        }

        foreach ($this->getMenuLocationLabels() as $slug => $label) {
            $tm->registerMenuLocation($slug, $label);
        }
    }

    public function registerMenuLocations(array $locations): array
    {
        foreach ($this->getMenuLocationLabels() as $slug => $label) {
            $locations[] = ['slug' => $slug, 'label' => $label];
        }

        return $locations;
    }

    /** Standard-Navigation beim Erststart anlegen */
    public function seedDefaultMenus(): void
    {
        if ($this->defaultMenusSeeded) {
            return;
        }
        $this->defaultMenusSeeded = true;

        try {
            $tm = \CMS\ThemeManager::instance();

            if (!$this->hasMenuEntries($tm->getMenu('primary'))) {
                $tm->saveMenu('primary', [
                    ['label' => 'Startseite',    'url' => '/'],
                    ['label' => 'Linux / BASH',  'url' => '/linux'],
                    ['label' => 'PowerShell',    'url' => '/powershell', 'children' => [
                        ['label' => 'Grundlagen',   'url' => '/powershell/grundlagen'],
                        ['label' => 'Glossar',      'url' => '/powershell/glossar'],
                    ]],
                    ['label' => 'Microsoft 365', 'url' => '/microsoft-365', 'children' => [
                        ['label' => 'Microsoft 365 Admin', 'url' => '/microsoft-365/admin'],
                        ['label' => 'Exchange Online',     'url' => '/microsoft-365/exchange'],
                        ['label' => 'Teams & SharePoint',  'url' => '/microsoft-365/teams'],
                    ]],
                    ['label' => 'Datenschutz',   'url' => '/datenschutz'],
                    ['label' => 'News',          'url' => '/news'],
                ]);
            }
            if (!$this->hasMenuEntries($tm->getMenu('primary_en'))) {
                $tm->saveMenu('primary_en', [
                    ['label' => 'HOME',       'url' => '/'],
                    ['label' => 'ABOUT ME',   'url' => '/about-me'],
                    ['label' => 'SUPPORT ME', 'url' => '/support'],
                    ['label' => 'LINUX TUTORIALS', 'url' => '/linux'],
                    ['label' => 'POWERSHELL', 'url' => '/powershell', 'children' => [
                        ['label' => 'PS | WINDOWS SYSTEM', 'url' => '/powershell/windows'],
                        ['label' => 'PS | ACTIVE DIRECTORY', 'url' => '/powershell/active-directory'],
                        ['label' => 'MS365 | INSTALL, CONNECT', 'url' => '/microsoft-365/connect'],
                        ['label' => 'MS365 | AZURE', 'url' => '/microsoft-365/azure'],
                        ['label' => 'MS365 | ENTRAID', 'url' => '/microsoft-365/entra-id'],
                        ['label' => 'MS365 | EXCHANGE', 'url' => '/microsoft-365/exchange'],
                        ['label' => 'MS365 | TEAMS', 'url' => '/microsoft-365/teams'],
                        ['label' => 'MS365 | SHAREPOINT ONLINE', 'url' => '/microsoft-365/sharepoint'],
                    ]],
                    ['label' => 'MICROSOFT 365', 'url' => '/microsoft-365', 'children' => [
                        ['label' => 'MS365 | TUTORIALS', 'url' => '/microsoft-365/tutorials'],
                        ['label' => 'AZURE | SERVICES', 'url' => '/azure/services'],
                        ['label' => 'MS365 | LICENSING', 'url' => '/microsoft-365/licensing'],
                        ['label' => 'COPILOT | LICENSING', 'url' => '/copilot/licensing'],
                        ['label' => 'MS365 | SERVICES', 'url' => '/microsoft-365/services'],
                    ]],
                ]);
            }

            if (!$this->hasMenuEntries($tm->getMenu('quicklinks'))) {
                $tm->saveMenu('quicklinks', [
                    ['label' => 'Entra ID',    'url' => '/kategorie/entra-id'],
                    ['label' => 'Intune',      'url' => '/kategorie/intune'],
                    ['label' => 'Compliance',  'url' => '/kategorie/compliance'],
                    ['label' => 'Graph API',   'url' => '/kategorie/graph-api'],
                    ['label' => 'PowerShell',  'url' => '/kategorie/powershell'],
                    ['label' => 'Security',    'url' => '/kategorie/security'],
                    ['label' => 'Exchange',    'url' => '/kategorie/exchange'],
                ]);
            }
            if (!$this->hasMenuEntries($tm->getMenu('quicklinks_en'))) {
                $tm->saveMenu('quicklinks_en', [
                    ['label' => 'MS365 PRIVACY', 'url' => '/privacy', 'children' => [
                        ['label' => 'ORGANIZATION SETTINGS', 'url' => '/privacy/org-settings'],
                        ['label' => 'SERVICES', 'url' => '/privacy/services'],
                        ['label' => 'SECURITY & PRIVACY', 'url' => '/privacy/security'],
                        ['label' => 'ORGANIZATION PROFILE', 'url' => '/privacy/profile'],
                        ['label' => 'MICROSOFT EXCHANGE', 'url' => '/privacy/exchange'],
                        ['label' => 'MICROSOFT PURVIEW', 'url' => '/purview', 'children' => [
                            ['label' => 'DLP | ARCHITECTURE & STRATEGY', 'url' => '/purview/dlp'],
                        ]],
                    ]],
                ]);
            }

            if (!$this->hasMenuEntries($tm->getMenu('footer-topics'))) {
                $tm->saveMenu('footer-topics', [
                    ['label' => 'Linux & BASH',          'url' => '/linux'],
                    ['label' => 'PowerShell',            'url' => '/powershell'],
                    ['label' => 'Microsoft 365',         'url' => '/microsoft-365'],
                    ['label' => 'Intune & MDM',          'url' => '/intune'],
                    ['label' => 'Datenschutz & DSGVO',   'url' => '/datenschutz'],
                    ['label' => 'IT-News',               'url' => '/news'],
                ]);
            }
            if (!$this->hasMenuEntries($tm->getMenu('footer-topics_en'))) {
                $tm->saveMenu('footer-topics_en', [
                    ['label' => 'LINUX TUTORIALS', 'url' => '/linux'],
                    ['label' => 'POWERSHELL', 'url' => '/powershell', 'children' => [
                        ['label' => 'PS | WINDOWS SYSTEM', 'url' => '/powershell/windows'],
                        ['label' => 'PS | ACTIVE DIRECTORY', 'url' => '/powershell/active-directory'],
                        ['label' => 'MS365 | INSTALL, CONNECT', 'url' => '/microsoft-365/connect'],
                        ['label' => 'MS365 | AZURE', 'url' => '/microsoft-365/azure'],
                        ['label' => 'MS365 | ENTRAID', 'url' => '/microsoft-365/entra-id'],
                        ['label' => 'MS365 | EXCHANGE', 'url' => '/microsoft-365/exchange'],
                        ['label' => 'MS365 | TEAMS', 'url' => '/microsoft-365/teams'],
                        ['label' => 'MS365 | SHAREPOINT ONLINE', 'url' => '/microsoft-365/sharepoint'],
                    ]],
                    ['label' => 'MICROSOFT 365', 'url' => '/microsoft-365', 'children' => [
                        ['label' => 'MS365 | TUTORIALS', 'url' => '/microsoft-365/tutorials'],
                        ['label' => 'AZURE | SERVICES', 'url' => '/azure/services'],
                        ['label' => 'MS365 | LICENSING', 'url' => '/microsoft-365/licensing'],
                        ['label' => 'COPILOT | LICENSING', 'url' => '/copilot/licensing'],
                        ['label' => 'MS365 | SERVICES', 'url' => '/microsoft-365/services'],
                    ]],
                    ['label' => 'MS365 PRIVACY', 'url' => '/privacy', 'children' => [
                        ['label' => 'ORGANIZATION SETTINGS', 'url' => '/privacy/org-settings'],
                        ['label' => 'SERVICES', 'url' => '/privacy/services'],
                        ['label' => 'SECURITY & PRIVACY', 'url' => '/privacy/security'],
                        ['label' => 'ORGANIZATION PROFILE', 'url' => '/privacy/profile'],
                        ['label' => 'MICROSOFT EXCHANGE', 'url' => '/privacy/exchange'],
                        ['label' => 'MICROSOFT PURVIEW', 'url' => '/purview', 'children' => [
                            ['label' => 'DLP | ARCHITECTURE & STRATEGY', 'url' => '/purview/dlp'],
                        ]],
                    ]],
                ]);
            }

            if (!$this->hasMenuEntries($tm->getMenu('footer-pages'))) {
                $tm->saveMenu('footer-pages', [
                    ['label' => 'Über mich', 'url' => '/ueber-uns'],
                    ['label' => 'Kontakt',   'url' => '/contact'],
                    ['label' => 'RSS-Feed',  'url' => '/feed'],
                ]);
            }
            if (!$this->hasMenuEntries($tm->getMenu('footer-pages_en'))) {
                $tm->saveMenu('footer-pages_en', [
                    ['label' => 'HOME', 'url' => '/'],
                    ['label' => 'ABOUT ME', 'url' => '/about-me'],
                    ['label' => 'SUPPORT ME', 'url' => '/support'],
                    ['label' => 'CONTACT', 'url' => '/contact'],
                ]);
            }

            if (!$this->hasMenuEntries($tm->getMenu('footer'))) {
                $tm->saveMenu('footer', [
                    ['label' => 'Impressum',            'url' => '/impressum'],
                    ['label' => 'Datenschutz',          'url' => '/datenschutz'],
                    ['label' => 'AGB',                  'url' => '/agb'],
                ]);
            }
            if (!$this->hasMenuEntries($tm->getMenu('footer_en'))) {
                $tm->saveMenu('footer_en', [
                    ['label' => 'LEGAL NOTICE', 'url' => '/legal-notice'],
                    ['label' => 'PRIVACY POLICY', 'url' => '/privacy-policy'],
                    ['label' => 'TERMS & CONDITIONS', 'url' => '/terms'],
                    ['label' => 'CONTACT', 'url' => '/contact'],
                ]);
            }
        } catch (\Throwable $e) {
        }
    }
}
