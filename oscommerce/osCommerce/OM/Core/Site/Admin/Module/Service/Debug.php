<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Module\Service;

  use osCommerce\OM\Core\OSCOM;

/**
 * @since v3.0.2
 */

  class Debug extends \osCommerce\OM\Core\Site\Admin\ServiceAbstract {
    var $depends = 'Language';

    protected function initialize() {
      $this->title = OSCOM::getDef('services_debug_title');
      $this->description = OSCOM::getDef('services_debug_description');
    }

    public function install() {
      $data = array(array('title' => 'Page Execution Time Log File',
                          'key' => 'SERVICE_DEBUG_EXECUTION_TIME_LOG',
                          'value' => '',
                          'description' => 'Location of the page execution time log file (eg, /www/log/page_parse.log).',
                          'group_id' => '6'),
                    array('title' => 'Show The Page Execution Time',
                          'key' => 'SERVICE_DEBUG_EXECUTION_DISPLAY',
                          'value' => '1',
                          'description' => 'Show the page execution time.',
                          'group_id' => '6',
                          'use_function' => 'osc_cfg_use_get_boolean_value',
                          'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))'),
                    array('title' => 'Log Database Queries',
                          'key' => 'SERVICE_DEBUG_LOG_DB_QUERIES',
                          'value' => '-1',
                          'description' => 'Log all database queries in the page execution time log file.',
                          'group_id' => '6',
                          'use_function' => 'osc_cfg_use_get_boolean_value',
                          'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))'),
                    array('title' => 'Show Database Queries',
                          'key' => 'SERVICE_DEBUG_OUTPUT_DB_QUERIES',
                          'value' => '-1',
                          'description' => 'Show all database queries made.',
                          'group_id' => '6',
                          'use_function' => 'osc_cfg_use_get_boolean_value',
                          'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))'),
                    array('title' => 'Check Language Locale',
                          'key' => 'SERVICE_DEBUG_CHECK_LOCALE',
                          'value' => '1',
                          'description' => 'Show a warning message if the set language locale does not exist on the server.',
                          'group_id' => '6',
                          'use_function' => 'osc_cfg_use_get_boolean_value',
                          'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))'),
                    array('title' => 'Check Installation Module',
                          'key' => 'SERVICE_DEBUG_CHECK_INSTALLATION_MODULE',
                          'value' => '1',
                          'description' => 'Show a warning message if the installation module exists.',
                          'group_id' => '6',
                          'use_function' => 'osc_cfg_use_get_boolean_value',
                          'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))'),
                    array('title' => 'Check Configuration File',
                          'key' => 'SERVICE_DEBUG_CHECK_CONFIGURATION',
                          'value' => '1',
                          'description' => 'Show a warning if the configuration file is writeable.',
                          'group_id' => '6',
                          'use_function' => 'osc_cfg_use_get_boolean_value',
                          'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))'),
                    array('title' => 'Check Sessions Directory',
                          'key' => 'SERVICE_DEBUG_CHECK_SESSION_DIRECTORY',
                          'value' => '1',
                          'description' => 'Show a warning if the file-based session directory does not exist.',
                          'group_id' => '6',
                          'use_function' => 'osc_cfg_use_get_boolean_value',
                          'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))'),
                    array('title' => 'Check Sessions Auto Start',
                          'key' => 'SERVICE_DEBUG_CHECK_SESSION_AUTOSTART',
                          'value' => '1',
                          'description' => 'Show a warning if PHP is configured to automatically start sessions.',
                          'group_id' => '6',
                          'use_function' => 'osc_cfg_use_get_boolean_value',
                          'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))'),
                    array('title' => 'Check Download Directory',
                          'key' => 'SERVICE_DEBUG_CHECK_DOWNLOAD_DIRECTORY',
                          'value' => '1',
                          'description' => 'Show a warning if the digital product download directory does not exist.',
                          'group_id' => '6',
                          'use_function' => 'osc_cfg_use_get_boolean_value',
                          'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))')
                   );

      OSCOM::callDB('Admin\InsertConfigurationParameters', $data, 'Site');
    }

    public function remove() {
      OSCOM::callDB('Admin\DeleteConfigurationParameters', $this->keys(), 'Site');
    }

    public function keys() {
      return array('SERVICE_DEBUG_OUTPUT_DB_QUERIES',
                   'SERVICE_DEBUG_LOG_DB_QUERIES',
                   'SERVICE_DEBUG_EXECUTION_TIME_LOG',
                   'SERVICE_DEBUG_EXECUTION_DISPLAY',
                   'SERVICE_DEBUG_SHOW_DEVELOPMENT_WARNING',
                   'SERVICE_DEBUG_CHECK_LOCALE',
                   'SERVICE_DEBUG_CHECK_INSTALLATION_MODULE',
                   'SERVICE_DEBUG_CHECK_CONFIGURATION',
                   'SERVICE_DEBUG_CHECK_SESSION_DIRECTORY',
                   'SERVICE_DEBUG_CHECK_SESSION_AUTOSTART',
                   'SERVICE_DEBUG_CHECK_DOWNLOAD_DIRECTORY');
    }
  }
?>
