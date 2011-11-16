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

  class SaveSortOrder {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $error = false;

      $OSCOM_PDO->beginTransaction();

      foreach ( $data as $c ) {
        $Qcategory = $OSCOM_PDO->prepare('update :table_categories set sort_order = :sort_order, last_modified = now() where categories_id = :categories_id');
        $Qcategory->bindInt(':sort_order', $c['sort_order']);
        $Qcategory->bindInt(':categories_id', $c['id']);
        $Qcategory->execute();

        if ( $Qcategory->isError() ) {
          $error = true;
          break;
        }
      }

      if ( $error === false ) {
        $OSCOM_PDO->commit();

        return true;
      }

      $OSCOM_PDO->rollBack();

      return false;
    }
  }
?>
