<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Application\Checkout\Action\Billing;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Shop\PaymentModuleAbstract;

  class Process {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_Payment = Registry::get('Payment');
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');

      if ( isset($_POST['payment_method']) && !empty($_POST['payment_method']) ) {
        if ( $OSCOM_Payment->hasActive() && Registry::exists('Payment_' . $_POST['payment_method']) && (Registry::get('Payment_' . $_POST['payment_method']) instanceof PaymentModuleAbstract) && Registry::get('Payment_' . $_POST['payment_method'])->isEnabled() ) {
          $OSCOM_ShoppingCart->setBillingMethod(array('id' => Registry::get('Payment_' . $_POST['payment_method'])->getCode(),
                                                      'title' => Registry::get('Payment_' . $_POST['payment_method'])->getMethodTitle()));

          OSCOM::redirect(OSCOM::getLink(null, null, null, 'SSL'));
        }
      }
    }
  }
?>
