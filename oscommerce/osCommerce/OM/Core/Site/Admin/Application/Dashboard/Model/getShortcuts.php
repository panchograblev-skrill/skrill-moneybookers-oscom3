<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Dashboard\Model;

  use osCommerce\OM\Core\OSCOM;

  class getShortcuts {
    public static function execute($admin_id) {
      $data = array('admin_id' => $admin_id);

      return OSCOM::callDB('Admin\Dashboard\GetShortcuts', $data);
    }
  }
?>
