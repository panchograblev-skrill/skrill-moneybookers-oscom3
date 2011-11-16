<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Countries\SQL\Microsoft\SqlServer;

  use osCommerce\OM\Core\Registry;

  class Delete {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Q = $OSCOM_PDO->prepare('delete from :table_countries where countries_id = :countries_id');
      $Q->bindInt(':countries_id', $data['id']);
      $Q->execute();

      return !$Q->isError();
    }
  }
?>
