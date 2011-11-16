<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\CoreUpdate\Model;

  use osCommerce\OM\Core\Site\Admin\Application\CoreUpdate\CoreUpdate;

  class getAvailablePackageInfo {
    public static function execute($key = null) {
      $versions = CoreUpdate::getAvailablePackages();

      if ( !empty($versions['entries']) ) {
        if ( !empty($key) && isset($versions['entries'][0][$key]) ) {
          return $versions['entries'][0][$key];
        } else {
          return $versions['entries'][0];
        }
      }

      return false;
    }
  }
?>
