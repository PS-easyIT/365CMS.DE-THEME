<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/** @var array<string, mixed> $page */
/** @var array<string, mixed> $imageArchive */

$imageArchive = isset($imageArchive) && is_array($imageArchive) ? $imageArchive : phinit_build_image_archive_view_model($page ?? []);
$pageTitle = trim((string) ($page['title'] ?? 'Bilder-Archiv'));
$pageContent = trim((string) ($page['content'] ?? ''));
$pageDescription = $pageContent !== '' ? phinit_prepare_renderable_content($pageContent, 'page', (int) ($page['id'] ?? 0)) : '';
$stats = is_array($imageArchive['stats'] ?? null) ? $imageArchive['stats'] : [];
$groups = is_array($imageArchive['groups'] ?? null) ? $imageArchive['groups'] : [];
$emptyMessage = trim((string) ($imageArchive['empty_message'] ?? ''));
?>
<section class="phinit-image-archive" aria-labelledby="image-archive-title">
    <header class="phinit-image-archive__hero">
        <div class="phinit-image-archive__hero-inner">
            <p class="phinit-image-archive__eyebrow">365CMS Medienarchiv</p>
            <h1 id="image-archive-title"><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
            <p class="phinit-image-archive__lead">Alle lokal verknüpften Artikelbilder und Cover aus veröffentlichten Beiträgen – sauber nach echten Medienkategorien sortiert. Quasi der Türsteher für valide Bilder. 🖼️</p>
            <?php if ($pageDescription !== ''): ?>
                <div class="phinit-image-archive__description">
                    <?php echo $pageDescription; ?>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <div class="phinit-image-archive__stats" aria-label="Archivstatistik">
        <article class="phinit-image-archive__stat-card">
            <span class="phinit-image-archive__stat-number"><?php echo (int) ($stats['images'] ?? 0); ?></span>
            <span class="phinit-image-archive__stat-label">eindeutige Bilder</span>
        </article>
        <article class="phinit-image-archive__stat-card">
            <span class="phinit-image-archive__stat-number"><?php echo (int) ($stats['cover_images'] ?? 0); ?></span>
            <span class="phinit-image-archive__stat-label">als Cover genutzt</span>
        </article>
        <article class="phinit-image-archive__stat-card">
            <span class="phinit-image-archive__stat-number"><?php echo (int) ($stats['content_images'] ?? 0); ?></span>
            <span class="phinit-image-archive__stat-label">im Artikelinhalt genutzt</span>
        </article>
        <article class="phinit-image-archive__stat-card">
            <span class="phinit-image-archive__stat-number"><?php echo (int) ($stats['categories'] ?? 0); ?></span>
            <span class="phinit-image-archive__stat-label">Medienkategorien</span>
        </article>
        <article class="phinit-image-archive__stat-card">
            <span class="phinit-image-archive__stat-number"><?php echo (int) ($stats['articles'] ?? 0); ?></span>
            <span class="phinit-image-archive__stat-label">verknüpfte Artikel</span>
        </article>
    </div>

    <?php if ($groups !== []): ?>
        <nav class="phinit-image-archive__category-nav" aria-label="Medienkategorien im Bildarchiv">
            <ul>
                <?php foreach ($groups as $group): ?>
                    <?php if (!is_array($group)): continue; endif; ?>
                    <li>
                        <a href="#archive-<?php echo htmlspecialchars((string) ($group['slug'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars((string) ($group['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                            <span><?php echo (int) ($group['item_count'] ?? 0); ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    <?php endif; ?>

    <?php if ($groups === []): ?>
        <div class="phinit-image-archive__empty">
            <h2>Keine zugeordneten Bilder gefunden</h2>
            <p><?php echo htmlspecialchars($emptyMessage !== '' ? $emptyMessage : 'Sobald Artikel Cover oder Inhaltsbilder sauber aus dem Medienbereich referenzieren, erscheinen sie hier automatisch.', ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
    <?php else: ?>
        <div class="phinit-image-archive__groups">
            <?php foreach ($groups as $group): ?>
                <?php if (!is_array($group) || !is_array($group['items'] ?? null)): continue; endif; ?>
                <section class="phinit-image-archive__group" id="archive-<?php echo htmlspecialchars((string) ($group['slug'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" aria-labelledby="archive-heading-<?php echo htmlspecialchars((string) ($group['slug'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                    <header class="phinit-image-archive__group-header">
                        <div>
                            <p class="phinit-image-archive__group-meta">Medienkategorie</p>
                            <h2 id="archive-heading-<?php echo htmlspecialchars((string) ($group['slug'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars((string) ($group['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></h2>
                        </div>
                        <span class="phinit-image-archive__group-count"><?php echo (int) ($group['item_count'] ?? count($group['items'])); ?> Bilder</span>
                    </header>

                    <div class="phinit-image-archive__grid">
                        <?php foreach ($group['items'] as $item): ?>
                            <?php if (!is_array($item)): continue; endif; ?>
                            <?php
                            $usageTypes = array_map('strval', is_array($item['usage_types'] ?? null) ? $item['usage_types'] : []);
                            $posts = is_array($item['posts'] ?? null) ? $item['posts'] : [];
                            $alt = trim((string) ($item['alt'] ?? $item['file_name'] ?? 'Archivbild'));
                            ?>
                            <article class="phinit-image-archive__card">
                                <a class="phinit-image-archive__image-link" href="<?php echo htmlspecialchars((string) ($item['download_url'] ?? $item['image_url'] ?? '#'), ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" aria-label="Bild <?php echo htmlspecialchars((string) ($item['file_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?> im neuen Tab öffnen">
                                    <img
                                        src="<?php echo htmlspecialchars((string) ($item['image_url'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                                        alt="<?php echo htmlspecialchars($alt, ENT_QUOTES, 'UTF-8'); ?>"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                </a>

                                <div class="phinit-image-archive__card-body">
                                    <div class="phinit-image-archive__card-topline">
                                        <h3><?php echo htmlspecialchars((string) ($item['file_name'] ?? 'Bilddatei'), ENT_QUOTES, 'UTF-8'); ?></h3>
                                        <span><?php echo (int) ($item['article_count'] ?? count($posts)); ?> Artikel</span>
                                    </div>

                                    <ul class="phinit-image-archive__usage-tags" aria-label="Nutzungsart">
                                        <?php foreach ($usageTypes as $usageType): ?>
                                            <li>
                                                <?php echo htmlspecialchars($usageType === 'cover' ? 'Cover' : 'Artikelinhalt', ENT_QUOTES, 'UTF-8'); ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>

                                    <?php if ($posts !== []): ?>
                                        <div class="phinit-image-archive__usage-list">
                                            <p>Verwendet in:</p>
                                            <ul>
                                                <?php foreach ($posts as $post): ?>
                                                    <?php if (!is_array($post)): continue; endif; ?>
                                                    <li>
                                                        <a href="<?php echo htmlspecialchars((string) ($post['url'] ?? '#'), ENT_QUOTES, 'UTF-8'); ?>">
                                                            <?php echo htmlspecialchars((string) ($post['title'] ?? 'Artikel'), ENT_QUOTES, 'UTF-8'); ?>
                                                        </a>
                                                        <?php if (trim((string) ($post['category_name'] ?? '')) !== ''): ?>
                                                            <span><?php echo htmlspecialchars((string) $post['category_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                                        <?php endif; ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
