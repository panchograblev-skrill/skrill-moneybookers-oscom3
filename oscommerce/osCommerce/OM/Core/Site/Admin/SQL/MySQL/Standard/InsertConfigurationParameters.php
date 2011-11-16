<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class InsertConfigurationParameters {
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

      $Qcfg = $OSCOM_PDO->prepare('insert into :table_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values (:configuration_title, :configuration_key, :configuration_value, :configuration_description, :configuration_group_id, :sort_order, :use_function, :set_function, now())');

      foreach ( $data as $d ) {
        if ( !isset($d['sort_order']) ) {
          $d['sort_order'] = '0';
        }

        if ( !isset($d['use_function']) ) {
          $d['use_function'] = '';
        }

        if ( !isset($d['set_function']) ) {
          $d['set_function'] = '';
        }

        $Qcfg->bindValue(':configuration_title', $d['title']);
        $Qcfg->bindValue(':configuration_key', $d['key']);
        $Qcfg->bindValue(':configuration_value', $d['value']);
        $Qcfg->bindValue(':configuration_description', $d['description']);
        $Qcfg->bindInt(':configuration_group_id', $d['group_id']);
        $Qcfg->bindInt(':sort_order', $d['sort_order']);
        $Qcfg->bindValue(':use_function', $d['use_function']);
        $Qcfg->bindValue(':set_function', $d['set_function']);
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
