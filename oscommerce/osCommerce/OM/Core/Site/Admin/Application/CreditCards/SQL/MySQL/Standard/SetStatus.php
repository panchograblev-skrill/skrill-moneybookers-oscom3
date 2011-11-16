<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\CreditCards\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class SetStatus {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qcc = $OSCOM_PDO->prepare('update :table_credit_cards set credit_card_status = :credit_card_status where id = :id');
      $Qcc->bindInt(':credit_card_status', ($data['status'] === true) ? 1 : 0);
      $Qcc->bindInt(':id', $data['id']);
      $Qcc->execute();

      return ( ($Qcc->rowCount() === 1) || !$Qcc->isError() );
    }
  }
?>
