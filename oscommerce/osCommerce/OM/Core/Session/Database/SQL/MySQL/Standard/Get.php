<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Session\Database\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class Get {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $sql_query = 'select value from :table_sessions where id = :id';

      if ( isset($data['expiry']) ) {
        $sql_query .= ' and expiry >= :expiry';
      }

      $Qsession = $OSCOM_PDO->prepare($sql_query);
      $Qsession->bindValue(':id', $data['id']);

      if ( isset($data['expiry']) ) {
        $Qsession->bindInt(':expiry', $data['expiry']);
      }

      $Qsession->execute();

      return $Qsession->fetch();
    }
  }
?>
