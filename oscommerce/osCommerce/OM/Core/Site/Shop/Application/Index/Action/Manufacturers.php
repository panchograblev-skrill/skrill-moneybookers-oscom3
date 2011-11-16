<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Application\Index\Action;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Shop\Manufacturer;
  use osCommerce\OM\Core\Site\Shop\Products;

  class Manufacturers {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_Breadcrumb = Registry::get('Breadcrumb');

      $application->setPageTitle(sprintf(OSCOM::getDef('index_heading'), STORE_NAME));
      $application->setPageContent('product_listing.php');

      if ( is_numeric($_GET['Manufacturers']) ) {
        Registry::set('Manufacturer', new Manufacturer($_GET['Manufacturers']));
        $OSCOM_Manufacturer = Registry::get('Manufacturer');

        $application->setPageTitle($OSCOM_Manufacturer->getTitle());
// HPDL        $application->setPageImage('manufacturers/' . $OSCOM_Manufacturer->getImage());

        if ( $OSCOM_Service->isStarted('Breadcrumb') ) {
          $OSCOM_Breadcrumb->add($OSCOM_Manufacturer->getTitle(), OSCOM::getLink());
        }

        Registry::set('Products', new Products());
        $OSCOM_Products = Registry::get('Products');
        $OSCOM_Products->setManufacturer($OSCOM_Manufacturer->getID());

        if ( isset($_GET['filter']) && is_numeric($_GET['filter']) && ($_GET['filter'] > 0) ) {
          $OSCOM_Products->setCategory($_GET['filter']);
        }

        if ( isset($_GET['sort']) && !empty($_GET['sort']) ) {
          if ( strpos($_GET['sort'], '|d') !== false ) {
            $OSCOM_Products->setSortBy(substr($_GET['sort'], 0, -2), '-');
          } else {
            $OSCOM_Products->setSortBy($_GET['sort']);
          }
        }
      } else {
        OSCOM::redirect(OSCOM::getLink(OSCOM::getDefaultSite(), OSCOM::getDefaultSiteApplication()));
      }
    }
  }
?>
