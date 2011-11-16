<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core;

  class HttpRequest {
    protected static $_drivers = array('HttpRequest', 'Curl', 'Stream');

/**
 *
 * @param array $parameters url, header, parameters, method, certificate
 * @param string $driver 
 */

    public static function getResponse($parameters, $driver = null) {
      if ( !isset($driver) ) {
        foreach ( static::$_drivers as $d ) {
          if ( call_user_func(array('osCommerce\\OM\\Core\\HttpRequest\\' . $d, 'canUse')) ) {
            $driver = $d;

            break;
          }
        }
      }

      if ( !isset($parameters['header']) || !is_array($parameters['header'])) {
        $parameters['header'] = array();
      }

      if ( !isset($parameters['parameters']) ) {
        $parameters['parameters'] = '';
      }

      if ( !isset($parameters['method']) ) {
        $parameters['method'] = 'post';
      }

      $parameters['server'] = parse_url($parameters['url']);

      if ( !isset($parameters['server']['port']) ) {
        $parameters['server']['port'] = ($parameters['server']['scheme'] == 'https') ? 443 : 80;
      }

      if ( !isset($parameters['server']['path']) ) {
        $parameters['server']['path'] = '/';
      }

      if ( isset($parameters['server']['user']) && isset($parameters['server']['pass']) ) {
        $parameters['header'][] = 'Authorization: Basic ' . base64_encode($parameters['server']['user'] . ':' . $parameters['server']['pass']);
      }

      return trim(call_user_func(array('osCommerce\\OM\\Core\\HttpRequest\\' . $driver, 'execute'), $parameters));
    }
  }
?>
