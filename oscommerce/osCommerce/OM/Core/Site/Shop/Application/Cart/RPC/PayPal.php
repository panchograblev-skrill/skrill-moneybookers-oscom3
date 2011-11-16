<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Application\Cart\RPC;

  use osCommerce\OM\Core\OSCOM;

  class PayPal {
    public static function execute() {
// List of safe IP-Addresses found here:
// https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/howto_api_golivechecklist

      $firewall = array(
//www.paypal.com
                        '64.4.241.16',
                        '64.4.241.32',
                        '64.4.241.33',
                        '64.4.241.34',
                        '64.4.241.35',
                        '64.4.241.36',
                        '64.4.241.37',
                        '64.4.241.38',
                        '64.4.241.39',
                        '216.113.188.32',
                        '216.113.188.33',
                        '216.113.188.34',
                        '216.113.188.35',
                        '216.113.188.64',
                        '216.113.188.65',
                        '216.113.188.66',
                        '216.113.188.67',
                        '66.211.169.2',
                        '66.211.169.65',
//api.paypal.com
                        '216.113.188.39',
                        '216.113.188.71',
                        '66.211.168.91',
                        '66.211.168.123',
//api-aa.paypal.com
                        '216.113.188.52',
                        '216.113.188.84',
                        '66.211.168.92',
                        '66.211.168.124',
//api-3t.paypal.com
                        '216.113.188.10',
                        '66.211.168.126',
//api-aa-3t.paypal.com
                        '216.113.188.11',
                        '66.211.168.125',
//notify.paypal.com
                        '216.113.188.202',
                        '216.113.188.203',
                        '216.113.188.204',
                        '66.211.170.66',
//developer.paypal.com
                        '66.135.197.163',
//sandbox.paypal.com
                        '216.113.169.205',
//www.sandbox.paypal.com
                        '66.135.197.160',
//api.sandbox.paypal.com
                        '66.135.197.162',
//api-aa.sandbox.paypal.com
                        '66.135.197.141',
//ipn.sandbox.paypal.com
                        '66.135.197.164'
                       );

      if ( !in_array(OSCOM::getIPAddress(), $firewall) ) {
        exit;
      }
    }
  }
?>
