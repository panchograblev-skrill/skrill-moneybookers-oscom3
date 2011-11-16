<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Module\ProductType;

  use osCommerce\OM\Core\Site\Shop\Product;

  class False {
    public static function getTitle() {
      return 'False';
    }

    public static function getDescription() {
      return 'Fail action with false';
    }

    public static function isValid(Product $OSCOM_Product) {
      return false;
    }
  }
?>
