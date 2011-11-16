<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Setup\Application\Install\Model;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\PDO;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Admin\Application\Administrators\Administrators;

  class configureShop {
    public static function execute($data) {
      Registry::set('PDO', PDO::initialize($data['server'], $data['username'], $data['password'], $data['database'], $data['port'], $data['class']));

      OSCOM::setConfig('db_table_prefix', $data['table_prefix'], 'Admin');
      OSCOM::setConfig('db_table_prefix', $data['table_prefix'], 'Shop');
      OSCOM::setConfig('db_table_prefix', $data['table_prefix'], 'Setup');

      $cfg_data = array(array('key' => 'STORE_NAME',
                              'value' => $data['shop_name']),
                        array('key' => 'STORE_OWNER',
                              'value' => $data['shop_owner_name']),
                        array('key' => 'STORE_OWNER_EMAIL_ADDRESS',
                              'value' => $data['shop_owner_email']),
                        array('key' => 'EMAIL_FROM',
                              'value' => '"' . $data['shop_owner_name'] . '" <' . $data['shop_owner_email'] . '>')
                       );

      OSCOM::callDB('Admin\UpdateConfigurationParameters', $cfg_data, 'Site');

      $admin_data = array('username' => $data['admin_username'],
                          'password' => $data['admin_password'],
                          'modules' => array('0'));

      Administrators::save($admin_data);
    }
  }
?>
