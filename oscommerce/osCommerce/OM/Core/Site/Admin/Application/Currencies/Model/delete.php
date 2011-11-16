<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Currencies\Model;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Cache;

  class delete {
    public static function execute($id) {
      $data = array('id' => $id);

      if ( OSCOM::callDB('Admin\Currencies\Delete', $data) ) {
        Cache::clear('currencies');

        return true;
      }

      return false;
    }
  }
?>
