<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\TaxClasses\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class Delete {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qclass = $OSCOM_PDO->prepare('delete from :table_tax_class where tax_class_id = :tax_class_id');
      $Qclass->bindInt(':tax_class_id', $data['id']);
      $Qclass->execute();

      return ( $Qclass->rowCount() === 1 );
    }
  }
?>
