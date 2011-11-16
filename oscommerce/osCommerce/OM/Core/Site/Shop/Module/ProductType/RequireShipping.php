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
  use osCommerce\OM\Core\Site\Shop\Product;

  class RequireShipping {
    public static function getTitle() {
      return 'Require Shipping';
    }

    public static function getDescription() {
      return 'Require shipping';
    }

    public static function isValid(Product $OSCOM_Product) {
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');
      $OSCOM_Customer = Registry::get('Customer');

      if ( $OSCOM_ShoppingCart->hasShippingAddress() === false ) {
        if ( $OSCOM_Customer->hasDefaultAddress() ) {
          $OSCOM_ShoppingCart->setShippingAddress($OSCOM_Customer->getDefaultAddressID());
          $OSCOM_ShoppingCart->resetShippingMethod();
        } elseif ( $OSCOM_ShoppingCart->hasBillingAddress() ) {
          $OSCOM_ShoppingCart->setShippingAddress($OSCOM_ShoppingCart->getBillingAddress());
          $OSCOM_ShoppingCart->resetShippingMethod();
        }
      }

      return $OSCOM_ShoppingCart->hasShippingAddress() && $OSCOM_ShoppingCart->hasShippingMethod();
    }

    public static function onFail(Product $OSCOM_Product) {
      $OSCOM_NavigationHistory = Registry::get('NavigationHistory');

      if ( !isset($_GET['Shipping']) ) {
        $OSCOM_NavigationHistory->setSnapshot();

        OSCOM::redirect(OSCOM::getLink(null, 'Checkout', 'Shipping', 'SSL'));
      }
    }
  }
?>
