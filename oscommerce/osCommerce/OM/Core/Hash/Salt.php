<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Hash;

  class Salt {
    public static function get($string) {
      $hash = '';

      for ($i=0; $i<10; $i++) {
        $hash .= mt_rand();
      }

      $salt = substr(md5($hash), 0, 2);

      $hash = md5($salt . $string) . ':' . $salt;

      return $hash;
    }

    public static function validate($plain, $hashed) {
      if ( !empty($plain) && !empty($hashed) ) {
  // split apart the hash / salt
        $stack = explode(':', $hashed);

        if ( count($stack) != 2 ) {
          return false;
        }

        return ( md5($stack[1] . $plain) == $stack[0] );
      }

      return false;
    }

    public static function canUse() {
      return function_exists('mt_rand');
    }
  }
?>
