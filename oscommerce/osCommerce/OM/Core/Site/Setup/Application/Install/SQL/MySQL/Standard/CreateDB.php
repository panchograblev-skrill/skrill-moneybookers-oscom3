<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Setup\Application\Install\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class CreateDB {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      return ($OSCOM_PDO->exec('create database if not exists `' . $data['database'] . '` CHARACTER SET utf8 COLLATE utf8_general_ci') !== false);
    }
  }
?>
