<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class DeleteModule {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qdel = $OSCOM_PDO->prepare('delete from :table_modules where code = :code and modules_group = :modules_group');
      $Qdel->bindValue(':code', $data['code']);
      $Qdel->bindValue(':modules_group', $data['group']);
      $Qdel->execute();

      return !$Qdel->isError();
    }
  }
?>
