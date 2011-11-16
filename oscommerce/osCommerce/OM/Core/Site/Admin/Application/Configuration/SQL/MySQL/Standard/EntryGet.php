<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Configuration\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class EntryGet {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      if ( isset($data['key']) ) {
        $Qcfg = $OSCOM_PDO->prepare('select * from :table_configuration where configuration_key = :configuration_key');
        $Qcfg->bindValue(':configuration_key', $data['key']);
      } else {
        $Qcfg = $OSCOM_PDO->prepare('select * from :table_configuration where configuration_id = :configuration_id');
        $Qcfg->bindInt(':configuration_id', $data['id']);
      }

      $Qcfg->execute();

      return $Qcfg->fetch();
    }
  }
?>
