<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Session\Database\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class Save {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qsession = $OSCOM_PDO->prepare('replace into :table_sessions values (:id, :expiry, :value)');
      $Qsession->bindValue(':id', $data['id']);
      $Qsession->bindInt(':expiry', $data['expiry']);
      $Qsession->bindValue(':value', $data['value']);
      $Qsession->execute();

      return ( ($Qsession->rowCount() === 1) || !$Qsession->isError() );
    }
  }
?>
