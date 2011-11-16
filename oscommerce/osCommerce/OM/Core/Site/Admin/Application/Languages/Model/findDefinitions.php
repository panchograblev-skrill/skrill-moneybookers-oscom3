<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Languages\Model;

  use osCommerce\OM\Core\OSCOM;

  class findDefinitions {
    public static function execute($language_id, $group, $search) {
      $data = array('id' => $language_id,
                    'group' => $group,
                    'keywords' => $search);

      return OSCOM::callDB('Admin\Languages\FindDefinitions', $data);
    }
  }
?>
