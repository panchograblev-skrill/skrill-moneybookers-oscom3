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
  use osCommerce\OM\Core\Site\Shop\Reviews;

  class Process {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_NavigationHistory = Registry::get('NavigationHistory');
      $OSCOM_MessageStack = Registry::get('MessageStack');
      $OSCOM_Reviews = Registry::get('Reviews');
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

      $data = array('products_id' => $OSCOM_Product->getID());

      if ( $OSCOM_Customer->isLoggedOn() ) {
        $data['customer_id'] = $OSCOM_Customer->getID();
        $data['customer_name'] = $OSCOM_Customer->getName();
      } else {
        $data['customer_id'] = '0';
        $data['customer_name'] = $_POST['customer_name'];
      }

      if ( strlen(trim($_POST['review'])) < REVIEW_TEXT_MIN_LENGTH ) {
        $OSCOM_MessageStack->add('Reviews', sprintf(OSCOM::getDef('js_review_text'), REVIEW_TEXT_MIN_LENGTH));
      } else {
        $data['review'] = $_POST['review'];
      }

      if ( ($_POST['rating'] < 1) || ($_POST['rating'] > 5) ) {
        $OSCOM_MessageStack->add('Reviews', OSCOM::getDef('js_review_rating'));
      } else {
        $data['rating'] = $_POST['rating'];
      }

      if ( $OSCOM_MessageStack->size('Reviews') < 1 ) {
        if ( $OSCOM_Reviews->isModerated() ) {
          $data['status'] = '0';

          $OSCOM_MessageStack->add('Reviews', OSCOM::getDef('success_review_moderation'), 'success');
        } else {
          $data['status'] = '1';

          $OSCOM_MessageStack->add('Reviews', OSCOM::getDef('success_review_new'), 'success');
        }

        Reviews::saveEntry($data);

        OSCOM::redirect(OSCOM::getLink(null, null, 'Reviews&' . $OSCOM_Product->getID()));
      }

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
