<?php
/**
 * Partial: Arzt-Karte – MedCare Pro Theme
 *
 * Erwartet $doctor (array|object) mit:
 *   - id, name, title, specialty, specialty_slug, avatar_url,
 *     insurance (array: 'gkv', 'pkv'), rating, review_count,
 *     location, next_appointment, url, verified
 *
 * Verwendung:
 *   <?php $doctor = [...]; include THEME_PATH . 'medcarepro/partials/doctor-card.php'; ?>
 *
 * @package MedCarePro_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$safe = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');

if (empty($doctor)) {
    return;
}

$d          = (array) $doctor;
$id         = (int) ($d['id']               ?? 0);
$name       = $safe((string) ($d['name']             ?? $d['display_name'] ?? ''));
$titleAbbr  = $safe((string) ($d['title']            ?? $d['academic_title'] ?? ''));
$specialty  = $safe((string) ($d['specialty']        ?? $d['specialty_name'] ?? ''));
$specSlug   = $safe((string) ($d['specialty_slug']   ?? strtolower(str_replace([' ', '/'], '-', (string) ($d['specialty'] ?? 'general')))));
$avatarUrl  = $safe((string) ($d['avatar_url']       ?? ''));
$location   = $safe((string) ($d['location']         ?? $d['city'] ?? ''));
$rating     = (float) ($d['rating']         ?? 0);
$reviews    = (int) ($d['review_count']   ?? 0);
$nextAppt   = $safe((string) ($d['next_appointment'] ?? ''));
$profileUrl = $safe((string) ($d['url']              ?? mc_href('/arzt/' . $id)));
$bookingUrl = $safe(mc_href('/termin?doctor=' . $id));
$hasGkv     = !empty($d['insurance']['gkv']) || !empty($d['gkv']);
$hasPkv     = !empty($d['insurance']['pkv']) || !empty($d['pkv']);
$verified   = !empty($d['verified']);
$ratingRnd  = (int) round($rating);
?>
<article class="mc-card mc-doctor-card" aria-labelledby="doctor-<?php echo $id; ?>">
    <div class="mc-doctor-card__head">
        <?php if ($avatarUrl !== '') : ?>
        <img src="<?php echo $avatarUrl; ?>" alt="Profilbild von <?php echo $name; ?>"
             class="mc-doctor-avatar" width="80" height="80">
        <?php else : ?>
        <div class="mc-doctor-avatar mc-doctor-avatar--placeholder" aria-hidden="true">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                 focusable="false" aria-hidden="true">
                <circle cx="12" cy="8" r="4"/>
                <path d="M4 21c0-4.418 3.582-8 8-8s8 3.582 8 8"/>
            </svg>
        </div>
        <?php endif; ?>

        <div class="mc-doctor-card__title-block">
            <h2 class="mc-doctor-name" id="doctor-<?php echo $id; ?>">
                <?php echo $titleAbbr !== '' ? $titleAbbr . ' ' . $name : $name; ?>
                <?php if ($verified) : ?>
                <span class="mc-doctor-verified" title="Verifizierter Arzt" aria-label="Verifizierter Arzt">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"
                         focusable="false" aria-hidden="true">
                        <path d="M12 1l3.09 6.26L22 8.27l-5 4.87 1.18 6.88L12 17.27 5.82 20l1.18-6.88L2 8.27l6.91-1.01z"/>
                    </svg>
                </span>
                <?php endif; ?>
            </h2>
            <?php if ($specialty !== '') : ?>
            <p class="mc-doctor-title">
                <span class="mc-specialty-badge mc-specialty-badge--<?php echo $specSlug; ?>"><?php echo $specialty; ?></span>
            </p>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($hasGkv || $hasPkv) : ?>
    <div class="mc-doctor-card__insurance">
        <?php if ($hasGkv) : ?>
        <span class="mc-insurance-badge mc-insurance--gkv">GKV</span>
        <?php endif; ?>
        <?php if ($hasPkv) : ?>
        <span class="mc-insurance-badge mc-insurance--pkv">PKV</span>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if ($rating > 0) : ?>
    <div class="mc-doctor-rating">
        <span class="mc-doctor-rating__stars" aria-hidden="true">
            <?php echo str_repeat('★', $ratingRnd) . str_repeat('☆', 5 - $ratingRnd); ?>
        </span>
        <span class="mc-doctor-rating__value"><?php echo number_format($rating, 1); ?></span>
        <?php if ($reviews > 0) : ?>
        <span class="mc-doctor-rating__count">(<?php echo $reviews; ?> Bewertungen)</span>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if ($location !== '') : ?>
    <p class="mc-doctor-location">
        <span class="mc-doctor-location__icon" aria-hidden="true">📍</span>
        <?php echo $location; ?>
    </p>
    <?php endif; ?>

    <?php if ($nextAppt !== '') : ?>
    <p class="mc-doctor-appointment">
        <span class="mc-doctor-appointment__icon" aria-hidden="true">🗓</span>
        Nächster Termin: <?php echo $nextAppt; ?>
    </p>
    <?php endif; ?>

    <div class="mc-doctor-card__actions">
        <a href="<?php echo $profileUrl; ?>"
           class="mc-btn mc-btn-outline mc-btn-sm mc-doctor-card__profile-btn"
           aria-label="Profil von <?php echo $name; ?> anzeigen">
            Profil
        </a>
        <a href="<?php echo $bookingUrl; ?>"
           class="mc-btn mc-btn-primary mc-btn-sm mc-doctor-card__book-btn"
           aria-label="Termin bei <?php echo $name; ?> buchen">
            Termin buchen
        </a>
    </div>
</article>
