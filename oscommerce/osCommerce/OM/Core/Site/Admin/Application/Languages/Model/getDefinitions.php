<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Languages\Model;

  use osCommerce\OM\Core\OSCOM;

  class getDefinitions {
    public static function execute($language_id, $group) {
      $data = array('id' => $language_id,
                    'group' => $group);

      return OSCOM::callDB('Admin\Languages\GetDefinitions', $data);
    }
  }
?>
