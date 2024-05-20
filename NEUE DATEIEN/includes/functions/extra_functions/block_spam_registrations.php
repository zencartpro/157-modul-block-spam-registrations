<?php
/**
 * Block Spam Registrations for Zen Cart 1.5.7h German
 * based on ZX Antispam and Email Verification by ZenExpert - https://zenexpert.com
 * Zen Cart German Specific 
 * @copyright Copyright 2003-2024 Zen Cart Development Team
 * Zen Cart German Version - www.zen-cart-pro.at 
 * @license https://www.zen-cart-pro.at/license/3_0.txt GNU General Public License V3.0
 * @version $Id: block-spam-registrations.php 2024-05-16 19:32:16Z webchills $
 */

if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}
if (!defined('BLOCKSPAMREGISTRATIONS_FIRSTNAME')) define('BLOCKSPAMREGISTRATIONS_FIRSTNAME', '3');
if (!defined('BLOCKSPAMREGISTRATIONS_LASTNAME')) define('BLOCKSPAMREGISTRATIONS_LASTNAME', '3');
if (!defined('BLOCKSPAMREGISTRATIONS_CITY')) define('BLOCKSPAMREGISTRATIONS_CITY', '3');
if (!defined('BLOCKSPAMREGISTRATIONS_SUBURB')) define('BLOCKSPAMREGISTRATIONS_SUBURB', '5');

function block_spam_registrations($firstname, $lastname, $city, $suburb) {
    global $db;
    $error = false;
    $reason = '';
    if(BLOCKSPAMREGISTRATIONS_FIRSTNAME > 0) {
        $count = countcase($firstname);
        if($count >= BLOCKSPAMREGISTRATIONS_FIRSTNAME) {
            $error = true;
            $reason .= 'Vorname Check nicht bestanden. ';
        }
    }
    if(BLOCKSPAMREGISTRATIONS_LASTNAME > 0) {
        $count = countcase($lastname);
        if($count >= BLOCKSPAMREGISTRATIONS_LASTNAME) {
            $error = true;
            $reason .= 'Nachname Check nicht bestanden. ';
        }
    }
    if(BLOCKSPAMREGISTRATIONS_CITY > 0) {
        $count = countcase($city);
        if($count >= BLOCKSPAMREGISTRATIONS_CITY) {
            $error = true;
            $reason .= 'Ortsname Check nicht bestanden. ';
        }
    }
    if(BLOCKSPAMREGISTRATIONS_SUBURB > 0) {
        $count = countcase($suburb);
        if($count >= BLOCKSPAMREGISTRATIONS_SUBURB) {
            $error = true;
            $reason .= 'Adresszeile 2 Check nicht bestanden. ';
        }
    } 
    return array('error'=>$error,'reason'=>$reason);
}

function countcase($str)
{
    $upper = 0;

    for ($i = 0; $i < strlen($str); $i++)
    {
        if ($str[$i] >= 'A' &&
            $str[$i] <= 'Z')
            $upper++;
    }
    return $upper;
}
function RandomToken($length = 32){
    if(!isset($length) || intval($length) <= 8 ){
        $length = 32;
    }
    if (function_exists('random_bytes')) {
        return bin2hex(random_bytes($length));
    }
    if (function_exists('mcrypt_create_iv')) {
        return bin2hex(mcrypt_create_iv($length, MCRYPT_DEV_URANDOM));
    }
    if (function_exists('openssl_random_pseudo_bytes')) {
        return bin2hex(openssl_random_pseudo_bytes($length));
    }
}
