<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Customers\RPC;

  use osCommerce\OM\Core\Site\RPC\Controller as RPC;
  use osCommerce\OM\Core\Site\Shop\Address;

/**
 * @since v3.0.2
 */

  class FormatAddress {
    public static function execute() {
      $data = array('firstname' => $_GET['firstname'],
                    'lastname' => $_GET['lastname'],
                    'street_address' => $_GET['street_address'],
                    'city' => $_GET['city'],
                    'postcode' => $_GET['postcode'],
                    'zone_code' => '',
                    'state' => '',
                    'country_id' => $_GET['country_id'],
                    'country_title' => '');

      if ( isset($_GET['company']) ) {
        $data['company'] = $_GET['company'];
      }

      if ( isset($_GET['suburb']) ) {
        $data['suburb'] = $_GET['suburb'];
      }

      if ( isset($_GET['zone_id']) ) {
        $data['zone_id'] = $_GET['zone_id'];
      } elseif ( isset($_GET['state']) ) {
        $data['state'] = $_GET['state'];
      }

      $result = array('address' => Address::format($data, '<br />'),
                      'rpcStatus' => RPC::STATUS_SUCCESS);

      echo json_encode($result);
    }
  }
?>
