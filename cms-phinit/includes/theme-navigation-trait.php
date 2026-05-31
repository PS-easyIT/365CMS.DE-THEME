<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

trait CMS_Phinit_Theme_Navigation_Trait
{
    public function registerMenuLocations(array $locations): array
    {
        $locations[] = ['slug' => 'primary',          'label' => phinit_t('menu_location_primary')];
        $locations[] = ['slug' => 'primary_en',       'label' => phinit_t('menu_location_primary_en')];
        $locations[] = ['slug' => 'quicklinks',       'label' => phinit_t('menu_location_quicklinks')];
        $locations[] = ['slug' => 'quicklinks_en',    'label' => phinit_t('menu_location_quicklinks_en')];
        $locations[] = ['slug' => 'footer-topics',    'label' => phinit_t('menu_location_footer_topics')];
        $locations[] = ['slug' => 'footer-topics_en', 'label' => phinit_t('menu_location_footer_topics_en')];
        $locations[] = ['slug' => 'footer-pages',     'label' => phinit_t('menu_location_footer_pages')];
        $locations[] = ['slug' => 'footer-pages_en',  'label' => phinit_t('menu_location_footer_pages_en')];
        $locations[] = ['slug' => 'footer',           'label' => phinit_t('menu_location_footer_legal')];
        $locations[] = ['slug' => 'footer_en',        'label' => phinit_t('menu_location_footer_legal_en')];
        return $locations;
    }

    /** Standard-Navigation beim Erststart anlegen */
    public function seedDefaultMenus(): void
    {
        try {
            $tm = \CMS\ThemeManager::instance();

            if (empty($tm->getMenu('primary'))) {
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
            if (empty($tm->getMenu('primary_en'))) {
                $tm->saveMenu('primary_en', [
                    ['label' => 'Home',         'url' => '/'],
                    ['label' => 'Linux / BASH', 'url' => '/linux'],
                    ['label' => 'PowerShell',   'url' => '/powershell', 'children' => [
                        ['label' => 'Basics', 'url' => '/powershell/grundlagen'],
                        ['label' => 'Glossary', 'url' => '/powershell/glossar'],
                    ]],
                    ['label' => 'Microsoft 365', 'url' => '/microsoft-365', 'children' => [
                        ['label' => 'Microsoft 365 Admin', 'url' => '/microsoft-365/admin'],
                        ['label' => 'Exchange Online',     'url' => '/microsoft-365/exchange'],
                        ['label' => 'Teams & SharePoint',  'url' => '/microsoft-365/teams'],
                    ]],
                    ['label' => 'Privacy', 'url' => '/datenschutz'],
                    ['label' => 'News',    'url' => '/news'],
                ]);
            }

            if (empty($tm->getMenu('quicklinks'))) {
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
            if (empty($tm->getMenu('quicklinks_en'))) {
                $tm->saveMenu('quicklinks_en', [
                    ['label' => 'Entra ID',   'url' => '/kategorie/entra-id'],
                    ['label' => 'Intune',     'url' => '/kategorie/intune'],
                    ['label' => 'Compliance', 'url' => '/kategorie/compliance'],
                    ['label' => 'Graph API',  'url' => '/kategorie/graph-api'],
                    ['label' => 'PowerShell', 'url' => '/kategorie/powershell'],
                    ['label' => 'Security',   'url' => '/kategorie/security'],
                    ['label' => 'Exchange',   'url' => '/kategorie/exchange'],
                ]);
            }

            if (empty($tm->getMenu('footer-topics'))) {
                $tm->saveMenu('footer-topics', [
                    ['label' => 'Linux & BASH',          'url' => '/linux'],
                    ['label' => 'PowerShell',            'url' => '/powershell'],
                    ['label' => 'Microsoft 365',         'url' => '/microsoft-365'],
                    ['label' => 'Intune & MDM',          'url' => '/intune'],
                    ['label' => 'Datenschutz & DSGVO',   'url' => '/datenschutz'],
                    ['label' => 'IT-News',               'url' => '/news'],
                ]);
            }
            if (empty($tm->getMenu('footer-topics_en'))) {
                $tm->saveMenu('footer-topics_en', [
                    ['label' => 'Linux & BASH',      'url' => '/linux'],
                    ['label' => 'PowerShell',        'url' => '/powershell'],
                    ['label' => 'Microsoft 365',     'url' => '/microsoft-365'],
                    ['label' => 'Intune & MDM',      'url' => '/intune'],
                    ['label' => 'Privacy & GDPR',    'url' => '/datenschutz'],
                    ['label' => 'IT News',           'url' => '/news'],
                ]);
            }

            if (empty($tm->getMenu('footer-pages'))) {
                $tm->saveMenu('footer-pages', [
                    ['label' => 'Über mich', 'url' => '/ueber-uns'],
                    ['label' => 'Kontakt',   'url' => '/contact'],
                    ['label' => 'RSS-Feed',  'url' => '/feed'],
                ]);
            }
            if (empty($tm->getMenu('footer-pages_en'))) {
                $tm->saveMenu('footer-pages_en', [
                    ['label' => 'About me',  'url' => '/ueber-uns'],
                    ['label' => 'Contact',   'url' => '/contact'],
                    ['label' => 'RSS Feed',  'url' => '/feed'],
                ]);
            }

            if (empty($tm->getMenu('footer'))) {
                $tm->saveMenu('footer', [
                    ['label' => 'Impressum',            'url' => '/impressum'],
                    ['label' => 'Datenschutz',          'url' => '/datenschutz'],
                    ['label' => 'AGB',                  'url' => '/agb'],
                ]);
            }
            if (empty($tm->getMenu('footer_en'))) {
                $tm->saveMenu('footer_en', [
                    ['label' => 'Imprint', 'url' => '/impressum'],
                    ['label' => 'Privacy', 'url' => '/datenschutz'],
                    ['label' => 'Terms',   'url' => '/agb'],
                ]);
            }
        } catch (\Throwable $e) {
        }
    }
}
