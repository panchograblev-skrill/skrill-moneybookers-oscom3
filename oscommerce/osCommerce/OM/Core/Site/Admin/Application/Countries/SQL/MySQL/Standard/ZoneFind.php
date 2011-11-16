<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Countries\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class ZoneFind {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $result = array();

      $Qzones = $OSCOM_PDO->prepare('select * from :table_zones where zone_country_id = :zone_country_id and (zone_name like :zone_name or zone_code like :zone_code) order by zone_name');
      $Qzones->bindInt(':zone_country_id', $data['country_id']);
      $Qzones->bindValue(':zone_name', '%' . $data['keywords'] . '%');
      $Qzones->bindValue(':zone_code', '%' . $data['keywords'] . '%');
      $Qzones->execute();

      $result['entries'] = $Qzones->fetchAll();

      $result['total'] = count($result['entries']);

      return $result;
    }
  }
?>
