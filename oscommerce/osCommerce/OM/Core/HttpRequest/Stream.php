<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\HttpRequest;

  class Stream {
    public static function execute($parameters) {
      $options = array('http' => array('method' => ($parameters['method'] == 'get' ? 'GET' : 'POST'),
                                       'follow_location' => true,
                                       'max_redirects' => 5,
                                       'content' => $parameters['parameters']));

      if ( !isset($parameters['header']) ) {
        $parameters['header'] = array();
      }

      $parameters['header'][] = 'Content-type: application/x-www-form-urlencoded';

      $options['http']['header'] = implode("\r\n", $parameters['header']);

      if ( !empty($parameters['certificate']) ) {
        $options['ssl'] = array('local_cert' => $parameters['certificate']);
      }

      $context = stream_context_create($options);

      return file_get_contents($parameters['url'], false, $context);
    }

    public static function canUse() {
      return extension_loaded('openssl');
    }
  }
?>
