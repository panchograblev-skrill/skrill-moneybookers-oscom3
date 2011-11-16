<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Module\ProductType;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Shop\Payment;
  use osCommerce\OM\Core\Site\Shop\Product;

  class RequireBilling {
    public static function getTitle() {
      return 'Require Billing';
    }

    public static function getDescription() {
      return 'Require billing';
    }

    public static function isValid(Product $OSCOM_Product) {
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');

      if ( $OSCOM_ShoppingCart->hasBillingAddress() === false ) {
        if ( $OSCOM_Customer->hasDefaultAddress() ) {
          $OSCOM_ShoppingCart->setBillingAddress($OSCOM_Customer->getDefaultAddressID());
          $OSCOM_ShoppingCart->resetBillingMethod();
        } elseif ( $OSCOM_ShoppingCart->hasShippingAddress() ) {
          $OSCOM_ShoppingCart->setBillingAddress($OSCOM_ShoppingCart->getShippingAddress());
          $OSCOM_ShoppingCart->resetBillingMethod();
        }
      }

      if ( $OSCOM_ShoppingCart->hasBillingMethod() === false ) {
        if ( Registry::exists('Payment') === false ) {
          Registry::set('Payment', new Payment());
        }

        $OSCOM_Payment = Registry::get('Payment');
        $OSCOM_Payment->loadAll();

        if ( $OSCOM_Payment->hasActive() ) {
          $payment_modules = $OSCOM_Payment->getActive();
          $payment_module = $payment_modules[0];

          $OSCOM_PaymentModule = Registry::get('Payment_' . $payment_module);

          $OSCOM_ShoppingCart->setBillingMethod(array('id' => $OSCOM_PaymentModule->getCode(),
                                                      'title' => $OSCOM_PaymentModule->getMethodTitle()));
        }
      }

      return $OSCOM_ShoppingCart->hasBillingAddress() && $OSCOM_ShoppingCart->hasBillingMethod();
    }

    public static function onFail(Product $OSCOM_Product) {
      $OSCOM_NavigationHistory = Registry::get('NavigationHistory');

      if ( !isset($_GET['Billing']) ) {
        $OSCOM_NavigationHistory->setSnapshot();

        OSCOM::redirect(OSCOM::getLink(null, 'Checkout', 'Billing', 'SSL'));
      }
    }
  }
?>
