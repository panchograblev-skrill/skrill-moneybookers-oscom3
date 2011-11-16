<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Module\Service;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Shop\CategoryTree;
  use osCommerce\OM\Core\Site\Shop\Category;

  class CategoryPath implements \osCommerce\OM\Core\Site\Shop\ServiceInterface {
    public static function start() {
      Registry::set('CategoryTree', new CategoryTree());
      Registry::set('Category', new Category());

      return true;
    }

    public static function stop() {
      return true;
    }
  }
?>
