<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Categories\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

/**
 * @since v3.0.2
 */

  class Move {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $error = false;

      $Qupdate = $OSCOM_PDO->prepare('update :table_categories set parent_id = :parent_id, last_modified = now() where categories_id = :categories_id');

      if ( $data['parent_id'] > 0 ) {
        $Qupdate->bindInt(':parent_id', $data['parent_id']);
      } else {
        $Qupdate->bindNull(':parent_id');
      }

      $Qupdate->bindInt(':categories_id', $data['id']);
      $Qupdate->execute();

      return ( ($Qupdate->rowCount() === 1) || !$Qupdate->isError() );
    }
  }
?>
