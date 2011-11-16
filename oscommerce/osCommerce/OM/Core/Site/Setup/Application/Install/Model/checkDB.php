<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Setup\Application\Install\Model;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\PDO;
  use osCommerce\OM\Core\Registry;

  class checkDB {
    public static function execute($data) {
      if ( $OSCOM_PDO = PDO::initialize($data['server'], $data['username'], $data['password'], null, $data['port'], $data['class']) ) {
        Registry::set('PDO', $OSCOM_PDO);

        OSCOM::callDB('Setup\Install\CreateDB', array('database' => $data['database']));
      }

      return PDO::initialize($data['server'], $data['username'], $data['password'], $data['database'], $data['port'], $data['class']);
    }
  }
?>
