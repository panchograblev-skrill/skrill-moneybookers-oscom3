<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Administrators\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class Get {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qadmin = $OSCOM_PDO->prepare('select * from :table_administrators where id = :id');
      $Qadmin->bindInt(':id', $data['id']);
      $Qadmin->execute();

      $result = $Qadmin->fetch();

      $result['access_modules'] = array();

      $Qaccess = $OSCOM_PDO->prepare('select module from :table_administrators_access where administrators_id = :administrators_id');
      $Qaccess->bindInt(':administrators_id', $data['id']);
      $Qaccess->execute();

      while ( $row = $Qaccess->fetch() ) {
        $result['access_modules'][] = $row['module'];
      }

      return $result;
    }
  }
?>
