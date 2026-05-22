<?php
/**
 * PersonalFlow Theme – Home / Startseite
 *
 * @package PersonalFlow_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$safe = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');

// ── Hero ────────────────────────────────────────────────────────────────────
$heroBadge       = (string) pf_get_setting('hr_hero', 'hero_badge',     'HR & Recruiting Plattform');
$heroHeadline    = (string) pf_get_setting('hr_hero', 'hero_headline',  'Vom Lebenslauf zum <span class="hl">Onboarding</span> – eine Pipeline.');
$heroSubline     = (string) pf_get_setting('hr_hero', 'hero_subline',   'Strukturierte Bewerbungs-Pipelines, transparente Status-Updates, faire Match-Scores.');
$ctaSeekerLabel  = (string) pf_get_setting('hr_hero', 'cta_jobseeker_label', 'Profil anlegen');
$ctaSeekerUrl    = (string) pf_get_setting('hr_hero', 'cta_jobseeker_url',   '/register?type=candidate');
$ctaEmpLabel     = (string) pf_get_setting('hr_hero', 'cta_employer_label', 'Talente finden');
$ctaEmpUrl       = (string) pf_get_setting('hr_hero', 'cta_employer_url',   '/arbeitgeber');
$heroAsideTitle  = (string) pf_get_setting('hr_hero', 'hero_aside_title',   'Aktuelle Pipeline');
$showRibbon      = filter_var(pf_get_setting('hr_hero', 'show_pipeline_ribbon', true), FILTER_VALIDATE_BOOLEAN);

// ── Pipeline Counts ─────────────────────────────────────────────────────────
$stageCounts = [
    'applied'   => (int) pf_get_setting('pipeline', 'stage_applied_count',   184),
    'screened'  => (int) pf_get_setting('pipeline', 'stage_screened_count',  62),
    'interview' => (int) pf_get_setting('pipeline', 'stage_interview_count', 24),
    'offer'     => (int) pf_get_setting('pipeline', 'stage_offer_count',     8),
    'hired'     => (int) pf_get_setting('pipeline', 'stage_hired_count',     31),
];
$stageLabels = [
    'applied'   => pf_stage_label('applied'),
    'screened'  => pf_stage_label('screened'),
    'interview' => pf_stage_label('interview'),
    'offer'     => pf_stage_label('offer'),
    'hired'     => pf_stage_label('hired'),
];

// ── Section Titles & KPIs ───────────────────────────────────────────────────
$talentTitle     = (string) pf_get_setting('hr_content', 'talent_section_title',    'Top-Talente, gerade verfügbar');
$talentSubtitle  = (string) pf_get_setting('hr_content', 'talent_section_subtitle', '');
$jobsTitle       = (string) pf_get_setting('hr_content', 'jobs_section_title',     'Aktuelle Stellenausschreibungen');
$jobsSubtitle    = (string) pf_get_setting('hr_content', 'jobs_section_subtitle',  '');
$salaryLabel     = (string) pf_get_setting('hr_content', 'salary_label',           'Jahresgehalt');
$remoteLabel     = (string) pf_get_setting('hr_content', 'remote_label',           'Remote möglich');
$kpiTitle        = (string) pf_get_setting('hr_content', 'kpi_section_title',      'Recruiting in Zahlen');

$kpis = [
    ['label' => pf_get_setting('hr_content', 'kpi_open_positions_label',    'Offene Stellen'),
     'value' => pf_get_setting('hr_content', 'kpi_open_positions_value',    '347')],
    ['label' => pf_get_setting('hr_content', 'kpi_avg_time_to_hire_label',  'Ø Time-to-Hire'),
     'value' => pf_get_setting('hr_content', 'kpi_avg_time_to_hire_value',  '21 Tage')],
    ['label' => pf_get_setting('hr_content', 'kpi_active_candidates_label', 'Aktive Kandidaten'),
     'value' => pf_get_setting('hr_content', 'kpi_active_candidates_value', '1.241')],
    ['label' => pf_get_setting('hr_content', 'kpi_match_rate_label',        'Match-Rate'),
     'value' => pf_get_setting('hr_content', 'kpi_match_rate_value',        '82 %')],
];

$ctaTitle      = (string) pf_get_setting('hr_content', 'cta_section_title',  'Bereit für den nächsten Schritt?');
$ctaText       = (string) pf_get_setting('hr_content', 'cta_section_text',   'Erstelle dein kostenloses Kandidaten- oder Arbeitgeber-Profil.');
$ctaRegLabel   = (string) pf_get_setting('hr_content', 'cta_register_label', 'Kostenlos registrieren');

$isLoggedIn  = theme_is_logged_in();
$registerUrl = pf_href('/register');

// ── Demo-Daten (Static editorial placeholders) ──────────────────────────────
// Mirrored from the HR-pipeline prototype intent. Production sites swap to a
// CMS-driven data source via CandidateService / JobService once available.
$talents = [
    [
        'name'        => 'Lena Brandt',
        'role'        => 'Senior Product Designer',
        'location'    => 'Berlin',
        'stage'       => 'interview',
        'skills'      => ['Figma', 'Design Systems', 'Research'],
        'available'   => true,
        'profile_url' => '/kandidaten/lena-brandt',
    ],
    [
        'name'        => 'Tarek Aksoy',
        'role'        => 'DevOps Engineer · 6 J. Erfahrung',
        'location'    => 'Remote (DE)',
        'stage'       => 'screened',
        'skills'      => ['Kubernetes', 'Terraform', 'Azure'],
        'available'   => false,
        'profile_url' => '/kandidaten/tarek-aksoy',
    ],
    [
        'name'        => 'Marie Schäfer',
        'role'        => 'Marketing-Manager B2B SaaS',
        'location'    => 'München',
        'stage'       => 'offer',
        'skills'      => ['Content', 'Analytics', 'HubSpot'],
        'available'   => true,
        'profile_url' => '/kandidaten/marie-schaefer',
    ],
];

$jobs = [
    [
        'title'    => 'Lead Backend Engineer (m/w/d)',
        'company'  => 'Helio Health GmbH',
        'location' => 'Hamburg · Hybrid',
        'salary'   => '75.000 – 95.000 €',
        'remote'   => true,
        'tags'     => ['Vollzeit', 'PHP 8.4', 'Senior'],
        'href'     => '/jobs/lead-backend-helio',
    ],
    [
        'title'    => 'People & Culture Specialist',
        'company'  => 'Atlas Manufacturing AG',
        'location' => 'Stuttgart · vor Ort',
        'salary'   => '52.000 – 62.000 €',
        'remote'   => false,
        'tags'     => ['Vollzeit', 'HR Generalist', '3+ J. Erfahrung'],
        'href'     => '/jobs/people-atlas',
    ],
    [
        'title'    => 'Werkstudent:in Recruiting',
        'company'  => 'NorthCode IT',
        'location' => 'Remote (EU)',
        'salary'   => '18 € / h',
        'remote'   => true,
        'tags'     => ['Werkstudium', '20 h / Woche', 'Remote'],
        'href'     => '/jobs/werkstudent-northcode',
    ],
];
?>

<main id="main" class="pf-main" role="main">

    <!-- ── HERO ─────────────────────────────────────────────────────────── -->
    <section class="pf-hero" aria-label="Einleitung">
        <div class="pf-container">
            <div class="pf-hero-inner">
                <div class="pf-hero-copy">
                    <?php if ($heroBadge !== '') : ?>
                        <span class="pf-hero-eyebrow">
                            <span class="pf-hero-eyebrow-dot" aria-hidden="true"></span>
                            <?php echo $safe($heroBadge); ?>
                        </span>
                    <?php endif; ?>
                    <h1><?php echo pf_safe_headline($heroHeadline); ?></h1>
                    <?php if ($heroSubline !== '') : ?>
                        <p class="pf-hero-sub"><?php echo $safe($heroSubline); ?></p>
                    <?php endif; ?>
                    <div class="pf-cta-group">
                        <a href="<?php echo $safe(pf_href($ctaSeekerUrl)); ?>" class="pf-btn pf-btn-primary pf-focus-shadow">
                            <?php echo $safe($ctaSeekerLabel); ?>
                        </a>
                        <a href="<?php echo $safe(pf_href($ctaEmpUrl)); ?>" class="pf-btn pf-btn-secondary pf-focus-shadow">
                            <?php echo $safe($ctaEmpLabel); ?>
                        </a>
                    </div>
                </div>

                <aside class="pf-hero-aside" aria-label="<?php echo $safe($heroAsideTitle); ?>">
                    <div class="pf-hero-aside-title"><?php echo $safe($heroAsideTitle); ?></div>
                    <?php foreach (array_slice($talents, 0, 2) as $candidate) : ?>
                        <?php include __DIR__ . '/partials/candidate-card.php'; ?>
                    <?php endforeach; ?>
                </aside>
            </div>

            <?php if ($showRibbon) : ?>
                <div class="pf-pipeline-ribbon" role="group" aria-label="Pipeline-Übersicht">
                    <?php $stepNum = 1; foreach ($stageCounts as $key => $count) : ?>
                        <div class="pf-pipeline-step pf-pipeline-step--<?php echo $safe($key); ?>">
                            <span class="pf-pipeline-step-num">STAGE 0<?php echo $stepNum++; ?></span>
                            <span class="pf-pipeline-step-label"><?php echo $safe($stageLabels[$key] ?? ucfirst($key)); ?></span>
                            <span class="pf-pipeline-step-count"><?php echo $safe(number_format($count, 0, ',', '.')); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ── KPI STRIP ────────────────────────────────────────────────────── -->
    <section class="pf-section" aria-label="<?php echo $safe($kpiTitle); ?>">
        <div class="pf-container">
            <div class="pf-section-header">
                <span class="pf-section-eyebrow">Live-Metriken</span>
                <h2><?php echo $safe($kpiTitle); ?></h2>
            </div>
            <div class="pf-kpi-strip">
                <?php foreach ($kpis as $kpi) : ?>
                    <div class="pf-kpi-cell">
                        <span class="pf-kpi-value"><?php echo $safe((string) $kpi['value']); ?></span>
                        <span class="pf-kpi-label"><?php echo $safe((string) $kpi['label']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ── TALENT POOL ─────────────────────────────────────────────────── -->
    <section class="pf-section pf-section--alt" aria-label="<?php echo $safe($talentTitle); ?>">
        <div class="pf-container">
            <div class="pf-section-header">
                <span class="pf-section-eyebrow">Talent-Pool</span>
                <h2><?php echo $safe($talentTitle); ?></h2>
                <?php if ($talentSubtitle !== '') : ?>
                    <p><?php echo $safe($talentSubtitle); ?></p>
                <?php endif; ?>
            </div>
            <div class="pf-grid-3">
                <?php foreach ($talents as $candidate) : ?>
                    <?php include __DIR__ . '/partials/candidate-card.php'; ?>
                <?php endforeach; ?>
            </div>
            <div class="pf-section-cta">
                <a href="<?php echo $safe(theme_route_url('candidates')); ?>" class="pf-btn pf-btn-ghost pf-focus-shadow">
                    Alle Kandidaten anzeigen →
                </a>
            </div>
        </div>
    </section>

    <!-- ── JOB BOARD ───────────────────────────────────────────────────── -->
    <section class="pf-section pf-section--surface" aria-label="<?php echo $safe($jobsTitle); ?>">
        <div class="pf-container">
            <div class="pf-section-header">
                <span class="pf-section-eyebrow">Stellenmarkt</span>
                <h2><?php echo $safe($jobsTitle); ?></h2>
                <?php if ($jobsSubtitle !== '') : ?>
                    <p><?php echo $safe($jobsSubtitle); ?></p>
                <?php endif; ?>
            </div>
            <div class="pf-grid-3">
                <?php foreach ($jobs as $job) :
                    $jHref = $safe(pf_href((string) ($job['href'] ?? '#')));
                    ?>
                    <article class="pf-card pf-job-card pf-reveal">
                        <div class="pf-job-header">
                            <div>
                                <div class="pf-job-title">
                                    <a href="<?php echo $jHref; ?>" class="pf-focus-shadow"><?php echo $safe((string) $job['title']); ?></a>
                                </div>
                                <div class="pf-job-company"><?php echo $safe((string) $job['company']); ?></div>
                            </div>
                            <?php if (!empty($job['remote'])) : ?>
                                <span class="pf-stage-badge pf-stage-badge--interview"><?php echo $safe($remoteLabel); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="pf-job-meta">
                            <span><?php echo $safe((string) $job['location']); ?></span>
                            <span>
                                <strong><?php echo $safe($salaryLabel); ?>:</strong>
                                <span class="pf-job-salary"><?php echo $safe((string) $job['salary']); ?></span>
                            </span>
                        </div>
                        <div class="pf-job-tags">
                            <?php foreach ((array) ($job['tags'] ?? []) as $tag) : ?>
                                <span class="pf-skill-chip"><?php echo $safe((string) $tag); ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="pf-job-cta-row">
                            <a href="<?php echo $jHref; ?>" class="pf-btn pf-btn-primary pf-btn-sm pf-focus-shadow">Details &amp; Bewerben</a>
                            <a href="<?php echo $jHref; ?>?action=save" class="pf-btn pf-btn-ghost pf-btn-sm pf-focus-shadow">Merken</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ── CTA ─────────────────────────────────────────────────────────── -->
    <?php if (!$isLoggedIn) : ?>
        <section class="pf-cta-section" aria-label="Call to Action">
            <div class="pf-container">
                <h2><?php echo $safe($ctaTitle); ?></h2>
                <?php if ($ctaText !== '') : ?>
                    <p><?php echo $safe($ctaText); ?></p>
                <?php endif; ?>
                <a href="<?php echo $safe($registerUrl); ?>" class="pf-btn pf-btn-outline pf-focus-shadow">
                    <?php echo $safe($ctaRegLabel); ?>
                </a>
            </div>
        </section>
    <?php endif; ?>

</main>
