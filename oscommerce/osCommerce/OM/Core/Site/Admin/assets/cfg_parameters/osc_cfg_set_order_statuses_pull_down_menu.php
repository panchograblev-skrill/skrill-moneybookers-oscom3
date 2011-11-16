<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  function osc_cfg_set_order_statuses_pull_down_menu($default, $key = null) {
    $OSCOM_PDO = Registry::get('PDO');
    $OSCOM_Language = Registry::get('Language');

    $name = (empty($key)) ? 'configuration_value' : 'configuration[' . $key . ']';

    $statuses_array = array(array('id' => '0',
                                  'text' => OSCOM::getDef('default_entry')));

    $Qstatuses = $OSCOM_PDO->prepare('select orders_status_id, orders_status_name from :table_orders_status where language_id = :language_id order by orders_status_name');
    $Qstatuses->bindInt(':language_id', $OSCOM_Language->getID());
    $Qstatuses->execute();

    while ( $Qstatuses->fetch() ) {
      $statuses_array[] = array('id' => $Qstatuses->valueInt('orders_status_id'),
                                'text' => $Qstatuses->value('orders_status_name'));
    }

    return HTML::selectMenu($name, $statuses_array, $default);
  }
?>
