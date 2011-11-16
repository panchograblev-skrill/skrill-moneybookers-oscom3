<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Module\Service;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;

  class Debug implements \osCommerce\OM\Core\Site\Shop\ServiceInterface {
    public static function start() {
      $OSCOM_Language = Registry::get('Language');
      $OSCOM_MessageStack = Registry::get('MessageStack');

      if ( SERVICE_DEBUG_CHECK_LOCALE == '1' ) {
        $setlocale = setlocale(LC_TIME, explode(',', $OSCOM_Language->getLocale()));

        if ( ($setlocale === false) || ($setlocale === null) ) {
          $OSCOM_MessageStack->add('debug', 'Error: Locale does not exist: ' . $OSCOM_Language->getLocale(), 'error');
        }
      }

      if ( (SERVICE_DEBUG_CHECK_INSTALLATION_MODULE == '1') && file_exists(OSCOM::BASE_DIRECTORY . 'Core/Site/Setup') ) {
        $OSCOM_MessageStack->add('debug', sprintf(OSCOM::getDef('warning_install_directory_exists'), OSCOM::BASE_DIRECTORY . 'Core/Site/Setup'), 'warning');
      }

      if ( (SERVICE_DEBUG_CHECK_CONFIGURATION == '1') && is_writeable(OSCOM::BASE_DIRECTORY . 'Config/settings.ini') ) {
        $OSCOM_MessageStack->add('debug', sprintf(OSCOM::getDef('warning_config_file_writeable'), OSCOM::BASE_DIRECTORY . 'Config//settings.ini'), 'warning');
      }

      if ( (SERVICE_DEBUG_CHECK_SESSION_DIRECTORY == '1') && (OSCOM::getConfig('store_sessions') == '') ) {
        if ( !is_dir(OSCOM_Registry::get('Session')->getSavePath()) ) {
          $OSCOM_MessageStack->add('debug', sprintf(OSCOM::getDef('warning_session_directory_non_existent'), OSCOM_Registry::get('Session')->getSavePath()), 'warning');
        } elseif ( !is_writeable(OSCOM_Registry::get('Session')->getSavePath()) ) {
          $OSCOM_MessageStack->add('debug', sprintf(OSCOM::getDef('warning_session_directory_not_writeable'), OSCOM_Registry::get('Session')->getSavePath()), 'warning');
        }
      }

      if ( (SERVICE_DEBUG_CHECK_SESSION_AUTOSTART == '1') && (bool)ini_get('session.auto_start') ) {
        $OSCOM_MessageStack->add('debug', OSCOM::getDef('warning_session_auto_start'), 'warning');
      }

      if ( (SERVICE_DEBUG_CHECK_DOWNLOAD_DIRECTORY == '1') && (DOWNLOAD_ENABLED == '1') ) {
        if ( !is_dir(DIR_FS_DOWNLOAD) ) {
          $OSCOM_MessageStack->add('debug', sprintf(OSCOM::getDef('warning_download_directory_non_existent'), DIR_FS_DOWNLOAD), 'warning');
        }
      }

      return true;
    }

    public static function stop() {
      $OSCOM_MessageStack = Registry::get('MessageStack');
      $OSCOM_Template = Registry::get('Template');

      $time_start = explode(' ', OSCOM_TIMESTAMP_START);
      $time_end = explode(' ', microtime());
      $parse_time = number_format(($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0])), 3);

      if ( strlen(SERVICE_DEBUG_EXECUTION_TIME_LOG) > 0 ) {
        if ( !error_log(strftime('%c') . ' - ' . $_SERVER['REQUEST_URI'] . ' (' . $parse_time . 's)' . "\n", 3, SERVICE_DEBUG_EXECUTION_TIME_LOG)) {
          if ( !file_exists(SERVICE_DEBUG_EXECUTION_TIME_LOG) || !is_writable(SERVICE_DEBUG_EXECUTION_TIME_LOG) ) {
            $OSCOM_MessageStack->add('debug', 'Error: Execution time log file not writeable: ' . SERVICE_DEBUG_EXECUTION_TIME_LOG, 'error');
          }
        }
      }

      if ( SERVICE_DEBUG_EXECUTION_DISPLAY == '1' ) {
        $OSCOM_MessageStack->add('debug', 'Execution Time: ' . $parse_time . 's', 'warning');
      }

      if ( $OSCOM_Template->showDebugMessages() && $OSCOM_MessageStack->exists('debug') ) {
        echo $OSCOM_MessageStack->get('debug');
      }

      return true;
    }
  }
?>
