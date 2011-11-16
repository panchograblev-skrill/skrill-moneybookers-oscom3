<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Application\Products\Action;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Shop\Product;

  class TellAFriend {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_NavigationHistory = Registry::get('NavigationHistory');
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_Breadcrumb = Registry::get('Breadcrumb');

      if ( (ALLOW_GUEST_TO_TELL_A_FRIEND == '-1') && ($OSCOM_Customer->isLoggedOn() === false) ) {
        $OSCOM_NavigationHistory->setSnapshot();

        OSCOM::redirect(OSCOM::getLink(null, 'Account', 'LogIn', 'SSL'));
      }

      $requested_product = null;
      $product_check = false;

      if ( count($_GET) > 2 ) {
        $requested_product = basename(key(array_slice($_GET, 2, 1, true)));

        if ( $requested_product == OSCOM::getSiteApplication() ) {
          unset($requested_product);

          if ( count($_GET) > 3 ) {
            $requested_product = basename(key(array_slice($_GET, 3, 1, true)));
          }
        }
      }

      if ( isset($requested_product) ) {
        if ( Product::checkEntry($requested_product) ) {
          $product_check = true;

          Registry::set('Product', new Product($requested_product));
          $OSCOM_Product = Registry::get('Product');

          $application->setPageTitle($OSCOM_Product->getTitle());
          $application->setPageContent('tell_a_friend.php');

          if ( $OSCOM_Service->isStarted('Breadcrumb')) {
            $OSCOM_Breadcrumb->add($OSCOM_Product->getTitle(), OSCOM::getLink(null, null, $OSCOM_Product->getKeyword()));
            $OSCOM_Breadcrumb->add(OSCOM::getDef('breadcrumb_tell_a_friend'), OSCOM::getLink(null, null, 'TellAFriend&' . $OSCOM_Product->getKeyword()));
          }
        }

        if ( $product_check === false ) {
          $application->setPageContent('not_found.php');
        }
      }
    }
  }
?>
