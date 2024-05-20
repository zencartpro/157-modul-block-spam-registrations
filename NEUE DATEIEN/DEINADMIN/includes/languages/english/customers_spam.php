<?php
/**
 * Block Spam Registrations for Zen Cart 1.5.7h German
 * based on ZX Antispam and Email Verification by ZenExpert - https://zenexpert.com
 * @copyright Copyright 2003-2024 Zen Cart Development Team
 * Zen Cart German Version - www.zen-cart-pro.at
 * @license https://www.zen-cart-pro.at/license/3_0.txt GNU General Public License V3.0
 * @version $Id: customers_spam.php 2024-05-16 10:40:39Z webchills $
 */
define('HEADING_TITLE', 'Spam Registrierungen');
define('TABLE_HEADING_ID', 'ID#');
define('TABLE_HEADING_FIRSTNAME', 'Vorname');
define('TABLE_HEADING_LASTNAME', 'Nachname');
define('TABLE_HEADING_EMAIL', 'Email');
define('TABLE_HEADING_COMPANY', 'Firma');
define('TABLE_HEADING_ACCOUNT_CREATED', 'registriert am');
define('TABLE_HEADING_COUNTRY', 'Land');
define('TABLE_HEADING_STATE', 'Bundesland');
define('TABLE_HEADING_IP', 'IP-Adresse');
define('TABLE_HEADING_ACTION', 'Aktion');
define('TEXT_INFO_HEADING_DELETE_CUSTOMER', 'Kunden löschen');
define('TEXT_DELETE_INTRO', 'Wollen Sie diesen Kunden wirklich löschen?');
define('IMAGE_APPROVE_ACCOUNT', 'Freischalten');
define('CUSTOMERS_AUTHORIZATION', 'Kunden - Autorisierungsstatus');
define('CUSTOMERS_AUTHORIZATION_0', 'Geprüft');
define('CUSTOMERS_AUTHORIZATION_1', 'Anstehende Autorisierung - Muss zum Browsen im Shop authorisiert sein');
define('CUSTOMERS_AUTHORIZATION_2', 'Anstehende Autorisierung - Darf im Shop browsen, aber keine Preise sehen');
define('CUSTOMERS_AUTHORIZATION_3', 'Anstehende Autorisierung - Darf im Shop browsen und Preise sehen, aber nicht kaufen');
define('CUSTOMERS_AUTHORIZATION_4', 'Gesperrt - Darf sich nicht anmelden oder einkaufen');
define('ERROR_CUSTOMER_APPROVAL_CORRECTION1', 'WARNUNG: Ihr Shop ist auf "Autorisierung ohne Browsen" eingestellt. Der Kunde wurde auf "Anstehende Authorisierung - Muss zum Browsen im Shop authorisiert sein" gesetzt');
define('ERROR_CUSTOMER_APPROVAL_CORRECTION2', 'WARNUNG: Ihr Shop ist auf "Autorisierung mit browsen ohne Preisanzeige" eingestellt. Der Kunde wurde auf "Anstehende Authorisierung - Darf im Shop browsen, aber keine Preise sehen" gesetzt');
define('CUSTOMERS_REFERRAL', 'Kundenverweis (Referal)<br>Erster Aktionskupon');
define('TEXT_CUSTOMER_GROUPS','Kundengruppen');