<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Module\Service;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Shop\Customer;
  use osCommerce\OM\Core\Site\Shop\Tax;
  use osCommerce\OM\Core\Site\Shop\Weight;
  use osCommerce\OM\Core\Site\Shop\ShoppingCart;
  use osCommerce\OM\Core\Site\Shop\NavigationHistory;
  use osCommerce\OM\Core\Site\Shop\Image;

  class Core implements \osCommerce\OM\Core\Site\Shop\ServiceInterface {
    public static function start() {
      Registry::set('Customer', new Customer());

      Registry::set('Tax', new Tax());

      Registry::set('Weight', new Weight());

      Registry::set('ShoppingCart', new ShoppingCart());
      Registry::get('ShoppingCart')->refresh();

      Registry::set('NavigationHistory', new NavigationHistory(true));

      Registry::set('Image', new Image());

      return true;
    }

    public static function stop() {
      return true;
    }
  }
?>
