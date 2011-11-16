<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Application\Checkout\Action;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\HTML;

  class Callback {
    public static function execute(ApplicationAbstract $application) {
      if ( isset($_GET['module']) && !empty($_GET['module']) ) {
        $module = HTML::sanitize($_GET['module']);

        if ( class_exists('osCommerce\\OM\\Core\\Site\\Shop\\Module\\Payment\\' . $module) ) {
          $module = 'osCommerce\\OM\\Core\\Site\\Shop\\Module\\Payment\\' . $module;
          $module = new $module();
          $module->callback();
        }
      }

      exit;
    }
  }
?>
