<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Application\Account\Action;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Shop\Order;

  class Orders {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_NavigationHistory = Registry::get('NavigationHistory');
      $OSCOM_Language = Registry::get('Language');
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_Breadcrumb = Registry::get('Breadcrumb');

      if ( $OSCOM_Customer->isLoggedOn() === false ) {
        $OSCOM_NavigationHistory->setSnapshot();

        OSCOM::redirect(OSCOM::getLink(null, null, 'LogIn', 'SSL'));
      }

      $application->setPageTitle(OSCOM::getDef('orders_heading'));
      $application->setPageContent('orders.php');

      $OSCOM_Language->load('order');

      if ( $OSCOM_Service->isStarted('Breadcrumb') ) {
        $OSCOM_Breadcrumb->add(OSCOM::getDef('breadcrumb_my_orders'), OSCOM::getLink(null, null, 'Orders', 'SSL'));

        if ( is_numeric($_GET['Orders']) ) {
          $OSCOM_Breadcrumb->add(sprintf(OSCOM::getDef('breadcrumb_order_information'), $_GET['Orders']), OSCOM::getLink(null, null, 'Orders=' . $_GET['Orders'], 'SSL'));
        }
      }

      if ( is_numeric($_GET['Orders']) ) {
        if ( Order::getCustomerID($_GET['Orders']) !== $OSCOM_Customer->getID() ) {
          OSCOM::redirect(OSCOM::getLink(null, null, null, 'SSL'));
        }

        $application->setPageTitle(sprintf(OSCOM::getDef('order_information_heading'), $_GET['Orders']));
        $application->setPageContent('orders_info.php');
      }
    }
  }
?>
