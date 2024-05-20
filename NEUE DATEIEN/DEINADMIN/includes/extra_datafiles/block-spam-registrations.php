<?php
/**
 * Block Spam Registrations for Zen Cart 1.5.7h German
 * based on ZX Antispam and Email Verification by ZenExpert - https://zenexpert.com
 * Zen Cart German Specific 
 * @copyright Copyright 2003-2024 Zen Cart Development Team
 * Zen Cart German Version - www.zen-cart-pro.at 
 * @license https://www.zen-cart-pro.at/license/3_0.txt GNU General Public License V3.0
 * @version $Id: block-spam-registrations.php 2024-05-16 11:51:16Z webchills $
 */
if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}
define('TABLE_CUSTOMERS_SPAM', DB_PREFIX . 'customers_spam');
define('FILENAME_CUSTOMERS_SPAM', 'customers_spam');