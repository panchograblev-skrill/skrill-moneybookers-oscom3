<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Application\Account\Action\Create;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Shop\Account;

  class Process {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_MessageStack = Registry::get('MessageStack');

      $data = array();

      if ( DISPLAY_PRIVACY_CONDITIONS == '1' ) {
        if ( (isset($_POST['privacy_conditions']) === false) || (isset($_POST['privacy_conditions']) && ($_POST['privacy_conditions'] != '1')) ) {
          $OSCOM_MessageStack->add('Create', OSCOM::getDef('error_privacy_statement_not_accepted'));
        }
      }

      if ( ACCOUNT_GENDER >= 0 ) {
        if ( isset($_POST['gender']) && (($_POST['gender'] == 'm') || ($_POST['gender'] == 'f')) ) {
          $data['gender'] = $_POST['gender'];
        } else {
          $OSCOM_MessageStack->add('Create', OSCOM::getDef('field_customer_gender_error'));
        }
      }

      if ( isset($_POST['firstname']) && (strlen(trim($_POST['firstname'])) >= ACCOUNT_FIRST_NAME) ) {
        $data['firstname'] = $_POST['firstname'];
      } else {
        $OSCOM_MessageStack->add('Create', sprintf(OSCOM::getDef('field_customer_first_name_error'), ACCOUNT_FIRST_NAME));
      }

      if ( isset($_POST['lastname']) && (strlen(trim($_POST['lastname'])) >= ACCOUNT_LAST_NAME) ) {
        $data['lastname'] = $_POST['lastname'];
      } else {
        $OSCOM_MessageStack->add('Create', sprintf(OSCOM::getDef('field_customer_last_name_error'), ACCOUNT_LAST_NAME));
      }

      if ( ACCOUNT_DATE_OF_BIRTH == '1' ) {
        if ( isset($_POST['dob_days']) && isset($_POST['dob_months']) && isset($_POST['dob_years']) && checkdate($_POST['dob_months'], $_POST['dob_days'], $_POST['dob_years']) ) {
          $data['dob'] = mktime(0, 0, 0, $_POST['dob_months'], $_POST['dob_days'], $_POST['dob_years']);
        } else {
          $OSCOM_MessageStack->add('Create', OSCOM::getDef('field_customer_date_of_birth_error'));
        }
      }

      if ( isset($_POST['email_address']) && (strlen(trim($_POST['email_address'])) >= ACCOUNT_EMAIL_ADDRESS) ) {
        if ( filter_var($_POST['email_address'], FILTER_VALIDATE_EMAIL) ) {
          if ( Account::checkEntry($_POST['email_address']) === false ) {
            $data['email_address'] = $_POST['email_address'];
          } else {
            $OSCOM_MessageStack->add('Create', OSCOM::getDef('field_customer_email_address_exists_error'));
          }
        } else {
          $OSCOM_MessageStack->add('Create', OSCOM::getDef('field_customer_email_address_check_error'));
        }
      } else {
        $OSCOM_MessageStack->add('Create', sprintf(OSCOM::getDef('field_customer_email_address_error'), ACCOUNT_EMAIL_ADDRESS));
      }

      if ( (isset($_POST['password']) === false) || (isset($_POST['password']) && (strlen(trim($_POST['password'])) < ACCOUNT_PASSWORD)) ) {
        $OSCOM_MessageStack->add('Create', sprintf(OSCOM::getDef('field_customer_password_error'), ACCOUNT_PASSWORD));
      } elseif ( (isset($_POST['confirmation']) === false) || (isset($_POST['confirmation']) && (trim($_POST['password']) != trim($_POST['confirmation']))) ) {
        $OSCOM_MessageStack->add('Create', OSCOM::getDef('field_customer_password_mismatch_with_confirmation'));
      } else {
        $data['password'] = $_POST['password'];
      }

      if ( $OSCOM_MessageStack->size('Create') === 0 ) {
        if ( Account::createEntry($data) ) {
          $OSCOM_MessageStack->add('Create', OSCOM::getDef('success_account_updated'), 'success');
        }

        OSCOM::redirect(OSCOM::getLink(null, null, 'Create&Success', 'SSL'));
      }
    }
  }
?>
