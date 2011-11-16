<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop;

  use osCommerce\OM\Core\Registry;

/**
 * The Category class manages category information
 */

  class Category {
    protected $_id;
    protected $_title;
    protected $_image;
    protected $_parent_id;

/**
 * An array containing the category information
 *
 * @var array
 * @access protected
 */

    protected $_data = array();

/**
 * Constructor
 *
 * @param int $id The ID of the category to retrieve information from
 * @access public
 */

    public function __construct($id = null) {
      $OSCOM_CategoryTree = Registry::get('CategoryTree');

      if ( !isset($id) && isset($_GET['cPath']) ) {
        $cPath_array = array_unique(array_filter(explode('_', $_GET['cPath']), 'is_numeric'));

        if ( !empty($cPath_array) ) {
          $id = end($cPath_array);
        }
      }

      if ( isset($id) && $OSCOM_CategoryTree->exists($id) ) {
        $this->_data = $OSCOM_CategoryTree->getData($id);

        $this->_id = $this->_data['id'];
        $this->_title = $this->_data['name'];
        $this->_image = $this->_data['image'];
        $this->_parent_id = $this->_data['parent_id'];
      }
    }

/**
 * Return the ID of the assigned category
 *
 * @access public
 * @return integer
 */

    public function getID() {
      return $this->_id;
    }

/**
 * Return the title of the assigned category
 *
 * @access public
 * @return string
 */

    public function getTitle() {
      return $this->_title;
    }

/**
 * Check if the category has an image
 *
 * @access public
 * @return string
 */

    public function hasImage() {
      return ( !empty($this->_image) );
    }

/**
 * Return the image of the assigned category
 *
 * @access public
 * @return string
 */

    public function getImage() {
      return $this->_image;
    }

/**
 * Check if the assigned category has a parent category
 *
 * @access public
 * @return boolean
 */

    public function hasParent() {
      return ( $this->_parent_id > 0 );
    }

/**
 * Return the parent ID of the assigned category
 *
 * @access public
 * @return integer
 */

    public function getParent() {
      return $this->_parent_id;
    }

/**
 * Return the breadcrumb path of the assigned category
 *
 * @access public
 * @return string
 */

    public function getPath() {
      $OSCOM_CategoryTree = Registry::get('CategoryTree');

      return $OSCOM_CategoryTree->buildBreadcrumb($this->_id);
    }

    public function getPathArray($id = null) {
      $cPath_array = explode('_', $this->getPath());

      if ( isset($id) ) {
        return $cPath_array[$id];
      }

      return $cPath_array;
    }

/**
 * Return specific information from the assigned category
 *
 * @access public
 * @return mixed
 */

    public function getData($keyword) {
      return $this->_data[$keyword];
    }
  }
?>
