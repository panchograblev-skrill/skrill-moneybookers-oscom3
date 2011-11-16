<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Application\Checkout\Action;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\ObjectInfo;
  use osCommerce\OM\Core\Site\Shop\Product;
  use osCommerce\OM\Core\Site\Shop\Payment;

  class Billing {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_Breadcrumb = Registry::get('Breadcrumb');
      $OSCOM_Template = Registry::get('Template');

      global $osC_oiAddress; // HPDL

      $application->setPageTitle(OSCOM::getDef('payment_method_heading'));
      $application->setPageContent('billing.php');

      if ( $OSCOM_Service->isStarted('Breadcrumb') ) {
        $OSCOM_Breadcrumb->add(OSCOM::getDef('breadcrumb_checkout_payment'), OSCOM::getLink(null, null, 'Billing', 'SSL'));
      }

// load billing address page if no default address exists
      if ( !$OSCOM_ShoppingCart->hasBillingAddress() ) {
        $application->setPageTitle(OSCOM::getDef('payment_address_heading'));
        $application->setPageContent('billing_address.php');

        $OSCOM_Template->addJavascriptFilename(OSCOM::getPublicSiteLink('javascript/checkout_payment_address.js'));
        $OSCOM_Template->addJavascriptPhpFilename(OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/assets/form_check.js.php');

        if ( !$OSCOM_Customer->isLoggedOn() ) {
          $osC_oiAddress = new ObjectInfo($OSCOM_ShoppingCart->getBillingAddress());
        }
      } else {
        $OSCOM_Template->addJavascriptFilename(OSCOM::getPublicSiteLink('javascript/checkout_payment.js'));

// load all enabled payment modules
        $OSCOM_Payment = Registry::get('Payment');
        $OSCOM_Payment->loadAll();

        $OSCOM_Template->addJavascriptBlock($OSCOM_Payment->getJavascriptBlocks());
      }

// HPDL
//      if (isset($_GET['payment_error']) && is_object(${$_GET['payment_error']}) && ($error = ${$_GET['payment_error']}->get_error())) {
//        $OSCOM_MessageStack->add('CheckoutBilling', $error['error'], 'error');
//      }
    }
  }
?>
