<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\ZoneGroups\Model;

  use osCommerce\OM\Core\OSCOM;

  class delete {
    public static function execute($id) {
      $data = array('id' => $id);

      return OSCOM::callDB('Admin\ZoneGroups\Delete', $data);
    }
  }
?>
