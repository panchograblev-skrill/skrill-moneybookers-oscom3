<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Categories\Model;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Admin\CategoryTree;

/**
 * @since v3.0.2
 */

  class getAll {
    public static function execute($parent_id = 0) {
      if ( Registry::exists('CategoryTree') ) {
        $OSCOM_CategoryTree = Registry::get('CategoryTree');
      } else {
        $OSCOM_CategoryTree = new CategoryTree();
        Registry::set('CategoryTree', $OSCOM_CategoryTree);
      }

      $OSCOM_CategoryTree->reset();
      $OSCOM_CategoryTree->setMaximumLevel(1);
      $OSCOM_CategoryTree->setBreadcrumbUsage(false);

      $result = $OSCOM_CategoryTree->getArray($parent_id);

      foreach ( $result as &$c ) {
        $c['products'] = $OSCOM_CategoryTree->getData($c['id'], 'count');
      }

      return array('entries' => $result,
                   'total' => count($result));
    }
  }
?>
