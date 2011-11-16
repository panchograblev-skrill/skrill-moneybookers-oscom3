<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\ZoneGroups\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class GetTotalTaxRates {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qtotal = $OSCOM_PDO->prepare('select count(*) as total from :table_tax_rates where tax_zone_id = :tax_zone_id');
      $Qtotal->bindInt(':tax_zone_id', $data['tax_zone_id']);
      $Qtotal->execute();

      $result = $Qtotal->fetch();

      return $result['total'];
    }
  }
?>
