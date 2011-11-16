<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Administrators\Model;

  use osCommerce\OM\Core\Site\Admin\Application\Administrators\Administrators;
  use osCommerce\OM\Core\OSCOM;

  class setAccessLevels {
    public static function execute($id, $modules, $mode = Administrators::ACCESS_MODE_ADD) {
      $data = array('id' => $id,
                    'modules' => $modules,
                    'mode' => $mode);

      if ( in_array('0', $data['modules']) ) {
        $data['modules'] = array('*');
      }

      return OSCOM::callDB('Admin\Administrators\SavePermissions', $data);
    }
  }
?>
