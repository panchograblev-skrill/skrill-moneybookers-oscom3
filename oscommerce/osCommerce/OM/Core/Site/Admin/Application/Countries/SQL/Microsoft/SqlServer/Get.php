<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Countries\SQL\Microsoft\SqlServer;

  use osCommerce\OM\Core\Registry;

  class Get {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Q = $OSCOM_PDO->prepare('EXEC CountriesGet :countries_id');
      $Q->bindInt(':countries_id', $data['id']);
      $Q->execute();

      $result_1 = $Q->toArray();

      $Q->nextResultSet();

      $result_2 = $Q->toArray();

      return array_merge($result_1, $result_2);
    }
  }
?>
