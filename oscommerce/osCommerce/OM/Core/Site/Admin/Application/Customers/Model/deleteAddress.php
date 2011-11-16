<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Customers\Model;

  use osCommerce\OM\Core\OSCOM;

/**
 * @since v3.0.2
 */

  class deleteAddress {
    public static function execute($id, $customer_id) {
      $data = array('id' => $id,
                    'customer_id' => $customer_id);

      return OSCOM::callDB('Admin\Customers\DeleteAddress', $data);
    }
  }
?>
