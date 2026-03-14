<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$feedSections = isset($feedSections) && is_array($feedSections) ? $feedSections : [];

if ($feedSections === []) {
    return;
}
?>
<section class="content-section home-section home-section--rss" data-anim data-anim-delay="3" data-cms-feed-protected>
    <div class="feed-dual-grid">
        <?php foreach ($feedSections as $feedSection): ?>
        <section class="feed-dual-col">
            <div class="section-header">
                <span class="section-label section-label--dark"><?php echo htmlspecialchars((string) ($feedSection['channel']['name'] ?? 'Feed'), ENT_QUOTES); ?></span>
            </div>
            <ul class="feed-list feed-list--inline">
                <?php foreach (array_slice((array) ($feedSection['items'] ?? []), 0, 4) as $feedItem): ?>
                <li>
                    <a href="<?php echo htmlspecialchars((string) ($feedItem['link'] ?? '#'), ENT_QUOTES); ?>"
                       target="_blank" rel="noopener noreferrer">
                        <?php echo htmlspecialchars((string) ($feedItem['title'] ?? ''), ENT_QUOTES); ?>
                    </a>
                    <span class="meta"><?php
                        $timestamp = !empty($feedItem['pub_date']) ? strtotime((string) $feedItem['pub_date']) : false;
                        echo htmlspecialchars($timestamp ? date('j. M Y', $timestamp) : '', ENT_QUOTES);
                    ?></span>
                </li>
                <?php endforeach; ?>
                <?php if (empty($feedSection['items'])): ?>
                <li class="feed-list__empty">Keine Einträge verfügbar.</li>
                <?php endif; ?>
            </ul>
        </section>
        <?php endforeach; ?>
    </div>
</section>