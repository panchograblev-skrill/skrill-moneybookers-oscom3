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

  class Images {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_Template = Registry::get('Template');
      $OSCOM_NavigationHistory = Registry::get('NavigationHistory');

// HPDL
      $OSCOM_Template->setHasHeader(false);
      $OSCOM_Template->setHasFooter(false);
      $OSCOM_Template->setHasBoxModules(false);
      $OSCOM_Template->setHasContentModules(false);
      $OSCOM_Template->setShowDebugMessages(false);

      $OSCOM_NavigationHistory->removeCurrentPage();

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
        if ( !$application->siteApplicationActionExists($requested_product) ) {
          if ( Product::checkEntry($requested_product) ) {
            $product_check = true;

            Registry::set('Product', new Product($requested_product));
            $OSCOM_Product = Registry::get('Product');

            $OSCOM_Template->addPageTags('keywords', $OSCOM_Product->getTitle());
            $OSCOM_Template->addPageTags('keywords', $OSCOM_Product->getModel());

            if ( $OSCOM_Product->hasTags() ) {
              $OSCOM_Template->addPageTags('keywords', $OSCOM_Product->getTags());
            }

            $application->setPageTitle($OSCOM_Product->getTitle());
            $application->setPageContent('images.php');
          }
        }
      }

      if ( $product_check === false ) {
        $application->setPageTitle(OSCOM::getDef('product_not_found_heading'));
        $application->setPageContent('not_found.php');
      }
    }
  }
?>