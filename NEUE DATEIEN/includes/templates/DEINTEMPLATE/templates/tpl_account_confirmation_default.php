<?php
/**
 * Block Spam Registrations for Zen Cart 1.5.7h German
 * based on ZX Antispam and Email Verification by ZenExpert - https://zenexpert.com
 * @copyright Copyright 2003-2024 Zen Cart Development Team
 * Zen Cart German Version - www.zen-cart-pro.at
 * @license https://www.zen-cart-pro.at/license/3_0.txt GNU General Public License V3.0
 * @version $Id: tpl_account_confirmation_default.php 2024-05-16 19:55:39Z webchills $
 */
?>

<div class="centerColumn group" id="accountDefault">
<h1 id="accountDefaultHeading"><?php echo HEADING_TITLE; ?></h1>
<?php if ($messageStack->size('account_confirmation') > 0) echo $messageStack->output('account_confirmation'); ?>
<?php echo ACCOUNT_CONFIRMATION_TEXT; ?>
</div>