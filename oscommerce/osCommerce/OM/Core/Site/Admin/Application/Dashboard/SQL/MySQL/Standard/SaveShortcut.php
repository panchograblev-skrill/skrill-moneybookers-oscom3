<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Dashboard\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class SaveShortcut {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qsc = $OSCOM_PDO->prepare('insert into :table_administrator_shortcuts (administrators_id, module, last_viewed) values (:administrators_id, :module, null)');
      $Qsc->bindInt(':administrators_id', $data['admin_id']);
      $Qsc->bindValue(':module', $data['application']);
      $Qsc->execute();

      return ( ($Qsc->rowCount() === 1) || !$Qsc->isError() );
    }
  }
?>
