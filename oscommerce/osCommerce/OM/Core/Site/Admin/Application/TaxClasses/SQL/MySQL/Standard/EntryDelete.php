<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\TaxClasses\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class EntryDelete {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qrate = $OSCOM_PDO->prepare('delete from :table_tax_rates where tax_rates_id = :tax_rates_id');
      $Qrate->bindInt(':tax_rates_id', $data['id']);
      $Qrate->execute();

      return ( $Qrate->rowCount() === 1 );
    }
  }
?>
