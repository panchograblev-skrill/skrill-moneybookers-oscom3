<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Configuration\Model;

  use osCommerce\OM\Core\OSCOM;

  class callUserFunc {
    public static function execute($function, $default = null, $key = null) {
      if ( strpos($function, '::') !== false ) {
        $class_method = explode('::', $function);

        return call_user_func(array($class_method[0], $class_method[1]), $default, $key);
      } else {
        $function_name = $function;
        $function_parameter = '';

        if ( strpos($function, '(') !== false ) {
          $function_array = explode('(', $function, 2);

          $function_name = $function_array[0];
          $function_parameter = substr($function_array[1], 0, -1);
        }

        if ( !function_exists($function_name) ) {
          include(OSCOM::BASE_DIRECTORY . 'Core/Site/Admin/assets/cfg_parameters/' . $function_name . '.php');
        }

        if ( !empty($function_parameter) ) {
          return call_user_func($function_name, $function_parameter, $default, $key);
        } else {
          return call_user_func($function_name, $default, $key);
        }
      }
    }
  }
?>
