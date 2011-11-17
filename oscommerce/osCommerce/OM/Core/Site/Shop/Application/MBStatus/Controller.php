<?php

namespace osCommerce\OM\Core\Site\Shop\Application\MBStatus;

use osCommerce\OM\Core\Site\Shop\Order;

class Controller extends \osCommerce\OM\Core\Site\Shop\ApplicationAbstract
{
    protected function initialize() { }

    protected function process()
    {
        $merchant_sig = $_POST['merchant_id'] . 
                        $_POST['transaction_id'] . 
                        strtoupper(md5( MODULE_PAYMENT_MONEYBOOKERS_SECRET_WORD )) . 
                        $_POST['mb_amount'] . 
                        $_POST['mb_currency'] . 
                        $_POST['status'];
		
	$merchant_sig = strtoupper(md5($merchant_sig));
	$mb_sig = isset($_POST['md5sig']) ? $_POST['md5sig'] : '';
        
        $order_status = ( constant('MODULE_PAYMENT_MONEYBOOKERS_ORDER_STATUS_ID') > 0 ) ? 
                            MODULE_PAYMENT_MONEYBOOKERS_ORDER_STATUS_ID : null;
	
	if ( ($mb_sig === $merchant_sig ) && $_POST['status'] == '2' )
            Order::process($_POST['transaction_id'], $order_status  ) ;
        
        file_put_contents("status.txt", print_r($_POST, true), FILE_APPEND);
    }

}