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

  class Save {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_Language = Registry::get('Language');

      if ( !isset($data['id']) ) {
        $data['id'] = null;
      }

      $error = false;

      $OSCOM_PDO->beginTransaction();

      if ( is_numeric($data['id']) ) {
        $Qcat = $OSCOM_PDO->prepare('update :table_categories set parent_id = :parent_id, last_modified = now() where categories_id = :categories_id');
        $Qcat->bindInt(':categories_id', $data['id']);
      } else {
        $Qcat = $OSCOM_PDO->prepare('insert into :table_categories (parent_id, date_added) values (:parent_id, now())');
      }

      if ( $data['parent_id'] > 0 ) {
        $Qcat->bindInt(':parent_id', $data['parent_id']);
      } else {
        $Qcat->bindNull(':parent_id');
      }

      $Qcat->execute();

      if ( !$Qcat->isError() ) {
        $category_id = ( is_numeric($data['id']) ) ? $data['id'] : $OSCOM_PDO->lastInsertId();

        foreach ( $OSCOM_Language->getAll() as $l ) {
          if ( is_numeric($data['id']) ) {
            $Qcd = $OSCOM_PDO->prepare('update :table_categories_description set categories_name = :categories_name where categories_id = :categories_id and language_id = :language_id');
          } else {
            $Qcd = $OSCOM_PDO->prepare('insert into :table_categories_description (categories_id, language_id, categories_name) values (:categories_id, :language_id, :categories_name)');
          }

          $Qcd->bindInt(':categories_id', $category_id);
          $Qcd->bindInt(':language_id', $l['id']);
          $Qcd->bindValue(':categories_name', $data['name'][$l['id']]);
          $Qcd->execute();

          if ( $Qcd->isError() ) {
            $error = true;
            break;
          }
        }

        if ( $error === false ) {
          if ( isset($data['image']) ) {
            $Qci = $OSCOM_PDO->prepare('update :table_categories set categories_image = :categories_image where categories_id = :categories_id');
            $Qci->bindValue(':categories_image', $data['image']);
            $Qci->bindInt(':categories_id', $category_id);
            $Qci->execute();

            if ( $Qci->isError() ) {
              $error = true;
            }
          }
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
