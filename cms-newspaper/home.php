<?php
/**
 * CMS Newspaper Theme – Front Page
 *
 * Editorial broadsheet stack:
 *   Hero (Repo-Card) → Breaking Stack → Info-Box-Grid → Archive Grid → Feed-Grid
 *
 * @package CmsNewspaper_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$safe = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');

$heroKicker   = (string) news_config('hero_kicker',   '[ V5.0 DEPLOYED ]');
$heroHeadline = (string) news_config('hero_headline', 'PhinIT Script-Repository');
$heroLead     = (string) news_config('hero_lead',     'Holen Sie sich 150+ validierte, code-signierte PowerShell-Module für Ihre Microsoft 365 Administration. Open-Source und Enterprise-Ready.');
$heroCtaLabel = (string) news_config('hero_cta_label','Open GitHub');
$heroCtaUrl   = (string) news_config('hero_cta_url',  'https://github.com/phinit-de');

$breakingHeading  = (string) news_config('breaking_heading',  'Breaking Intelligence');
$archiveHeading   = (string) news_config('archive_heading',   'Deep-Dive Archiv');
$feedLeftHeading  = (string) news_config('feed_left_heading', 'Borns IT-Blog Feed');
$feedRightHeading = (string) news_config('feed_right_heading','Heise IT-News');

$infoLeftHeading  = (string) news_config('info_left_heading',  'Admin Playbooks');
$infoLeftText     = (string) news_config('info_left_text',     'Exklusive Tutorials für die tägliche Administration Ihrer Microsoft 365 Umgebung. Von Teams Policies bis SharePoint Architekturen.');
$infoLeftLabel    = (string) news_config('info_left_cta_label','Playbooks durchsuchen');
$infoLeftUrl      = (string) news_config('info_left_cta_url',  '/playbooks');

$infoRightHeading = (string) news_config('info_right_heading', 'DSGVO & Compliance');
$infoRightText    = (string) news_config('info_right_text',    'Konfigurations-Checklisten und rechtliche Best Practices für Purview, das Security Center und Datenschutz-Beauftragte.');
$infoRightLabel   = (string) news_config('info_right_cta_label','Compliance Hub öffnen');
$infoRightUrl     = (string) news_config('info_right_cta_url', '/compliance');

$showBreakingLive = filter_var(
    news_get_setting('news_content', 'show_breaking_label', true),
    FILTER_VALIDATE_BOOLEAN
);

/**
 * Static editorial placeholders – mirror the broadsheet layout of the
 * original `newspaper.html` prototype. These are intentionally inline
 * (not from DB) so the theme works out-of-the-box for marketing pages.
 * Editors can later swap to a CMS-driven article source if available.
 */
$breaking = [
    [
        'badge'    => 'Security',
        'badge_q'  => false,
        'title'    => 'Multi-Tenant Apps: Zugriff jetzt granular einschränken',
        'lead'     => 'Ein technischer Leitfaden zur Implementierung von Tenant-Einschränkungen für Drittanbieter-Applikationen in Entra ID.',
        'date'     => '23. Feb 2026',
        'read'     => '8 Min Lesezeit',
        'href'     => '/artikel/multi-tenant-apps-entra-id',
        'img'      => 'https://picsum.photos/seed/news-security/400/250',
    ],
    [
        'badge'    => 'PowerShell',
        'badge_q'  => false,
        'title'    => 'Graph API: User Lifecycle Automatisierung v2.4',
        'lead'     => 'Vollautomatisches Onboarding inklusive Lizenzzuweisung, Teams-Mitgliedschaft und Willkommens-Mails über das neue Modul.',
        'date'     => '21. Feb 2026',
        'read'     => '12 Min Lesezeit',
        'href'     => '/artikel/graph-api-user-lifecycle-v24',
        'img'      => 'https://picsum.photos/seed/news-powershell/400/250',
    ],
    [
        'badge'    => 'Compliance',
        'badge_q'  => true,
        'title'    => 'Purview | DLP Custom Dialoge im neuen Outlook Client',
        'lead'     => 'Verhindern Sie Datenabfluss durch kontextsensitive Benutzerführung. Ein Blueprint für starke DLP-Policies.',
        'date'     => '18. Feb 2026',
        'read'     => '6 Min Lesezeit',
        'href'     => '/artikel/purview-dlp-custom-dialoge',
        'img'      => 'https://picsum.photos/seed/news-purview/400/250',
    ],
    [
        'badge'    => 'Exchange',
        'badge_q'  => false,
        'title'    => 'Endgültiger Zeitplan: EWS-Abschaltung Mai 2027',
        'lead'     => 'Microsoft hat die Deadline für SOAP fixiert. Erfahren Sie, welche Legacy-Systeme Sie jetzt zwingend umstellen müssen.',
        'date'     => '15. Feb 2026',
        'read'     => '10 Min Lesezeit',
        'href'     => '/artikel/ews-abschaltung-mai-2027',
        'img'      => 'https://picsum.photos/seed/news-exchange/400/250',
    ],
];

$archive = [
    [
        'badge' => 'Intune',
        'title' => 'App Protection Policies für iOS & Android konfigurieren',
        'date'  => '12. Feb 2026',
        'kind'  => 'Guide',
        'href'  => '/artikel/intune-app-protection',
        'img'   => 'https://picsum.photos/seed/news-intune/400/250',
    ],
    [
        'badge' => 'Identity',
        'title' => 'Defender for Identity: Sensor-Setup in hybriden Umgebungen',
        'date'  => '08. Feb 2026',
        'kind'  => 'Tutorial',
        'href'  => '/artikel/defender-identity-sensor',
        'img'   => 'https://picsum.photos/seed/news-identity/400/250',
    ],
    [
        'badge' => 'Azure',
        'title' => 'Entra ID Connect Health Monitoring und Alerting',
        'date'  => '05. Feb 2026',
        'kind'  => 'Docs',
        'href'  => '/artikel/entra-connect-health',
        'img'   => 'https://picsum.photos/seed/news-azure/400/250',
    ],
    [
        'badge' => 'Teams',
        'title' => 'Teams Governance: Externe Gäste automatisch bereinigen',
        'date'  => '01. Feb 2026',
        'kind'  => 'Script',
        'href'  => '/artikel/teams-governance-gaeste',
        'img'   => 'https://picsum.photos/seed/news-teams/400/250',
    ],
    [
        'badge' => 'Hardening',
        'title' => 'M365 Tenant Hardening: Die Security Baseline 2026',
        'date'  => '28. Jan 2026',
        'kind'  => 'Checkliste',
        'href'  => '/artikel/m365-hardening-baseline-2026',
        'img'   => 'https://picsum.photos/seed/news-hardening/400/250',
    ],
    [
        'badge' => 'Migration',
        'title' => 'Modern Auth in Exchange Server 2019 erzwingen',
        'date'  => '22. Jan 2026',
        'kind'  => 'Guide',
        'href'  => '/artikel/modern-auth-exchange-2019',
        'img'   => 'https://picsum.photos/seed/news-modernauth/400/250',
    ],
    [
        'badge' => 'Purview',
        'title' => 'eDiscovery Premium: Analyse von Terabyte-Datensätzen',
        'date'  => '15. Jan 2026',
        'kind'  => 'Deep Dive',
        'href'  => '/artikel/ediscovery-premium-tb',
        'img'   => 'https://picsum.photos/seed/news-ediscovery/400/250',
    ],
    [
        'badge' => 'Graph',
        'title' => 'App-Permissions-Audit: Risikoreiche Apps finden (.ps1)',
        'date'  => '10. Jan 2026',
        'kind'  => 'Script',
        'href'  => '/artikel/app-permissions-audit',
        'img'   => 'https://picsum.photos/seed/news-graph/400/250',
    ],
    [
        'badge' => 'Storage',
        'title' => 'OneDrive Sync Security: Datenabfluss auf privaten PCs stoppen',
        'date'  => '02. Jan 2026',
        'kind'  => 'Tutorial',
        'href'  => '/artikel/onedrive-sync-security',
        'img'   => 'https://picsum.photos/seed/news-onedrive/400/250',
    ],
];

$feedLeft = [
    ['title' => 'Windows Notepad: CVE-2026-20841 patched',         'date' => '23.02.', 'href' => '#'],
    ['title' => 'Microsoft Edge: Passwort-Änderungen im Sommer',   'date' => '22.02.', 'href' => '#'],
    ['title' => 'Google beendet Gmailify für externe Konten',      'date' => '21.02.', 'href' => '#'],
];

$feedRight = [
    ['title' => 'Linux: Torvalds startet Kernel 7.0 Entwicklung',           'date' => '23.02.', 'href' => '#'],
    ['title' => 'KI-Update: GitHub Copilot Security Trends',                'date' => '23.02.', 'href' => '#'],
    ['title' => 'BSI warnt vor massiven Cloud-Infrastruktur-Angriffen',     'date' => '22.02.', 'href' => '#'],
];
?>

<div class="news-page">

    <!-- ██ HERO / REPO-CARD ██████████████████████████████████████████████ -->
    <section class="news-hero-card" aria-label="<?php echo $safe($heroHeadline); ?>">
        <div class="news-hero-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" focusable="false"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
        </div>
        <div class="news-hero-body">
            <span class="news-kicker"><?php echo $safe($heroKicker); ?></span>
            <h1 class="news-hero-title"><?php echo $safe($heroHeadline); ?></h1>
            <p class="news-hero-lead"><?php echo $safe($heroLead); ?></p>
        </div>
        <a href="<?php echo $safe(news_href($heroCtaUrl)); ?>"
           class="news-btn news-btn-on-dark"
           <?php if (str_starts_with($heroCtaUrl, 'http')) : ?>target="_blank" rel="noopener noreferrer"<?php endif; ?>>
            <?php echo $safe($heroCtaLabel); ?>
        </a>
    </section>

    <!-- ██ BREAKING STACK ████████████████████████████████████████████████ -->
    <h2 class="news-section-hdr">
        <?php echo $safe($breakingHeading); ?>
        <?php if ($showBreakingLive) : ?>
            <span class="live-dot" aria-label="Live-Inhalte">Live</span>
        <?php endif; ?>
    </h2>

    <div class="news-stack">
        <?php foreach ($breaking as $item) :
            $img      = (string) ($item['img']   ?? '');
            $title    = (string) ($item['title'] ?? '');
            $lead     = (string) ($item['lead']  ?? '');
            $date     = (string) ($item['date']  ?? '');
            $read     = (string) ($item['read']  ?? '');
            $badge    = (string) ($item['badge'] ?? '');
            $badgeQ   = !empty($item['badge_q']);
            $href     = news_href((string) ($item['href'] ?? '#'));
            ?>
            <a href="<?php echo $safe($href); ?>" class="news-list-card news-reveal">
                <div class="news-list-img">
                    <?php if ($badge !== '') : ?>
                        <span class="news-badge<?php echo $badgeQ ? ' is-quiet' : ''; ?>"><?php echo $safe($badge); ?></span>
                    <?php endif; ?>
                    <?php if ($img !== '') : ?>
                        <img src="<?php echo $safe($img); ?>" alt="" loading="lazy" decoding="async">
                    <?php endif; ?>
                </div>
                <div class="news-list-body">
                    <h3 class="news-list-title"><?php echo $safe($title); ?></h3>
                    <p><?php echo $safe($lead); ?></p>
                    <div class="news-meta">
                        <span class="news-meta-date"><?php echo $safe(strtoupper($date)); ?></span>
                        <span class="news-meta-sep" aria-hidden="true"></span>
                        <span><?php echo $safe(strtoupper($read)); ?></span>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- ██ INFO-BOXEN (2-Spalter) ████████████████████████████████████████ -->
    <div class="news-info-grid">
        <div class="news-info-box news-reveal">
            <h3>
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                </svg>
                <?php echo $safe($infoLeftHeading); ?>
            </h3>
            <p><?php echo $safe($infoLeftText); ?></p>
            <a href="<?php echo $safe(news_href($infoLeftUrl)); ?>" class="news-info-link">
                <?php echo $safe($infoLeftLabel); ?> →
            </a>
        </div>
        <div class="news-info-box is-accent news-reveal">
            <h3>
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                <?php echo $safe($infoRightHeading); ?>
            </h3>
            <p><?php echo $safe($infoRightText); ?></p>
            <a href="<?php echo $safe(news_href($infoRightUrl)); ?>" class="news-info-link">
                <?php echo $safe($infoRightLabel); ?> →
            </a>
        </div>
    </div>

    <!-- ██ ARCHIVE GRID (Broadsheet 3-col) ███████████████████████████████ -->
    <h2 class="news-section-hdr"><?php echo $safe($archiveHeading); ?></h2>

    <div class="news-grid">
        <?php foreach ($archive as $tile) :
            $tImg   = (string) ($tile['img']   ?? '');
            $tTitle = (string) ($tile['title'] ?? '');
            $tDate  = (string) ($tile['date']  ?? '');
            $tKind  = (string) ($tile['kind']  ?? '');
            $tBadge = (string) ($tile['badge'] ?? '');
            $tHref  = news_href((string) ($tile['href'] ?? '#'));
            ?>
            <a href="<?php echo $safe($tHref); ?>" class="news-tile news-reveal">
                <div class="news-tile-img">
                    <?php if ($tBadge !== '') : ?>
                        <span class="news-badge is-quiet"><?php echo $safe($tBadge); ?></span>
                    <?php endif; ?>
                    <?php if ($tImg !== '') : ?>
                        <img src="<?php echo $safe($tImg); ?>" alt="" loading="lazy" decoding="async">
                    <?php endif; ?>
                </div>
                <div class="news-tile-body">
                    <h3 class="news-tile-title"><?php echo $safe($tTitle); ?></h3>
                    <div class="news-tile-meta">
                        <span><?php echo $safe($tDate); ?></span>
                        <span><?php echo $safe(strtoupper($tKind)); ?></span>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- ██ FEED-GRID (Externe RSS-Highlights) ████████████████████████████ -->
    <div class="news-feed-grid">

        <div>
            <h2 class="news-section-hdr"><?php echo $safe($feedLeftHeading); ?></h2>
            <div class="news-feed-box">
                <?php foreach ($feedLeft as $row) : ?>
                    <a href="<?php echo $safe(news_href((string) ($row['href'] ?? '#'))); ?>" class="news-feed-item">
                        <span class="news-feed-title"><?php echo $safe((string) ($row['title'] ?? '')); ?></span>
                        <span class="news-feed-date"><?php echo $safe((string) ($row['date'] ?? '')); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div>
            <h2 class="news-section-hdr"><?php echo $safe($feedRightHeading); ?></h2>
            <div class="news-feed-box">
                <?php foreach ($feedRight as $row) : ?>
                    <a href="<?php echo $safe(news_href((string) ($row['href'] ?? '#'))); ?>" class="news-feed-item">
                        <span class="news-feed-title"><?php echo $safe((string) ($row['title'] ?? '')); ?></span>
                        <span class="news-feed-date"><?php echo $safe((string) ($row['date'] ?? '')); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

</div>
