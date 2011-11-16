<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\CoreUpdate\Model;

  use osCommerce\OM\Core\OSCOM;

/**
 * @since v3.0.2
 */

  class logExists {
    public static function execute($log) {
      $log = basename($log);

      if ( substr($log, 0, -4) != '.txt' ) {
        $log .= '.txt';
      }

      return file_exists(OSCOM::BASE_DIRECTORY . 'Work/Logs/' . $log);
    }
  }
?>
