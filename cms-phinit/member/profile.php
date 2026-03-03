<?php
/**
 * Member Profil – CMS Phinit Theme
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$auth = \CMS\Auth::instance();
if (!$auth->isLoggedIn()) {
    header('Location: ' . SITE_URL . '/login');
    exit;
}

$currentUser = $auth->getCurrentUser();
$db          = \CMS\Database::instance();
$prefix      = $db->getPrefix();
$siteUrl     = SITE_URL;
$activePage  = 'profile';

$success = '';
$error   = '';

// CSRF
$csrfToken = \CMS\Security::instance()->generateToken('member_profile');

// POST-Verarbeitung
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!\CMS\Security::instance()->verifyToken($_POST['csrf_token'] ?? '', 'member_profile')) {
        $error = 'Sicherheitscheck fehlgeschlagen.';
    } else {
        $username  = sanitize_text_field($_POST['username'] ?? '');
        $email     = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $firstName = sanitize_text_field($_POST['first_name'] ?? '');
        $lastName  = sanitize_text_field($_POST['last_name'] ?? '');
        $bio       = strip_tags($_POST['bio'] ?? '', '<p><a><strong><em><br>');
        $website   = filter_var($_POST['website'] ?? '', FILTER_VALIDATE_URL) ?: '';

        if (empty($username)) {
            $error = 'Benutzername darf nicht leer sein.';
        } elseif (!$email) {
            $error = 'Ungültige E-Mail-Adresse.';
        } else {
            try {
                $db->execute(
                    "UPDATE {$prefix}users SET username = ?, email = ? WHERE id = ?",
                    [$username, $email, (int)$currentUser->id]
                );
                // User-Meta aktualisieren
                foreach (['first_name' => $firstName, 'last_name' => $lastName, 'bio' => $bio, 'website' => $website] as $metaKey => $metaValue) {
                    $exists = $db->get_var(
                        "SELECT COUNT(*) FROM {$prefix}user_meta WHERE user_id = ? AND meta_key = ?",
                        [(int)$currentUser->id, $metaKey]
                    );
                    if ($exists) {
                        $db->execute(
                            "UPDATE {$prefix}user_meta SET meta_value = ? WHERE user_id = ? AND meta_key = ?",
                            [$metaValue, (int)$currentUser->id, $metaKey]
                        );
                    } else {
                        $db->execute(
                            "INSERT INTO {$prefix}user_meta (user_id, meta_key, meta_value) VALUES (?, ?, ?)",
                            [(int)$currentUser->id, $metaKey, $metaValue]
                        );
                    }
                }
                $success = 'Profil erfolgreich aktualisiert!';
                // Daten neu laden
                $currentUser = $auth->getCurrentUser();
                $csrfToken   = \CMS\Security::instance()->generateToken('member_profile');
            } catch (\Throwable $e) {
                $error = 'Fehler beim Speichern: ' . $e->getMessage();
            }
        }
    }
}

// Meta-Daten laden
$userMeta = [];
$metaRows = $db->get_results(
    "SELECT meta_key, meta_value FROM {$prefix}user_meta WHERE user_id = ?",
    [(int)$currentUser->id]
) ?: [];
foreach ($metaRows as $row) {
    $userMeta[$row['meta_key']] = $row['meta_value'];
}

$themeDir = \CMS\ThemeManager::instance()->getThemePath();
include $themeDir . 'header.php';
?>

<div class="container member-container">
    <?php include __DIR__ . '/partials/member-nav.php'; ?>

    <div class="member-main">

        <div class="member-page-title" data-anim>
            <h1>👤 Mein Profil</h1>
            <p>Verwalte deine persönlichen Informationen und Einstellungen.</p>
        </div>

        <?php if ($success): ?>
        <div class="member-alert member-alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div class="member-alert member-alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">

            <div class="member-grid-2" data-anim data-anim-delay="1">

                <!-- Persönliche Daten -->
                <div class="member-card">
                    <div class="member-card-header"><h3>🏷️ Persönliche Daten</h3></div>
                    <div class="member-form-group">
                        <label class="member-label" for="username">Benutzername <span class="req">*</span></label>
                        <input type="text" id="username" name="username" class="member-input"
                               value="<?php echo htmlspecialchars($currentUser->username ?? '', ENT_QUOTES); ?>" required>
                    </div>
                    <div class="member-form-group">
                        <label class="member-label" for="email">E-Mail <span class="req">*</span></label>
                        <input type="email" id="email" name="email" class="member-input"
                               value="<?php echo htmlspecialchars($currentUser->email ?? '', ENT_QUOTES); ?>" required>
                    </div>
                    <div class="member-form-row">
                        <div class="member-form-group">
                            <label class="member-label" for="first_name">Vorname</label>
                            <input type="text" id="first_name" name="first_name" class="member-input"
                                   value="<?php echo htmlspecialchars($userMeta['first_name'] ?? '', ENT_QUOTES); ?>">
                        </div>
                        <div class="member-form-group">
                            <label class="member-label" for="last_name">Nachname</label>
                            <input type="text" id="last_name" name="last_name" class="member-input"
                                   value="<?php echo htmlspecialchars($userMeta['last_name'] ?? '', ENT_QUOTES); ?>">
                        </div>
                    </div>
                </div>

                <!-- Erweitert -->
                <div class="member-card">
                    <div class="member-card-header"><h3>🌐 Erweitert</h3></div>
                    <div class="member-form-group">
                        <label class="member-label" for="website">Website</label>
                        <input type="url" id="website" name="website" class="member-input" placeholder="https://"
                               value="<?php echo htmlspecialchars($userMeta['website'] ?? '', ENT_QUOTES); ?>">
                    </div>
                    <div class="member-form-group">
                        <label class="member-label" for="bio">Über mich</label>
                        <textarea id="bio" name="bio" class="member-input member-textarea"
                                  rows="5" placeholder="Kurze Beschreibung …"><?php echo htmlspecialchars($userMeta['bio'] ?? '', ENT_QUOTES); ?></textarea>
                    </div>
                    <div class="member-form-info">
                        <p>📅 Mitglied seit: <strong><?php echo date('d.m.Y', strtotime($currentUser->created_at ?? 'now')); ?></strong></p>
                        <p>🔑 Rolle: <strong><?php echo htmlspecialchars(ucfirst($currentUser->role ?? 'member')); ?></strong></p>
                    </div>
                </div>

            </div>

            <div class="member-actions" data-anim data-anim-delay="2">
                <button type="submit" class="btn btn-primary">💾 Profil speichern</button>
            </div>

        </form>

    </div><!-- /.member-main -->
</div><!-- /.member-container -->

<?php include $themeDir . 'footer.php'; ?>
