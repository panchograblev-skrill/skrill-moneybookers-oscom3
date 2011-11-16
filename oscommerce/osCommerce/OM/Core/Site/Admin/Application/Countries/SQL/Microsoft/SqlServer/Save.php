<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Countries\SQL\Microsoft\SqlServer;

  use osCommerce\OM\Core\Registry;

  class Save {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      if ( isset($data['id']) ) {
        $Q = $OSCOM_PDO->prepare('update :table_countries set countries_name = :countries_name, countries_iso_code_2 = :countries_iso_code_2, countries_iso_code_3 = :countries_iso_code_3, address_format = :address_format where countries_id = :countries_id');
        $Q->bindInt(':countries_id', $data['id']);
      } else {
        $Q = $OSCOM_PDO->prepare('insert into :table_countries (countries_name, countries_iso_code_2, countries_iso_code_3, address_format) values (:countries_name, :countries_iso_code_2, :countries_iso_code_3, :address_format)');
      }

      $Q->bindValue(':countries_name', $data['name']);
      $Q->bindValue(':countries_iso_code_2', $data['iso_code_2']);
      $Q->bindValue(':countries_iso_code_3', $data['iso_code_3']);
      $Q->bindValue(':address_format', $data['address_format']);
      $Q->execute();

      return !$Q->isError();
    }
  }
?>
