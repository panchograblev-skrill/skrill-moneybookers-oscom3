<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Dashboard\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class DeleteShortcut {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qsc = $OSCOM_PDO->prepare('delete from :table_administrator_shortcuts where administrators_id = :administrators_id and module = :module');
      $Qsc->bindInt(':administrators_id', $data['admin_id']);
      $Qsc->bindValue(':module', $data['application']);
      $Qsc->execute();

      return ( ($Qsc->rowCount() === 1) || !$Qsc->isError() );
    }
  }
?>
