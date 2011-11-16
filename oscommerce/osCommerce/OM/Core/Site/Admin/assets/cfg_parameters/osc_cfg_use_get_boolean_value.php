<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\OSCOM;

  function osc_cfg_use_get_boolean_value($string) {
    switch ($string) {
      case -1:
      case '-1':
        return OSCOM::getDef('parameter_false');
        break;

      case 0:
      case '0':
        return OSCOM::getDef('parameter_optional');
        break;

      case 1:
      case '1':
        return OSCOM::getDef('parameter_true');
        break;

      default:
        return $string;
        break;
    }
  }
?>
