<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Languages\Model;

  use osCommerce\OM\Core\Site\Admin\Application\Languages\Languages;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Cache;

  class deleteDefinition {
    public static function execute($id) {
      $def = Languages::getDefinition($id);

      $data = array('id' => $id);

      if ( OSCOM::callDB('Admin\Languages\DeleteDefinition', $data) ) {
        Cache::clear('languages-' . Languages::get($def['languages_id'], 'code') . '-' . $def['content_group']);

        return true;
      }

      return false;
    }
  }
?>
