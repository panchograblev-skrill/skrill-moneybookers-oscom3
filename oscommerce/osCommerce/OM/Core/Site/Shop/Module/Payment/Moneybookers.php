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
use osCommerce\OM\Core\Template;

class Moneybookers extends \osCommerce\OM\Core\Site\Shop\PaymentModuleAbstract
{
    /**
     * initialise 
     */
    protected function initialize()
    {
        $OSCOM_PDO = Registry::get('PDO');
        $OSCOM_ShoppingCart = Registry::get('ShoppingCart');

        $this->_title = OSCOM::getDef('moneybookers_title');
        $this->_method_title = OSCOM::getDef('moneybookers_method_title');
        $this->_status = (MODULE_PAYMENT_MONEYBOOKERS_STATUS == '1') ? true : false;
        $this->_sort_order = MODULE_PAYMENT_MONEYBOOKERS_SORT_ORDER;

        if ($this->_status === true) {
            if ((int) MODULE_PAYMENT_MONEYBOOKERS_ORDER_STATUS_ID > 0) {
                $this->order_status = MODULE_PAYMENT_MONEYBOOKERS_ORDER_STATUS_ID;
            }
        }
    }
    
    public function process()
    {
        $OSCOM_ShoppingCart = Registry::get('ShoppingCart');
        $OSCOM_Currencies = Registry::get('Currencies');
        
        //if (!isset($_SESSION['Shop']['PM']['MONEYBOOKERS']['SID'])) {
            $this->prepareQuickCheckout();
        //}

        //$response_array = $this->doQuickCheckoutPayment($params);

//        if (($response_array['ACK'] != 'Success') && ($response_array['ACK'] != 'SuccessWithWarning')) {
//            OSCOM::redirect(OSCOM::getLink(null, 'Cart', 'error_message=' . stripslashes($response_array['L_LONGMESSAGE0']), 'SSL'));
//        }

        $this->_order_id = Order::insert();
        Order::process($this->_order_id, $this->_order_status);

        //unset($_SESSION['Shop']['PM']['MONEYBOOKERS']);
    }

    protected function prepareQuickCheckout() {
        $OSCOM_Currencies = Registry::get('Currencies');
        $OSCOM_ShoppingCart = Registry::get('ShoppingCart');
        $OSCOM_Tax = Registry::get('Tax');
        $OSCOM_Customer = Registry::get('Customer');
        
        $moneybookers_url = 'https://www.moneybookers.com/app/payment.pl';

        $params = array('pay_to_email' => MODULE_PAYMENT_MONEYBOOKERS_PAY_TO_EMAIL);
        $params['currency'] = $OSCOM_Currencies->getCode();
        $params['prepare_only'] = '1';
        $params['pay_from_email'] = $OSCOM_Customer->getEmailAddress();
        $params['return_url'] = OSCOM::getLink(null, null, 'Success', 'SSL');
        $params['cancel_url'] = OSCOM::getLink(null, 'Cart', 'error_message=' . urlencode('The user canceled payment.'), 'SSL');

        $line_item_no = 0;
        $items_total = 0;
        $tax_total = 0;

        foreach ($OSCOM_ShoppingCart->getProducts() as $product) {
//            $params['L_NAME' . $line_item_no] = $product['name'];
//            $params['L_AMT' . $line_item_no] = $OSCOM_Currencies->formatRaw($product['price']);
//            $params['L_NUMBER' . $line_item_no] = $product['id'];
//            $params['L_QTY' . $line_item_no] = $product['quantity'];

            $product_tax = $OSCOM_Currencies->formatRaw($product['price'] * ($OSCOM_Tax->getTaxRate($product['tax_class_id']) / 100));

//            $params['L_TAXAMT' . $line_item_no] = $product_tax;
            $tax_total += $product_tax * $product['quantity'];

            $items_total += $OSCOM_Currencies->formatRaw($product['price']) * $product['quantity'];

            $line_item_no++;
        }

        // total amount
        $params['amount2_description'] = 'Products Price:';
        $params['amount2'] = $items_total;
        
        // taxes
        $params['amount4_description'] = 'VAT (Taxes):';
        $params['amount4'] = $tax_total;

        if ($OSCOM_ShoppingCart->hasShippingAddress()) {
//            $params['ADDROVERRIDE'] = '1';
            $params['firstname'] = $OSCOM_ShoppingCart->getShippingAddress('firstname');
            $params['lastname'] = $OSCOM_ShoppingCart->getShippingAddress('lastname');
            $params['address'] = $OSCOM_ShoppingCart->getShippingAddress('street_address');
            $params['city'] = $OSCOM_ShoppingCart->getShippingAddress('city');
            $params['state'] = $OSCOM_ShoppingCart->getShippingAddress('zone_code');
            $params['country'] = $OSCOM_ShoppingCart->getShippingAddress('country_iso_code_2');
            $params['postal_code'] = $OSCOM_ShoppingCart->getShippingAddress('postcode');
        }

        $OSCOM_Shipping = new Shipping();

        $quotes_array = array();

        foreach ($OSCOM_Shipping->getQuotes() as $quote) {
            if (!isset($quote['error'])) {
                foreach ($quote['methods'] as $rate) {
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

        foreach ($quotes_array as $quote) {
            $shipping_rate = $OSCOM_Currencies->formatRaw($quote['cost'] + ($quote['cost'] * ($quote['tax'] / 100)));

//            $params['L_SHIPPINGOPTIONNAME' . $counter] = $quote['name'] . ' (' . $quote['label'] . ')';
//            $params['L_SHIPINGPOPTIONLABEL' . $counter] = $quote['name'] . ' (' . $quote['label'] . ')';
//            $params['L_SHIPPINGOPTIONAMOUNT' . $counter] = $shipping_rate;
//            $params['L_SHIPPINGOPTIONISDEFAULT' . $counter] = 'false';

            if (is_null($cheapest_rate) || ($shipping_rate < $cheapest_rate)) {
                $cheapest_rate = $shipping_rate;
                $cheapest_counter = $counter;
            }

            if ($shipping_rate > $expensive_rate) {
                $expensive_rate = $shipping_rate;
            }

            if ($OSCOM_ShoppingCart->getShippingMethod('id') == $quote['id']) {
                $default_shipping = $counter;
            }

            $counter++;
        }

        // shipping amount
        if (!is_null($default_shipping)) {
//            $cheapest_rate = $params['L_SHIPPINGOPTIONAMOUNT' . $default_shipping];
            $cheapest_counter = $default_shipping;
        }

//        if (!is_null($cheapest_rate)) {
//            if ((MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_INSTANT_UPDATE == '1') && ((MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_TRANSACTION_SERVER != 'Live') || ((MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_TRANSACTION_SERVER == 'Live') && (ENABLE_SSL == true)))) { // Live server requires SSL to be enabled
//                $params['CALLBACK'] = OSCOM::getRPCLink(null, 'Cart', 'PayPal&ExpressCheckoutInstantUpdate', 'SSL', false, false);
//                $params['CALLBACKTIMEOUT'] = '5';
//            }
//
//            $params['INSURANCEOPTIONSOFFERED'] = 'false';
//            $params['L_SHIPPINGOPTIONISDEFAULT' . $cheapest_counter] = 'true';
//        }

        // don't recalculate currency values as they have already been calculated
        $params['amount3_description'] = 'Shipping Fees:';
        $params['amount3'] = $OSCOM_Currencies->formatRaw($OSCOM_ShoppingCart->getShippingMethod('cost'));
        $params['amount'] = $OSCOM_Currencies->formatRaw($params['amount2'] + $params['amount4'] + $params['amount3'], '', 1);
        //$params['MAXAMT'] = $OSCOM_Currencies->formatRaw($params['AMT'] + $expensive_rate + 100, '', 1); // safely pad higher for dynamic shipping rates (eg, USPS express)
        
        $ch = curl_init($moneybookers_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($ch);
        curl_close($ch);

        //$_SESSION['Shop']['PM']['MONEYBOOKERS']['SID'] = $result;
        
        //OSCOM::redirect($moneybookers_url . '?sid=' . $result);
        $_SESSION['Shop']['PM']['MONEYBOOKERS']['sid'] = $result;
        OSCOM::redirect( OSCOM::getLink(null, 'Checkout', 'MBPayment&sid='. $result, 'SSL') );
        //OSCOM::redirect(OSCOM::getLink(null, 'Checkout', 'error_message=' . stripslashes($response_array['L_LONGMESSAGE0']), 'SSL'));
    }
    
    public function preConfirmationCheck()
    {
        return false; 
        /*
        if (isset($_GET['action'])){
            switch ($_GET['action']) {
                case 'cancel':
                    $this->cancelQuickCheckout();
                    break;

                case 'retrieve':
                    $this->retrieveQuickCheckout();
                    break;
            }
        }
         */
    }

    protected function cancelQuickCheckout() {
        unset($_SESSION['Shop']['PM']['MONEYBOOKERS']);

        OSCOM::redirect(OSCOM::getLink(null, 'Cart', null, 'SSL'));
    }
}

?>
