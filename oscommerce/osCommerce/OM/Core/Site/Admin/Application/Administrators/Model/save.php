<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Administrators\Model;

  use osCommerce\OM\Core\Hash;
  use osCommerce\OM\Core\OSCOM;

  class save {
    public static function execute($data) {
      if ( !empty($data['password']) ) {
        $data['password'] = Hash::get(trim($data['password']));
      }

      return OSCOM::callDB('Admin\Administrators\Save', $data);
    }
  }
?>
