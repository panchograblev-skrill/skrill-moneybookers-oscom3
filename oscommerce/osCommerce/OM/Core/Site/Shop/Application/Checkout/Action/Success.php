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

  class Success {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_Breadcrumb = Registry::get('Breadcrumb');

      $OSCOM_ShoppingCart->reset(true);

// unregister session variables used during checkout
      unset($_SESSION['comments']);

      $application->setPageTitle(OSCOM::getDef('success_heading'));
      $application->setPageContent('success.php');

      if ( $OSCOM_Service->isStarted('Breadcrumb') ) {
        $OSCOM_Breadcrumb->add(OSCOM::getDef('breadcrumb_checkout_success'), OSCOM::getLink(null, null, 'Success', 'SSL'));
      }

//      if ( $_GET[$this->_module] == 'update' ) {
//        $this->_process();
//      }
    }

    protected function _process() {
      global $osC_Customer;

      $notify_string = '';

      if ( $osC_Customer->isLoggedOn() ) {
        $products_array = (isset($_POST['notify']) ? $_POST['notify'] : array());

        if ( !is_array($products_array) ) {
          $products_array = array($products_array);
        }

        $notifications = array();

        foreach ( $products_array as $product_id ) {
          if ( is_numeric($product_id) && !in_array($product_id, $notifications) ) {
            $notifications[] = $product_id;
          }
        }

        if ( !empty($notifications) ) {
          $notify_string = 'action=notify_add&products=' . implode(';', $notifications);
        }
      }

      osc_redirect(osc_href_link(FILENAME_DEFAULT, $notify_string, 'AUTO'));
    }
  }
?>
