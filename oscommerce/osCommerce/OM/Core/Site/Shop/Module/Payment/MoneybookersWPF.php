<?php

/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core\Site\Shop\Module\Payment;

use osCommerce\OM\Core\OSCOM;
use osCommerce\OM\Core\Registry;
use osCommerce\OM\Core\Site\Shop\Account;
use osCommerce\OM\Core\Site\Shop\Order;
use osCommerce\OM\Core\Site\Shop\Shipping;

class MoneybookersWPF extends \osCommerce\OM\Core\Site\Shop\PaymentModuleAbstract
{
    /**
     * @var string
     */
    protected $_host = 'test.nextgenpay.com';
    
    /**
     * @var string
     */
    protected $_path = '/frontend/payment.prc';
    
    /**
     * initialise 
     */
    protected function initialize()
    {
        $OSCOM_PDO = Registry::get('PDO');
        $OSCOM_ShoppingCart = Registry::get('ShoppingCart');

        $this->_title = "Moneybookers Web Payment Frontend";
        $this->_method_title = "Moneybookers Web Payment Frontend";
        $this->_sort_order = 1;
        $this->_status = true;
    }
    
    /**
     * process
     */
    public function process()
    {
        $OSCOM_Currencies = Registry::get('Currencies');
        $OSCOM_ShoppingCart = Registry::get('ShoppingCart');
        $OSCOM_Tax = Registry::get('Tax');
        $OSCOM_Customer = Registry::get('Customer');
        $Qaccount = Account::getEntry();
        
        $order_id = Order::insert();
        
        $post = array();
        
        // init
        $post["server"] = $this->_host;
        $post["path"] = $this->_path;
        $post["request.version"] = "1.0";
        $post["security.token"] = 'demo';
        $post["security.sender"] = '8a829417296a2c6f01296a3810ac005d';
        $post["transaction.channel"] = '8a829417296a2c6f01296a3810ad0062';
        $post["user.login"] = '8a829417296a2c6f01296a3810ac005f';
        $post["user.pwd"] = 'demo';
        $post["transaction.mode"] = 'INTEGRATOR_TEST';
        $post["transaction.response"] = 'SYNC';
        
        // payment information
        $post['payment.code'] = 'CC.DB';
        $post["presentation.amount"] = '15.00';
        $post["presentation.usage"] = 'order#' . $order_id . '/oscom3.local';
        $post["identification.transactionID"] = $order_id;
        $post["presentation.currency"] = $OSCOM_Currencies->getCode();
        
        // customer contact
        $post["contact.email"] = $OSCOM_Customer->getEmailAddress();
        $post["contact.mobile"] = $Qaccount->value('customers_telephone');
        $post["contact.ip"] = $_SERVER['REMOTE_ADDR'];
        $post["contact.phone"] = $Qaccount->value('customers_telephone');
        
        // customer address
        if ($OSCOM_ShoppingCart->hasShippingAddress()) {
            $post["address.street"] = $OSCOM_ShoppingCart->getShippingAddress('street_address');
            $post["address.zip"] = $OSCOM_ShoppingCart->getShippingAddress('postcode');
            $post["address.city"] = $OSCOM_ShoppingCart->getShippingAddress('city');
            $post["address.state"] = $OSCOM_ShoppingCart->getShippingAddress('zone_code');
            $post["address.country"] = $OSCOM_ShoppingCart->getShippingAddress('country_iso_code_3');
        }
        
        // customers name
        $post["name.salutation"] = '';
        $post["name.title"] = '';
        $post["name.given"] = $OSCOM_ShoppingCart->getShippingAddress('firstname');
        $post["name.family"] = $OSCOM_ShoppingCart->getShippingAddress('lastname');
        $post["name.company"] = $OSCOM_ShoppingCart->getShippingAddress('company');
        
        // wpf params
        $post["FRONTEND.ENABLED"] = 'true';
        $post["FRONTEND.POPUP"] = 'false';
        $post["FRONTEND.MODE"] = 'DEFAULT';
        $post["FRONTEND.LANGUAGE"] = 'en';
        $post["FRONTEND.RESPONSE_URL"] = OSCOM::getLink(null, 'Checkout', null, 'SSL');
        $post["FRONTEND.CSS_PATH"] = OSCOM::getConfig('http_server') . '/' . OSCOM::getPublicSiteLink('templates/oscom/stylesheets/mb_wpf.css');
        
        $this->commitPayment($post);
    }
    
    public function commitPayment($post)
    {
        $ch = curl_init();
        
        foreach ( $post as $key => $value ) {
            $post[strtoupper($key)] = $value;
            //unset($post[$key]);
        }

        curl_setopt($ch, CURLOPT_URL, "https://{$this->_host}{$this->_path}");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'php ctpepost'); /** @todo fix hardcoded string */
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $info = curl_getinfo($ch);
        
        curl_close($ch);
        
        $output = '';
        $output = $this->parseResult($response);
        
        $processingresult = $output["POST.VALIDATION"];
        $redirectURL = $output["FRONTEND.REDIRECT_URL"];
        
        //die($redirectURL);

        if ($processingresult == "ACK") {
            if (strstr($redirectURL, "http")) {  // redirect url is returned ==> everything ok

                $_SESSION['Shop']['PM']['WPF']['url'] = $redirectURL;
                OSCOM::redirect( OSCOM::getLink(null, 'Checkout', 'WPF', 'SSL') );
                
            } else { // error-code is returned ... failure
                OSCOM::redirect( OSCOM::getLink(null, 'Cart', null, 'SSL') );
            }
        }
        else {
            OSCOM::redirect( OSCOM::getLink(null, 'Cart', null, 'SSL') );
        }
    }
    
    /**
     *
     * @param type $resultURL
     * @return type
     */
    public function parseResult($resultURL)
    {
        $r_arr = explode("&", $resultURL);

        foreach ($r_arr AS $buf) {
            $temp = urldecode($buf);
            $temp = split("=", $temp, 2);

            $postatt = $temp[0];
            $postvar = $temp[1];

            $returnvalue[$postatt] = $postvar;
        }
        return($returnvalue);
    }
}