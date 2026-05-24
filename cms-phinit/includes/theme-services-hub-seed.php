<?php
/**
 * Einmaliger Seed für die PHINIT-Dienstleistungs-HubSite.
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

function phinit_seed_services_hub_site(): void
{
    static $didRun = false;
    if ($didRun || !class_exists(\CMS\Database::class)) {
        return;
    }
    $didRun = true;

    try {
        $db = \CMS\Database::instance();
        $prefix = $db->getPrefix();
        $optionKey = 'cms_phinit_services_hub_seeded';
        $seedKey = 'cms-phinit-services-hub-v1';

        $seeded = trim((string) ($db->get_var("SELECT option_value FROM {$prefix}settings WHERE option_name = ? LIMIT 1", [$optionKey]) ?? ''));
        if ($seeded !== '') {
            return;
        }

        $existingSeed = (int) ($db->get_var(
            "SELECT id FROM {$prefix}site_tables
             WHERE COALESCE(JSON_UNQUOTE(JSON_EXTRACT(settings_json, '$.content_mode')), 'table') = 'hub'
               AND JSON_UNQUOTE(JSON_EXTRACT(settings_json, '$.phinit_seed_key')) = ?
             LIMIT 1",
            [$seedKey]
        ) ?? 0);
        if ($existingSeed > 0) {
            phinit_services_hub_write_seed_option($db, $prefix, $optionKey, CMS_PHINIT_THEME_VERSION);
            return;
        }

        $hasTableSlugColumn = phinit_services_hub_has_table_slug_column($db, $prefix);
        $slug = phinit_services_hub_unique_slug($db, $prefix, 'it-dienstleistungen', $hasTableSlugColumn);
        $settings = phinit_services_hub_settings($slug, $seedKey);
        $cards = phinit_services_hub_cards();
        $description = '<p>Landing-Hub für Beratungs-, Umsetzungs- und Betriebsleistungen rund um Microsoft 365, 365CMS, Automatisierung, Security und laufenden IT-Betrieb.</p>';

        $columns = ['table_name', 'description', 'columns_json', 'rows_json', 'settings_json'];
        $placeholders = ['?', '?', "'[]'", '?', '?'];
        $params = [
            'IT-Dienstleistungen',
            $description,
            json_encode($cards, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '[]',
            json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}',
        ];

        if ($hasTableSlugColumn) {
            $columns[] = 'table_slug';
            $placeholders[] = '?';
            $params[] = $slug;
        }

        $columns[] = 'created_at';
        $columns[] = 'updated_at';
        $placeholders[] = 'NOW()';
        $placeholders[] = 'NOW()';

        $db->execute(
            "INSERT INTO {$prefix}site_tables (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")",
            $params
        );

        phinit_services_hub_write_seed_option($db, $prefix, $optionKey, CMS_PHINIT_THEME_VERSION);
    } catch (\Throwable $e) {
        error_log('CMS Phinit: Dienstleistungs-HubSite konnte nicht angelegt werden: ' . $e->getMessage());
    }
}

function phinit_services_hub_settings(string $slug, string $seedKey): array
{
    $links = [
        ['label' => 'Beratung', 'url' => '#beratung'],
        ['label' => 'Microsoft 365', 'url' => '#microsoft-365'],
        ['label' => 'CMS & Web', 'url' => '#cms-web'],
        ['label' => 'Betrieb', 'url' => '#betrieb'],
        ['label' => 'Workshops', 'url' => '#workshops'],
        ['label' => 'Kontakt', 'url' => '/contact'],
    ];

    $sections = [
        [
            'title' => 'Von der Idee zur belastbaren Roadmap',
            'text' => 'Strukturierte Beratung, Bestandsaufnahme und Priorisierung für Microsoft 365, Security, Web-Plattformen und Automatisierung.',
            'actionLabel' => 'Roadmap starten',
            'actionUrl' => '#beratung',
        ],
        [
            'title' => 'Umsetzung mit Betriebsblick',
            'text' => 'Technische Umsetzung, Dokumentation, Übergabe und optionaler Betrieb werden von Anfang an zusammen gedacht.',
            'actionLabel' => 'Delivery ansehen',
            'actionUrl' => '#betrieb',
        ],
        [
            'title' => 'Enablement statt Blackbox',
            'text' => 'Workshops, Runbooks und klare Entscheidungsgrundlagen sorgen dafür, dass Teams Lösungen auch nach dem Projekt souverän betreiben können.',
            'actionLabel' => 'Workshops planen',
            'actionUrl' => '#workshops',
        ],
    ];

    return [
        'content_mode' => 'hub',
        'phinit_seed_key' => $seedKey,
        'hub_slug' => $slug,
        'hub_domains' => [],
        'hub_template' => 'services',
        'hub_feature_card_interval' => 0,
        'hub_feature_cards_json' => '[]',
        'hub_badge' => 'PHINIT Services',
        'hub_badge_en' => 'PHINIT Services',
        'hub_hero_title' => 'IT-Dienstleistungen für sichere Microsoft-365-, Cloud- und CMS-Umgebungen',
        'hub_hero_title_en' => 'IT services for secure Microsoft 365, cloud and CMS environments',
        'hub_hero_text' => '<p>Beratung, Umsetzung und Betrieb aus einer Hand: pragmatisch, sauber dokumentiert und mit Fokus auf Lösungen, die im Alltag funktionieren.</p>',
        'hub_hero_text_en' => '<p>Consulting, implementation and operations from one source: pragmatic, well documented and focused on solutions that work in daily business.</p>',
        'hub_cta_label' => 'Kostenloses Erstgespräch',
        'hub_cta_label_en' => 'Book an intro call',
        'hub_cta_url' => '/contact',
        'hub_meta_audience' => 'KMU, IT-Leitung & Fachbereiche',
        'hub_meta_audience_en' => 'SMBs, IT leads & business teams',
        'hub_meta_owner' => 'phinIT Consulting',
        'hub_meta_owner_en' => 'phinIT Consulting',
        'hub_meta_update_cycle' => 'Quartalsweise',
        'hub_meta_update_cycle_en' => 'Quarterly',
        'hub_meta_focus' => 'Beratung, Umsetzung & Betrieb',
        'hub_meta_focus_en' => 'Consulting, delivery & operations',
        'hub_meta_kpi' => 'Time-to-Value',
        'hub_meta_kpi_en' => 'Time-to-value',
        'hub_links_json' => json_encode($links, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '[]',
        'hub_sections_json' => json_encode($sections, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '[]',
        'hub_card_layout' => 'feature',
        'hub_card_image_position' => 'top',
        'hub_card_image_fit' => 'cover',
        'hub_card_image_ratio' => 'wide',
        'hub_card_meta_layout' => 'split',
    ];
}

function phinit_services_hub_cards(): array
{
    return [
        [
            'title' => 'IT-Strategie & Architektur',
            'title_en' => 'IT strategy & architecture',
            'url' => '#beratung',
            'summary' => 'Bestandsaufnahme, Zielbild, Roadmap und priorisierte Maßnahmen für moderne IT- und Cloud-Umgebungen.',
            'summary_en' => 'Assessment, target architecture, roadmap and prioritized actions for modern IT and cloud environments.',
            'badge' => 'Consulting',
            'badge_en' => 'Consulting',
            'meta_left' => 'Workshop',
            'meta_left_en' => 'Workshop',
            'meta_right' => 'Roadmap',
            'meta_right_en' => 'Roadmap',
            'button_text' => 'Beratung anfragen',
            'button_text_en' => 'Request consulting',
            'button_link' => '/contact',
        ],
        [
            'title' => 'Microsoft 365 & Security',
            'title_en' => 'Microsoft 365 & security',
            'url' => '#microsoft-365',
            'summary' => 'Governance, Identitäten, Teams, SharePoint, Exchange, Security-Baselines und Adoption sauber zusammengedacht.',
            'summary_en' => 'Governance, identities, Teams, SharePoint, Exchange, security baselines and adoption aligned end to end.',
            'badge' => 'Microsoft 365',
            'badge_en' => 'Microsoft 365',
            'meta_left' => 'Projekt',
            'meta_left_en' => 'Project',
            'meta_right' => 'Secure Rollout',
            'meta_right_en' => 'Secure rollout',
            'button_text' => 'M365 besprechen',
            'button_text_en' => 'Discuss M365',
            'button_link' => '/contact',
        ],
        [
            'title' => '365CMS & Web-Plattformen',
            'title_en' => '365CMS & web platforms',
            'url' => '#cms-web',
            'summary' => 'Konzeption, Theme-Anpassung, Performance, Inhalte und sichere CMS-Prozesse für professionelle Websites.',
            'summary_en' => 'Concept, theme customization, performance, content and secure CMS processes for professional websites.',
            'badge' => 'CMS',
            'badge_en' => 'CMS',
            'meta_left' => 'Build',
            'meta_left_en' => 'Build',
            'meta_right' => 'Go-live',
            'meta_right_en' => 'Go-live',
            'button_text' => 'CMS-Projekt starten',
            'button_text_en' => 'Start CMS project',
            'button_link' => '/contact',
        ],
        [
            'title' => 'Automation & PowerShell',
            'title_en' => 'Automation & PowerShell',
            'url' => '#automation',
            'summary' => 'Runbooks, Skripte, Reporting und wiederholbare Abläufe, die Admin-Alltag und Betrieb spürbar entlasten.',
            'summary_en' => 'Runbooks, scripts, reporting and repeatable workflows that reduce operational load.',
            'badge' => 'Automation',
            'badge_en' => 'Automation',
            'meta_left' => 'Runbook',
            'meta_left_en' => 'Runbook',
            'meta_right' => 'Effizienz',
            'meta_right_en' => 'Efficiency',
            'button_text' => 'Automation planen',
            'button_text_en' => 'Plan automation',
            'button_link' => '/contact',
        ],
        [
            'title' => 'Managed Services & Betrieb',
            'title_en' => 'Managed services & operations',
            'url' => '#betrieb',
            'summary' => 'Betriebsunterstützung, Monitoring, Wartung, Release-Begleitung und klare Eskalationspfade für laufende Systeme.',
            'summary_en' => 'Operational support, monitoring, maintenance, release assistance and clear escalation paths for running systems.',
            'badge' => 'Betrieb',
            'badge_en' => 'Operations',
            'meta_left' => 'Support',
            'meta_left_en' => 'Support',
            'meta_right' => 'SLA-ready',
            'meta_right_en' => 'SLA-ready',
            'button_text' => 'Betrieb anfragen',
            'button_text_en' => 'Request operations',
            'button_link' => '/contact',
        ],
        [
            'title' => 'Workshops & Enablement',
            'title_en' => 'Workshops & enablement',
            'url' => '#workshops',
            'summary' => 'Praxisnahe Workshops, Admin-Schulungen und Übergaben, damit Wissen nicht im Projektordner verstaubt.',
            'summary_en' => 'Hands-on workshops, admin enablement and handovers so knowledge does not disappear in project folders.',
            'badge' => 'Training',
            'badge_en' => 'Training',
            'meta_left' => 'Workshop',
            'meta_left_en' => 'Workshop',
            'meta_right' => 'Enablement',
            'meta_right_en' => 'Enablement',
            'button_text' => 'Workshop buchen',
            'button_text_en' => 'Book workshop',
            'button_link' => '/contact',
        ],
    ];
}

function phinit_services_hub_unique_slug(\CMS\Database $db, string $prefix, string $baseSlug, ?bool $hasTableSlugColumn = null): string
{
    $slug = $baseSlug;
    $suffix = 2;
    $hasTableSlugColumn ??= phinit_services_hub_has_table_slug_column($db, $prefix);
    while (phinit_services_hub_slug_exists($db, $prefix, $slug, $hasTableSlugColumn)) {
        $slug = $baseSlug . '-' . $suffix;
        $suffix++;
    }

    return $slug;
}

function phinit_services_hub_slug_exists(\CMS\Database $db, string $prefix, string $slug, ?bool $hasTableSlugColumn = null): bool
{
    $hasTableSlugColumn ??= phinit_services_hub_has_table_slug_column($db, $prefix);
    $slugWhere = $hasTableSlugColumn
        ? "(table_slug = ? OR JSON_UNQUOTE(JSON_EXTRACT(settings_json, '$.hub_slug')) = ?)"
        : "JSON_UNQUOTE(JSON_EXTRACT(settings_json, '$.hub_slug')) = ?";
    $params = $hasTableSlugColumn ? [$slug, $slug] : [$slug];

    $hubCount = (int) ($db->get_var(
        "SELECT COUNT(*) FROM {$prefix}site_tables
         WHERE COALESCE(JSON_UNQUOTE(JSON_EXTRACT(settings_json, '$.content_mode')), 'table') = 'hub'
           AND {$slugWhere}",
        $params
    ) ?? 0);
    if ($hubCount > 0) {
        return true;
    }

    try {
        return (int) ($db->get_var("SELECT COUNT(*) FROM {$prefix}pages WHERE slug = ?", [$slug]) ?? 0) > 0;
    } catch (\Throwable $e) {
        return false;
    }
}

function phinit_services_hub_has_table_slug_column(\CMS\Database $db, string $prefix): bool
{
    try {
        return (bool) $db->get_var("SHOW COLUMNS FROM {$prefix}site_tables LIKE 'table_slug'");
    } catch (\Throwable $e) {
        return false;
    }
}

function phinit_services_hub_write_seed_option(\CMS\Database $db, string $prefix, string $optionKey, string $value): void
{
    $exists = (int) ($db->get_var("SELECT COUNT(*) FROM {$prefix}settings WHERE option_name = ?", [$optionKey]) ?? 0);
    if ($exists > 0) {
        $db->execute("UPDATE {$prefix}settings SET option_value = ? WHERE option_name = ?", [$value, $optionKey]);
        return;
    }

    $db->execute("INSERT INTO {$prefix}settings (option_name, option_value) VALUES (?, ?)", [$optionKey, $value]);
}
