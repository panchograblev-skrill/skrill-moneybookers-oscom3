<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Application\Checkout\Action\MBPayment;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Shop\PaymentModuleAbstract;

  class Status {
    public static function execute(ApplicationAbstract $application) {
      
        $merchant_sig = $_POST['merchant_id'] . 
                        $_POST['transaction_id'] . 
                        strtoupper(md5( MODULE_PAYMENT_MONEYBOOKERS_SECRET_WORD )) . 
                        $_POST['mb_amount'] . 
                        $_POST['mb_currency'] . 
                        $_POST['status'];
		
	$merchant_sig = strtoupper(md5($merchant_sig));
	$mb_sig = isset($_POST['md5sig']) ? $_POST['md5sig'] : '';
	
	if ( $mb_sig === $merchant_sig && $_POST['status'] == '2' )
            Order::process($_POST['transaction_id'], MODULE_PAYMENT_MONEYBOOKERS_ORDER_STATUS_ID);
        
        file_put_contents("status.txt", print_r($_POST, true), FILE_APPEND);
        
    }
  }
?>
