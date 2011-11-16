<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\CoreUpdate\Model;

  use \Phar;
  use osCommerce\OM\Core\OSCOM;

  class deletePackage {
    public static function execute() {
      return Phar::unlinkArchive(OSCOM::BASE_DIRECTORY . 'Work/CoreUpdate/update.phar');
    }
  }
?>
