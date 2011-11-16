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

  class emailAddressExists {
    public static function execute($email_address, $customer_id = null) {
      $data = array('email_address' => $email_address);

      $result = OSCOM::callDB('Admin\Customers\Get', $data);

      if ( isset($customer_id) ) {
        return $result['customers_id'] != $customer_id;
      }

      return !empty($result);
    }
  }
?>
