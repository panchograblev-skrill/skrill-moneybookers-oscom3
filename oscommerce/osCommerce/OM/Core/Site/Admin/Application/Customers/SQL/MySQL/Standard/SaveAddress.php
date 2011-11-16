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

  class SaveAddress {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      if ( !isset($data['id']) ) {
        $data['id'] = null;
      }

      $error = false;

      $OSCOM_PDO->beginTransaction();

      if ( isset($data['id']) && is_numeric($data['id']) ) {
        $Qab = $OSCOM_PDO->prepare('update :table_address_book set entry_gender = :entry_gender, entry_company = :entry_company, entry_firstname = :entry_firstname, entry_lastname = :entry_lastname, entry_street_address = :entry_street_address, entry_suburb = :entry_suburb, entry_postcode = :entry_postcode, entry_city = :entry_city, entry_state = :entry_state, entry_country_id = :entry_country_id, entry_zone_id = :entry_zone_id, entry_telephone = :entry_telephone, entry_fax = :entry_fax where address_book_id = :address_book_id and customers_id = :customers_id');
        $Qab->bindInt(':address_book_id', $data['id']);
      } else {
        $Qab = $OSCOM_PDO->prepare('insert into :table_address_book (customers_id, entry_gender, entry_company, entry_firstname, entry_lastname, entry_street_address, entry_suburb, entry_postcode, entry_city, entry_state, entry_country_id, entry_zone_id, entry_telephone, entry_fax) values (:customers_id, :entry_gender, :entry_company, :entry_firstname, :entry_lastname, :entry_street_address, :entry_suburb, :entry_postcode, :entry_city, :entry_state, :entry_country_id, :entry_zone_id, :entry_telephone, :entry_fax)');
      }

      $Qab->bindInt(':customers_id', $data['customer_id']);
      $Qab->bindValue(':entry_gender', $data['gender']);
      $Qab->bindValue(':entry_company', $data['company']);
      $Qab->bindValue(':entry_firstname', $data['firstname']);
      $Qab->bindValue(':entry_lastname', $data['lastname']);
      $Qab->bindValue(':entry_street_address', $data['street_address']);
      $Qab->bindValue(':entry_suburb', $data['suburb']);
      $Qab->bindValue(':entry_postcode', $data['postcode']);
      $Qab->bindValue(':entry_city', $data['city']);
      $Qab->bindValue(':entry_state', $data['state']);
      $Qab->bindInt(':entry_country_id', $data['country_id']);

      if ( is_numeric($data['zone_id']) ) {
        $Qab->bindInt(':entry_zone_id', $data['zone_id']);
      } else {
        $Qab->bindNull(':entry_zone_id');
      }

      $Qab->bindValue(':entry_telephone', $data['telephone']);
      $Qab->bindValue(':entry_fax', $data['fax']);
      $Qab->execute();

      if ( !$Qab->isError() ) {
        if ( isset($data['default']) && ($data['default'] === true) ) {
          $address_book_id = ( isset($data['id']) && is_numeric($data['id']) ) ? $data['id'] : $OSCOM_PDO->lastInsertId();

          $Qupdate = $OSCOM_PDO->prepare('update :table_customers set customers_default_address_id = :customers_default_address_id where customers_id = :customers_id');
          $Qupdate->bindInt(':customers_default_address_id', $address_book_id);
          $Qupdate->bindInt(':customers_id', $data['customer_id']);
          $Qupdate->execute();

          if ( $Qupdate->isError() ) {
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
