<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop;

  use osCommerce\OM\Core\Hash;
  use osCommerce\OM\Core\Mail;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

/**
 * The Account class manages customer accounts
 */

  class Account {

/**
 * Returns the account information for the current customer
 *
 * @access public
 * @return object
 */

    public static function getEntry() {
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_Customer = Registry::get('Customer');

      $Qaccount = $OSCOM_PDO->prepare('select customers_gender, customers_firstname, customers_lastname, date_format(customers_dob, "%Y") as customers_dob_year, date_format(customers_dob, "%m") as customers_dob_month, date_format(customers_dob, "%d") as customers_dob_date, customers_email_address from :table_customers where customers_id = :customers_id');
      $Qaccount->bindInt(':customers_id', $OSCOM_Customer->getID());
      $Qaccount->execute();

      return $Qaccount;
    }

/**
 * Returns the customer ID from a given email address
 *
 * @param string $email_address The customers email address
 * @access public
 */

    public static function getID($email_address) {
      $OSCOM_PDO = Registry::get('PDO');

      $Quser = $OSCOM_PDO->prepare('select customers_id from :table_customers where customers_email_address = :customers_email_address limit 1');
      $Quser->bindValue(':customers_email_address', $email_address);
      $Quser->execute();

      $result = $Quser->fetch();

      if ( $result !== false ) {
        return $result['customers_id'];
      }

      return false;
    }

/**
 * Stores a new customer account entry in the database
 *
 * @param array $data An array containing the customers information
 * @access public
 * @return boolean
 */

    public static function createEntry($data) {
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_Session = Registry::get('Session');
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');
      $OSCOM_NavigationHistory = Registry::get('NavigationHistory');

      $Qcustomer = $OSCOM_PDO->prepare('insert into :table_customers (customers_firstname, customers_lastname, customers_email_address, customers_newsletter, customers_status, customers_ip_address, customers_password, customers_gender, customers_dob, number_of_logons, date_account_created) values (:customers_firstname, :customers_lastname, :customers_email_address, :customers_newsletter, :customers_status, :customers_ip_address, :customers_password, :customers_gender, :customers_dob, :number_of_logons, now())');
      $Qcustomer->bindValue(':customers_firstname', $data['firstname']);
      $Qcustomer->bindValue(':customers_lastname', $data['lastname']);
      $Qcustomer->bindValue(':customers_email_address', $data['email_address']);
      $Qcustomer->bindValue(':customers_newsletter', (isset($data['newsletter']) && ($data['newsletter'] == '1') ? '1' : ''));
      $Qcustomer->bindValue(':customers_status', '1');
      $Qcustomer->bindValue(':customers_ip_address', OSCOM::getIPAddress());
      $Qcustomer->bindValue(':customers_password', Hash::get($data['password']));
      $Qcustomer->bindValue(':customers_gender', (((ACCOUNT_GENDER > -1) && isset($data['gender']) && (($data['gender'] == 'm') || ($data['gender'] == 'f'))) ? $data['gender'] : ''));
      $Qcustomer->bindValue(':customers_dob', ((ACCOUNT_DATE_OF_BIRTH == '1') ? date('Ymd', $data['dob']) : ''));
      $Qcustomer->bindInt(':number_of_logons', 0);
      $Qcustomer->execute();

      if ( $Qcustomer->rowCount() === 1 ) {
        $customer_id = $OSCOM_PDO->lastInsertId();

        if ( SERVICE_SESSION_REGENERATE_ID == '1' ) {
          $OSCOM_Session->recreate();
        }

        $OSCOM_Customer->setCustomerData($customer_id);

// restore cart contents
        $OSCOM_ShoppingCart->synchronizeWithDatabase();

        $OSCOM_NavigationHistory->removeCurrentPage();

// build the welcome email content
        if ( (ACCOUNT_GENDER > -1) && isset($data['gender']) ) {
           if ( $data['gender'] == 'm' ) {
             $email_text = sprintf(OSCOM::getDef('email_addressing_gender_male'), $OSCOM_Customer->getLastName()) . "\n\n";
           } else {
             $email_text = sprintf(OSCOM::getDef('email_addressing_gender_female'), $OSCOM_Customer->getLastName()) . "\n\n";
           }
        } else {
          $email_text = sprintf(OSCOM::getDef('email_addressing_gender_unknown'), $OSCOM_Customer->getName()) . "\n\n";
        }

        $email_text .= sprintf(OSCOM::getDef('email_create_account_body'), STORE_NAME, STORE_OWNER_EMAIL_ADDRESS);

        $c_email = new Mail($OSCOM_Customer->getName(), $OSCOM_Customer->getEmailAddress(), STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, sprintf(OSCOM::getDef('email_create_account_subject'), STORE_NAME));
        $c_email->setBodyPlain($email_text);
        $c_email->send();

        return true;
      }

      return false;
    }

/**
 * Update the current customer account record in the database
 *
 * @param array $data An array containing the customer account information
 * @access public
 * @return boolean
 */

    public static function saveEntry($data) {
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_Customer = Registry::get('Customer');

      $Qcustomer = $OSCOM_PDO->prepare('update :table_customers set customers_gender = :customers_gender, customers_firstname = :customers_firstname, customers_lastname = :customers_lastname, customers_email_address = :customers_email_address, customers_dob = :customers_dob, date_account_last_modified = now() where customers_id = :customers_id');
      $Qcustomer->bindValue(':customers_gender', ((ACCOUNT_GENDER > -1) && isset($data['gender']) && (($data['gender'] == 'm') || ($data['gender'] == 'f'))) ? $data['gender'] : '');
      $Qcustomer->bindValue(':customers_firstname', $data['firstname']);
      $Qcustomer->bindValue(':customers_lastname', $data['lastname']);
      $Qcustomer->bindValue(':customers_email_address', $data['email_address']);
      $Qcustomer->bindValue(':customers_dob', (ACCOUNT_DATE_OF_BIRTH == '1') ? date('Ymd', $data['dob']) : '');
      $Qcustomer->bindInt(':customers_id', $OSCOM_Customer->getID());
      $Qcustomer->execute();

      return ( $Qcustomer->rowCount() === 1 );
    }

/**
 * Updates the password in a customers account
 *
 * @param string $password The new password
 * @param integer $customer_id The ID of the customer account to update
 * @access public
 * @return boolean
 */

    public static function savePassword($password, $customer_id = null) {
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_Customer = Registry::get('Customer');

      if ( !is_numeric($customer_id) ) {
        $customer_id = $OSCOM_Customer->getID();
      }

      $Qcustomer = $OSCOM_PDO->prepare('update :table_customers set customers_password = :customers_password, date_account_last_modified = now() where customers_id = :customers_id');
      $Qcustomer->bindValue(':customers_password', Hash::get($password));
      $Qcustomer->bindInt(':customers_id', $customer_id);
      $Qcustomer->execute();

      return ( $Qcustomer->rowCount() === 1 );
    }

/**
 * Checks if a customer account record exists with the provided e-mail address
 *
 * @param string $email_address The e-mail address to check for
 * @access public
 * @return boolean
 */

    public static function checkEntry($email_address) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qcheck = $OSCOM_PDO->prepare('select customers_id from :table_customers where customers_email_address = :customers_email_address limit 1');
      $Qcheck->bindValue(':customers_email_address', $email_address);
      $Qcheck->execute();

      return ( $Qcheck->fetch() !== false );
    }

/**
 * Checks if a password matches the current or provided customer account
 *
 * @param string $password The unencrypted password to confirm
 * @param string $email_address The email address of the customer account to check against
 * @access public
 * @return boolean
 */

    public static function checkPassword($password, $email_address = null) {
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_Customer = Registry::get('Customer');

      if ( empty($email_address) ) {
        $Qcheck = $OSCOM_PDO->prepare('select customers_password from :table_customers where customers_id = :customers_id');
        $Qcheck->bindInt(':customers_id', $OSCOM_Customer->getID());
        $Qcheck->execute();
      } else {
        $Qcheck = $OSCOM_PDO->prepare('select customers_password from :table_customers where customers_email_address = :customers_email_address limit 1');
        $Qcheck->bindValue(':customers_email_address', $email_address);
        $Qcheck->execute();
      }

      $result = $Qcheck->fetch();

      if ( $result !== false ) {
        return Hash::validate($password, $Qcheck->value('customers_password'));
      }

      return false;
    }

/**
 * Checks if an e-mail address already exists in another customer account record
 *
 * @param string $email_address The e-mail address to check
 * @access public
 * @return boolean
 */

    public static function checkDuplicateEntry($email_address) {
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_Customer = Registry::get('Customer');

      $Qcheck = $OSCOM_PDO->prepare('select customers_id from :table_customers where customers_email_address = :customers_email_address and customers_id != :customers_id limit 1');
      $Qcheck->bindValue(':customers_email_address', $email_address);
      $Qcheck->bindInt(':customers_id', $OSCOM_Customer->getID());
      $Qcheck->execute();

      return ( $Qcheck->fetch() !== false );
    }

/**
 * Perform a login
 *
 * @param string $email_address The e-mail address to login with
 * @param string $password The password to verify the account with
 * @access public
 * @return boolean
 */

    public static function logIn($email_address, $password) {
      $OSCOM_Session = Registry::get('Session');
      $OSCOM_Customer= Registry::get('Customer');
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');

      if ( self::checkEntry($email_address) && self::checkPassword($password, $email_address) ) {
        if ( SERVICE_SESSION_REGENERATE_ID == '1' ) {
          $OSCOM_Session->recreate();
        }

        $OSCOM_Customer->setCustomerData(self::getID($email_address));

        $Qupdate = $OSCOM_PDO->prepare('update :table_customers set date_last_logon = now(), number_of_logons = number_of_logons+1 where customers_id = :customers_id');
        $Qupdate->bindInt(':customers_id', $OSCOM_Customer->getID());
        $Qupdate->execute();

        $OSCOM_ShoppingCart->synchronizeWithDatabase();

        return true;
      }

      return false;
    }
  }
?>
