<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\CoreUpdate\Model;

  use osCommerce\OM\Core\Site\Admin\Application\CoreUpdate\CoreUpdate;

  class findAvailablePackages {
    public static function execute($search) {
      $result = CoreUpdate::getAvailablePackages();

      foreach ( $result['entries'] as $k => $v ) {
        if ( strpos($v['version'], $search) === false ) {
          unset($result['entries'][$k]);
        }
      }

      $result['total'] = count($result['entries']);

      return $result;
    }
  }
?>
