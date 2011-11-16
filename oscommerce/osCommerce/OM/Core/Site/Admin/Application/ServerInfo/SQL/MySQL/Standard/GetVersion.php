<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\ServerInfo\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class GetVersion {
    public static function execute() {
      $OSCOM_PDO = Registry::get('PDO');

      $result = $OSCOM_PDO->query('select version() as version')->fetch();

      return 'MySQL v' . $result['version'];
    }
  }
?>
