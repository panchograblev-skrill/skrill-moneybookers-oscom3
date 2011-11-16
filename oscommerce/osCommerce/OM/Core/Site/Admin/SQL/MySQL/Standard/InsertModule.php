<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class InsertModule {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qinstall = $OSCOM_PDO->prepare('insert into :table_modules (title, code, author_name, author_www, modules_group) values (:title, :code, :author_name, :author_www, :modules_group)');
      $Qinstall->bindValue(':title', $data['title']);
      $Qinstall->bindValue(':code', $data['code']);
      $Qinstall->bindValue(':author_name', $data['author_name']);
      $Qinstall->bindValue(':author_www', $data['author_www']);
      $Qinstall->bindValue(':modules_group', $data['group']);
      $Qinstall->execute();

      return !$Qinstall->isError();
    }
  }
?>
