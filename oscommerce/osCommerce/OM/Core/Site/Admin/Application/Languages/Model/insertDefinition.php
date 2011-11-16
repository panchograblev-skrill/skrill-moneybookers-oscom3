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

  class insertDefinition {
    public static function execute($data) {
      $languages = Languages::getAll(-1);
      $languages = $languages['entries'];

      $values = $data['values'];

      unset($data['values']);

      foreach ( $languages as $l ) {
        $data['language_id'] = $l['languages_id'];
        $data['value'] = $values[$l['languages_id']];

        OSCOM::callDB('Admin\Languages\InsertDefinition', $data);

        Cache::clear('languages-' . $l['code'] . '-' . $data['group']);
      }

      return true;
    }
  }
?>
