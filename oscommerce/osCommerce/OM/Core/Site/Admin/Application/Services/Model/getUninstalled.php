<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Services\Model;

  use osCommerce\OM\Core\DirectoryListing;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Admin\Application\Services\Services;

/**
 * @since v3.0.2
 */

  class getUninstalled {
    public static function execute() {
      $installed_modules = Services::getInstalled();
      $installed = array();

      foreach ( $installed_modules['entries'] as $module ) {
        $installed[] = $module['code'];
      }

      $result = array('entries' => array());

      $DLsm = new DirectoryListing(OSCOM::BASE_DIRECTORY . 'Core/Site/Admin/Module/Service');
      $DLsm->setIncludeDirectories(false);

      foreach ( $DLsm->getFiles() as $file ) {
        $module = substr($file['name'], 0, strrpos($file['name'], '.'));

        if ( !in_array($module, $installed) ) {
          $class = 'osCommerce\\OM\\Core\\Site\\Admin\\Module\\Service\\' . $module;

          $OSCOM_SM = new $class();

          $result['entries'][] = array('code' => $OSCOM_SM->getCode(),
                                       'title' => $OSCOM_SM->getTitle());
        }
      }

      $result['total'] = count($result['entries']);

      return $result;
    }
  }
?>
