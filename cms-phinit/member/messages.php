<?php
/**
 * Legacy Member-Route Redirect – CMS Phinit Theme
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

require_once ABSPATH . 'member/includes/bootstrap.php';

header('Location: ' . SITE_URL . '/member/dashboard', true, 302);
exit;
