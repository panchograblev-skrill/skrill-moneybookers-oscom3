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

  class find {
    public static function execute($search, $parent_id = 0) {
      if ( Registry::exists('CategoryTree') ) {
        $OSCOM_CategoryTree = Registry::get('CategoryTree');
      } else {
        $OSCOM_CategoryTree = new CategoryTree();
        Registry::set('CategoryTree', $OSCOM_CategoryTree);
      }

      $OSCOM_CategoryTree->reset();
      $OSCOM_CategoryTree->setRootCategoryID($parent_id);
      $OSCOM_CategoryTree->setBreadcrumbUsage(false);

      $categories = array();

      foreach ( $OSCOM_CategoryTree->getArray() as $c ) {
        if ( stripos($c['title'], $search) !== false ) {
          if ( $c['id'] != $parent_id ) {
            $category_path = $OSCOM_CategoryTree->getPathArray($c['id']);
            $top_category_id = $category_path[0]['id'];

            if ( !in_array($top_category_id, $categories) ) {
              $categories[] = $top_category_id;
            }
          }
        }
      }

      $result = array('entries' => array());

      foreach ( $categories as $c ) {
        $result['entries'][] = array('id' => $OSCOM_CategoryTree->getData($c, 'id'),
                                     'title' => $OSCOM_CategoryTree->getData($c, 'name'),
                                     'products' => $OSCOM_CategoryTree->getData($c, 'count'));
      }

      $result['total'] = count($result['entries']);

      return $result;
    }
  }
?>
