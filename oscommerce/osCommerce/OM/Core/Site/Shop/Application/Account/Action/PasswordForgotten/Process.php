<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Application\Account\Action\PasswordForgotten;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Hash;
  use osCommerce\OM\Core\Mail;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Shop\Account;

  class Process {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_MessageStack = Registry::get('MessageStack');

      $Qcheck = $OSCOM_PDO->prepare('select customers_id, customers_firstname, customers_lastname, customers_gender, customers_email_address, customers_password from :table_customers where customers_email_address = :customers_email_address limit 1');
      $Qcheck->bindValue(':customers_email_address', $_POST['email_address']);
      $Qcheck->execute();

      if ( $Qcheck->fetch() !== false ) {
        $password = Hash::getRandomString(ACCOUNT_PASSWORD);

        if ( Account::savePassword($password, $Qcheck->valueInt('customers_id')) ) {
          if ( ACCOUNT_GENDER > -1 ) {
             if ( $Qcheck->value('customers_gender') == 'm' ) {
               $email_text = sprintf(OSCOM::getDef('email_addressing_gender_male'), $Qcheck->valueProtected('customers_lastname')) . "\n\n";
             } else {
               $email_text = sprintf(OSCOM::getDef('email_addressing_gender_female'), $Qcheck->valueProtected('customers_lastname')) . "\n\n";
             }
          } else {
            $email_text = sprintf(OSCOM::getDef('email_addressing_gender_unknown'), $Qcheck->valueProtected('customers_firstname') . ' ' . $Qcheck->valueProtected('customers_lastname')) . "\n\n";
          }

          $email_text .= sprintf(OSCOM::getDef('email_password_reminder_body'), OSCOM::getIPAddress(), STORE_NAME, $password, STORE_OWNER_EMAIL_ADDRESS);

          $pEmail = new Mail($Qcheck->valueProtected('customers_firstname') . ' ' . $Qcheck->valueProtected('customers_lastname'), $Qcheck->valueProtected('customers_email_address'), STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, sprintf(OSCOM::getDef('email_password_reminder_subject'), STORE_NAME));
          $pEmail->setBodyPlain($email_text);
          $pEmail->send();

          $OSCOM_MessageStack->add('LogIn', OSCOM::getDef('success_password_forgotten_sent'), 'success');
        }

        OSCOM::redirect(OSCOM::getLink(null, null, 'LogIn', 'SSL'));
      } else {
        $OSCOM_MessageStack->add('PasswordForgotten', OSCOM::getDef('error_password_forgotten_no_email_address_found'));
      }
    }
  }
?>
