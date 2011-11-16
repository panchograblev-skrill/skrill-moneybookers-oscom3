<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\Site\Shop\Address;

  function osc_cfg_set_countries_pulldown_menu($default, $key = null) {
    $name = (!empty($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    $countries_array = array();

    foreach ( Address::getCountries() as $country ) {
      $countries_array[] = array('id' => $country['id'],
                                 'text' => $country['name']);
    }

    return HTML::selectMenu($name, $countries_array, $default);
  }
?>
