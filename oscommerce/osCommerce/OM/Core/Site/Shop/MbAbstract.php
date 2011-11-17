<?php

namespace osCommerce\OM\Core\Site\Shop;

use osCommerce\OM\Core\HttpRequest;
use osCommerce\OM\Core\Mail;
use osCommerce\OM\Core\OSCOM;
use osCommerce\OM\Core\Registry;
use osCommerce\OM\Core\Site\Shop\Order;
use osCommerce\OM\Core\Site\Shop\Shipping;
use osCommerce\OM\Core\Site\Shop\Account;

abstract class MbAbstract extends \osCommerce\OM\Core\Site\Shop\PaymentModuleAbstract
{
    /**
     * Moneybookers Payment Code
     * 
     * @var string
     */
    protected $_mb_code = '';
    
    /**
     * Params to send to Moneybookers Gateway
     * 
     * @var array
     */
    protected $_params = array();
    
    /**
     * ISO 3 Codes of the allowed countries for this method
     * @var array
     */
    protected $_allowedCountries = array();
    
    /**
     * Order Id
     * 
     * @var int
     */
    protected $_order_id = null;
    
    /**
     * initialize
     */
    protected function initialize()
    {
        $this->_mb_code = str_replace('Mb', '', $this->_code);
        
        $this->_title = '<img src="http://www.moneybookers.com/ads/skrill-brand-centre/resources/images/plain-four-versions-rgb_168x45.gif" /> ' . OSCOM::getDef('moneybookers_' . strtolower($this->_mb_code) . '_title');
        $this->_method_title =  '<img src="http://www.moneybookers.com/ads/skrill-brand-centre/resources/images/plain-four-versions-rgb_168x45.gif" /> ' . OSCOM::getDef('moneybookers_' . strtolower($this->_mb_code) . '_description');
        
        $this->_status = ( constant('MODULE_PAYMENT_MONEYBOOKERS_' . $this->_mb_code) == '1') ? true : false;
        $this->_sort_order = constant('MODULE_PAYMENT_MONEYBOOKERS_' . $this->_mb_code . '_SORT_ORDER');

        $OSCOM_ShoppingCart = Registry::get('ShoppingCart');
        
        $iso3 = $OSCOM_ShoppingCart->getBillingAddress('country_iso_code_3');
        if ( !empty($this->_allowedCountries) && !in_array($iso3, $this->_allowedCountries)) {
            $this->_status = false;
        }
        
        if ($this->_status === true) {
            if ((int) MODULE_PAYMENT_MONEYBOOKERS_ORDER_STATUS_ID > 0) {
                $this->_order_status = MODULE_PAYMENT_MONEYBOOKERS_ORDER_STATUS_ID;
            }
        }
    }
    
    public function preConfirmationCheck()
    {
        if (isset($_GET['action'])){
            switch ($_GET['action']) {
                case 'cancel':
                    $this->_cancelQuickCheckout();
                    break;
            }
        }
    }
    
    /**
     * Prepare and payment process
     * 
     * @access public
     * @return void
     */
    public function process()
    {
        $this->_order_id = Order::insert();
        Order::process($this->_order_id, 1);
        
        $this->_prepareQuickCheckout();
        $this->_doQuickCheckoutPayment($this->_params);
    }
    
    /**
     * Cancel payment
     */
    protected function _cancelQuickCheckout() {
        unset($_SESSION['Shop']['PM']['MONEYBOOKERS']);

        OSCOM::redirect(OSCOM::getLink(null, 'Cart', 'error_message=' . urlencode('The user canceled payment.'), 'SSL'));
    }
    
    /**
     * Prepare payment
     */
    protected function _prepareQuickCheckout()
    {
        $OSCOM_Currencies = Registry::get('Currencies');
        $OSCOM_ShoppingCart = Registry::get('ShoppingCart');
        $OSCOM_Tax = Registry::get('Tax');
        $OSCOM_Customer = Registry::get('Customer');
        
        $this->_params['payment_methods'] = $this->_mb_code;
        $this->_params['pay_to_email'] = MODULE_PAYMENT_MONEYBOOKERS_PAY_TO_EMAIL;
        $this->_params['currency'] = $OSCOM_Currencies->getCode();
        $this->_params['prepare_only'] = '1';
        $this->_params['hide_login'] = '1';
        $this->_params['transaction_id'] = $this->_order_id;

        $Qaccount = Account::getEntry();
        if ( ACCOUNT_DATE_OF_BIRTH == '1' ) {
            $dob = date("dmY", strtotime($Qaccount->value('customers_dob_date') . '-' .
                                       $Qaccount->value('customers_dob_month') . '-'.
                                       $Qaccount->value('customers_dob_year')));
            $this->_params['date_of_birth'] = $dob;
        }
        
        $this->_params['pay_from_email'] = $OSCOM_Customer->getEmailAddress();
        $this->_params['return_url'] = OSCOM::getLink(null, null, 'Success', 'SSL');
        $this->_params['cancel_url'] = OSCOM::getLink(null, 'Checkout', 'action=cancel', 'SSL');
        $this->_params['status_url'] = OSCOM::getLink(null, 'MBStatus', null, 'SSL');

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
        $this->_params['amount2_description'] = 'Products Price:';
        $this->_params['amount2'] = $items_total;
        
        // taxes
        $this->_params['amount4_description'] = 'VAT (Taxes):';
        $this->_params['amount4'] = $tax_total;

        if ($OSCOM_ShoppingCart->hasShippingAddress()) {
//            $params['ADDROVERRIDE'] = '1';
            $this->_params['firstname'] = $OSCOM_ShoppingCart->getShippingAddress('firstname');
            $this->_params['lastname'] = $OSCOM_ShoppingCart->getShippingAddress('lastname');
            $this->_params['address'] = $OSCOM_ShoppingCart->getShippingAddress('street_address');
            $this->_params['city'] = $OSCOM_ShoppingCart->getShippingAddress('city');
            $this->_params['state'] = $OSCOM_ShoppingCart->getShippingAddress('zone_code');
            $this->_params['country'] = $OSCOM_ShoppingCart->getShippingAddress('country_iso_code_3');
            $this->_params['postal_code'] = $OSCOM_ShoppingCart->getShippingAddress('postcode');
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
        $this->_params['amount3_description'] = 'Shipping Fees:';
        $this->_params['amount3'] = $OSCOM_Currencies->formatRaw($OSCOM_ShoppingCart->getShippingMethod('cost'));
        $this->_params['amount'] = $OSCOM_Currencies->formatRaw($this->_params['amount2'] + $this->_params['amount4'] + $this->_params['amount3'], '', 1);
        //$params['MAXAMT'] = $OSCOM_Currencies->formatRaw($params['AMT'] + $expensive_rate + 100, '', 1); // safely pad higher for dynamic shipping rates (eg, USPS express)
        
        //OSCOM::redirect(OSCOM::getLink(null, 'Checkout', 'error_message=' . stripslashes($response_array['L_LONGMESSAGE0']), 'SSL'));
    }
    
    /**
     * Generates payment sid and redirects to the Skrill ( Moneybookers ) gateway
     * to complete payment
     */
    protected function _doQuickCheckoutPayment()
    {
        $moneybookers_url = 'https://www.moneybookers.com/app/payment.pl';
        
        $ch = curl_init($moneybookers_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->_params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($ch);
        curl_close($ch);
        
        // check if ther is an error
        //if (($response_array['ACK'] != 'Success') && ($response_array['ACK'] != 'SuccessWithWarning')) {
            //OSCOM::redirect(OSCOM::getLink(null, 'Cart', 'error_message=' . stripslashes($response_array['L_LONGMESSAGE0']), 'SSL'));
        //}
        
        $_SESSION['Shop']['PM']['MONEYBOOKERS']['sid'] = $result;
        OSCOM::redirect( OSCOM::getLink(null, 'Checkout', 'MBPayment&sid='. $result, 'SSL') );
    }
}
