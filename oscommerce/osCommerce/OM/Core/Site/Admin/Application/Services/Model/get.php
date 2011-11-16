<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Services\Model;

/**
 * @since v3.0.2
 */

  class get {
    public static function execute($code, $key = null) {
      $class = 'osCommerce\\OM\\Core\\Site\\Admin\\Module\\Service\\' . $code;

      $OSCOM_SM = new $class();

      $result = array('code' => $OSCOM_SM->getCode(),
                      'title' => $OSCOM_SM->getTitle(),
                      'description' => $OSCOM_SM->getDescription(),
                      'uninstallable' => $OSCOM_SM->isUninstallable(),
                      'keys' => $OSCOM_SM->keys());

      if ( isset($key) ) {
        $result = $result[$key] ?: null;
      }

      return $result;
    }
  }
?>
