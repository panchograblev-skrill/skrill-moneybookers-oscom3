<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\TaxClasses\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class GetTotalProducts {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qtotal = $OSCOM_PDO->prepare('select count(*) as total from :table_products where products_tax_class_id = :products_tax_class_id');
      $Qtotal->bindInt(':products_tax_class_id', $data['id']);
      $Qtotal->execute();

      $result = $Qtotal->fetch();

      return $result['total'];
    }
  }
?>
