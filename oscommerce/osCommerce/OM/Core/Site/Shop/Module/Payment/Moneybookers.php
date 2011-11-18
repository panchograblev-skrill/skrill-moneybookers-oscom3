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

class Moneybookers extends \osCommerce\OM\Core\Site\Shop\MbAbstract
{
    protected $_image_name = 'digitalwallet-rgb.gif';
    
    /**
     * initialise 
     */
    protected function initialize()
    {
        $OSCOM_PDO = Registry::get('PDO');
        $OSCOM_ShoppingCart = Registry::get('ShoppingCart');
        
        $image_url = '';
        if ( strlen($this->_image_name) ) {
            $image_url = '<img src="' . OSCOM::getPublicSiteLink('images/skrill/payment-options/' . $this->_image_name, null, 'Shop') . '" /> ';
        }

        $this->_title = $image_url . OSCOM::getDef('moneybookers_title');
        $this->_method_title = $image_url . OSCOM::getDef('moneybookers_method_title');
        $this->_status = (MODULE_PAYMENT_MONEYBOOKERS_STATUS == '1') ? true : false;
        $this->_sort_order = MODULE_PAYMENT_MONEYBOOKERS_SORT_ORDER;

        if ($this->_status === true) {
            if ((int) MODULE_PAYMENT_MONEYBOOKERS_ORDER_STATUS_ID > 0) {
                $this->order_status = MODULE_PAYMENT_MONEYBOOKERS_ORDER_STATUS_ID;
            }
        }
    }
}

?>
