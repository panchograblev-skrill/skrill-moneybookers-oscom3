<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class GetConfiguration {
    public static function execute() {
      $OSCOM_PDO = Registry::get('PDO');

      $Qcfg = $OSCOM_PDO->query('select configuration_key as cfgKey, configuration_value as cfgValue from :table_configuration');
      $Qcfg->setCache('configuration');
      $Qcfg->execute();

      return $Qcfg->fetchAll();
    }
  }
?>
