<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\ZoneGroups\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class EntryDelete {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qentry = $OSCOM_PDO->prepare('delete from :table_zones_to_geo_zones where association_id = :association_id');
      $Qentry->bindInt(':association_id', $data['id']);
      $Qentry->execute();

      return ( $Qentry->rowCount() === 1 );
    }
  }
?>
