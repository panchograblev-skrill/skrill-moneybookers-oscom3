<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Categories\Model;

  use osCommerce\OM\Core\Cache;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Admin\CategoryTree;

/**
 * @since v3.0.2
 */

  class move {
    public static function execute($id, $parent_id) {
      if ( Registry::exists('CategoryTree') ) {
        $OSCOM_CategoryTree = Registry::get('CategoryTree');
      } else {
        $OSCOM_CategoryTree = new CategoryTree();
        Registry::set('CategoryTree', $OSCOM_CategoryTree);
      }

      $data = array('id' => $id,
                    'parent_id' => $parent_id);

// Prevent another big bang and check if category is not being moved to a child category
      if ( $OSCOM_CategoryTree->getParentID($data['id']) != $data['parent_id'] ) {
        if ( in_array($data['id'], explode('_', $OSCOM_CategoryTree->buildBreadcrumb($data['parent_id']))) ) {
          return false;
        }
      }

      if ( OSCOM::callDB('Admin\Categories\Move', $data) ) {
        Cache::clear('categories');
        Cache::clear('category_tree');
        Cache::clear('also_purchased');

        return true;
      }

      return false;
    }
  }
?>
