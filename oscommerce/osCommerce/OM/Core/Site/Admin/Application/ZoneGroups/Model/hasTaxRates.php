<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\ZoneGroups\Model;

  use osCommerce\OM\Core\OSCOM;

  class hasTaxRates {
    public static function execute($tax_zone_id) {
      $data = array('tax_zone_id' => $tax_zone_id);

      $result = OSCOM::callDB('Admin\ZoneGroups\GetTotalTaxRates', $data);

      return $result > 0;
    }
  }
?>
