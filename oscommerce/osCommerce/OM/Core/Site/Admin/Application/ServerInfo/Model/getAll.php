<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\ServerInfo\Model;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\DateTime;

  class getAll {
    public static function execute() {
      $result = array();

      $db_time = OSCOM::callDB('Admin\ServerInfo\GetTime');
      $db_uptime = OSCOM::callDB('Admin\ServerInfo\GetUptime');
      $db_version = OSCOM::callDB('Admin\ServerInfo\GetVersion');

      $uptime = '---';

      if ( !in_array('exec', explode(',', str_replace(' ', '', ini_get('disable_functions')))) ) {
        $uptime = @exec('uptime');
      }

      $data = array(array('key' => 'date',
                          'title' => OSCOM::getDef('field_server_date'),
                          'value' => DateTime::getShort(null, true)),
                    array('key' => 'system',
                          'title' => OSCOM::getDef('field_server_operating_system'),
                          'value' => php_uname('s') . ' ' . php_uname('r')),
                    array('key' => 'host',
                          'title' => OSCOM::getDef('field_server_host'),
                          'value' => php_uname('n') . ' (' . gethostbyname(php_uname('n')) . ')'),
                    array('key' => 'uptime',
                          'title' => OSCOM::getDef('field_server_up_time'),
                          'value' => $uptime),
                    array('key' => 'http_server',
                          'title' => OSCOM::getDef('field_http_server'),
                          'value' => $_SERVER['SERVER_SOFTWARE']),
                    array('key' => 'php',
                          'title' => OSCOM::getDef('field_php_version'),
                          'value' => 'PHP v' . PHP_VERSION . ' / Zend v' . zend_version()),
                    array('key' => 'db_server',
                          'title' => OSCOM::getDef('field_database_host'),
                          'value' => OSCOM::getConfig('db_server') . ' (' . gethostbyname(OSCOM::getConfig('db_server')) . ')'),
                    array('key' => 'db_version',
                          'title' => OSCOM::getDef('field_database_version'),
                          'value' => $db_version),
                    array('key' => 'db_date',
                          'title' => OSCOM::getDef('field_database_date'),
                          'value' => DateTime::getShort($db_time, true)),
                    array('key' => 'db_uptime',
                          'title' => OSCOM::getDef('field_database_up_time'),
                          'value' => $db_uptime));

      $result['entries'] = $data;

      $result['total'] = count($data);

      return $result;
    }
  }
?>
