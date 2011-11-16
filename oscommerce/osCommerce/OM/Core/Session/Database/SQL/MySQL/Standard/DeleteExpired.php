<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Session\Database\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class DeleteExpired {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qsession = $OSCOM_PDO->prepare('delete from :table_sessions where expiry < :expiry');
      $Qsession->bindInt(':expiry', $data['expiry']);
      $Qsession->execute();

      return ( $Qsession->rowCount() > 0 );
    }
  }
?>
