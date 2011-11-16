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

  class getInstalled {
    public static function execute() {
      $result = array();
      $result['entries'] = array();

      foreach ( explode(';', MODULE_SERVICES_INSTALLED) as $sm ) {
        $result['entries'][] = array('code' => $sm);
      }

      $result['total'] = count($result['entries']);

      foreach ( $result['entries'] as &$module ) {
        $class = 'osCommerce\\OM\\Core\\Site\\Admin\\Module\\Service\\' . $module['code'];

        $OSCOM_SM = new $class();

        $module['code'] = $OSCOM_SM->getCode();
        $module['title'] = $OSCOM_SM->getTitle();
        $module['description'] = $OSCOM_SM->getDescription();
        $module['uninstallable'] = $OSCOM_SM->isUninstallable();
        $module['has_keys'] = $OSCOM_SM->hasKeys();
      }

      return $result;
    }
  }
?>
