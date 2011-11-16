<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Login\Model;

  use osCommerce\OM\Core\Hash;
  use osCommerce\OM\Core\OSCOM;

  class isValidCredentials {
    public static function execute($data) {
      $result = OSCOM::callDB('Admin\Login\GetAdmin', array('username' => $data['username']));

      if ( !empty($result) ) {
        return Hash::validate($data['password'], $result['user_password']);
      }

      return false;
    }
  }
?>
