<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Module\Service;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Shop\Specials as SpecialsClass;

  class Specials implements \osCommerce\OM\Core\Site\Shop\ServiceInterface {
    public static function start() {
      Registry::set('Specials', new SpecialsClass());

      $OSCOM_Specials = Registry::get('Specials');

      $OSCOM_Specials->activateAll();
      $OSCOM_Specials->expireAll();

      return true;
    }

    public static function stop() {
      return true;
    }
  }
?>
