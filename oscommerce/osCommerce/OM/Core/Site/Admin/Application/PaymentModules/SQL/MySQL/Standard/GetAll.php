<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\PaymentModules\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class GetAll {
    public static function execute() {
      $OSCOM_PDO = Registry::get('PDO');

      $result = array();

      $Qpm = $OSCOM_PDO->prepare('select code from :table_modules where modules_group = :modules_group order by code');
      $Qpm->bindValue(':modules_group', 'Payment');
      $Qpm->execute();

      $result['entries'] = $Qpm->fetchAll();

      $result['total'] = count($result['entries']);

      return $result;
    }
  }
?>
