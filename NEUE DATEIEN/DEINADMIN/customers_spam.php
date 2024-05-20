<?php
/**
 * Block Spam Registrations for Zen Cart 1.5.7h German
 * based on ZX Antispam and Email Verification by ZenExpert - https://zenexpert.com
 * @copyright Copyright 2003-2024 Zen Cart Development Team
 * Zen Cart German Version - www.zen-cart-pro.at
 * @license https://www.zen-cart-pro.at/license/3_0.txt GNU General Public License V3.0
 * @version $Id: customers_spam.php 2024-05-20 18:44:39Z webchills $
 */
require 'includes/application_top.php';

$action = (isset($_GET['action']) ? $_GET['action'] : '');
$customers_spam_id = isset($_GET['cID']) ? (int)$_GET['cID'] : 0;

if (isset($_POST['cID'])) $customers_spam_id = (int)$_POST['cID'];
if (!isset($_GET['page'])) $_GET['page'] = '';
if (!isset($_GET['list_order'])) $_GET['list_order'] = '';

$error = false;
$processed = false;

if (!empty($action)) {
  switch ($action) {
    case 'update':
      $customers_firstname = zen_db_prepare_input(zen_sanitize_string($_POST['customers_firstname']));
      $customers_lastname = zen_db_prepare_input(zen_sanitize_string($_POST['customers_lastname']));
      $customers_email_address = zen_db_prepare_input($_POST['customers_email_address']);
      $customers_telephone = zen_db_prepare_input($_POST['customers_telephone']);
            $customers_fax = '';
            if (ACCOUNT_FAX_NUMBER === 'true') {
                $customers_fax = zen_db_prepare_input($_POST['customers_fax']);
            }
      $customers_newsletter = zen_db_prepare_input($_POST['customers_newsletter']);
      $customers_email_format = zen_db_prepare_input($_POST['customers_email_format']);
            $customers_gender = !empty($_POST['customers_gender']) ?
                zen_db_prepare_input($_POST['customers_gender']) : '';
            $customers_dob = (empty($_POST['customers_dob'])) ?
                zen_db_prepare_input('0001-01-01 00:00:00') : zen_db_prepare_input($_POST['customers_dob']);

            $customers_authorization = (int)$_POST['customers_authorization'];
            $customers_referral = zen_db_prepare_input($_POST['customers_referral']);

            if (CUSTOMERS_APPROVAL_AUTHORIZATION === '2' && $customers_authorization === 1) {
                $customers_authorization = 2;
                $messageStack->add_session(ERROR_CUSTOMER_APPROVAL_CORRECTION2, 'caution');
            }

            if (CUSTOMERS_APPROVAL_AUTHORIZATION === '1' && $customers_authorization === 2) {
        $customers_authorization = 1;
        $messageStack->add_session(ERROR_CUSTOMER_APPROVAL_CORRECTION1, 'caution');
      }

            $default_address_id = (int)$_POST['default_address_id'];
      $entry_street_address = zen_db_prepare_input($_POST['entry_street_address']);
            $entry_suburb = !empty($_POST['entry_suburb']) ? zen_db_prepare_input($_POST['entry_suburb']) : '';
      
      $entry_postcode = zen_db_prepare_input($_POST['entry_postcode']);
      $entry_city = zen_db_prepare_input($_POST['entry_city']);
            $entry_country_id = (int)$_POST['entry_country_id'];

            $entry_company = !empty($_POST['entry_company']) ? zen_db_prepare_input($_POST['entry_company']) : '';
            $entry_state = !empty($_POST['entry_state']) ? zen_db_prepare_input($_POST['entry_state']) : '';
            $entry_zone_id = (int)($_POST['entry_zone_id'] ?? 0);

      if (strlen($customers_firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
        $error = true;
        $entry_firstname_error = true;
      } else {
        $entry_firstname_error = false;
      }

      if (strlen($customers_lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
        $error = true;
        $entry_lastname_error = true;
      } else {
        $entry_lastname_error = false;
      }

      if (ACCOUNT_DOB == 'true') {
        if (ENTRY_DOB_MIN_LENGTH > 0) {
          if (checkdate(substr(zen_date_raw($customers_dob), 4, 2), substr(zen_date_raw($customers_dob), 6, 2), substr(zen_date_raw($customers_dob), 0, 4))) {
            $entry_date_of_birth_error = false;
          } else {
            $error = true;
            $entry_date_of_birth_error = true;
          }
        }
      } else {
        $customers_dob = '0001-01-01 00:00:00';
      }

      if (strlen($customers_email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
        $error = true;
        $entry_email_address_error = true;
      } else {
        $entry_email_address_error = false;
      }

      if (!zen_validate_email($customers_email_address)) {
        $error = true;
        $entry_email_address_check_error = true;
      } else {
        $entry_email_address_check_error = false;
      }

      if (strlen($entry_street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
        $error = true;
        $entry_street_address_error = true;
      } else {
        $entry_street_address_error = false;
      }

      if (strlen($entry_postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
        $error = true;
        $entry_post_code_error = true;
      } else {
        $entry_post_code_error = false;
      }

      if (strlen($entry_city) < ENTRY_CITY_MIN_LENGTH) {
        $error = true;
        $entry_city_error = true;
      } else {
        $entry_city_error = false;
      }

      if ($entry_country_id == false) {
        $error = true;
        $entry_country_error = true;
      } else {
        $entry_country_error = false;
      }

      if (ACCOUNT_STATE == 'true') {
        if ($entry_country_error == true) {
          $entry_state_error = true;
        } else {
          $zone_id = 0;
          $entry_state_error = false;
          $check_value = $db->Execute("SELECT COUNT(*) AS total
                                       FROM " . TABLE_ZONES . "
                                       WHERE zone_country_id = " . (int)$entry_country_id);

          $entry_state_has_zones = ($check_value->fields['total'] > 0);
          if ($entry_state_has_zones == true) {
            $zone_query = $db->Execute("SELECT zone_id
                                        FROM " . TABLE_ZONES . "
                                        WHERE zone_country_id = " . (int)$entry_country_id . "
                                        AND zone_name = '" . zen_db_input($entry_state) . "'");

            if ($zone_query->RecordCount() > 0) {
              $entry_zone_id = $zone_query->fields['zone_id'];
            } else {
              $error = true;
              $entry_state_error = true;
            }
          } else {
            if (strlen($entry_state) < (int)ENTRY_STATE_MIN_LENGTH) {
              $error = true;
              $entry_state_error = true;
            }
          }
        }
      }

      if (strlen($customers_telephone) < ENTRY_TELEPHONE_MIN_LENGTH) {
        $error = true;
        $entry_telephone_error = true;
      } else {
        $entry_telephone_error = false;
      }


      $zco_notifier->notify('NOTIFY_ADMIN_CUSTOMERS_UPDATE_VALIDATE', array(), $error);

      if ($error == false) {
        $sql_data_array = array(array('fieldName' => 'customers_firstname', 'value' => $customers_firstname, 'type' => 'stringIgnoreNull'),
          array('fieldName' => 'customers_lastname', 'value' => $customers_lastname, 'type' => 'stringIgnoreNull'),
          array('fieldName' => 'customers_email_address', 'value' => $customers_email_address, 'type' => 'stringIgnoreNull'),
          array('fieldName' => 'customers_telephone', 'value' => $customers_telephone, 'type' => 'stringIgnoreNull'),
          array('fieldName' => 'customers_fax', 'value' => $customers_fax, 'type' => 'stringIgnoreNull'),
          array('fieldName' => 'customers_newsletter', 'value' => $customers_newsletter, 'type' => 'stringIgnoreNull'),
          array('fieldName' => 'customers_email_format', 'value' => $customers_email_format, 'type' => 'stringIgnoreNull'),
          array('fieldName' => 'customers_authorization', 'value' => $customers_authorization, 'type' => 'stringIgnoreNull'),
          array('fieldName' => 'customers_referral', 'value' => $customers_referral, 'type' => 'stringIgnoreNull'),
        );

        if (ACCOUNT_GENDER == 'true') {
          $sql_data_array[] = array('fieldName' => 'customers_gender', 'value' => $customers_gender, 'type' => 'stringIgnoreNull');
        }
        if (ACCOUNT_DOB == 'true') {
          $sql_data_array[] = array('fieldName' => 'customers_dob', 'value' => ($customers_dob == '0001-01-01 00:00:00' ? '0001-01-01 00:00:00' : zen_date_raw($customers_dob)), 'type' => 'date');
        }

     

          $sql_data_array[] = array('fieldName' => 'entry_street_address', 'value' => $entry_street_address, 'type' => 'stringIgnoreNull');
          $sql_data_array[] = array('fieldName' => 'entry_postcode', 'value' => $entry_postcode, 'type' => 'stringIgnoreNull');
          $sql_data_array[] = array('fieldName' => 'entry_city', 'value' => $entry_city, 'type' => 'stringIgnoreNull');
          $sql_data_array[] = array('fieldName' => 'entry_country_id', 'value' => $entry_country_id, 'type' => 'integer');


        if (ACCOUNT_COMPANY == 'true') {
          $sql_data_array[] = array('fieldName' => 'entry_company', 'value' => $entry_company, 'type' => 'stringIgnoreNull');
        }
        if (ACCOUNT_SUBURB == 'true') {
          $sql_data_array[] = array('fieldName' => 'entry_suburb', 'value' => $entry_suburb, 'type' => 'stringIgnoreNull');
        }

        
            $sql_data_array[] = array('fieldName' => 'entry_zone_id', 'value' => 0, 'type' => 'integer');
            $sql_data_array[] = array('fieldName' => 'entry_state', 'value' => '', 'type' => 'stringIgnoreNull');


        $zco_notifier->notify('NOTIFY_ADMIN_CUSTOMERS_B4_ADDRESS_UPDATE', array('customers_id' => $customers_spam_id, 'address_book_id' => $default_address_id), $sql_data_array);

          $db->perform(TABLE_CUSTOMERS_SPAM, $sql_data_array, 'update', "customers_spam_id = '" . (int)$customers_spam_id . "'");


          zen_record_admin_activity('Customer record updated for customer ID ' . (int)$customers_spam_id, 'notice');
        $zco_notifier->notify('ADMIN_CUSTOMER_UPDATE', (int)$customers_spam_id, $default_address_id, $sql_data_array);
        zen_redirect(zen_href_link(FILENAME_CUSTOMERS_SPAM, zen_get_all_get_params(array('cID', 'action')) . 'cID=' . $customers_spam_id, 'NONSSL'));
      } else if ($error == true) {
        $cInfo = new objectInfo($_POST);
        $processed = true;
      }
      

      break;
    case 'deleteconfirm':
      $customers_spam_id = zen_db_prepare_input($_POST['cID']);

      $zco_notifier->notify('NOTIFIER_ADMIN_ZEN_CUSTOMERS_DELETE_CONFIRM', array('customers_id' => $customers_spam_id));

        $db->Execute("DELETE FROM " . TABLE_CUSTOMERS_SPAM . "
                    WHERE customers_spam_id = " . (int)$customers_spam_id);

      zen_record_admin_activity('SPAM Customer with customer ID ' . (int)$customers_spam_id . ' deleted.', 'warning');
      zen_redirect(zen_href_link(FILENAME_CUSTOMERS_SPAM, zen_get_all_get_params(array('cID', 'action')), 'NONSSL'));
      break;
    case 'approve':
        $sql = $db->Execute("SELECT * FROM ".TABLE_CUSTOMERS_SPAM ." WHERE customers_spam_id = '".$customers_spam_id."'");
        if($sql->RecordCount() > 0) {
            // account confirmed
            $sql_data_array = array(array('fieldName' => 'customers_firstname', 'value' => $sql->fields['customers_firstname'], 'type' => 'stringIgnoreNull'),
                array('fieldName' => 'customers_lastname', 'value' => $sql->fields['customers_lastname'], 'type' => 'stringIgnoreNull'),
                array('fieldName' => 'customers_email_address', 'value' => $sql->fields['customers_email_address'], 'type' => 'stringIgnoreNull'),
                array('fieldName' => 'customers_nick', 'value' => $sql->fields['customers_nick'], 'type' => 'stringIgnoreNull'),
                array('fieldName' => 'customers_telephone', 'value' => $sql->fields['customers_telephone'], 'type' => 'stringIgnoreNull'),
                array('fieldName' => 'customers_fax', 'value' => $sql->fields['customers_fax'], 'type' => 'stringIgnoreNull'),
                array('fieldName' => 'customers_newsletter', 'value' => $sql->fields['customers_newsletter'], 'type' => 'integer'),
                array('fieldName' => 'customers_email_format', 'value' => $sql->fields['customers_email_format'], 'type' => 'stringIgnoreNull'),
                array('fieldName' => 'customers_default_address_id', 'value' => 0, 'type' => 'integer'),
                array('fieldName' => 'customers_password', 'value' => $sql->fields['customers_password'], 'type' => 'stringIgnoreNull'),
                array('fieldName' => 'customers_authorization', 'value' => $sql->fields['customers_authorization'], 'type' => 'integer'),
            );

            if ((CUSTOMERS_REFERRAL_STATUS == '2' and $sql->fields['customers_referral'] != '')) $sql_data_array[] = array('fieldName' => 'customers_referral', 'value' => $sql->fields['customers_referral'], 'type' => 'stringIgnoreNull');
            if (ACCOUNT_GENDER == 'true') $sql_data_array[] = array('fieldName' => 'customers_gender', 'value' => $sql->fields['customers_gender'], 'type' => 'stringIgnoreNull');
            if (ACCOUNT_DOB == 'true') $sql_data_array[] = array('fieldName' => 'customers_dob', 'value' => empty($sql->fields['customers_dob']) || $sql->fields['customers_dob'] == '0001-01-01 00:00:00' ? zen_db_prepare_input('0001-01-01 00:00:00') : zen_date_raw($sql->fields['customers_dob']), 'type' => 'date');

            $db->perform(TABLE_CUSTOMERS, $sql_data_array);

            $customer_created_id = $db->Insert_ID();

            $zco_notifier->notify('NOTIFY_MODULE_CREATE_ACCOUNT_ADDED_CUSTOMER_RECORD', array_merge(array('customer_id' => $customer_created_id), $sql_data_array));

            $sql_data_array = array(array('fieldName' => 'customers_id', 'value' => $customer_created_id, 'type' => 'integer'),
                array('fieldName' => 'entry_firstname', 'value' => $sql->fields['customers_firstname'], 'type' => 'stringIgnoreNull'),
                array('fieldName' => 'entry_lastname', 'value' => $sql->fields['customers_lastname'], 'type' => 'stringIgnoreNull'),
                array('fieldName' => 'entry_street_address', 'value' => $sql->fields['entry_street_address'], 'type' => 'stringIgnoreNull'),
                array('fieldName' => 'entry_postcode', 'value' => $sql->fields['entry_postcode'], 'type' => 'stringIgnoreNull'),
                array('fieldName' => 'entry_city', 'value' => $sql->fields['entry_city'], 'type' => 'stringIgnoreNull'),
                array('fieldName' => 'entry_country_id', 'value' => $sql->fields['entry_country_id'], 'type' => 'integer'),
            );

            if (ACCOUNT_GENDER == 'true') $sql_data_array[] = array('fieldName' => 'entry_gender', 'value' => $sql->fields['entry_gender'], 'type' => 'stringIgnoreNull');
            if (ACCOUNT_COMPANY == 'true') $sql_data_array[] = array('fieldName' => 'entry_company', 'value' => $sql->fields['entry_company'], 'type' => 'stringIgnoreNull');
            if (ACCOUNT_SUBURB == 'true') $sql_data_array[] = array('fieldName' => 'entry_suburb', 'value' => $sql->fields['entry_suburb'], 'type' => 'stringIgnoreNull');

            if (ACCOUNT_STATE == 'true') {
                if ($sql->fields['entry_zone_id'] > 0) {
                    $sql_data_array[] = array('fieldName' => 'entry_zone_id', 'value' => $sql->fields['entry_zone_id'], 'type' => 'integer');
                    $sql_data_array[] = array('fieldName' => 'entry_state', 'value' => '', 'type' => 'stringIgnoreNull');
                } else {
                    $sql_data_array[] = array('fieldName' => 'entry_zone_id', 'value' => 0, 'type' => 'integer');
                    $sql_data_array[] = array('fieldName' => 'entry_state', 'value' => $sql->fields['entry_state'], 'type' => 'stringIgnoreNull');
                }
            }

            $db->perform(TABLE_ADDRESS_BOOK, $sql_data_array);

            $address_id = $db->Insert_ID();

            $zco_notifier->notify('NOTIFY_MODULE_CREATE_ACCOUNT_ADDED_ADDRESS_BOOK_RECORD', array_merge(array('address_id' => $address_id), $sql_data_array));

            $sql = "UPDATE " . TABLE_CUSTOMERS . "
              SET customers_default_address_id = '" . (int)$address_id . "'
              WHERE customers_id = '" . (int)$customer_created_id . "'";

            $db->Execute($sql);

            $sql = "INSERT INTO " . TABLE_CUSTOMERS_INFO . "
                          (customers_info_id, customers_info_number_of_logons,
                           customers_info_date_account_created, customers_info_date_of_last_logon)
              VALUES ('" . (int)$customer_created_id . "', '1', now(), now())";

            $db->Execute($sql);

           
            $send_welcome_email = false;

           


            $db->Execute("DELETE FROM ".TABLE_CUSTOMERS_SPAM." WHERE customers_spam_id = '".$customers_spam_id."'");
            zen_redirect(zen_href_link(FILENAME_CUSTOMERS_SPAM, zen_get_all_get_params(array('cID', 'action')), 'NONSSL'));
        }

      break;
      default:
      $customers = $db->Execute("SELECT *
                                 FROM " . TABLE_CUSTOMERS_SPAM . "
                                 WHERE customers_spam_id = '" . $customers_spam_id."'");

      $cInfo = new objectInfo($customers->fields);
  }
}
?>
<!doctype html>
    <html <?php echo HTML_PARAMS; ?>>
    <head>
        <?php require DIR_WS_INCLUDES . 'admin_html_head.php'; ?>
<?php
if ($action === 'edit' || $action === 'update') {
?>
        <script>
            function check_form() {
                var error = 0;
                var error_message = '<?php echo JS_ERROR; ?>';

<?php
    if (ACCOUNT_GENDER === 'true') {
?>
                if (document.customers.customers_gender[0].checked || document.customers.customers_gender[1].checked || document.customers.customers_gender[2].checked) {
                } else {
                    error_message = error_message + '<?php echo JS_GENDER; ?>';
                    error = 1;
                }
<?php
}
?>

                if (document.customers.elements['entry_country_id'].type != 'hidden') {
                    if (document.customers.entry_country_id.value == 0) {
                        error_message = error_message + '<?php echo JS_COUNTRY; ?>';
                        error = 1;
                    }
                }

                if (error == 1) {
                    alert(error_message);
                    return false;
                } else {
                    return true;
                }
            }
        </script>
<?php
}
?>
    </head>
    <body>
    <!-- header //-->
    <?php require DIR_WS_INCLUDES . 'header.php'; ?>
    <!-- header_eof //-->

    <!-- body //-->
    <div class="container-fluid">
      <!-- body_text //-->
      <h1><?php echo HEADING_TITLE; ?></h1>
<?php
if ($action === 'edit' || $action === 'update') {
    $newsletter_array = [
        ['id' => '1', 'text' => ENTRY_NEWSLETTER_YES],
        ['id' => '0', 'text' => ENTRY_NEWSLETTER_NO]
    ];
        
        echo zen_draw_form(
	'customers_spam',
	FILENAME_CUSTOMERS_SPAM, 
        zen_get_all_get_params(['action']) . 'action=update',
        'post',
        'onsubmit="return check_form(customers);" class="form-horizontal"',
        true
    );
    echo zen_draw_hidden_field('default_address_id', $cInfo->customers_default_address_id);
    echo zen_hide_session_id();
        ?>
        <div class="row formAreaTitle"><?php echo CATEGORY_PERSONAL; ?></div>
        <div class="formArea">
<?php
    if (ACCOUNT_GENDER === 'true') {
?>
            <div class="form-group">
                <div class="col-sm-3">
                    <p class="control-label"><?php echo ENTRY_GENDER; ?></p>
                </div>
                <div class="col-sm-9 col-md-6">
                    <label class="radio-inline"><?php
                        echo zen_draw_radio_field(
                                'customers_gender',
                                'm',
                                false,
                                $cInfo->customers_gender
                            ) . MALE; ?>
                    </label>
                    <label class="radio-inline"><?php
                        echo zen_draw_radio_field(
                                'customers_gender',
                                'f',
                                false,
                                $cInfo->customers_gender
                            ) . FEMALE; ?>
                    </label>
                    <label class="radio-inline"><?php
                        echo zen_draw_radio_field(
                                'customers_gender',
                                'd',
                                false,
                                $cInfo->customers_gender
                            ) . DIVERS; ?>
                    </label>
                    
                </div>
            </div>
<?php
    }

    $customers_authorization_array = [
        ['id' => '0', 'text' => CUSTOMERS_AUTHORIZATION_0],
        ['id' => '1', 'text' => CUSTOMERS_AUTHORIZATION_1],
        ['id' => '2', 'text' => CUSTOMERS_AUTHORIZATION_2],
        ['id' => '3', 'text' => CUSTOMERS_AUTHORIZATION_3],
        ['id' => '4', 'text' => CUSTOMERS_AUTHORIZATION_4], // banned
    ];
?>
            <div class="form-group">
                <?php
                echo zen_draw_label(
                    CUSTOMERS_AUTHORIZATION,
                    'customers_authorization',
                    'class="col-sm-3 control-label"'
                ); ?>
                <div class="col-sm-9 col-md-6">
                    <?php
                    echo zen_draw_pull_down_menu(
                        'customers_authorization',
                        $customers_authorization_array,
                        $cInfo->customers_authorization,
                        'class="form-control" id="customers_authorization"'
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php
                echo zen_draw_label(ENTRY_FIRST_NAME, 'customers_firstname', 'class="col-sm-3 control-label"'); ?>
                <div class="col-sm-9 col-md-6">
                    <?php
                    echo zen_draw_input_field(
                        'customers_firstname',
                        htmlspecialchars(
                            $cInfo->customers_firstname,
                            ENT_COMPAT,
                            CHARSET,
                            true
                        ),
                        zen_set_field_length(
                            TABLE_CUSTOMERS,
                            'customers_firstname',
                            50
                        ) . ' class="form-control" id="customers_firstname" minlength="' . ENTRY_FIRST_NAME_MIN_LENGTH . '"',
                        true
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php
                echo zen_draw_label(ENTRY_LAST_NAME, 'customers_lastname', 'class="col-sm-3 control-label"'); ?>
                <div class="col-sm-9 col-md-6">
                    <?php
                    echo zen_draw_input_field(
                        'customers_lastname',
                        htmlspecialchars(
                            $cInfo->customers_lastname,
                            ENT_COMPAT,
                            CHARSET,
                            true
                        ),
                        zen_set_field_length(
                            TABLE_CUSTOMERS,
                            'customers_lastname',
                            50
                        ) . ' class="form-control" id="customers_lastname" minlength="' . ENTRY_LAST_NAME_MIN_LENGTH . '"',
                        true
                    ); ?>
                </div>
            </div>
<?php
    if (ACCOUNT_DOB === 'true') {
?>
            <div class="form-group">
                <?php
                echo zen_draw_label(ENTRY_DATE_OF_BIRTH, 'customers_dob', 'class="col-sm-3 control-label"'); ?>
                <div class="col-sm-9 col-md-6">
                    <?php
                    echo zen_draw_input_field(
                        'customers_dob',
                        ((empty($cInfo->customers_dob) || $cInfo->customers_dob <= '0001-01-01' || $cInfo->customers_dob === '0001-01-01 00:00:00') ? '' :
                            (($action === 'edit') ? zen_date_short($cInfo->customers_dob) : $cInfo->customers_dob)
                        ),
                        'maxlength="10" class="form-control" id="customers_dob" minlength="' . ENTRY_DOB_MIN_LENGTH . '"',
                        (ACCOUNT_DOB === 'true' && (int)ENTRY_DOB_MIN_LENGTH !== 0)
                    );
                    echo ($error === true && $entry_date_of_birth_error === true) ? '&nbsp;' . ENTRY_DATE_OF_BIRTH_ERROR : '';?>
                </div>
            </div>
<?php
    }
?>
            <div class="form-group">
                <?php echo zen_draw_label(ENTRY_EMAIL_ADDRESS, 'customers_email_address', 'class="col-sm-3 control-label"'); ?>
                <div class="col-sm-9 col-md-6">
                    <?php
                    echo zen_draw_input_field(
                        'customers_email_address',
                        htmlspecialchars(
                            $cInfo->customers_email_address,
                            ENT_COMPAT,
                            CHARSET,
                            true
                        ),
                        zen_set_field_length(
                            TABLE_CUSTOMERS,
                            'customers_email_address',
                            50
                        ) . ' class="form-control" id="customers_email_address" minlength="' . ENTRY_EMAIL_ADDRESS_MIN_LENGTH . '"',
                        true
                        );
                        echo ($error === true && $entry_email_address_check_error === true) ? '&nbsp;' . ENTRY_EMAIL_ADDRESS_ERROR : ''; ?>
                </div>
            </div>

        </div>
<?php
    if (ACCOUNT_COMPANY === 'true') {
?>
        <div class="row">
            <?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?>
        </div>
        <div class="row formAreaTitle">
            <?php echo CATEGORY_COMPANY; ?>
        </div>
        <div class="formArea">
            <div class="form-group">
                <?php echo zen_draw_label(ENTRY_COMPANY, 'entry_company', 'class="col-sm-3 control-label"'); ?>
                <div class="col-sm-9 col-md-6">
                    <?php
                    echo zen_draw_input_field(
                        'entry_company',
                        htmlspecialchars(($cInfo->company ?? ''), ENT_COMPAT, CHARSET, true),
                        zen_set_field_length(
                            TABLE_ADDRESS_BOOK,
                            'entry_company',
                            50
                        ) . ' class="form-control" id="entry_company" minlength="' . ENTRY_COMPANY_MIN_LENGTH . '"'
                    ); ?>
                </div>
            </div>
        </div>
<?php
    }
?>
        <div class="row">
            <?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?>
        </div>
        <div class="row formAreaTitle"><?php echo CATEGORY_ADDRESS; ?></div>
        <div class="formArea">
            <div class="form-group">
                <?php
                echo zen_draw_label(ENTRY_STREET_ADDRESS, 'entry_street_address', 'class="col-sm-3 control-label"'); ?>
                <div class="col-sm-9 col-md-6">
                    <?php
                    echo zen_draw_input_field(
                        'entry_street_address',
                        htmlspecialchars($cInfo->entry_street_address, ENT_COMPAT, CHARSET, true),
                        zen_set_field_length(
                            TABLE_ADDRESS_BOOK,
                            'entry_street_address',
                            50
                        ) . ' class="form-control" id="entry_street_address" minlength="' . ENTRY_STREET_ADDRESS_MIN_LENGTH . '"',
                        true
                    ); ?>
                </div>
            </div>
<?php
    if (ACCOUNT_SUBURB === 'true') {
?>
            <div class="form-group">
                <?php
                echo zen_draw_label(ENTRY_SUBURB, 'entry_suburb', 'class="col-sm-3 control-label"'); ?>
                <div class="col-sm-9 col-md-6">
                    <?php
                    echo zen_draw_input_field(
                        'entry_suburb',
                        htmlspecialchars((string)$cInfo->suburb, ENT_COMPAT, CHARSET, true),
                        zen_set_field_length(
                            TABLE_ADDRESS_BOOK,
                            'entry_suburb',
                            50
                        ) . ' class="form-control" id="entry_suburb"'
                    ); ?>
                </div>
            </div>
<?php
    }
?>
            <div class="form-group">
                <?php echo zen_draw_label(ENTRY_POST_CODE, 'entry_postcode', 'class="col-sm-3 control-label"'); ?>
                <div class="col-sm-9 col-md-6">
                    <?php
                    echo zen_draw_input_field(
                        'entry_postcode',
                        htmlspecialchars($cInfo->entry_postcode, ENT_COMPAT, CHARSET, true),
                        zen_set_field_length(
                            TABLE_ADDRESS_BOOK,
                            'entry_postcode',
                            10
                        ) . ' class="form-control" id="entry_postcode" minlength="' . ENTRY_POSTCODE_MIN_LENGTH . '"',
                        true
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php
                echo zen_draw_label(ENTRY_CITY, 'entry_city', 'class="col-sm-3 control-label"'); ?>
                <div class="col-sm-9 col-md-6">
                    <?php
                    echo zen_draw_input_field(
                        'entry_city',
                        htmlspecialchars($cInfo->entry_city, ENT_COMPAT, CHARSET, true),
                        zen_set_field_length(
                            TABLE_ADDRESS_BOOK,
                            'entry_city',
                            50
                        ) . ' class="form-control" id="entry_city" minlength="' . ENTRY_CITY_MIN_LENGTH . '"',
                        true
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo zen_draw_label(ENTRY_COUNTRY, 'entry_country_id', 'class="col-sm-3 control-label"'); ?>
                <div class="col-sm-9 col-md-6">
                    <?php
                    echo zen_draw_pull_down_menu(
                        'entry_country_id',
                        zen_get_countries_for_admin_pulldown(),
                        $cInfo->entry_country_id,
                        'class="form-control" id="entry_country_id"'
                    ); ?>
                </div>
            </div>
        </div>
        <div class="row">
            <?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?>
        </div>
        <div class="row formAreaTitle"><?php echo CATEGORY_CONTACT; ?></div>
        <div class="formArea">
            <div class="form-group">
                <?php echo zen_draw_label(ENTRY_TELEPHONE_NUMBER, 'customers_telephone', 'class="col-sm-3 control-label"'); ?>
                <div class="col-sm-9 col-md-6">
                    <?php
                    echo zen_draw_input_field(
                        'customers_telephone',
                        htmlspecialchars(
                            $cInfo->customers_telephone,
                            ENT_COMPAT,
                            CHARSET,
                            true
                        ),
                        zen_set_field_length(
                            TABLE_CUSTOMERS,
                            'customers_telephone',
                            15
                        ) . ' class="form-control" id="customers_telephone" minlength="' . ENTRY_TELEPHONE_MIN_LENGTH . '"',
                        true
                    ); ?>
                </div>
            </div>
<?php
    if (ACCOUNT_FAX_NUMBER === 'true') {
?>
            <div class="form-group">
                <?php echo zen_draw_label(ENTRY_FAX_NUMBER, 'customers_fax', 'class="col-sm-3 control-label"'); ?>
                <div class="col-sm-9 col-md-6">
<?php
        if ($processed === true) {
            echo $cInfo->customers_fax . zen_draw_hidden_field('customers_fax');
        } else {
            echo zen_draw_input_field(
                'customers_fax',
                htmlspecialchars(
                    (string)$cInfo->customers_fax,
                    ENT_COMPAT,
                    CHARSET,
                    true
                ),
                zen_set_field_length(
                    TABLE_CUSTOMERS,
                    'customers_fax',
                    15
                ) . ' class="form-control" id="customers_fax"'
            );
        }
?>
                </div>
            </div>
<?php
    }
?>
        </div>
        <div class="row">
            <?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?>
        </div>
        <div class="row formAreaTitle"><?php echo CATEGORY_OPTIONS; ?></div>
        <div class="formArea">
            <div class="form-group">
                <div class="col-sm-3">
                    <p class="control-label"><?php echo ENTRY_EMAIL_PREFERENCE; ?></p>
                </div>
                <div class="col-sm-9 col-md-6">
<?php
    if ($processed === true) {
        if ($cInfo->customers_email_format) {
            echo $customers_email_format . zen_draw_hidden_field('customers_email_format');
        }
    } else {
        $email_pref_text = ($cInfo->customers_email_format === 'TEXT');
        $email_pref_html = !$email_pref_text;
?>
                    <label class="radio-inline"><?php
                        echo zen_draw_radio_field(
                                'customers_email_format',
                                'HTML',
                                $email_pref_html
                            ) . ENTRY_EMAIL_HTML_DISPLAY; ?>
                    </label>
                    <label class="radio-inline"><?php
                        echo zen_draw_radio_field(
                                'customers_email_format',
                                'TEXT',
                                $email_pref_text
                            ) . ENTRY_EMAIL_TEXT_DISPLAY; ?>
                    </label>
<?php
    }
?>
                </div>
            </div>
            <div class="form-group">
                <?php echo zen_draw_label(ENTRY_NEWSLETTER, 'customers_newsletter', 'class="col-sm-3 control-label"'); ?>
                <div class="col-sm-9 col-md-6">
<?php
    if ($processed === true) {
        if ($cInfo->customers_newsletter === 1) {
            echo ENTRY_NEWSLETTER_YES;
        } else {
            echo ENTRY_NEWSLETTER_NO;
        }
        echo zen_draw_hidden_field('customers_newsletter');
    } else {
        echo zen_draw_pull_down_menu(
            'customers_newsletter',
            $newsletter_array,
            ($cInfo->customers_newsletter === 1) ? '1' : '0',
            'class="form-control" id="customers_newsletter"'
        );
    }
?>
                </div>
            </div>
            <div class="form-group">
                <?php echo zen_draw_label(ENTRY_PRICING_GROUP, 'customers_group_pricing', 'class="col-sm-3 control-label"'); ?>
                <div class="col-sm-9 col-md-6">
<?php
    if ($processed === true) {
        if ($cInfo->customers_group_pricing) {
            $group_query = $db->Execute(
                "SELECT group_name, group_percentage
                   FROM " . TABLE_GROUP_PRICING . "
                  WHERE group_id = " . (int)$cInfo->customers_group_pricing,
                  1
            );
            echo $group_query->fields['group_name'] . '&nbsp;' . $group_query->fields['group_percentage'] . '%';
        } else {
            echo ENTRY_NONE;
        }
        echo zen_draw_hidden_field('customers_group_pricing', $cInfo->customers_group_pricing);
    } else {
        $group_array_query = $db->Execute(
            "SELECT group_id, group_name, group_percentage
               FROM " . TABLE_GROUP_PRICING
        );
        $group_array[] = [
            'id' => 0,
            'text' => TEXT_NONE
        ];
        foreach ($group_array_query as $item) {
            $group_array[] = [
                'id' => $item['group_id'],
                'text' => $item['group_name'] . '&nbsp;' . $item['group_percentage'] . '%'
            ];
        }
        echo zen_draw_pull_down_menu(
            'customers_group_pricing',
            $group_array,
            $cInfo->customers_group_pricing,
            'class="form-control" id="customers_group_pricing"'
        );
    }
?>
                </div>
            </div>
            <div class="form-group">
                <?php echo zen_draw_label(CUSTOMERS_REFERRAL, 'customers_referral', 'class="col-sm-3 control-label"'); ?>
                <div class="col-sm-9 col-md-6">
                    <?php
                    echo zen_draw_input_field(
                        'customers_referral',
                        htmlspecialchars(
                            $cInfo->customers_referral,
                            ENT_COMPAT,
                            CHARSET,
                            true
                        ),
                        zen_set_field_length(
                            TABLE_CUSTOMERS,
                            'customers_referral',
                            15
                        ) . ' class="form-control" id="customers_referral"'
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-3">
                    <p class="control-label"><?php echo TEXT_CUSTOMER_GROUPS; ?></p>
                </div>
                <div class="col-sm-9 col-md-6">
                    <div class="row">
                        <div class="col-sm-4">
                            <input type="hidden" name="customer_groups[]" value="0">
<?php
    $groups_already_in = zen_groups_customer_belongs_to($cInfo->customers_id);
    foreach (zen_get_all_customer_groups() as $group) {
?>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="customer_groups[]" value="<?php
                                    echo $group['id']; ?>" <?php
                                    if (array_key_exists($group['id'], $groups_already_in)) {
                                        echo 'checked';
                                    } ?>>
                                    <?php
                                    echo $group['text']; ?>
                                </label>
                            </div>
<?php
    }
?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?>
        </div>
        <div class="row text-right">
            <button type="submit" class="btn btn-primary">
                <?php echo IMAGE_UPDATE; ?>
            </button>
            <a href="<?php
            echo zen_href_link(FILENAME_CUSTOMERS_SPAM, zen_get_all_get_params(['action'])); ?>"
               class="btn btn-default"><?php
                echo IMAGE_CANCEL; ?></a>
        </div>
        <?php echo '</form>'; ?>
<?php
} elseif ($action === 'list_addresses') {
?>
        <div class="row">
            <fieldset>
                <legend><?php
                    echo ADDRESS_BOOK_TITLE; ?></legend>
                <div class="alert forward"><?php
                    echo sprintf(TEXT_MAXIMUM_ENTRIES, MAX_ADDRESS_BOOK_ENTRIES); ?></div>
                <br class="clearBoth">
<?php
    /**
     * Used to loop thru and display address book entries
     */
    foreach ($addressArray as $addresses) {
?>
                    <h3 class="addressBookDefaultName"><?php
                        echo zen_output_string_protected(
                            $addresses['firstname'] . ' ' . $addresses['lastname']
                        );
                        echo ((int)$addresses['address_book_id'] === zen_get_customers_address_primary((int)$_GET['cID'])) ?
                            '&nbsp;' . PRIMARY_ADDRESS : ''; ?>
                    </h3>
                    <address><?php
                        echo zen_address_format(
                            $addresses['format_id'],
                            $addresses['address'],
                            true,
                            ' ',
                            '<br>'
                        ); ?>
                    </address>

                    <br class="clearBoth">
<?php
    }
?>
                    <div class="buttonRow forward">
                        <a href="<?php
                            echo zen_href_link(
                                FILENAME_CUSTOMERS_SPAM,
                                zen_get_all_get_params(['action']),
                                'NONSSL'
                            ); ?>" class="btn btn-default" role="button">
                            <?php echo IMAGE_BACK; ?>
                        </a>
                    </div>
                </fieldset>
            </div>
<?php
} else {
?>
        <div class="row text-right">
            <?php echo zen_draw_form('search', FILENAME_CUSTOMERS_SPAM, '', 'get', '', true); ?>
            <?php
// show reset search
            if (isset($_GET['search']) && zen_not_null($_GET['search'])) {
              echo '<a href="' . zen_href_link(FILENAME_CUSTOMERS_SPAM, '', 'NONSSL') . '" class="btn btn-default" role="button">' . IMAGE_RESET . '</a>&nbsp;&nbsp;';
            }
            echo HEADING_TITLE_SEARCH_DETAIL . ' ' . zen_draw_input_field('search') . zen_hide_session_id();
            if (isset($_GET['search']) && zen_not_null($_GET['search'])) {
              $keywords = zen_db_prepare_input($_GET['search']);
              echo '<br>' . TEXT_INFO_SEARCH_DETAIL_FILTER . zen_output_string_protected($keywords);
            }
            ?>
            <?php echo '</form>'; ?>
        </div>
        <?php
// Sort Listing
        switch ($_GET['list_order']) {
          case 'id-asc':
            $disp_order = "date_created";
            break;
          case 'firstname':
            $disp_order = "customers_firstname";
            break;
          case 'firstname-desc':
            $disp_order = "customers_firstname DESC";
            break;
          case 'lastname':
            $disp_order = "customers_lastname, customers_firstname";
            break;
          case 'lastname-desc':
            $disp_order = "customers_lastname DESC, customers_firstname";
            break;
          case 'ip':
            $disp_order = "registration_ip";
            break;
          case 'ip-desc':
            $disp_order = "registration_ip DESC";
            break;
          default:
            $disp_order = "customers_spam_id DESC";

        }
        ?>
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 configurationColumnLeft">
                    <table class="table table-hover" role="listbox">
              <thead>
                <tr class="dataTableHeadingRow">
                  <th class="dataTableHeadingContent text-right">
                      <?php echo TABLE_HEADING_ID; ?>
                  </th>
                   <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_EMAIL; ?></th>
                  <th class="dataTableHeadingContent">
                    <?php echo (($_GET['list_order'] == 'lastname' or $_GET['list_order'] == 'lastname-desc') ? '<span class="SortOrderHeader">' . TABLE_HEADING_LASTNAME . '</span>' : TABLE_HEADING_LASTNAME); ?><br>
                    <a href="<?php echo zen_href_link(basename($PHP_SELF), zen_get_all_get_params(array('list_order', 'page')) . 'list_order=lastname', 'NONSSL'); ?>"><?php echo ($_GET['list_order'] == 'lastname' ? '<span class="SortOrderHeader">Asc</span>' : '<span class="SortOrderHeaderLink">Asc</span>'); ?></a>&nbsp;
                    <a href="<?php echo zen_href_link(basename($PHP_SELF), zen_get_all_get_params(array('list_order', 'page')) . 'list_order=lastname-desc', 'NONSSL'); ?>"><?php echo ($_GET['list_order'] == 'lastname-desc' ? '<span class="SortOrderHeader">Desc</span>' : '<span class="SortOrderHeaderLink">Desc</span>'); ?></a>
                  </th>
                  <th class="dataTableHeadingContent">
                    <?php echo (($_GET['list_order'] == 'firstname' or $_GET['list_order'] == 'firstname-desc') ? '<span class="SortOrderHeader">' . TABLE_HEADING_FIRSTNAME . '</span>' : TABLE_HEADING_FIRSTNAME); ?><br>
                    <a href="<?php echo zen_href_link(basename($PHP_SELF), zen_get_all_get_params(array('list_order', 'page')) . 'list_order=firstname', 'NONSSL'); ?>"><?php echo ($_GET['list_order'] == 'firstname' ? '<span class="SortOrderHeader">Asc</span>' : '<span class="SortOrderHeaderLink">Asc</span>'); ?></a>&nbsp;
                    <a href="<?php echo zen_href_link(basename($PHP_SELF), zen_get_all_get_params(array('list_order', 'page')) . 'list_order=firstname-desc', 'NONSSL'); ?>"><?php echo ($_GET['list_order'] == 'firstname-desc' ? '<span class="SortOrderHeader">Desc</span>' : '<span class="SortOrderHeaderLink">Desc</span>'); ?></a>
                  </th>
                 
                   
                    <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_COUNTRY; ?></th>
                 <th class="dataTableHeadingContent">
                    <?php echo (($_GET['list_order'] == 'firstname' or $_GET['list_order'] == 'ip-desc') ? '<span class="SortOrderHeader">' . TABLE_HEADING_IP . '</span>' : TABLE_HEADING_IP); ?><br>
                    <a href="<?php echo zen_href_link(basename($PHP_SELF), zen_get_all_get_params(array('list_order', 'page')) . 'list_order=ip', 'NONSSL'); ?>"><?php echo ($_GET['list_order'] == 'ip' ? '<span class="SortOrderHeader">Asc</span>' : '<span class="SortOrderHeaderLink">Asc</span>'); ?></a>&nbsp;
                    <a href="<?php echo zen_href_link(basename($PHP_SELF), zen_get_all_get_params(array('list_order', 'page')) . 'list_order=ip-desc', 'NONSSL'); ?>"><?php echo ($_GET['list_order'] == 'ip-desc' ? '<span class="SortOrderHeader">Desc</span>' : '<span class="SortOrderHeaderLink">Desc</span>'); ?></a>
                  </th>
                   
                  <th class="dataTableHeadingContent">
                    <?php echo (($_GET['list_order'] == 'id-asc' or $_GET['list_order'] == 'id-desc') ? '<span class="SortOrderHeader">' . TABLE_HEADING_ACCOUNT_CREATED . '</span>' : TABLE_HEADING_ACCOUNT_CREATED); ?><br>
                    <a href="<?php echo zen_href_link(basename($PHP_SELF), zen_get_all_get_params(array('list_order', 'page')) . 'list_order=id-asc', 'NONSSL'); ?>"><?php echo ($_GET['list_order'] == 'id-asc' ? '<span class="SortOrderHeader">Asc</span>' : '<span class="SortOrderHeaderLink">Asc</span>'); ?></a>&nbsp;
                    <a href="<?php echo zen_href_link(basename($PHP_SELF), zen_get_all_get_params(array('list_order', 'page')) . 'list_order=id-desc', 'NONSSL'); ?>"><?php echo ($_GET['list_order'] == 'id-desc' ? '<span class="SortOrderHeader">Desc</span>' : '<span class="SortOrderHeaderLink">Desc</span>'); ?></a>
                  </th>

                    <th class="dataTableHeadingContent text-right"><?php echo TABLE_HEADING_ACTION; ?></th>
                </tr>
              </thead>
              <tbody>
                  <?php
                  $search = '';
                  if (isset($_GET['search']) && zen_not_null($_GET['search'])) {
                    $keywords = zen_db_input(zen_db_prepare_input($_GET['search']));
                    $parts = explode(" ", trim($keywords));
                    $search = 'where ';
                    foreach ($parts as $k => $v) {
                      $sql_add = " (customers_lastname LIKE '%:part%'
                         OR customers_firstname LIKE '%:part%'
                         OR customers_email_address LIKE '%:part%'
                         OR customers_telephone RLIKE '
                         :keywords:'
                         OR entry_company RLIKE ':keywords:'
                         OR entry_street_address RLIKE ':keywords:'
                         OR entry_city RLIKE ':keywords:'
                         OR entry_postcode RLIKE ':keywords:')";
                      if ($k != 0) {
                        $sql_add = ' AND ' . $sql_add;
                      }
                      $sql_add = $db->bindVars($sql_add, ':part', $v, 'noquotestring');
                      $sql_add = $db->bindVars($sql_add, ':keywords:', $v, 'regexp');
                      $search .= $sql_add;
                    }
                  }
    $new_fields = '';

    $zco_notifier->notify(
        'NOTIFY_ADMIN_CUSTOMERS_LISTING_NEW_FIELDS',
        [],
        $new_fields,
        $disp_order
    );

                  $customers_query_raw = "SELECT *
                                    FROM " . TABLE_CUSTOMERS_SPAM . " 
                                    " . $search . "
                                    ORDER BY " . $disp_order;



    // Split Page
// reset page when page is unknown
                  if (($_GET['page'] == '' || $_GET['page'] == '1') && !empty($_GET['cID'])) {
                    $check_page = $db->Execute($customers_query_raw);
                    $check_count = 1;
                    if ($check_page->RecordCount() > MAX_DISPLAY_SEARCH_RESULTS_CUSTOMER) {
                      foreach ($check_page as $item) {
                        if ($item['customers_spam_id'] == $_GET['cID']) {
                          break;
                        }
                        $check_count++;
                      }
                      $_GET['page'] = round((($check_count / MAX_DISPLAY_SEARCH_RESULTS_CUSTOMER) + (fmod_round($check_count, MAX_DISPLAY_SEARCH_RESULTS_CUSTOMER) != 0 ? .5 : 0)), 0);
//    zen_redirect(zen_href_link(FILENAME_CUSTOMERS_SPAM, 'cID=' . $_GET['cID'] . (isset($_GET['page']) ? '&page=' . $_GET['page'] : ''), 'NONSSL'));
                    } else {
                      $_GET['page'] = 1;
                    }
                  }

                  $customers_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS_CUSTOMER, $customers_query_raw, $customers_query_numrows);
                  $customers = $db->Execute($customers_query_raw);
    foreach ($customers as $customer) {
        
                    if ((!isset($_GET['cID']) || (isset($_GET['cID']) && ($_GET['cID'] == $customer['customers_spam_id']))) && !isset($cInfo)) {
                      $country = $db->Execute("SELECT countries_name
                                         FROM " . TABLE_COUNTRIES . "
                                         WHERE countries_id = " . (int)$customer['entry_country_id']);

                      $cInfo_array = array_merge($customer, $country->fields);
                      $cInfo = new objectInfo($cInfo_array);
                    }

                    if (isset($cInfo) && is_object($cInfo) && ($customer['customers_spam_id'] == $cInfo->customers_spam_id)) {
                      echo '          <tr id="defaultSelected" class="dataTableRowSelected" onclick="document.location.href=\'' . zen_href_link(FILENAME_CUSTOMERS_SPAM, zen_get_all_get_params(array('cID', 'action')) . 'cID=' . $cInfo->customers_spam_id . '&action=edit', 'NONSSL') . '\'" role="button">' . "\n";
                    } else {
                      echo '          <tr class="dataTableRow" onclick="document.location.href=\'' . zen_href_link(FILENAME_CUSTOMERS_SPAM, zen_get_all_get_params(array('cID', 'action')) . 'cID=' . $customer['customers_spam_id'], 'NONSSL') . '\'" role="button">' . "\n";
                    }

                    ?>
                                <td class="dataTableContent text-right"><?php echo $customer['customers_spam_id']; ?></td>
                                 <td class="dataTableContent"><?php echo $customer['customers_email_address']; ?></td>
                                <td class="dataTableContent"><?php echo $customer['customers_lastname']; ?></td>
                                <td class="dataTableContent"><?php echo $customer['customers_firstname']; ?></td>
				<td class="dataTableContent"><?php echo zen_get_country_name($customer['entry_country_id']); ?></td>
                                
                                <td class="dataTableContent"><?php echo $customer['registration_ip']; ?></td>
                                
				<td class="dataTableContent"><?php echo $customer['date_created']; ?></td>

				

                                <td class="dataTableContent text-right">
<?php
        if (isset($cInfo) && is_object($cInfo) && ($customer['customers_spam_id'] === (int)$cInfo->customers_spam_id)) {
?>
                                    <i class="fa-solid fa-caret-right fa-2x fa-fw txt-navy align-middle"></i>
<?php
        } else {
?>
                                    <a href="<?php
                                        echo zen_href_link(
                                            FILENAME_CUSTOMERS_SPAM,
                                                zen_get_all_get_params(['cID']) . 'cID=' . $customer['customers_spam_id'],
                                                'NONSSL'
                                        ); ?>" title="<?php echo IMAGE_ICON_INFO; ?>" role="button">
                                        <i class="fa-solid fa-circle-info fa-2x fa-fw txt-black align-middle"></i>
                                    </a>
<?php
        }
?>
                                </td>
                            </tr>
<?php
    }
?>
                        </tbody>
                    </table>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 configurationColumnRight">
<?php
    $heading = [];
    $contents = [];

    switch ($action) {
        case 'confirm':
            $heading[] = ['text' => '<h4>' . TEXT_INFO_HEADING_DELETE_CUSTOMER . '</h4>'];

            $contents = [
                'form' =>
                zen_draw_form(
                    'customers',
                    FILENAME_CUSTOMERS_SPAM,
                    zen_get_all_get_params(['cID', 'action', 'search']) . 'action=deleteconfirm',
                    'post',
                    '',
                    true
                ) .
                zen_draw_hidden_field('cID', $cInfo->customers_spam_id)
            ];
            $contents[] = [
                'text' =>
                    TEXT_DELETE_INTRO . '<br><br>' .
                    '<b>' . $cInfo->customers_firstname . ' ' . $cInfo->customers_lastname . '</b>'
            ];
            
            $contents[] = [
                'align' => 'text-center',
                'text' =>
                    '<br>' .
                   
                    '<button type="submit" name="delete_type_full" value="delete" class="btn btn-danger">' . IMAGE_DELETE . '</button>' .
                    ' ' .
                    '<a href="' .
                        zen_href_link(
                            FILENAME_CUSTOMERS_SPAM,
                            zen_get_all_get_params(['cID', 'action']) . 'cID=' . $cInfo->customers_spam_id,
                            'NONSSL'
                        ) . '" class="btn btn-default" role="button">' . IMAGE_CANCEL .
                    '</a>'
            ];
            break;
        default:
            if (isset($_GET['search'])) {
                $_GET['search'] = zen_output_string_protected($_GET['search']);
            }
            if (isset($cInfo) && is_object($cInfo)) {
                $heading[] = [
                    'text' =>
                        '<h4>' .
                            TABLE_HEADING_ID . $cInfo->customers_spam_id . ' ' .
                            $cInfo->customers_firstname . ' ' . $cInfo->customers_lastname .
                        '</h4>' .
                        '<br>' .
                        $cInfo->customers_email_address
                ];

                    $contents[] = array('align' => 'text-center', 'text' => '<a href="' . zen_href_link(FILENAME_CUSTOMERS_SPAM, zen_get_all_get_params(array('cID', 'action', 'search')) . 'cID=' . $cInfo->customers_spam_id . '&action=edit', 'NONSSL') . '" class="btn btn-primary" role="button">' . IMAGE_EDIT . '</a> <a href="' . zen_href_link(FILENAME_CUSTOMERS_SPAM, zen_get_all_get_params(array('cID', 'action', 'search')) . 'cID=' . $cInfo->customers_spam_id . '&action=confirm', 'NONSSL') . '" class="btn btn-warning" role="button">' . IMAGE_DELETE . '</a>');
                    $contents[] = array('align' => 'text-center', 'text' => '<a href="' . zen_href_link(FILENAME_CUSTOMERS_SPAM, zen_get_all_get_params(array('cID', 'action', 'search')) . 'cID=' . $cInfo->customers_spam_id . '&action=approve') . '" class="btn btn-success" role="button">' . IMAGE_APPROVE_ACCOUNT . '</a>');
                    $contents[] = array('align' => 'text-left', 'text' => '<strong>Grund für Spam Einstufung: </strong><br>'.$cInfo->reason);
                    $contents[] = array('align' => 'text-left', 'text' => '<br><strong>IP: </strong><br>'.$customer['registration_ip']);


                $zco_notifier->notify('NOTIFY_ADMIN_CUSTOMERS_SPAM_MENU_BUTTONS', $cInfo, $contents);

}
            break;
    }
    $zco_notifier->notify('NOTIFY_ADMIN_CUSTOMERS_MENU_BUTTONS_END', $cInfo ?? new stdClass, $contents);

    if (!empty($heading) && !empty($contents)) {
        $box = new box();
        echo $box->infoBox($heading, $contents);
    }
?>
                </div>
            </div>
            <div class="row">
                <table class="table">
                    <tr>
                        <td>
                            <?php echo $customers_split->display_count(
                                $customers_query_numrows,
                                MAX_DISPLAY_SEARCH_RESULTS_CUSTOMER,
                                $_GET['page'],
                                TEXT_DISPLAY_NUMBER_OF_CUSTOMERS
                            ); ?>
                        </td>
                        <td class="text-right">
                            <?php echo $customers_split->display_links(
                                $customers_query_numrows,
                                MAX_DISPLAY_SEARCH_RESULTS_CUSTOMER,
                                MAX_DISPLAY_PAGE_LINKS,
                                $_GET['page'],
                                zen_get_all_get_params(['page', 'info', 'x', 'y', 'cID'])
                            ); ?>
                        </td>
                    </tr>
<?php
    if (!empty($_GET['search'])) {
?>
                    <tr>
                        <td colspan="2" class="text-right">
                            <a href="<?php echo zen_href_link(FILENAME_CUSTOMERS_SPAM); ?>" class="btn btn-default" role="button">
                                <?php echo IMAGE_RESET; ?>
                            </a>
                        </td>
                    </tr>
<?php
    }
?>
                </table>
            </div>
<?php
}
?>
        <!-- body_text_eof //-->
    </div>
    <!-- body_eof //-->

    <!-- footer //-->
    <?php require DIR_WS_INCLUDES . 'footer.php'; ?>
    <!-- footer_eof //-->
    <script>
        $(function () {
            $("#loginform").submit(function (event) {
                $("#emp-timestamp").val(Date.now() / 1000);
            });
        });
    </script>
    </body>
    </html>
<?php
require DIR_WS_INCLUDES . 'application_bottom.php';
