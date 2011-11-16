<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Application\Index;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Shop\Products;

  class Controller extends \osCommerce\OM\Core\Site\Shop\ApplicationAbstract {

    protected function initialize() {}

    protected function process() {
      $OSCOM_Category = Registry::get('Category');
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_Language = Registry::get('Language');
      $OSCOM_Breadcrumb = Registry::get('Breadcrumb');

      $this->_page_title = sprintf(OSCOM::getDef('index_heading'), STORE_NAME);

      if ( $OSCOM_Category->getID() > 0 ) {
        if ( $OSCOM_Service->isStarted('Breadcrumb') ) {
          $Qcategories = $OSCOM_PDO->prepare('select categories_id, categories_name from :table_categories_description where categories_id in (' . implode(',', $OSCOM_Category->getPathArray()) . ') and language_id = :language_id');
          $Qcategories->bindInt(':language_id', $OSCOM_Language->getID());
          $Qcategories->execute();

          $categories = array();

          while ( $Qcategories->fetch() ) {
            $categories[$Qcategories->value('categories_id')] = $Qcategories->valueProtected('categories_name');
          }

          for ( $i=0, $n=sizeof($OSCOM_Category->getPathArray()); $i<$n; $i++ ) {
            $OSCOM_Breadcrumb->add($categories[$OSCOM_Category->getPathArray($i)], OSCOM::getLink(null, 'Index', 'cPath=' . implode('_', array_slice($OSCOM_Category->getPathArray(), 0, ($i+1)))));
          }
        }

        $this->_page_title = $OSCOM_Category->getTitle();

        if ( $OSCOM_Category->hasImage() ) {
//HPDL          $this->_page_image = 'categories/' . $OSCOM_Category->getImage();
        }

        $Qproducts = $OSCOM_PDO->prepare('select products_id from :table_products_to_categories where categories_id = :categories_id limit 1');
        $Qproducts->bindInt(':categories_id', $OSCOM_Category->getID());
        $Qproducts->execute();

        if ( count($Qproducts->fetchAll()) > 0 ) {
          $this->_page_contents = 'product_listing.php';

          $this->_process();
        } else {
          $Qparent = $OSCOM_PDO->prepare('select categories_id from :table_categories where parent_id = :parent_id limit 1');
          $Qparent->bindInt(':parent_id', $OSCOM_Category->getID());
          $Qparent->execute();

          if ( count($Qparent->fetchAll()) > 0 ) {
            $this->_page_contents = 'category_listing.php';
          } else {
            $this->_page_contents = 'product_listing.php';

            $this->_process();
          }
        }
      }
    }

    protected function _process() {
      $OSCOM_Category = Registry::get('Category');

      Registry::set('Products', new Products($OSCOM_Category->getID()));
      $OSCOM_Products = Registry::get('Products');

      if ( isset($_GET['filter']) && is_numeric($_GET['filter']) && ($_GET['filter'] > 0) ) {
        $OSCOM_Products->setManufacturer($_GET['filter']);
      }

      if ( isset($_GET['sort']) && !empty($_GET['sort']) ) {
        if ( strpos($_GET['sort'], '|d') !== false ) {
          $OSCOM_Products->setSortBy(substr($_GET['sort'], 0, -2), '-');
        } else {
          $OSCOM_Products->setSortBy($_GET['sort']);
        }
      }
    }
  }
?>
