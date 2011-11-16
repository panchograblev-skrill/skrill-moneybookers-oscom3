<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Application\Cart\Action;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Shop\Product;
  use osCommerce\OM\Core\OSCOM;

  class Add {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');

      $requested_product = null;

      if ( count($_GET) > 2 ) {
        $requested_product = basename(key(array_slice($_GET, 2, 1, true)));

        if ( $requested_product == 'Add' ) {
          unset($requested_product);

          if ( count($_GET) > 3 ) {
            $requested_product = basename(key(array_slice($_GET, 3, 1, true)));
          }
        }
      }

      if ( isset($requested_product) ) {
        if ( Product::checkEntry($requested_product) ) {
          $OSCOM_Product = new Product($requested_product);

          if ( $OSCOM_Product->isTypeActionAllowed('AddToShoppingCart') ) {
            if ( $OSCOM_Product->hasVariants() ) {
              if ( isset($_POST['variants']) && is_array($_POST['variants']) && !empty($_POST['variants']) ) {
                if ( $OSCOM_Product->variantExists($_POST['variants']) ) {
                  $OSCOM_ShoppingCart->add($OSCOM_Product->getProductVariantID($_POST['variants']));
                } else {
                  OSCOM::redirect(OSCOM::getLink(null, 'Products', $OSCOM_Product->getKeyword()));
                }
              } else {
                OSCOM::redirect(OSCOM::getLink(null, 'Products', $OSCOM_Product->getKeyword()));
              }
            } else {
              $OSCOM_ShoppingCart->add($OSCOM_Product->getID());
            }
          }
        }
      }

      OSCOM::redirect(OSCOM::getLink(null, 'Cart'));
    }
  }
?>
