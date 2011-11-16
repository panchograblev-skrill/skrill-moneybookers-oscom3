<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Configuration\Model;

  use osCommerce\OM\Core\OSCOM;

  class findEntries {
    public static function execute($search, $group_id) {
      $data = array('group_id' => $group_id,
                    'search' => $search);

      return OSCOM::callDB('Admin\Configuration\EntryFind', $data);
    }
  }
?>
