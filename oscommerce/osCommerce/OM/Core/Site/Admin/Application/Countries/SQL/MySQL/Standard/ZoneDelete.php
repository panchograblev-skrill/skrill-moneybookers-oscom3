<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Countries\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class ZoneDelete {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qzone = $OSCOM_PDO->prepare('delete from :table_zones where zone_id = :zone_id');
      $Qzone->bindInt(':zone_id', $data['id']);
      $Qzone->execute();

      return ( $Qzone->rowCount() === 1 );
    }
  }
?>
