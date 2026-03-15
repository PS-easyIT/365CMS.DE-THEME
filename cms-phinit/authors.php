<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl = SITE_URL;
$currentLocale = function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de';
$authors = phinit_get_public_authors_overview();
$totalPosts = array_sum(array_map(static fn(array $author): int => (int) ($author['post_count'] ?? 0), $authors));
$newestAuthorDate = '';
foreach ($authors as $authorEntry) {
    $candidateDate = trim((string) ($authorEntry['latest_post_at'] ?? ''));
    if ($candidateDate !== '' && ($newestAuthorDate === '' || strtotime($candidateDate) > strtotime($newestAuthorDate))) {
        $newestAuthorDate = $candidateDate;
    }
}
?>

<div class="container phinit-special-page">
    <section class="phinit-special-hero" data-anim>
        <div class="phinit-special-hero__content">
            <span class="phinit-special-hero__eyebrow">CMS Phinit</span>
            <h1>Alle Autoren</h1>
            <p class="phinit-special-hero__lead">Hier findest du alle öffentlich sichtbaren Autorinnen und Autoren des Blogs – inklusive Kurzprofil, Aktivität und direktem Sprung zur persönlichen Artikelübersicht.</p>
        </div>
        <div class="phinit-special-hero__stats" aria-label="Autorenübersicht Statistik">
            <div class="phinit-special-stat">
                <span class="phinit-special-stat__value"><?php echo count($authors); ?></span>
                <span class="phinit-special-stat__label">Profile</span>
            </div>
            <div class="phinit-special-stat">
                <span class="phinit-special-stat__value"><?php echo $totalPosts; ?></span>
                <span class="phinit-special-stat__label">Beiträge</span>
            </div>
            <div class="phinit-special-stat">
                <span class="phinit-special-stat__value"><?php echo htmlspecialchars($newestAuthorDate !== '' ? phinit_format_date($newestAuthorDate, 'numeric', $currentLocale) : '–', ENT_QUOTES); ?></span>
                <span class="phinit-special-stat__label">Letzte Aktivität</span>
            </div>
        </div>
    </section>

    <?php if ($authors !== []): ?>
    <section class="phinit-authors-grid" data-anim data-anim-delay="1">
        <?php foreach ($authors as $author): ?>
        <?php
            $authorName = trim((string) ($author['display_name'] ?? 'Autor'));
            $authorBio = trim((string) ($author['bio'] ?? ''));
            $authorAvatar = trim((string) ($author['avatar_url'] ?? ''));
            $authorDetails = is_array($author['details'] ?? null) ? array_values(array_filter(array_slice($author['details'], 0, 3), static fn($detail): bool => is_array($detail))) : [];
            $authorUrl = function_exists('phinit_localized_href')
                ? phinit_localized_href((string) ($author['profile_url'] ?? '/author/user-' . (int) ($author['id'] ?? 0)), $currentLocale, $siteUrl)
                : $siteUrl . (string) ($author['profile_url'] ?? '/author/user-' . (int) ($author['id'] ?? 0));
            $initials = 'AU';
            if ($authorName !== '') {
                $parts = preg_split('/\s+/u', $authorName) ?: [];
                $initials = '';
                foreach (array_slice($parts, 0, 2) as $part) {
                    $initials .= mb_strtoupper((string) mb_substr((string) $part, 0, 1), 'UTF-8');
                }
                $initials = $initials !== '' ? $initials : 'AU';
            }
        ?>
        <article class="phinit-author-card">
            <div class="phinit-author-card__header">
                <?php if ($authorAvatar !== ''): ?>
                <img src="<?php echo htmlspecialchars($authorAvatar, ENT_QUOTES); ?>"
                     alt="<?php echo htmlspecialchars($authorName, ENT_QUOTES); ?>"
                     class="phinit-author-card__avatar"
                     <?php echo phinit_image_loading_attributes(true, false); ?>
                     width="72"
                     height="72">
                <?php else: ?>
                <div class="phinit-author-card__avatar phinit-author-card__avatar--placeholder" aria-hidden="true"><?php echo htmlspecialchars($initials, ENT_QUOTES); ?></div>
                <?php endif; ?>

                <div class="phinit-author-card__headline">
                    <h2><a href="<?php echo htmlspecialchars($authorUrl, ENT_QUOTES); ?>"><?php echo htmlspecialchars($authorName, ENT_QUOTES); ?></a></h2>
                    <p class="phinit-author-card__meta">
                        <span><?php echo (int) ($author['post_count'] ?? 0); ?> Beiträge</span>
                        <?php if (!empty($author['latest_post_at'])): ?>
                        <span>• Aktiv bis <?php echo htmlspecialchars(phinit_format_date((string) $author['latest_post_at'], 'numeric', $currentLocale), ENT_QUOTES); ?></span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>

            <?php if ($authorBio !== ''): ?>
            <p class="phinit-author-card__bio"><?php echo htmlspecialchars($authorBio, ENT_QUOTES); ?></p>
            <?php endif; ?>

            <?php if ($authorDetails !== []): ?>
            <ul class="phinit-author-card__details">
                <?php foreach ($authorDetails as $detail): ?>
                <?php
                    $detailLabel = trim((string) ($detail['label'] ?? ''));
                    $detailValue = trim((string) ($detail['value'] ?? ''));
                    $detailType = trim((string) ($detail['type'] ?? 'text'));
                    if ($detailLabel === '' || $detailValue === '') {
                        continue;
                    }
                ?>
                <li>
                    <span class="phinit-author-card__detail-label"><?php echo htmlspecialchars($detailLabel, ENT_QUOTES); ?>:</span>
                    <?php if ($detailType === 'url'): ?>
                    <?php $safeDetailUrl = phinit_safe_public_url($detailValue, $siteUrl); ?>
                    <?php if ($safeDetailUrl !== ''): ?>
                    <a href="<?php echo htmlspecialchars($safeDetailUrl, ENT_QUOTES); ?>"<?php echo preg_match('/^mailto:/i', $safeDetailUrl) === 1 ? '' : ' target="_blank" rel="noopener noreferrer"'; ?>><?php echo htmlspecialchars($detailValue, ENT_QUOTES); ?></a>
                    <?php else: ?>
                    <span><?php echo htmlspecialchars($detailValue, ENT_QUOTES); ?></span>
                    <?php endif; ?>
                    <?php else: ?>
                    <span><?php echo htmlspecialchars($detailValue, ENT_QUOTES); ?></span>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>

            <div class="phinit-author-card__footer">
                <a href="<?php echo htmlspecialchars($authorUrl, ENT_QUOTES); ?>" class="btn btn-primary btn-sm">Zum Autorenprofil</a>
            </div>
        </article>
        <?php endforeach; ?>
    </section>
    <?php else: ?>
    <div class="empty-state" data-anim data-anim-delay="1">
        <p class="empty-state__icon">✍️</p>
        <p><strong>Keine öffentlichen Autorenprofile gefunden</strong></p>
        <p class="empty-state__text">Sobald Autoren ihre öffentlichen Profilfelder freigeben und Beiträge veröffentlicht haben, erscheinen sie hier automatisch.</p>
    </div>
    <?php endif; ?>
</div>
