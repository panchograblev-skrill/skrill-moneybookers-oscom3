<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Customers\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

/**
 * @since v3.0.2
 */

  class DeleteAddress {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qdelete = $OSCOM_PDO->prepare('delete from :table_address_book where address_book_id = :address_book_id and customers_id = :customers_id');
      $Qdelete->bindInt(':address_book_id', $data['id']);
      $Qdelete->bindInt(':customers_id', $data['customer_id']);
      $Qdelete->execute();

      return ( $Qdelete->rowCount() === 1 );
    }
  }
?>
