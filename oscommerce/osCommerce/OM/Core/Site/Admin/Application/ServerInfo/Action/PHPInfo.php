<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\ServerInfo\Action;

  use osCommerce\OM\Core\ApplicationAbstract;

  class PHPInfo {
    public static function execute(ApplicationAbstract $application) {
      phpinfo();
      exit;
    }
  }
?>
