<?php
/**
 * PTC Theme – Suchergebnisse
 *
 * Erhält:
 *   $results - Array von Suchergebnissen (Seiten-Objekte)
 *   $query   - Suchbegriff (string)
 *
 * @package PTC_Theme
 * @var array  $results
 * @var string $query
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$results  = $results ?? [];
$query    = $query ?? '';
$siteUrl  = ptc_site_url();
$count    = count($results);

// Typ-Badge-Mapping
$typeBadges = [
    'page'    => ['label' => 'Seite',   'bg' => '#dbeafe', 'color' => '#1e40af'],
    'expert'  => ['label' => 'Experte', 'bg' => '#d1fae5', 'color' => '#065f46'],
    'company' => ['label' => 'Firma',   'bg' => '#fef3c7', 'color' => '#92400e'],
    'speaker' => ['label' => 'Speaker', 'bg' => '#ede9fe', 'color' => '#5b21b6'],
    'event'   => ['label' => 'Event',   'bg' => '#fce7f3', 'color' => '#9d174d'],
];
?>

<!-- Page Hero -->
<section class="ptc-page-hero">
    <div class="ptc-container">
        <p class="ptc-hero-overline">Suche</p>
        <?php if ($query && trim($query) !== ''): ?>
            <h1>Ergebnisse für „<?php echo htmlspecialchars($query, ENT_QUOTES, 'UTF-8'); ?>"</h1>
            <p><?php echo $count; ?> Ergebnis<?php echo $count !== 1 ? 'se' : ''; ?> gefunden</p>
        <?php else: ?>
            <h1>Suche</h1>
        <?php endif; ?>
    </div>
</section>

<section class="ptc-page-content">
    <div class="ptc-container" style="max-width:800px;">

        <!-- Suchformular -->
        <form action="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/search" method="GET"
              style="display:flex;gap:0.75rem;margin-bottom:2.5rem;">
            <input class="ptc-form-control"
                   type="search"
                   name="q"
                   placeholder="Suche nach Seiten, Themen…"
                   value="<?php echo htmlspecialchars($query, ENT_QUOTES, 'UTF-8'); ?>"
                   aria-label="Suchbegriff"
                   style="flex:1;">
            <button type="submit" class="btn-ptc btn-ptc-primary">Suchen</button>
        </form>

        <!-- Ergebnisse -->
        <?php if ($count > 0): ?>
            <div class="ptc-search-results">
                <?php foreach ($results as $result):
                    $resultTitle   = is_array($result) ? ($result['title'] ?? '') : ($result->title ?? '');
                    $resultSlug    = is_array($result) ? ($result['slug'] ?? '') : ($result->slug ?? '');
                    $resultExcerpt = is_array($result) ? ($result['meta_description'] ?? $result['content'] ?? '') : ($result->meta_description ?? $result->content ?? '');
                    $resultType    = is_array($result) ? ($result['_type'] ?? 'page') : ($result->_type ?? 'page');
                    $resultUrl     = htmlspecialchars($siteUrl . '/' . $resultSlug, ENT_QUOTES, 'UTF-8');
                    $badge         = $typeBadges[$resultType] ?? $typeBadges['page'];

                    // Excerpt kürzen
                    $excerpt = strip_tags((string) $resultExcerpt);
                    if (mb_strlen($excerpt) > 200) {
                        $excerpt = mb_substr($excerpt, 0, 200) . '…';
                    }
                ?>
                    <article class="ptc-search-item">
                        <h2 class="ptc-search-title">
                            <a href="<?php echo $resultUrl; ?>">
                                <?php echo htmlspecialchars($resultTitle, ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                            <span class="ptc-search-badge" style="background:<?php echo $badge['bg']; ?>;color:<?php echo $badge['color']; ?>;">
                                <?php echo htmlspecialchars($badge['label']); ?>
                            </span>
                        </h2>
                        <?php if ($excerpt && trim($excerpt) !== ''): ?>
                            <p class="ptc-search-excerpt"><?php echo htmlspecialchars($excerpt, ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php endif; ?>
                        <p class="ptc-search-url">
                            <a href="<?php echo $resultUrl; ?>"><?php echo $resultUrl; ?></a>
                        </p>
                    </article>
                <?php endforeach; ?>
            </div>

        <?php else: ?>
            <div class="ptc-empty-state">
                <p style="font-size:2.5rem;margin:0;">🔍</p>
                <?php if ($query && trim($query) !== ''): ?>
                    <p><strong>Keine Ergebnisse für „<?php echo htmlspecialchars($query, ENT_QUOTES, 'UTF-8'); ?>" gefunden.</strong></p>
                    <p class="ptc-text-muted">Versuche andere Suchbegriffe oder weniger Wörter.</p>
                <?php else: ?>
                    <p><strong>Gib einen Suchbegriff ein, um loszulegen.</strong></p>
                <?php endif; ?>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/"
                   class="btn-ptc btn-ptc-ghost" style="margin-top:1rem;">
                    ← Zur Startseite
                </a>
            </div>
        <?php endif; ?>

    </div>
</section>
