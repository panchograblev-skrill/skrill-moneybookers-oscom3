<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Module\Payment;

  use osCommerce\OM\Core\HttpRequest;
  use osCommerce\OM\Core\Mail;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Shop\Order;
  use osCommerce\OM\Core\Site\Shop\Shipping;

  class PayPalExpressCheckout extends \osCommerce\OM\Core\Site\Shop\PaymentModuleAbstract {
    protected function initialize() {
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');

      $this->_api_version = '60.0';

      $this->_title = OSCOM::getDef('paypal_express_checkout_title');
      $this->_method_title = OSCOM::getDef('paypal_express_checkout_method_title');
      $this->_status = (MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_STATUS == '1') ? true : false;
      $this->_sort_order = MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_SORT_ORDER;

      if ( $this->_status === true ) {
        if ( (int)MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_ORDER_STATUS_ID > 0 ) {
          $this->order_status = MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_ORDER_STATUS_ID;
        }

        if ( (int)MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_ZONE > 0 ) {
          $check_flag = false;

          $Qcheck = $OSCOM_PDO->prepare('select zone_id from :table_zones_to_geo_zones where geo_zone_id = :geo_zone_id and zone_country_id = :zone_country_id order by zone_id');
          $Qcheck->bindInt(':geo_zone_id', MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_ZONE);
          $Qcheck->bindInt(':zone_country_id', $OSCOM_ShoppingCart->getBillingAddress('country_id'));
          $Qcheck->execute();

          while ( $Qcheck->fetch() ) {
            if ( $Qcheck->valueInt('zone_id') < 1 ) {
              $check_flag = true;
              break;
            } elseif ( $Qcheck->valueInt('zone_id') == $OSCOM_ShoppingCart->getBillingAddress('zone_id') ) {
              $check_flag = true;
              break;
            }
          }

          if ( $check_flag === false ) {
            $this->_status = false;
          }
        }
      }
    }

    public function preConfirmationCheck() {
      if ( isset($_GET['ppx']) ) {
        switch ( $_GET['ppx'] ) {
          case 'cancel':
            $this->cancelExpressCheckout();
            break;

          case 'retrieve':
            $this->retrieveExpressCheckout();
            break;
        }
      }
    }

    public function process() {
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');
      $OSCOM_Currencies = Registry::get('Currencies');

      if ( !isset($_SESSION['Shop']['PM']['PAYPAL']['EC']['TOKEN']) ) {
        $this->initializeExpressCheckout();
      }

      $params = array('TOKEN' => $_SESSION['Shop']['PM']['PAYPAL']['EC']['TOKEN'],
                      'PAYERID' => $_SESSION['Shop']['PM']['PAYPAL']['EC']['PAYERID'],
                      'AMT' => $OSCOM_ShoppingCart->getTotal(),
                      'CURRENCYCODE' => $OSCOM_Currencies->getCode());

      if ( $OSCOM_ShoppingCart->hasShippingAddress() ) {
        $params['SHIPTONAME'] = $OSCOM_ShoppingCart->getShippingAddress('firstname') . ' ' . $OSCOM_ShoppingCart->getShippingAddress('lastname');
        $params['SHIPTOSTREET'] = $OSCOM_ShoppingCart->getShippingAddress('street_address');
        $params['SHIPTOCITY'] = $OSCOM_ShoppingCart->getShippingAddress('city');
        $params['SHIPTOSTATE'] = $OSCOM_ShoppingCart->getShippingAddress('zone_code');
        $params['SHIPTOCOUNTRYCODE'] = $OSCOM_ShoppingCart->getShippingAddress('country_iso_code_2');
        $params['SHIPTOZIP'] = $OSCOM_ShoppingCart->getShippingAddress('postcode');
      }

      $response_array = $this->doExpressCheckoutPayment($params);

      if (($response_array['ACK'] != 'Success') && ($response_array['ACK'] != 'SuccessWithWarning')) {
        OSCOM::redirect(OSCOM::getLink(null, 'Cart', 'error_message=' . stripslashes($response_array['L_LONGMESSAGE0']), 'SSL'));
      }

      $this->_order_id = Order::insert();
      Order::process($this->_order_id, $this->_order_status);

      unset($_SESSION['Shop']['PM']['PAYPAL']);
    }

    protected function setExpressCheckout($parameters) {
      if ( MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_TRANSACTION_SERVER == 'Live' ) {
        $api_url = 'https://api-3t.paypal.com/nvp';
      } else {
        $api_url = 'https://api-3t.sandbox.paypal.com/nvp';
      }

      $params = array('VERSION' => $this->_api_version,
                      'METHOD' => 'SetExpressCheckout',
                      'PAYMENTACTION' => ((MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_TRANSACTION_METHOD == 'Sale') || (strlen(MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_API_USERNAME) < 1) ? 'Sale' : 'Authorization'),
                      'RETURNURL' => OSCOM::getLink(null, null, 'ppx=retrieve', 'SSL', true, false),
                      'CANCELURL' => OSCOM::getLink(null, null, 'ppx=cancel', 'SSL', true, false));

      if ( strlen(MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_API_USERNAME) > 0 ) {
        $params['USER'] = MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_API_USERNAME;
        $params['PWD'] = MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_API_PASSWORD;
        $params['SIGNATURE'] = MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_API_SIGNATURE;
      } else {
        $params['SUBJECT'] = MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_SELLER_ACCOUNT;
      }

      if ( MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_ACCOUNT_OPTIONAL == '1' ) {
        $params['SOLUTIONTYPE'] = 'Sole';
      }

      if ( is_array($parameters) && !empty($parameters) ) {
        $params = array_merge($params, $parameters);
      }

      $post_string = '';

      foreach ( $params as $key => $value ) {
        $post_string .= $key . '=' . urlencode(utf8_encode(trim($value))) . '&';
      }

      $post_string = substr($post_string, 0, -1);

      $response = HttpRequest::getResponse(array('url' => $api_url, 'parameters' => $post_string));

      $response_array = array();
      parse_str($response, $response_array);

      if ( ($response_array['ACK'] != 'Success') && ($response_array['ACK'] != 'SuccessWithWarning') ) {
        $this->sendDebugEmail();
      }

      return $response_array;
    }

    protected function getExpressCheckoutDetails($token) {
      if ( MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_TRANSACTION_SERVER == 'Live' ) {
        $api_url = 'https://api-3t.paypal.com/nvp';
      } else {
        $api_url = 'https://api-3t.sandbox.paypal.com/nvp';
      }

      $params = array('VERSION' => $this->_api_version,
                      'METHOD' => 'GetExpressCheckoutDetails',
                      'TOKEN' => $token);

      if ( strlen(MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_API_USERNAME) > 0 ) {
        $params['USER'] = MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_API_USERNAME;
        $params['PWD'] = MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_API_PASSWORD;
        $params['SIGNATURE'] = MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_API_SIGNATURE;
      } else {
        $params['SUBJECT'] = MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_SELLER_ACCOUNT;
      }

      $post_string = '';

      foreach ( $params as $key => $value ) {
        $post_string .= $key . '=' . urlencode(utf8_encode(trim($value))) . '&';
      }

      $post_string = substr($post_string, 0, -1);

      $response = HttpRequest::getResponse(array('url' => $api_url, 'parameters' => $post_string));

      $response_array = array();
      parse_str($response, $response_array);

      if ( ($response_array['ACK'] != 'Success') && ($response_array['ACK'] != 'SuccessWithWarning') ) {
        $this->sendDebugEmail();
      }

      return $response_array;
    }

    protected function doExpressCheckoutPayment($parameters) {
      if ( MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_TRANSACTION_SERVER == 'Live' ) {
        $api_url = 'https://api-3t.paypal.com/nvp';
      } else {
        $api_url = 'https://api-3t.sandbox.paypal.com/nvp';
      }

      $params = array('VERSION' => $this->_api_version,
                      'METHOD' => 'DoExpressCheckoutPayment',
                      'PAYMENTACTION' => ((MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_TRANSACTION_METHOD == 'Sale') || (strlen(MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_API_USERNAME) < 1) ? 'Sale' : 'Authorization'),
                      'BUTTONSOURCE' => 'osCommerce30_Default_EC');

      if ( strlen(MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_API_USERNAME) > 0 ) {
        $params['USER'] = MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_API_USERNAME;
        $params['PWD'] = MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_API_PASSWORD;
        $params['SIGNATURE'] = MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_API_SIGNATURE;
      } else {
        $params['SUBJECT'] = MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_SELLER_ACCOUNT;
      }

      if ( is_array($parameters) && !empty($parameters) ) {
        $params = array_merge($params, $parameters);
      }

      $post_string = '';

      foreach ( $params as $key => $value ) {
        $post_string .= $key . '=' . urlencode(utf8_encode(trim($value))) . '&';
      }

      $post_string = substr($post_string, 0, -1);

      $response = HttpRequest::getResponse(array('url' => $api_url, 'parameters' => $post_string));

      $response_array = array();
      parse_str($response, $response_array);

      if ( ($response_array['ACK'] != 'Success') && ($response_array['ACK'] != 'SuccessWithWarning') ) {
        $this->sendDebugEmail();
      }

      return $response_array;
    }

    protected function initializeExpressCheckout() {
      $OSCOM_Currencies = Registry::get('Currencies');
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');
      $OSCOM_Tax = Registry::get('Tax');

      if ( MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_TRANSACTION_SERVER == 'Live' ) {
        $paypal_url = 'https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout';
      } else {
        $paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout';
      }

      $params = array('CURRENCYCODE' => $OSCOM_Currencies->getCode());

      $line_item_no = 0;
      $items_total = 0;
      $tax_total = 0;

      foreach ( $OSCOM_ShoppingCart->getProducts() as $product ) {
        $params['L_NAME' . $line_item_no] = $product['name'];
        $params['L_AMT' . $line_item_no] = $OSCOM_Currencies->formatRaw($product['price']);
        $params['L_NUMBER' . $line_item_no] = $product['id'];
        $params['L_QTY' . $line_item_no] = $product['quantity'];

        $product_tax = $OSCOM_Currencies->formatRaw($product['price'] * ($OSCOM_Tax->getTaxRate($product['tax_class_id']) / 100));

        $params['L_TAXAMT' . $line_item_no] = $product_tax;
        $tax_total += $product_tax * $product['quantity'];

        $items_total += $OSCOM_Currencies->formatRaw($product['price']) * $product['quantity'];

        $line_item_no++;
      }

      $params['ITEMAMT'] = $items_total;
      $params['TAXAMT'] = $tax_total;

      if ( $OSCOM_ShoppingCart->hasShippingAddress() ) {
        $params['ADDROVERRIDE'] = '1';
        $params['SHIPTONAME'] = $OSCOM_ShoppingCart->getShippingAddress('firstname') . ' ' . $OSCOM_ShoppingCart->getShippingAddress('lastname');
        $params['SHIPTOSTREET'] = $OSCOM_ShoppingCart->getShippingAddress('street_address');
        $params['SHIPTOCITY'] = $OSCOM_ShoppingCart->getShippingAddress('city');
        $params['SHIPTOSTATE'] = $OSCOM_ShoppingCart->getShippingAddress('zone_code');
        $params['SHIPTOCOUNTRYCODE'] = $OSCOM_ShoppingCart->getShippingAddress('country_iso_code_2');
        $params['SHIPTOZIP'] = $OSCOM_ShoppingCart->getShippingAddress('postcode');
      }

      $OSCOM_Shipping = new Shipping();

      $quotes_array = array();

      foreach ( $OSCOM_Shipping->getQuotes() as $quote ) {
        if ( !isset($quote['error']) ) {
          foreach ( $quote['methods'] as $rate ) {
            $quotes_array[] = array('id' => $quote['id'] . '_' . $rate['id'],
                                    'name' => $quote['module'],
                                    'label' => $rate['title'],
                                    'cost' => $rate['cost'],
                                    'tax' => $quote['tax']);
          }
        }
      }

      $counter = 0;
      $cheapest_rate = null;
      $expensive_rate = 0;
      $cheapest_counter = $counter;
      $default_shipping = null;

      foreach ( $quotes_array as $quote ) {
        $shipping_rate = $OSCOM_Currencies->formatRaw($quote['cost'] + ($quote['cost'] * ($quote['tax'] / 100)));

        $params['L_SHIPPINGOPTIONNAME' . $counter] = $quote['name'] . ' (' . $quote['label'] . ')';
        $params['L_SHIPINGPOPTIONLABEL' . $counter] = $quote['name'] . ' (' . $quote['label'] . ')';
        $params['L_SHIPPINGOPTIONAMOUNT' . $counter] = $shipping_rate;
        $params['L_SHIPPINGOPTIONISDEFAULT' . $counter] = 'false';

        if ( is_null($cheapest_rate) || ($shipping_rate < $cheapest_rate) ) {
          $cheapest_rate = $shipping_rate;
          $cheapest_counter = $counter;
        }

        if ( $shipping_rate > $expensive_rate ) {
          $expensive_rate = $shipping_rate;
        }

        if ( $OSCOM_ShoppingCart->getShippingMethod('id') == $quote['id'] ) {
          $default_shipping = $counter;
        }

        $counter++;
      }

      if ( !is_null($default_shipping) ) {
        $cheapest_rate = $params['L_SHIPPINGOPTIONAMOUNT' . $default_shipping];
        $cheapest_counter = $default_shipping;
      }

      if ( !is_null($cheapest_rate) ) {
        if ( (MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_INSTANT_UPDATE == '1') && ((MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_TRANSACTION_SERVER != 'Live') || ((MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_TRANSACTION_SERVER == 'Live') && (ENABLE_SSL == true))) ) { // Live server requires SSL to be enabled
          $params['CALLBACK'] = OSCOM::getRPCLink (null, 'Cart', 'PayPal&ExpressCheckoutInstantUpdate', 'SSL', false, false);
          $params['CALLBACKTIMEOUT'] = '5';
        }

        $params['INSURANCEOPTIONSOFFERED'] = 'false';
        $params['L_SHIPPINGOPTIONISDEFAULT' . $cheapest_counter] = 'true';
      }

// don't recalculate currency values as they have already been calculated
      $params['SHIPPINGAMT'] = $OSCOM_Currencies->formatRaw($OSCOM_ShoppingCart->getShippingMethod('cost'));
      $params['AMT'] = $OSCOM_Currencies->formatRaw($params['ITEMAMT'] + $params['TAXAMT'] + $params['SHIPPINGAMT'], '', 1);
      $params['MAXAMT'] = $OSCOM_Currencies->formatRaw($params['AMT'] + $expensive_rate + 100, '', 1); // safely pad higher for dynamic shipping rates (eg, USPS express)

      $response_array = $this->setExpressCheckout($params);

      if ( ($response_array['ACK'] == 'Success') || ($response_array['ACK'] == 'SuccessWithWarning') ) {
        OSCOM::redirect($paypal_url . '&token=' . $response_array['TOKEN'] . '&useraction=commit');
      }

      OSCOM::redirect(OSCOM::getLink(null, 'Cart', 'error_message=' . stripslashes($response_array['L_LONGMESSAGE0']), 'SSL'));
    }

    protected function cancelExpressCheckout() {
      unset($_SESSION['Shop']['PM']['PAYPAL']);

      OSCOM::redirect(OSCOM::getLink(null, 'Cart', null, 'SSL'));
    }

    protected function retrieveExpressCheckout() {
      $response_array = $this->getExpressCheckoutDetails($_GET['token']);

      if ( ($response_array['ACK'] == 'Success') || ($response_array['ACK'] == 'SuccessWithWarning') ) {
        $_SESSION['Shop']['PM']['PAYPAL']['EC']['TOKEN'] = $response_array['TOKEN'];
        $_SESSION['Shop']['PM']['PAYPAL']['EC']['PAYERID'] = $response_array['PAYERID'];
        $_SESSION['Shop']['PM']['PAYPAL']['EC']['PAYERSTATUS'] = $response_array['PAYERSTATUS'];
        $_SESSION['Shop']['PM']['PAYPAL']['EC']['ADDRESSSTATUS'] = $response_array['ADDRESSSTATUS'];

        OSCOM::redirect(OSCOM::getLink(null, null, 'Process', 'SSL'));
      }

      OSCOM::redirect(OSCOM::getLink(null, 'Cart', 'error_message=' . stripslashes($response_array['L_LONGMESSAGE0']), 'SSL'));
    }

    protected function sendDebugEmail() {
      if ( strlen(MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_DEBUG_EMAIL) > 0 ) {
        $email_body = '$_POST:' . "\n\n";

        foreach ( $_POST as $key => $value ) {
          $email_body .= $key . '=' . $value . "\n";
        }

        $email_body .= "\n" . '$_GET:' . "\n\n";

        foreach ( $_GET as $key => $value ) {
          $email_body .= $key . '=' . $value . "\n";
        }

        $email = new Mail(null, MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_DEBUG_EMAIL, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, 'PayPal Express Debug E-Mail');
        $email->setBodyPlain($email_body);
        $email->send();
      }
    }
  }
?>
