<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Customers\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

/**
 * @since v3.0.2
 */

  class Save {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      if ( !isset($data['id']) ) {
        $data['id'] = null;
      }

      $error = false;

      $OSCOM_PDO->beginTransaction();

      if ( is_numeric($data['id']) ) {
        $Qcustomer = $OSCOM_PDO->prepare('update :table_customers set customers_gender = :customers_gender, customers_firstname = :customers_firstname, customers_lastname = :customers_lastname, customers_email_address = :customers_email_address, customers_dob = :customers_dob, customers_newsletter = :customers_newsletter, customers_status = :customers_status, date_account_last_modified = now() where customers_id = :customers_id');
        $Qcustomer->bindInt(':customers_id', $data['id']);
      } else {
        $Qcustomer = $OSCOM_PDO->prepare('insert into :table_customers (customers_gender, customers_firstname, customers_lastname, customers_email_address, customers_dob, customers_newsletter, customers_status, number_of_logons, date_account_created) values (:customers_gender, :customers_firstname, :customers_lastname, :customers_email_address, :customers_dob, :customers_newsletter, :customers_status, :number_of_logons, now())');
        $Qcustomer->bindInt(':number_of_logons', 0);
      }

      $Qcustomer->bindValue(':customers_gender', $data['gender']);
      $Qcustomer->bindValue(':customers_firstname', $data['firstname']);
      $Qcustomer->bindValue(':customers_lastname', $data['lastname']);
      $Qcustomer->bindValue(':customers_email_address', $data['email_address']);
      $Qcustomer->bindValue(':customers_dob', $data['dob_year'] . '-' . $data['dob_month'] . '-' . $data['dob_day'] . ' 00:00:00');
      $Qcustomer->bindInt(':customers_newsletter', $data['newsletter']);
      $Qcustomer->bindInt(':customers_status', $data['status']);
      $Qcustomer->execute();

      if ( !$Qcustomer->isError() ) {
        if ( !empty($data['password']) ) {
          $customer_id = ( is_numeric($data['id']) ) ? $data['id'] : $OSCOM_PDO->lastInsertId();

          $Qpassword = $OSCOM_PDO->prepare('update :table_customers set customers_password = :customers_password where customers_id = :customers_id');
          $Qpassword->bindValue(':customers_password', $data['password']);
          $Qpassword->bindInt(':customers_id', $customer_id);
          $Qpassword->execute();

          if ( $Qpassword->isError() ) {
            $error = true;
          }
        }
      }

      if ( $error === false ) {
        $OSCOM_PDO->commit();

        return true;
      }

      $OSCOM_PDO->rollBack();

      return false;
    }
  }
?>
