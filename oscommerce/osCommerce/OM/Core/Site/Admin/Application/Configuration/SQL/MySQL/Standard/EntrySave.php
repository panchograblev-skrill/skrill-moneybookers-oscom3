<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Configuration\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class EntrySave {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qupdate = $OSCOM_PDO->prepare('update :table_configuration set configuration_value = :configuration_value, last_modified = now() where configuration_key = :configuration_key');
      $Qupdate->bindValue(':configuration_value', $data['value']);
      $Qupdate->bindValue(':configuration_key', $data['key']);
      $Qupdate->execute();

      return ( ($Qupdate->rowCount() === 1) || !$Qupdate->isError() );
    }
  }
?>
