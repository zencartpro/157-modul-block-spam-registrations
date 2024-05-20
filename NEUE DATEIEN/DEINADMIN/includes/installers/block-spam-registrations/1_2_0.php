<?php
/**
 * Block Spam Registrations for Zen Cart 1.5.7h German
 * based on ZX Antispam and Email Verification by ZenExpert - https://zenexpert.com
 * @copyright Copyright 2003-2024 Zen Cart Development Team
 * Zen Cart German Version - www.zen-cart-pro.at
 * @license https://www.zen-cart-pro.at/license/3_0.txt GNU General Public License V3.0
 * @version $Id: 1_2_0.php 2024-05-16 14:55:39Z webchills $
 */
 
$db->Execute(" SELECT @gid:=configuration_group_id
FROM ".TABLE_CONFIGURATION_GROUP."
WHERE configuration_group_title= 'Block Spam Registrations'
LIMIT 1;");

$db->Execute("INSERT IGNORE INTO ".TABLE_CONFIGURATION." (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function)  VALUES 
('Block Spam Registrations - Enabled', 'BLOCKSPAMREGISTRATIONS_ENABLED', 'false', 'Enable Block Spam Registrations Plugin',  @gid, '1', NULL, NOW(), NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),'),
('Block Spam Registrations - Housekeeping', 'BLOCKSPAMREGISTRATIONS_INTERVAL', '240',  'Number of <b>hours</b> before spam customers in the table customers_spam get automatically deleted<br>Default:240 (= 10 days)', @gid, '2', NULL, NOW(), NULL, NULL),
('Block Spam Registrations - Check First Name', 'BLOCKSPAMREGISTRATIONS_FIRSTNAME', '3', '<b>Check first name for multiple capital letters</b><br>Set the number of capital letters that trigger the spam flag. Set to 0 to disable.<br>Default: 3', @gid, '3', NULL, NOW(), NULL, 'zen_cfg_select_option(array(\'0\', \'1\', \'2\', \'3\', \'4\', \'5\'),'),
('Block Spam Registrations - Check Last Name', 'BLOCKSPAMREGISTRATIONS_LASTNAME', '3', '<b>Check last name for multiple capital letters</b><br>Set the number of capital letters that trigger the spam flag. Set to 0 to disable.<br>Default: 3', @gid, '4', NULL, NOW(), NULL, 'zen_cfg_select_option(array(\'0\', \'1\', \'2\', \'3\', \'4\', \'5\'),'),
('Block Spam Registrations - Check City', 'BLOCKSPAMREGISTRATIONS_CITY', '3', '<b>Check city for multiple capital letters</b><br>Set the number of capital letters that trigger the spam flag. Set to 0 to disable.<br>Default: 3', @gid, '5',  NULL, NOW(), NULL, 'zen_cfg_select_option(array(\'0\', \'1\', \'2\', \'3\', \'4\', \'5\'),'),
('Block Spam Registrations - Check Suburb', 'BLOCKSPAMREGISTRATIONS_SUBURB', '5', '<b>Check suburb (Address Line 2) for multiple capital letters</b><br>Set the number of capital letters that trigger the spam flag. Set to 0 to disable.<br>Default: 5', @gid, '6',  NULL, NOW(), NULL,  'zen_cfg_select_option(array(\'0\', \'1\', \'2\', \'3\', \'4\', \'5\'),')");
	
$db->Execute("REPLACE INTO " . TABLE_CONFIGURATION_LANGUAGE . " (configuration_title, configuration_key, configuration_language_id, configuration_description) VALUES
('Spam Registrierungen blocken - Version', 'BLOCKSPAMREGISTRATIONS_VERSION', 43, 'Aktuell installierte Version dieses Plugins'),
('Spam Registrierungen blocken - Aktivieren?', 'BLOCKSPAMREGISTRATIONS_ENABLED', 43, 'Wollen Sie das Plugin aktivieren?'),
('Spam Registrierungen blocken - Automatisch löschen nach gewissem Zeitraum', 'BLOCKSPAMREGISTRATIONS_INTERVAL', 43, 'Anzahl der <b>Stunden</b>, nach denen in der Tabelle customers_spam erfasste Spamkunden automatisch gelöscht werden sollen<br>Voreinstellung: 240 (= 10 Tage)'),
('Spam Registrierungen blocken - Vorname prüfen', 'BLOCKSPAMREGISTRATIONS_FIRSTNAME', 43, '<b>Vorname auf mehrfache Grossbuchstaben prüfen</b><br>Stellen Sie hier die Anzahl der Grossbuchstaben ein, die die Spambewertung auslösen sollen.<br>Auf 0 stellen, um diese Prüfung zu deaktivieren.<br>Voreinstellung: 3'),
('Spam Registrierungen blocken - Nachname prüfen', 'BLOCKSPAMREGISTRATIONS_LASTNAME', 43, '<b>Nachname auf mehrfache Grossbuchstaben prüfen</b><br>Stellen Sie hier die Anzahl der Grossbuchstaben ein, die die Spambewertung auslösen sollen.<br>Auf 0 stellen, um diese Prüfung zu deaktivieren.<br>Voreinstellung: 3'),   
('Spam Registrierungen blocken - Ort prüfen', 'BLOCKSPAMREGISTRATIONS_CITY', 43, '<b>Ort auf mehrfache Grossbuchstaben prüfen</b><br>Stellen Sie hier die Anzahl der Grossbuchstaben ein, die die Spambewertung auslösen sollen.<br>Auf 0 stellen, um diese Prüfung zu deaktivieren.<br>Voreinstellung: 3'),
('Spam Registrierungen blocken - Adresszeile 2 prüfen', 'BLOCKSPAMREGISTRATIONS_SUBURB', 43, '<b>Adresszeile 2 auf mehrfache Grossbuchstaben prüfen</b><br>Stellen Sie hier die Anzahl der Grossbuchstaben ein, die die Spambewertung auslösen sollen.<br>Auf 0 stellen, um diese Prüfung zu deaktivieren.<br>Voreinstellung: 5')");


// create new customers_spam table
$db->Execute("CREATE TABLE IF NOT EXISTS " . TABLE_CUSTOMERS_SPAM . " (
  `customers_spam_id` int(11) NOT NULL AUTO_INCREMENT,
  `customers_firstname` varchar(64) NOT NULL,
  `customers_lastname` varchar(64) NOT NULL,
  `customers_email_address` varchar(96) NOT NULL,
  `customers_nick` varchar(96) NOT NULL,
  `customers_telephone` varchar(32) NOT NULL,
  `customers_fax` varchar(32) DEFAULT NULL,
  `customers_newsletter` char(1) DEFAULT NULL,
  `customers_email_format` varchar(4) NOT NULL DEFAULT 'TEXT',
  `customers_default_address_id` int(11) NOT NULL,
  `customers_password` varchar(255) NOT NULL,
  `customers_authorization` int(11) NOT NULL DEFAULT 0,
  `customers_referral` varchar(32) NOT NULL,
  `customers_gender` char(1) NOT NULL,
  `customers_dob` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `entry_street_address` varchar(64) NOT NULL,
  `entry_postcode` varchar(64) NOT NULL,
  `entry_city` varchar(64) NOT NULL,
  `entry_country_id` int(11) NOT NULL DEFAULT 0,
  `entry_gender` char(1) NOT NULL,
  `entry_company` varchar(64) DEFAULT NULL,
  `entry_suburb` varchar(32) DEFAULT NULL,
  `entry_zone_id` int(11) NOT NULL DEFAULT 0,
  `entry_state` varchar(32) DEFAULT NULL,
  `token` varchar(64) NOT NULL,
  `token_manual` int(11) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `reason` text NOT NULL,
  `registration_ip` varchar(45) NOT NULL DEFAULT '0.0.0.0',
  PRIMARY KEY (`customers_spam_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");


  
// delete old configuration/customers menu
$admin_page = 'configBlockSpamRegistrations';
$db->Execute("DELETE FROM " . TABLE_ADMIN_PAGES . " WHERE page_key = '" . $admin_page . "' LIMIT 1;");
$admin_page_customers = 'customersBlockSpamRegistrations';
$db->Execute("DELETE FROM " . TABLE_ADMIN_PAGES . " WHERE page_key = '" . $admin_page_customers . "' LIMIT 1;");
// add configuration menu
if (!zen_page_key_exists($admin_page)) {
$db->Execute(" SELECT @gid:=configuration_group_id
FROM ".TABLE_CONFIGURATION_GROUP."
WHERE configuration_group_title= 'Block Spam Registrations'
LIMIT 1;");
$db->Execute("INSERT IGNORE INTO " . TABLE_ADMIN_PAGES . " (page_key,language_key,main_page,page_params,menu_key,display_on_menu,sort_order) VALUES 
('configBlockSpamRegistrations','BOX_CONFIGURATION_BLOCKSPAMREGISTRATIONS','FILENAME_CONFIGURATION',CONCAT('gID=',@gid),'configuration','Y',@gid)");
$db->Execute(" SELECT @gid:=configuration_group_id
FROM ".TABLE_CONFIGURATION_GROUP."
WHERE configuration_group_title= 'Block Spam Registrations'
LIMIT 1;");
$db->Execute("INSERT IGNORE INTO " . TABLE_ADMIN_PAGES . " (page_key,language_key,main_page,page_params,menu_key,display_on_menu,sort_order) VALUES 
('customersBlockSpamRegistrations','BOX_CUSTOMERS_BLOCKSPAMREGISTRATIONS','FILENAME_CUSTOMERS_SPAM','','customers','Y',111)");
$messageStack->add('Block Spam Registrations erfolgreich installiert.', 'success');  
}
