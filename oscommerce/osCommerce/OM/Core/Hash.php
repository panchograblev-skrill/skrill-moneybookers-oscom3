<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core;

  class Hash {
    protected static $_drivers = array('Salt');

/**
 *
 * @param string $string
 * @param string $driver 
 */

    public static function get($string, $driver = null) {
      if ( !isset($driver) ) {
        foreach ( static::$_drivers as $d ) {
          if ( call_user_func(array('osCommerce\\OM\\Core\\Hash\\' . $d, 'canUse')) ) {
            $driver = $d;

            break;
          }
        }
      }

      return call_user_func(array('osCommerce\\OM\\Core\\Hash\\' . $driver, 'get'), $string);
    }

    public static function validate($plain, $hashed, $driver = null) {
      if ( !isset($driver) ) {
        foreach ( static::$_drivers as $d ) {
          if ( call_user_func(array('osCommerce\\OM\\Core\\Hash\\' . $d, 'canUse')) ) {
            $driver = $d;

            break;
          }
        }
      }

      return call_user_func(array('osCommerce\\OM\\Core\\Hash\\' . $driver, 'validate'), $plain, $hashed);
    }

    public static function getRandomString($length, $type = 'mixed') {
      if ( !in_array($type, array('mixed', 'chars', 'digits')) ) {
        return false;
      }

      $chars_pattern = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz';
      $mixed_pattern = '1234567890' . $chars_pattern;

      $rand_value = '';

      while ( strlen($rand_value) < $length ) {
        if ( $type == 'digits' ) {
          $rand_value .= rand(0, 9);
        } elseif ( $type == 'chars' ) {
          $rand_value .= substr($chars_pattern, rand(0, 51), 1);
        } else {
          $rand_value .= substr($mixed_pattern, rand(0, 61), 1);
        }
      }

      return $rand_value;
    }
  }
?>
