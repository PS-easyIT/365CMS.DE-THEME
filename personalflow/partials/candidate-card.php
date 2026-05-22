<?php
/**
 * PersonalFlow Theme – Candidate / Talent Card Partial
 *
 * Erwartet ein assoziatives Array oder Objekt in $candidate mit den Feldern:
 *   - name        (string)
 *   - role        (string)
 *   - location    (string)
 *   - initials    (string, optional – wird sonst aus name abgeleitet)
 *   - avatar_url  (string, optional)
 *   - stage       (string: applied|screened|interview|offer|hired|archived)
 *   - skills      (array<string>, optional)
 *   - available   (bool, optional)
 *   - profile_url (string, optional – relativer Pfad oder absolute URL)
 *
 * @package PersonalFlow_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$candidate = $candidate ?? [];
$get = static function (string $k, mixed $d = null) use ($candidate): mixed {
    if (is_array($candidate)) {
        return $candidate[$k] ?? $d;
    }
    if (is_object($candidate)) {
        return $candidate->{$k} ?? $d;
    }
    return $d;
};

$pfCandName       = (string) $get('name', 'Unbenanntes Profil');
$pfCandRole       = (string) $get('role', '–');
$pfCandLocation   = (string) $get('location', '');
$pfCandAvatar     = (string) $get('avatar_url', '');
$pfCandStage      = (string) $get('stage', 'applied');
$pfCandSkills     = (array)  $get('skills', []);
$pfCandAvailable  = filter_var($get('available', false), FILTER_VALIDATE_BOOLEAN);
$pfCandProfileUrl = (string) $get('profile_url', '#');

$pfCandInitials   = (string) $get('initials', '');
if ($pfCandInitials === '') {
    $parts = preg_split('/\s+/', trim($pfCandName)) ?: [];
    $first = isset($parts[0]) ? mb_substr($parts[0], 0, 1) : '';
    $last  = isset($parts[count($parts) - 1]) && count($parts) > 1
        ? mb_substr($parts[count($parts) - 1], 0, 1)
        : '';
    $pfCandInitials = mb_strtoupper($first . $last);
    if ($pfCandInitials === '') {
        $pfCandInitials = '?';
    }
}

$pfValidStages = ['applied', 'screened', 'interview', 'offer', 'hired', 'archived'];
if (!in_array($pfCandStage, $pfValidStages, true)) {
    $pfCandStage = 'applied';
}

$pfStageLabel = function_exists('pf_stage_label')
    ? pf_stage_label($pfCandStage)
    : ucfirst($pfCandStage);

$safe        = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
$profileHref = $safe(function_exists('pf_href') ? pf_href($pfCandProfileUrl) : $pfCandProfileUrl);
?>
<article class="pf-card pf-talent-card pf-reveal" aria-label="Kandidat: <?php echo $safe($pfCandName); ?>">
    <div class="pf-talent-avatar" aria-hidden="true">
        <?php if ($pfCandAvatar !== '') : ?>
            <img src="<?php echo $safe($pfCandAvatar); ?>" alt="" loading="lazy" decoding="async">
        <?php else : ?>
            <?php echo $safe($pfCandInitials); ?>
        <?php endif; ?>
    </div>
    <div class="pf-talent-head">
        <span class="pf-talent-name"><?php echo $safe($pfCandName); ?></span>
        <span class="pf-talent-role"><?php echo $safe($pfCandRole); ?></span>
        <div class="pf-talent-meta">
            <?php if ($pfCandLocation !== '') : ?>
                <span class="pf-talent-meta-item">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" focusable="false" aria-hidden="true">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    <?php echo $safe($pfCandLocation); ?>
                </span>
            <?php endif; ?>
            <?php if ($pfCandAvailable) : ?>
                <span class="pf-talent-meta-item">
                    <span class="pf-stage-badge pf-stage-badge--hired"><?php echo $safe((string) pf_get_setting('hr_content', 'available_label', 'Sofort verfügbar')); ?></span>
                </span>
            <?php endif; ?>
        </div>
        <div class="pf-talent-stage-row">
            <span class="pf-stage-badge pf-stage-badge--<?php echo $safe($pfCandStage); ?>">
                <?php echo $safe((string) $pfStageLabel); ?>
            </span>
            <?php foreach (array_slice($pfCandSkills, 0, 3) as $skill) :
                $skill = (string) $skill;
                if (trim($skill) === '') { continue; }
                ?>
                <span class="pf-skill-chip"><?php echo $safe($skill); ?></span>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="pf-talent-actions">
        <a href="<?php echo $profileHref; ?>" class="pf-btn pf-btn-secondary pf-btn-sm pf-focus-shadow">
            Profil ansehen
        </a>
        <a href="<?php echo $profileHref; ?>?action=contact" class="pf-btn pf-btn-ghost pf-btn-sm pf-focus-shadow">
            Kontakt aufnehmen
        </a>
    </div>
</article>
