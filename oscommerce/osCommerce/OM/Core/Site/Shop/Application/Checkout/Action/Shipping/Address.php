<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Application\Checkout\Action\Shipping;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\ObjectInfo;

  class Address {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_Template = Registry::get('Template');
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_Breadcrumb = Registry::get('Breadcrumb');
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');

      global $osC_oiAddress;

      $application->setPageTitle(OSCOM::getDef('shipping_address_heading'));
      $application->setPageContent('shipping_address.php');

      $OSCOM_Template->addJavascriptFilename(OSCOM::getPublicSiteLink('javascript/checkout_shipping_address.js'));
      $OSCOM_Template->addJavascriptPhpFilename(OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/assets/form_check.js.php');

      if ( $OSCOM_Service->isStarted('Breadcrumb') ) {
        $OSCOM_Breadcrumb->add(OSCOM::getDef('breadcrumb_checkout_shipping_address'), OSCOM::getLink(null, null, 'Shipping&Address', 'SSL'));
      }

      if ( !$OSCOM_Customer->isLoggedOn() ) {
        $osC_oiAddress = new ObjectInfo($OSCOM_ShoppingCart->getShippingAddress());
      }
    }
  }
?>
