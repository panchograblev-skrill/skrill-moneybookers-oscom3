<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Currencies\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class UpdateRate {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qupdate = $OSCOM_PDO->prepare('update :table_currencies set value = :value, last_updated = now() where currencies_id = :currencies_id');
      $Qupdate->bindValue(':value', $data['rate']);
      $Qupdate->bindInt(':currencies_id', $data['id']);
      $Qupdate->execute();

      return ( ($Qupdate->rowCount() === 1) || !$Qupdate->isError() );
    }
  }
?>
