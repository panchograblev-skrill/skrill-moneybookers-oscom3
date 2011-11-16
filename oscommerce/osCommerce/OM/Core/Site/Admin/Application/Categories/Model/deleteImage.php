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

  class deleteImage {
    public static function execute($id) {
      if ( Registry::exists('CategoryTree') ) {
        $OSCOM_CategoryTree = Registry::get('CategoryTree');
      } else {
        $OSCOM_CategoryTree = new CategoryTree();
        Registry::set('CategoryTree', $OSCOM_CategoryTree);
      }

      $data = $OSCOM_CategoryTree->getData($id);

      if ( !empty($data['image']) && file_exists(OSCOM::getConfig('dir_fs_public', 'OSCOM') . 'categories/' . $data['image']) ) {
        unlink(OSCOM::getConfig('dir_fs_public', 'OSCOM') . 'categories/' . $data['image']);
      }
    }
  }
?>
