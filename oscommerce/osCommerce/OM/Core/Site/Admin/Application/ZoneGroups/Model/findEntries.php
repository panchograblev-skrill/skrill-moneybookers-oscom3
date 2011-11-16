<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\ZoneGroups\Model;

  use osCommerce\OM\Core\OSCOM;

  class findEntries {
    public static function execute($search, $group_id) {
      $data = array('keywords' => $search,
                    'group_id' => $group_id);

      return OSCOM::callDB('Admin\ZoneGroups\EntryFind', $data);
    }
  }
?>
