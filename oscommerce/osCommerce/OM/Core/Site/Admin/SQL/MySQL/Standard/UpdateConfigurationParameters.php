<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class UpdateConfigurationParameters {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      if ( isset($data['key']) && isset($data['value']) ) {
        $data = array($data);
      }

      $error = false;
      $in_transaction = false;

      if ( count($data) > 1 ) {
        $OSCOM_PDO->beginTransaction();

        $in_transaction = true;
      }

      $Qcfg = $OSCOM_PDO->prepare('update :table_configuration set configuration_value = :configuration_value, last_modified = now() where configuration_key = :configuration_key');

      foreach ( $data as $d ) {
        $Qcfg->bindValue(':configuration_value', $d['value']);
        $Qcfg->bindValue(':configuration_key', $d['key']);
        $Qcfg->execute();

        if ( $Qcfg->isError() ) {
          if ( $in_transaction === true ) {
            $OSCOM_PDO->rollBack();
          }

          $error = true;

          break;
        }
      }

      if ( ($error === false) && ($in_transaction === true) ) {
        $OSCOM_PDO->commit();
      }

      return !$error;
    }
  }
?>
