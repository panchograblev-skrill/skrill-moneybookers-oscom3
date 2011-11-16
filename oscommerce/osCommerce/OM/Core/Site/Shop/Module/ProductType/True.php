<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Module\ProductType;

  use osCommerce\OM\Core\Site\Shop\Product;

  class True {
    public static function getTitle() {
      return 'True';
    }

    public static function getDescription() {
      return 'Pass action with true';
    }

    public static function isValid(Product $OSCOM_Product) {
      return true;
    }
  }
?>
