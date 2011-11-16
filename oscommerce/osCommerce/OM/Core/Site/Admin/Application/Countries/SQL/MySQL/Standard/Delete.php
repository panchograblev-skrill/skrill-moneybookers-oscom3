<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Countries\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class Delete {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qcountry = $OSCOM_PDO->prepare('delete from :table_countries where countries_id = :countries_id');
      $Qcountry->bindInt(':countries_id', $data['id']);
      $Qcountry->execute();

      return ( $Qcountry->rowCount() === 1 );
    }
  }
?>
