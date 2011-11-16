<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\HttpRequest;

  class HttpRequest {
    protected static $_methods = array('get' => HTTP_METH_GET,
                                       'post' => HTTP_METH_POST);

    public static function execute($parameters) {
      $h = new \HttpRequest($parameters['server']['scheme'] . '://' . $parameters['server']['host'] . $parameters['server']['path'] . (isset($parameters['server']['query']) ? '?' . $parameters['server']['query'] : ''), static::$_methods[$parameters['method']], array('redirect' => 5));

      if ( $parameters['method'] == 'post' ) {
        $post_params = array();

        parse_str($parameters['parameters'], $post_params);

        $h->setPostFields($post_params);
      }

      $h->send();

      return $h->getResponseBody();
    }

    public static function canUse() {
      return class_exists('\HttpRequest');
    }
  }
?>
