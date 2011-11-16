<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Application\Products\Action\Reviews;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Shop\Product;
  use osCommerce\OM\Core\OSCOM;

  class Write {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_NavigationHistory = Registry::get('NavigationHistory');
      $OSCOM_Template = Registry::get('Template');
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_Breadcrumb = Registry::get('Breadcrumb');

      $requested_product = null;
      $product_check = false;

      if ( count($_GET) > 3 ) {
        $requested_product = basename(key(array_slice($_GET, 3, 1, true)));

        if ( $requested_product == 'Write' ) {
          unset($requested_product);

          if ( count($_GET) > 4 ) {
            $requested_product = basename(key(array_slice($_GET, 4, 1, true)));
          }
        }
      }

      if ( isset($requested_product) ) {
        if ( Product::checkEntry($requested_product) ) {
          $product_check = true;
        }
      }

      if ( $product_check === false ) {
        $application->setPageContent('not_found.php');

        return false;
      }

      if ( ($OSCOM_Customer->isLoggedOn() === false) && (SERVICE_REVIEW_ENABLE_REVIEWS == 1) ) {
        $OSCOM_NavigationHistory->setSnapshot();

        OSCOM::redirect(OSCOM::getLink(null, 'Account', 'LogIn', 'SSL'));
      }

      Registry::set('Product', new Product($requested_product));
      $OSCOM_Product = Registry::get('Product');

      $application->setPageTitle($OSCOM_Product->getTitle());
      $application->setPageContent('reviews_write.php');
      $OSCOM_Template->addJavascriptPhpFilename(OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/assets/reviews_new.php');

      if ( $OSCOM_Service->isStarted('Breadcrumb')) {
        $OSCOM_Breadcrumb->add($OSCOM_Product->getTitle(), OSCOM::getLink(null, null, 'Reviews&' . $OSCOM_Product->getKeyword()));
        $OSCOM_Breadcrumb->add(OSCOM::getDef('breadcrumb_reviews_new'), OSCOM::getLink(null, null, 'Reviews&Write&' . $OSCOM_Product->getKeyword()));
      }
    }
  }
?>
