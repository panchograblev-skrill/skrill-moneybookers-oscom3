<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\ErrorLog\Model;

  use osCommerce\OM\Core\ErrorHandler;

  class delete {
    public static function execute() {
      ErrorHandler::clear();

      return true;
    }
  }
?>
