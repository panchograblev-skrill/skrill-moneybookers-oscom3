<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\ZoneGroups\Model;

  use osCommerce\OM\Core\OSCOM;

  class getEntry {
    public static function execute($id, $key = null) {
      $data = array('id' => $id);

      $result = OSCOM::callDB('Admin\ZoneGroups\EntryGet', $data);

      if ( empty($result['countries_name']) ) {
        $result['countries_name'] = OSCOM::getDef('all_countries');
      }

      if ( empty($result['zone_name']) ) {
        $result['zone_name'] = OSCOM::getDef('all_zones');
      }

      if ( isset($key) ) {
        $result = $result[$key] ?: null;
      }

      return $result;
    }
  }
?>
