<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Login\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class GetAdmin {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qadmin = $OSCOM_PDO->prepare('select id, user_name, user_password from :table_administrators where user_name = :user_name limit 1');
      $Qadmin->bindValue(':user_name', $data['username']);
      $Qadmin->execute();

      return $Qadmin->fetch();
    }
  }
?>
