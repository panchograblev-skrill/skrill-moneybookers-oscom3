<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Countries\Model;

  use osCommerce\OM\Core\OSCOM;

  class getAllZones {
    public static function execute($country_id) {
      $data = array('country_id' => $country_id);

      return OSCOM::callDB('Admin\Countries\ZoneGetAll', $data);
    }
  }
?>
