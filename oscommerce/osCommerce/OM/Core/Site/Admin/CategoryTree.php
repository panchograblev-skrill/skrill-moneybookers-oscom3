<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

/**
 * @since v3.0.2
 */

  class CategoryTree extends \osCommerce\OM\Core\Site\Shop\CategoryTree {
    protected $_show_total_products = true;

    public function __construct() {
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_Language = Registry::get('Language');

      $Qcategories = $OSCOM_PDO->prepare('select c.categories_id, c.parent_id, c.categories_image, cd.categories_name from :table_categories c, :table_categories_description cd where c.categories_id = cd.categories_id and cd.language_id = :language_id order by c.parent_id, c.sort_order, cd.categories_name');
      $Qcategories->bindInt(':language_id', $OSCOM_Language->getID());
      $Qcategories->execute();

      $this->_data = array();

      while ( $Qcategories->fetch() ) {
        $this->_data[$Qcategories->valueInt('parent_id')][$Qcategories->valueInt('categories_id')] = array('name' => $Qcategories->value('categories_name'),
                                                                                                           'image' => $Qcategories->value('categories_image'),
                                                                                                           'count' => 0);
      }

      $this->_calculateProductTotals(false);
    }

    function getPath($category_id, $level = 0, $separator = ' ') {
      $path = '';

      foreach ( $this->_data as $parent => $categories ) {
        foreach ( $categories as $id => $info ) {
          if ( $id == $category_id ) {
            if ( $level < 1 ) {
              $path = $info['name'];
            } else {
              $path = $info['name'] . $separator . $path;
            }

            if ( $parent != $this->root_category_id ) {
              $path = $this->getPath($parent, $level+1, $separator) . $path;
            }
          }
        }
      }

      return $path;
    }

    function getPathArray($category_id) {
      static $path = array();

      foreach ( $this->_data as $parent => $categories ) {
        foreach ( $categories as $id => $info ) {
          if ( $id == $category_id ) {
            $path[] = array('id' => $id,
                            'name' => $info['name']);

            if ( $parent != $this->root_category_id ) {
              $this->getPathArray($parent);
            }
          }
        }
      }

      return array_reverse($path);
    }
  }
?>
