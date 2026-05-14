<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

require_once ABSPATH . 'member/includes/bootstrap.php';

use CMS\Database;
use CMS\Hooks;
use CMS\Services\EditorService;
use CMS\Services\MemberService;
use CMS\ThemeManager;

$currentUser = $controller->getCurrentUser();
$userId = $controller->getUserId();
$siteUrl = SITE_URL;
$activePage = 'posts';
$themeDir = ThemeManager::instance()->getThemePath();
$db = Database::instance();
$prefix = $db->getPrefix();
$memberService = MemberService::getInstance();
$permissions = $memberService->getUserPermissions($userId);
$canPost = !empty($permissions['can_post']);
$permalinkService = \CMS\Services\PermalinkService::getInstance();

Hooks::addAction('head', static function (): void {
    EditorService::getInstance()->enqueueEditorAssets();
}, 5);

$normalizeSlug = static function (string $slug): string {
    $slug = mb_strtolower(trim($slug), 'UTF-8');
    $slug = preg_replace('/[^a-z0-9\-]+/u', '-', $slug) ?? $slug;
    $slug = preg_replace('/-+/', '-', $slug) ?? $slug;

    return trim((string) $slug, '-');
};

$sendNotification = static function (int $targetUserId, string $title, string $message, ?string $url = null) use ($db, $prefix): void {
    if ($targetUserId <= 0) {
        return;
    }

    try {
        $db->execute(
            "INSERT INTO {$prefix}notifications (user_id, type, title, message, url, is_read) VALUES (?, 'system', ?, ?, ?, 0)",
            [$targetUserId, $title, $message, $url]
        );
    } catch (\Throwable) {
    }
};

if ($_SERVER['REQUEST_METHOD'] === 'POST' && phinit_input_string($_POST, 'action', '', 60) === 'submit_member_post') {
    if (!$canPost) {
        $controller->flash('danger', 'Dein aktuelles Konto darf derzeit keine Artikel einreichen.');
        $controller->redirect('/member/posts');
    }

    if (!$controller->verifyCsrf('article_submit')) {
        $controller->flash('danger', 'Sicherheitsüberprüfung fehlgeschlagen. Bitte versuche es erneut.');
        $controller->redirect('/member/posts');
    }

    $postId = phinit_input_int($_POST, 'post_id', 0, 0);
    $title = mb_substr(trim(strip_tags(phinit_input_string($_POST, 'title', '', 255))), 0, 255);
    $slug = $normalizeSlug(phinit_input_string($_POST, 'slug', '', 255));
    $excerpt = phinit_input_string($_POST, 'excerpt', '', 1000);
    $featuredImage = phinit_input_string($_POST, 'featured_image', '', 1024);
    $tags = phinit_input_string($_POST, 'tags', '', 500);
    $content = EditorService::getInstance()->sanitize(phinit_input_string($_POST, 'content', '', 200000));
    $decodedContent = json_decode($content, true);
    $categoryId = phinit_input_int($_POST, 'category_id', 0, 0);

    if ($title === '') {
        $controller->flash('danger', 'Bitte gib einen Titel für deinen Artikel an.');
        $controller->redirect('/member/posts' . ($postId > 0 ? '?edit=' . $postId : ''));
    }

    if (
        trim($content) === ''
        || (
            is_array($decodedContent)
            && isset($decodedContent['blocks'])
            && is_array($decodedContent['blocks'])
            && $decodedContent['blocks'] === []
        )
    ) {
        $controller->flash('danger', 'Bitte ergänze den eigentlichen Artikelinhalt.');
        $controller->redirect('/member/posts' . ($postId > 0 ? '?edit=' . $postId : ''));
    }

    if ($excerpt === '') {
        $excerpt = function_exists('phinit_excerpt_plain_text')
            ? phinit_excerpt_plain_text($content)
            : trim(strip_tags($content));
        $excerpt = mb_strimwidth($excerpt, 0, 220, '…');
    }

    $featuredImage = filter_var($featuredImage, FILTER_VALIDATE_URL) ? $featuredImage : '';

    $allowedCategoryId = 0;
    if ($categoryId > 0) {
        try {
            $allowedCategoryId = (int) ($db->get_var(
                "SELECT id FROM {$prefix}post_categories WHERE id = ? LIMIT 1",
                [$categoryId]
            ) ?: 0);
        } catch (\Throwable) {
            $allowedCategoryId = 0;
        }
    }

    $editUrlSuffix = '';
    if ($postId > 0) {
        $existingOwnerId = (int) ($db->get_var(
            "SELECT author_id FROM {$prefix}posts WHERE id = ? AND status = 'draft' LIMIT 1",
            [$postId]
        ) ?: 0);

        if ($existingOwnerId !== $userId) {
            $controller->flash('danger', 'Dieser Entwurf kann nicht bearbeitet werden.');
            $controller->redirect('/member/posts');
        }

        $editUrlSuffix = '?edit=' . $postId;
    }

    require_once ABSPATH . 'admin/modules/posts/PostsModule.php';
    $postsModule = new \PostsModule();

    $result = $postsModule->save([
        'id' => $postId,
        'title' => $title,
        'slug' => $slug,
        'excerpt' => $excerpt,
        'content' => $content,
        'category_id' => $allowedCategoryId,
        'featured_image' => $featuredImage,
        'tags' => $tags,
        'status' => 'draft',
        'meta_title' => $title,
        'meta_description' => $excerpt,
    ], $userId);

    if (!empty($result['success'])) {
        $savedPostId = (int) ($result['id'] ?? 0);
        $adminUsers = [];
        try {
            $adminUsers = $db->get_results(
                "SELECT id FROM {$prefix}users WHERE role = 'admin' AND status = 'active'"
            ) ?: [];
        } catch (\Throwable) {
            $adminUsers = [];
        }

        $authorName = trim((string) ($currentUser->display_name ?? $currentUser->username ?? 'Mitglied'));
        $adminUrl = '/admin/posts?action=edit&id=' . $savedPostId;
        $userUrl = '/member/posts' . ($savedPostId > 0 ? '?edit=' . $savedPostId : '');

        foreach ($adminUsers as $adminUser) {
            $adminId = (int) ($adminUser->id ?? 0);
            $sendNotification(
                $adminId,
                'Neuer Artikel wartet auf Freigabe',
                $authorName . ' hat den Artikel „' . $title . '“ zur Prüfung eingereicht.',
                $adminUrl
            );
        }

        $sendNotification(
            $userId,
            'Artikel eingereicht',
            'Dein Artikel „' . $title . '“ wurde gespeichert und wartet nun auf die Freigabe durch einen Admin.',
            $userUrl
        );

        $controller->flash('success', $postId > 0
            ? 'Dein Entwurf wurde aktualisiert und bleibt zur Freigabe eingereicht.'
            : 'Dein Artikel wurde eingereicht und wartet jetzt auf die Freigabe durch einen Admin.');
        $controller->redirect('/member/posts');
    }

    $controller->flash('danger', (string) ($result['error'] ?? 'Der Artikel konnte nicht gespeichert werden.'));
    $controller->redirect('/member/posts' . $editUrlSuffix);
}

$flash = $controller->consumeFlash();

$categories = [];
try {
    $categories = $db->get_results(
        "SELECT id, name FROM {$prefix}post_categories ORDER BY name ASC"
    ) ?: [];
} catch (\Throwable) {
    $categories = [];
}

$editId = phinit_input_int($_GET, 'edit', 0, 0);
$editablePost = null;
if ($editId > 0) {
    try {
        $editablePost = $db->get_row(
            "SELECT * FROM {$prefix}posts WHERE id = ? AND author_id = ? AND status = 'draft' LIMIT 1",
            [$editId, $userId]
        );
    } catch (\Throwable) {
        $editablePost = null;
    }
}

$formValues = [
    'id' => (int) ($editablePost->id ?? 0),
    'title' => (string) ($editablePost->title ?? ''),
    'slug' => (string) ($editablePost->slug ?? ''),
    'category_id' => (int) ($editablePost->category_id ?? 0),
    'tags' => '',
    'featured_image' => (string) ($editablePost->featured_image ?? ''),
    'excerpt' => (string) ($editablePost->excerpt ?? ''),
    'content' => (string) ($editablePost->content ?? ''),
];

if (!empty($formValues['id'])) {
    try {
        $tagRows = $db->get_results(
            "SELECT t.name
             FROM {$prefix}post_tags t
             INNER JOIN {$prefix}post_tag_rel rel ON rel.tag_id = t.id
             WHERE rel.post_id = ?
             ORDER BY t.name ASC",
            [$formValues['id']]
        ) ?: [];
        $formValues['tags'] = implode(', ', array_map(static fn (object $row): string => (string) ($row->name ?? ''), $tagRows));
    } catch (\Throwable) {
        $formValues['tags'] = '';
    }
}

$postRows = [];
try {
    $postRows = $db->get_results(
        "SELECT p.id, p.title, p.slug, p.status, p.updated_at, p.published_at, p.created_at, c.name AS category_name
         FROM {$prefix}posts p
         LEFT JOIN {$prefix}post_categories c ON c.id = p.category_id
         WHERE p.author_id = ?
         ORDER BY p.updated_at DESC
         LIMIT 50",
        [$userId]
    ) ?: [];
} catch (\Throwable) {
    $postRows = [];
}

$publishedCount = 0;
$draftCount = 0;
foreach ($postRows as $row) {
    if ((string) ($row->status ?? '') === 'published') {
        $publishedCount++;
        continue;
    }
    $draftCount++;
}

include $themeDir . 'header.php';
?>

<div class="container member-container">
    <?php include __DIR__ . '/partials/member-nav.php'; ?>

    <div class="member-main">
        <div class="member-page-title" data-anim>
            <h1>✍️ Artikel einreichen</h1>
            <p>Schreibe neue Beiträge direkt im Member-Dashboard mit Editor.js. Deine Einreichung wird als Entwurf gespeichert und kann anschließend vom Admin geprüft und freigeschaltet werden.</p>
        </div>

        <?php echo phinit_render_member_flash($flash); ?>

        <?php if (!$canPost): ?>
        <div class="member-alert member-alert-warning">
            <p class="member-alert__message">Dein aktuelles Konto darf momentan keine neuen Artikel einreichen. Wende dich an einen Admin, wenn du Content-Creator-Rechte benötigst.</p>
        </div>
        <?php endif; ?>

        <div class="member-stats member-stats--compact" data-anim data-anim-delay=".5">
            <div class="member-stat-card">
                <div class="member-stat-icon">📝</div>
                <div class="member-stat-value"><?php echo count($postRows); ?></div>
                <div class="member-stat-label">Eigene Beiträge</div>
            </div>
            <div class="member-stat-card">
                <div class="member-stat-icon">🕓</div>
                <div class="member-stat-value"><?php echo $draftCount; ?></div>
                <div class="member-stat-label">In Prüfung</div>
            </div>
            <div class="member-stat-card">
                <div class="member-stat-icon">✅</div>
                <div class="member-stat-value"><?php echo $publishedCount; ?></div>
                <div class="member-stat-label">Veröffentlicht</div>
            </div>
        </div>

        <div class="member-grid-2 member-grid-2--posts" data-anim data-anim-delay="1">
            <div class="member-card member-post-composer">
                <div class="member-card-header">
                    <h3><?php echo !empty($formValues['id']) ? '🛠️ Entwurf bearbeiten' : '🆕 Neuen Artikel schreiben'; ?></h3>
                    <?php if (!empty($formValues['id'])): ?>
                    <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/member/posts" class="member-card-link">Neu starten →</a>
                    <?php endif; ?>
                </div>

                <?php if ($canPost): ?>
                <form method="post">
                    <input type="hidden" name="action" value="submit_member_post">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($controller->csrfToken('article_submit'), ENT_QUOTES); ?>">
                    <input type="hidden" name="post_id" value="<?php echo (int) ($formValues['id'] ?? 0); ?>">

                    <div class="member-form-group">
                        <label class="member-label" for="member-post-title">Titel <span class="req">*</span></label>
                        <input type="text" id="member-post-title" name="title" class="member-input" required maxlength="255"
                               value="<?php echo htmlspecialchars((string) ($formValues['title'] ?? ''), ENT_QUOTES); ?>"
                               placeholder="Worum geht es in deinem Artikel?">
                    </div>

                    <div class="member-form-row">
                        <div class="member-form-group">
                            <label class="member-label" for="member-post-slug">Slug (optional)</label>
                            <input type="text" id="member-post-slug" name="slug" class="member-input"
                                   value="<?php echo htmlspecialchars((string) ($formValues['slug'] ?? ''), ENT_QUOTES); ?>"
                                   placeholder="wird-aus-dem-titel-generiert">
                        </div>
                        <div class="member-form-group">
                            <label class="member-label" for="member-post-category">Kategorie</label>
                            <select id="member-post-category" name="category_id" class="member-input">
                                <option value="0">Keine Kategorie</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo (int) ($category->id ?? 0); ?>" <?php echo (int) ($formValues['category_id'] ?? 0) === (int) ($category->id ?? 0) ? 'selected' : ''; ?>><?php echo htmlspecialchars((string) ($category->name ?? ''), ENT_QUOTES); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="member-form-row">
                        <div class="member-form-group">
                            <label class="member-label" for="member-post-tags">Tags</label>
                            <input type="text" id="member-post-tags" name="tags" class="member-input"
                                   value="<?php echo htmlspecialchars((string) ($formValues['tags'] ?? ''), ENT_QUOTES); ?>"
                                   placeholder="z. B. KI, Web, CMS">
                        </div>
                        <div class="member-form-group">
                            <label class="member-label" for="member-post-image">Beitragsbild (URL)</label>
                            <input type="url" id="member-post-image" name="featured_image" class="member-input"
                                   value="<?php echo htmlspecialchars((string) ($formValues['featured_image'] ?? ''), ENT_QUOTES); ?>"
                                   placeholder="https://...">
                        </div>
                    </div>

                    <div class="member-form-group">
                        <label class="member-label" for="member-post-excerpt">Kurzbeschreibung</label>
                        <textarea id="member-post-excerpt" name="excerpt" class="member-input member-textarea" rows="4" placeholder="Optionaler Teasertext für Listen und Vorschauen"><?php echo htmlspecialchars((string) ($formValues['excerpt'] ?? ''), ENT_QUOTES); ?></textarea>
                    </div>

                    <div class="member-form-group">
                        <label class="member-label">Inhalt <span class="req">*</span></label>
                        <?php echo EditorService::getInstance()->render('content', (string) ($formValues['content'] ?? ''), [
                            'height' => 460,
                            'context' => 'member-post-submit',
                            'content_width' => 860,
                            'content_width_expanded' => 1060,
                            'content_padding_x' => 28,
                        ]); ?>
                    </div>

                    <div class="member-form-info member-form-info--post-submit">
                        <p>📨 Neue Beiträge werden als <strong>Entwurf</strong> gespeichert und warten anschließend auf die Freigabe durch einen Admin.</p>
                        <p>🧼 Inhalte werden beim Speichern automatisch über die vorhandene Editor.js-Sanitizing-Logik geprüft.</p>
                    </div>

                    <div class="member-actions member-actions--row member-actions--compact">
                        <button type="submit" class="btn btn-primary"><?php echo !empty($formValues['id']) ? '💾 Entwurf aktualisieren' : '📤 Artikel einreichen'; ?></button>
                        <?php if (!empty($formValues['id'])): ?>
                        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/member/posts" class="btn btn-outline">Abbrechen</a>
                        <?php endif; ?>
                    </div>
                </form>
                <?php endif; ?>
            </div>

            <div class="member-card member-post-submissions">
                <div class="member-card-header">
                    <h3>📚 Meine Einreichungen</h3>
                    <span class="member-card-link"><?php echo (int) count($postRows); ?> Einträge</span>
                </div>

                <?php if (!empty($postRows)): ?>
                <div class="member-post-list">
                    <?php foreach ($postRows as $row): ?>
                    <?php
                        $rowId = (int) ($row->id ?? 0);
                        $rowStatus = (string) ($row->status ?? 'draft');
                        $isPublished = $rowStatus === 'published';
                        $statusLabel = $isPublished ? 'Veröffentlicht' : 'In Prüfung';
                        $statusClass = $isPublished ? 'member-post-status--published' : 'member-post-status--draft';
                        $postUrl = $siteUrl . $permalinkService->buildPostPath((array) $row);
                        if (function_exists('phinit_safe_public_url')) {
                            $postUrl = phinit_safe_public_url($postUrl, $siteUrl, ['http', 'https']) ?: '#';
                        }
                        $editUrl = $siteUrl . '/member/posts?edit=' . $rowId;
                        $dateValue = (string) ($row->updated_at ?? $row->created_at ?? '');
                        $dateTimestamp = strtotime($dateValue);
                    ?>
                    <article class="member-post-item">
                        <div class="member-post-item__main">
                            <div class="member-post-item__top">
                                <h4><?php echo htmlspecialchars((string) ($row->title ?? 'Unbenannter Beitrag'), ENT_QUOTES); ?></h4>
                                <span class="member-post-status <?php echo htmlspecialchars($statusClass, ENT_QUOTES); ?>"><?php echo htmlspecialchars($statusLabel, ENT_QUOTES); ?></span>
                            </div>
                            <div class="member-post-meta">
                                <span>Zuletzt geändert: <?php echo htmlspecialchars($dateTimestamp !== false ? date('d.m.Y H:i', $dateTimestamp) : '—', ENT_QUOTES); ?></span>
                                <?php if (!empty($row->category_name)): ?>
                                <span>Kategorie: <?php echo htmlspecialchars((string) $row->category_name, ENT_QUOTES); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="member-post-actions">
                            <?php if ($isPublished): ?>
                            <a href="<?php echo htmlspecialchars($postUrl, ENT_QUOTES); ?>" class="btn btn-outline btn-sm">Öffnen</a>
                            <?php else: ?>
                            <a href="<?php echo htmlspecialchars($editUrl, ENT_QUOTES); ?>" class="btn btn-outline btn-sm">Bearbeiten</a>
                            <?php endif; ?>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="member-empty-state member-empty-state--compact">
                    <div class="member-empty-state__icon member-empty-state__icon--compact">✍️</div>
                    <strong>Noch keine Artikel eingereicht</strong>
                    <p>Starte mit deinem ersten Beitrag direkt hier im Member-Dashboard.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include $themeDir . 'footer.php';
