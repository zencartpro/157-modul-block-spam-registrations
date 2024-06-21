<?php
/**
 * Block Spam Registrations for Zen Cart 1.5.7h German
 * based on ZX Antispam and Email Verification by ZenExpert - https://zenexpert.com
 * @copyright Copyright 2003-2024 Zen Cart Development Team
 * Zen Cart German Version - www.zen-cart-pro.at
 * @license https://www.zen-cart-pro.at/license/3_0.txt GNU General Public License V3.0
 * @version $Id: auto.block_spam_registrations.php 2024-06-21 14:34:39Z webchills $
 */

class zcObserverBlockSpamRegistrations extends base {

    public function __construct()
    {
        if(BLOCKSPAMREGISTRATIONS_ENABLED === 'true') {
            $this->attach(
                $this,
                array(
                    'NOTIFY_CREATE_ACCOUNT_ANTISPAM_CHECK',
                )
            );
        }
    }

    function updateNotifyCreateAccountAntispamCheck(&$class, $eventID, &$error, &$firstname, &$lastname, &$city, &$suburb) {
        global $db;
        
        if($error == true) return;

        $spam = block_spam_registrations($firstname, $lastname, $city, $suburb);        

        if($spam['error'] == true) {
        	$registration_ip = zen_get_ip_address();
        	$nick = 'nick';
          $firstname = zen_db_prepare_input(zen_sanitize_string($_POST['firstname']));
          $lastname = zen_db_prepare_input(zen_sanitize_string($_POST['lastname']));
          $email_address = zen_db_prepare_input($_POST['email_address']);
          $telephone = zen_db_prepare_input($_POST['telephone']);
          $fax = isset($_POST['fax']) ? zen_db_prepare_input($_POST['fax']) : '';
          $newsletter = 0;
         if (ACCOUNT_NEWSLETTER_STATUS == '1' || ACCOUNT_NEWSLETTER_STATUS == '2') {
         if (isset($_POST['newsletter'])) {
         $newsletter = zen_db_prepare_input($_POST['newsletter']);
        }
        }
        if (isset($_POST['password'])) {
        $password = zen_db_prepare_input($_POST['password']);
      } else {
      	$password = '';
        }
        $email_format = $_POST['email_format'];
        $customers_authorization = (int)CUSTOMERS_APPROVAL_AUTHORIZATION;
        $customers_referral = 'none';
          if (ACCOUNT_GENDER == 'true') {
        if (isset($_POST['gender'])) {
            $gender = zen_db_prepare_input($_POST['gender']);
        }
    }
    if (isset($_POST['dob'])) {
     $dob = zen_db_prepare_input($_POST['dob']);
     } else {
     $dob = '0001-01-01 00:00:00';
     }
     $street_address = zen_db_prepare_input($_POST['street_address']);
  if (ACCOUNT_SUBURB == 'true') $suburb = zen_db_prepare_input($_POST['suburb']);
  $postcode = zen_db_prepare_input($_POST['postcode']);
  $city = zen_db_prepare_input($_POST['city']);
  if (ACCOUNT_COMPANY == 'true') $company = zen_db_prepare_input($_POST['company']);
   $street_address = zen_db_prepare_input($_POST['street_address']);
   $postcode = zen_db_prepare_input($_POST['postcode']);
   $city = zen_db_prepare_input($_POST['city']);
   $country = zen_db_prepare_input($_POST['zone_country_id']);
if (isset($_POST['password'])) {
            $sql_data_array = array(array('fieldName'=>'customers_firstname', 'value'=>$firstname, 'type'=>'stringIgnoreNull'),
                array('fieldName'=>'customers_lastname', 'value'=>$lastname, 'type'=>'stringIgnoreNull'),
                array('fieldName'=>'customers_email_address', 'value'=>$email_address, 'type'=>'stringIgnoreNull'),               
                array('fieldName'=>'customers_nick', 'value'=>$nick, 'type'=>'stringIgnoreNull'),
                array('fieldName'=>'customers_telephone', 'value'=>$telephone, 'type'=>'stringIgnoreNull'),
                array('fieldName'=>'customers_fax', 'value'=>$fax, 'type'=>'stringIgnoreNull'),
                array('fieldName'=>'customers_newsletter', 'value'=>$newsletter, 'type'=>'integer'),
                array('fieldName'=>'customers_email_format', 'value'=>$email_format, 'type'=>'stringIgnoreNull'),
                array('fieldName'=>'customers_default_address_id', 'value'=>0, 'type'=>'integer'),               
                array('fieldName'=>'customers_password', 'value'=>zen_encrypt_password($password), 'type'=>'stringIgnoreNull'),               
                array('fieldName'=>'customers_authorization', 'value'=>$customers_authorization, 'type'=>'integer'),                                       
            );
          } else {
          	 $sql_data_array = array(array('fieldName'=>'customers_firstname', 'value'=>$firstname, 'type'=>'stringIgnoreNull'),
                array('fieldName'=>'customers_lastname', 'value'=>$lastname, 'type'=>'stringIgnoreNull'),
                array('fieldName'=>'customers_email_address', 'value'=>$email_address, 'type'=>'stringIgnoreNull'),               
                array('fieldName'=>'customers_nick', 'value'=>$nick, 'type'=>'stringIgnoreNull'),
                array('fieldName'=>'customers_telephone', 'value'=>$telephone, 'type'=>'stringIgnoreNull'),
                array('fieldName'=>'customers_fax', 'value'=>$fax, 'type'=>'stringIgnoreNull'),
                array('fieldName'=>'customers_newsletter', 'value'=>$newsletter, 'type'=>'integer'),
                array('fieldName'=>'customers_email_format', 'value'=>$email_format, 'type'=>'stringIgnoreNull'),
                array('fieldName'=>'customers_default_address_id', 'value'=>0, 'type'=>'integer'),  
                array('fieldName'=>'customers_authorization', 'value'=>$customers_authorization, 'type'=>'integer'),                                       
            );
          }

            $sql_data_array[] = array('fieldName'=>'customers_referral', 'value'=> $customers_referral, 'type'=>'string');
            if (ACCOUNT_GENDER == 'true') {
                $sql_data_array[] = array('fieldName'=>'customers_gender', 'value'=>$gender, 'type'=>'stringIgnoreNull');
                $sql_data_array[] = array('fieldName'=>'entry_gender', 'value'=>$gender, 'type'=>'stringIgnoreNull');
            }
            if (ACCOUNT_DOB == 'true')  $sql_data_array[] = array('fieldName'=>'customers_dob', 'value'=>empty($_POST['dob']) || $dob_entered == '0001-01-01 00:00:00' ? zen_db_prepare_input('0001-01-01 00:00:00') : zen_date_raw($_POST['dob']), 'type'=>'date');

            $sql_data_array[] = array('fieldName'=>'entry_street_address', 'value'=>$street_address, 'type'=>'stringIgnoreNull');
            $sql_data_array[] = array('fieldName'=>'entry_postcode', 'value'=>$postcode, 'type'=>'stringIgnoreNull');
            $sql_data_array[] = array('fieldName'=>'entry_city', 'value'=>$city, 'type'=>'stringIgnoreNull');
            $sql_data_array[] = array('fieldName'=>'entry_country_id', 'value'=>$country, 'type'=>'integer');

            if (ACCOUNT_COMPANY == 'true') $sql_data_array[] = array('fieldName'=>'entry_company', 'value'=>$company, 'type'=>'stringIgnoreNull');
            if (ACCOUNT_SUBURB == 'true') $sql_data_array[] = array('fieldName'=>'entry_suburb', 'value'=>$suburb, 'type'=>'stringIgnoreNull');    

            $sql_data_array[] = array('fieldName'=>'reason', 'value'=>$spam['reason'], 'type'=>'stringIgnoreNull');
            
            $sql_data_array[] = array('fieldName'=>'registration_ip', 'value'=>$registration_ip, 'type'=>'string');

            $db->perform(TABLE_CUSTOMERS_SPAM, $sql_data_array);

            $antispam_id = $db->Insert_ID();

            $_SESSION['customer_spam'] = $antispam_id;
            
            // redirect to a new page with info about spam registration
            zen_redirect(zen_href_link(FILENAME_ACCOUNT_CONFIRMATION));
        }
    }
}