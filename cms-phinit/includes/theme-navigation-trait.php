<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

trait CMS_Phinit_Theme_Navigation_Trait
{
    public function registerMenuLocations(array $locations): array
    {
        $locations[] = ['slug' => 'primary',       'label' => 'Hauptnavigation'];
        $locations[] = ['slug' => 'quicklinks',    'label' => 'Quicklinks (Sub-Navigation)'];
        $locations[] = ['slug' => 'footer-topics', 'label' => 'Footer – Themen'];
        $locations[] = ['slug' => 'footer-pages',  'label' => 'Footer – Seiten'];
        $locations[] = ['slug' => 'footer',        'label' => 'Footer – Rechtliches'];
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

            if (empty($tm->getMenu('footer-pages'))) {
                $tm->saveMenu('footer-pages', [
                    ['label' => 'Über mich', 'url' => '/ueber-uns'],
                    ['label' => 'Kontakt',   'url' => '/contact'],
                    ['label' => 'RSS-Feed',  'url' => '/feed'],
                ]);
            }

            if (empty($tm->getMenu('footer'))) {
                $tm->saveMenu('footer', [
                    ['label' => 'Impressum',            'url' => '/impressum'],
                    ['label' => 'Datenschutz',          'url' => '/datenschutz'],
                    ['label' => 'AGB',                  'url' => '/agb'],
                ]);
            }
        } catch (\Throwable $e) {
        }
    }
}
