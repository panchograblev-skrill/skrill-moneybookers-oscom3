<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Categories\Model;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Admin\CategoryTree;

/**
 * @since v3.0.2
 */

  class get {
    public static function execute($id, $key = null, $language_id = null) {
      $OSCOM_Language = Registry::get('Language');

      if ( Registry::exists('CategoryTree') ) {
        $OSCOM_CategoryTree = Registry::get('CategoryTree');
      } else {
        $OSCOM_CategoryTree = new CategoryTree();
        Registry::set('CategoryTree', $OSCOM_CategoryTree);
      }

      if ( !isset($language_id) ) {
        $language_id = $OSCOM_Language->getID();
      }

      $data = array('id' => $id,
                    'language_id' => $language_id);

      $result = OSCOM::callDB('Admin\Categories\Get', $data);

      $result['children_count'] = count($OSCOM_CategoryTree->getChildren($id));
      $result['product_count'] = $OSCOM_CategoryTree->getNumberOfProducts($id);

      if ( isset($key) ) {
        $result = $result[$key] ?: null;
      }

      return $result;
    }
  }
?>
