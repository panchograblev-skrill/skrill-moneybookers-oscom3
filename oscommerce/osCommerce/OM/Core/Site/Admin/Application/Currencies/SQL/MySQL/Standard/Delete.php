<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Currencies\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class Delete {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qcheck = $OSCOM_PDO->prepare('select code from :table_currencies where currencies_id = :currencies_id');
      $Qcheck->bindInt(':currencies_id', $data['id']);
      $Qcheck->execute();

      if ( $Qcheck->value('code') != DEFAULT_CURRENCY ) {
        $Qdelete = $OSCOM_PDO->prepare('delete from :table_currencies where currencies_id = :currencies_id');
        $Qdelete->bindInt(':currencies_id', $data['id']);
        $Qdelete->execute();

        return ( $Qdelete->rowCount() === 1 );
      }

      return false;
    }
  }
?>
