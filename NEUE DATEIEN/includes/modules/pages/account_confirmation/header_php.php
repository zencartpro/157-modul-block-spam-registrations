<?php
/**
 * Block Spam Registrations for Zen Cart 1.5.7h German
 * based on ZX Antispam and Email Verification by ZenExpert - https://zenexpert.com
 * @copyright Copyright 2003-2024 Zen Cart Development Team
 * Zen Cart German Version - www.zen-cart-pro.at
 * @license https://www.zen-cart-pro.at/license/3_0.txt GNU General Public License V3.0
 * @version $Id: header_php.php 2024-05-16 19:55:39Z webchills $
 */

require(DIR_WS_MODULES . zen_get_module_directory('require_languages.php'));

$breadcrumb->add(NAVBAR_TITLE);

// housekeeping
$db->Execute("DELETE FROM " . TABLE_CUSTOMERS_SPAM ." WHERE date_created < DATE_SUB(NOW(),INTERVAL ".(int)BLOCKSPAMREGISTRATIONS_INTERVAL." HOUR)");

// slamming protection
$slamming_threshold = 5;
if (!isset($_SESSION['account_confirmation_attempt'])) $_SESSION['account_confirmation_attempt'] = 0;
$_SESSION['account_confirmation_attempt']++;
if ($_SESSION['account_confirmation_attempt'] > $slamming_threshold) {
    zen_session_destroy();
    zen_redirect(zen_href_link(FILENAME_TIME_OUT));
}