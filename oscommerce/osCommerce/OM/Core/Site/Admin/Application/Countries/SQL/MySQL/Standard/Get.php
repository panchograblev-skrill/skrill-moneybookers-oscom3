<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Countries\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class Get {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qcountries = $OSCOM_PDO->prepare('select c.*, count(z.zone_id) as total_zones2 from :table_countries c left join :table_zones z on (c.countries_id = z.zone_country_id) where c.countries_id = :countries_id');
      $Qcountries->bindInt(':countries_id', $data['id']);
      $Qcountries->execute();

      return $Qcountries->fetch();
    }
  }
?>
