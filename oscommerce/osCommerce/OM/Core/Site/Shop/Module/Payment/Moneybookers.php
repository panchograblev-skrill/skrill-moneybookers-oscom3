<?php

namespace osCommerce\OM\Core\Site\Shop\Module\Payment;

use osCommerce\OM\Core\OSCOM;
use osCommerce\OM\Core\Registry;

class Moneybookers extends \osCommerce\OM\Core\Site\Shop\MbAbstract
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
}

?>
