<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop;

  interface ProductVariantInterface {
    public static function parse($data);

    public static function allowsMultipleValues();

    public static function hasCustomValue();

    public static function getGroupTitle($data);

    public static function getValueTitle($data);
  }
?>
