<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Languages\Model;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Cache;

  class update {
    public static function execute($data) {
      if ( OSCOM::callDB('Admin\Languages\Update', $data) ) {
        Cache::clear('languages');

        if ( $data['set_default'] === true ) {
          Cache::clear('configuration');
        }

        return true;
      }

      return false;
    }
  }
?>
