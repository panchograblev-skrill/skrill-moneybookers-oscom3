<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\TaxClasses\Model;

  use osCommerce\OM\Core\OSCOM;

  class getAllEntries {
    public static function execute($tax_class_id) {
      $data = array('tax_class_id' => $tax_class_id);

      return OSCOM::callDB('Admin\TaxClasses\EntryGetAll', $data);
    }
  }
?>
