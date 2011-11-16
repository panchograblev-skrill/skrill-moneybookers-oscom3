<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Administrators\Model;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\DirectoryListing;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Access;

  class getAccessModules {
    public static function execute() {
      $OSCOM_Language = Registry::get('Language');

      $module_files = array();

      $DLapps = new DirectoryListing(OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::getSite() . '/Application');
      $DLapps->setIncludeFiles(false);

      foreach ( $DLapps->getFiles() as $file ) {
        if ( !in_array($file['name'], call_user_func(array('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Controller', 'getGuestApplications'))) && file_exists($DLapps->getDirectory() . '/' . $file['name'] . '/Controller.php') ) {
          $module_files[] = $file['name'];
        }
      }

      $DLcapps = new DirectoryListing(OSCOM::BASE_DIRECTORY . 'Custom/Site/' . OSCOM::getSite() . '/Application');
      $DLcapps->setIncludeFiles(false);

      foreach ( $DLcapps->getFiles() as $file ) {
        if ( !in_array($file['name'], $module_files) && !in_array($file['name'], call_user_func(array('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Controller', 'getGuestApplications'))) && file_exists($DLcapps->getDirectory() . '/' . $file['name'] . '/Controller.php') ) {
          $module_files[] = $file['name'];
        }
      }

      $modules = array();

      foreach ( $module_files as $module ) {
        $application_class = 'osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Application\\' . $module . '\\Controller';

        if ( class_exists($application_class) ) {
          if ( $module == OSCOM::getSiteApplication() ) {
            $OSCOM_Application = Registry::get('Application');
          } else {
            Registry::get('Language')->loadIniFile($module . '.php');
            $OSCOM_Application = new $application_class(false);
          }

          $modules[Access::getGroupTitle($OSCOM_Application->getGroup())][] = array('id' => $module,
                                                                                    'text' => $OSCOM_Application->getTitle(),
                                                                                    'icon' => $OSCOM_Application->getIcon());
        }
      }

      ksort($modules);

      return $modules;
    }
  }
?>
