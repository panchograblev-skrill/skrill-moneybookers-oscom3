<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\ServerInfo\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class GetUptime {
    public static function execute() {
      $OSCOM_PDO = Registry::get('PDO');

      $result = $OSCOM_PDO->query('show status like "Uptime"')->fetch();

      return intval($result['Value'] / 3600) . ':' . str_pad(intval(($result['Value'] / 60) % 60), 2, '0', STR_PAD_LEFT);
    }
  }
?>
