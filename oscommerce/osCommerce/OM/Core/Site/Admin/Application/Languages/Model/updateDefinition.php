<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Languages\Model;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Cache;
  use osCommerce\OM\Core\Site\Admin\Application\Languages\Languages;

  class updateDefinition {
    public static function execute($data) {
      $definitions = $data['definitions'];

      unset($data['definitions']);

      foreach ( $definitions as $key => $value ) {
        $data['key'] = $key;
        $data['value'] = $value;

        OSCOM::callDB('Admin\Languages\UpdateDefinition', $data);

        Cache::clear('languages-' . Languages::get($data['language_id'], 'code') . '-' . $data['group']);
      }

      return true;
    }
  }
?>
