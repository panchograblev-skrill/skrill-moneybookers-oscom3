<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class GetAccessUserShortcuts {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qshortcuts = $OSCOM_PDO->prepare('select module from :table_administrator_shortcuts where administrators_id = :administrators_id');
      $Qshortcuts->bindInt(':administrators_id', $data['id']);
      $Qshortcuts->execute();

      return $Qshortcuts->fetchAll();
    }
  }
?>
