<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\CreditCards\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class Get {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qcc = $OSCOM_PDO->prepare('select * from :table_credit_cards where id = :id');
      $Qcc->bindInt(':id', $data['id']);
      $Qcc->execute();

      return $Qcc->fetch();
    }
  }
?>
