<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\OSCOM;

  function osc_cfg_set_boolean_value($select_array, $default, $key = null) {
    $string = '';

    $select_array = explode(',', substr($select_array, 6, -1));

    $name = (!empty($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    for ( $i = 0, $n = count($select_array); $i < $n; $i++ ) {
      $value = trim($select_array[$i]);

      if ( strpos($value, '\'') !== false ) {
        $value = substr($value, 1, -1);
      } else {
        $value = (int)$value;
      }

      $select_array[$i] = $value;

      if ( $value === -1 ) {
        $value = OSCOM::getDef('parameter_false');
      } elseif ( $value === 0 ) {
        $value = OSCOM::getDef('parameter_optional');
      } elseif ( $value === 1 ) {
        $value = OSCOM::getDef('parameter_true');
      }

      $string .= '<input type="radio" name="' . $name . '" value="' . $select_array[$i] . '"';

      if ( $default == $select_array[$i] ) {
        $string .= ' checked="checked"';
      }

      $string .= '> ' . $value . '<br />';
    }

    if ( !empty($string) ) {
      $string = substr($string, 0, -6);
    }

    return $string;
  }
?>
