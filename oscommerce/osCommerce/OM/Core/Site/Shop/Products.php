<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop;

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  class Products {
    var $_category,
        $_recursive = true,
        $_manufacturer,
        $_sort_by,
        $_sort_by_direction;

    public function __construct($id = null) {
      if (is_numeric($id)) {
        $this->_category = $id;
      }
    }

/* Public methods */

    function hasCategory() {
      return isset($this->_category) && !empty($this->_category);
    }

    function isRecursive() {
      return $this->_recursive;
    }

    function hasManufacturer() {
      return isset($this->_manufacturer) && !empty($this->_manufacturer);
    }

    function setCategory($id, $recursive = true) {
      $this->_category = $id;

      if ($recursive === false) {
        $this->_recursive = false;
      }
    }

    function setManufacturer($id) {
      $this->_manufacturer = $id;
    }

    function setSortBy($field, $direction = '+') {
      switch ($field) {
        case 'model':
          $this->_sort_by = 'p.products_model';
          break;
        case 'manufacturer':
          $this->_sort_by = 'm.manufacturers_name';
          break;
        case 'quantity':
          $this->_sort_by = 'p.products_quantity';
          break;
        case 'weight':
          $this->_sort_by = 'p.products_weight';
          break;
        case 'price':
          $this->_sort_by = 'p.products_price';
          break;
        case 'date_added':
          $this->_sort_by = 'p.products_date_added';
          break;
      }

      $this->_sort_by_direction = ($direction == '-') ? '-' : '+';
    }

    function setSortByDirection($direction) {
      $this->_sort_by_direction = ($direction == '-') ? '-' : '+';
    }

    function execute() {
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_Language = Registry::get('Language');
      $OSCOM_CategoryTree = Registry::get('CategoryTree');

      $result = array();

      $sql_query = 'select SQL_CALC_FOUND_ROWS distinct p.products_id from :table_products p left join :table_product_attributes pa on (p.products_id = pa.products_id) left join :table_templates_boxes tb on (pa.id = tb.id and tb.code = "Manufacturers"), :table_products_description pd, :table_categories c, :table_products_to_categories p2c where p.products_status = 1 and p.products_id = pd.products_id and pd.language_id = :language_id and p.products_id = p2c.products_id and p2c.categories_id = c.categories_id';

      if ( $this->hasCategory() ) {
        if ( $this->isRecursive() ) {
          $subcategories_array = array($this->_category);

          $sql_query .= ' and p2c.products_id = p.products_id and p2c.products_id = pd.products_id and p2c.categories_id in (' . implode(',', $OSCOM_CategoryTree->getChildren($this->_category, $subcategories_array)) . ')';
        } else {
          $sql_query .= ' and p2c.products_id = p.products_id and p2c.products_id = pd.products_id and pd.language_id = :language_id and p2c.categories_id = :categories_id';
        }
      }

      if ( $this->hasManufacturer() ) {
        $sql_query .= ' and pa.value = :manufacturers_id';
      }

      $sql_query .= ' order by';

      if ( isset($this->_sort_by) ) {
        $sql_query .= ' ' . $this->_sort_by . ' ' . (($this->_sort_by_direction == '-') ? 'desc' : '');
      } else {
        $sql_query .= ' pd.products_name ' . (($this->_sort_by_direction == '-') ? 'desc' : '');
      }

      $sql_query .= ' limit :batch_pageset, :batch_max_results; select found_rows();';

      $Qlisting = $OSCOM_PDO->prepare($sql_query);
      $Qlisting->bindInt(':language_id', $OSCOM_Language->getID());

      if ( $this->hasCategory() ) {
        if ( !$this->isRecursive() ) {
          $Qlisting->bindInt(':language_id', $OSCOM_Language->getID());
          $Qlisting->bindInt(':categories_id', $this->_category);
        }
      }

      if ( $this->hasManufacturer() ) {
        $Qlisting->bindInt(':manufacturers_id', $this->_manufacturer);
      }

      $Qlisting->bindInt(':batch_pageset', $OSCOM_PDO->getBatchFrom((isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1), MAX_DISPLAY_SEARCH_RESULTS));
      $Qlisting->bindInt(':batch_max_results', MAX_DISPLAY_SEARCH_RESULTS);
      $Qlisting->execute();

      $result['entries'] = $Qlisting->fetchAll();

      $Qlisting->nextRowset();

      $result['total'] = $Qlisting->fetchColumn();

      return $result;
    }

/**
 * Create a sort heading with appropriate sort link
 *
 * @param string $key The key used for sorting
 * @param string $heading The heading to use the link on
 * @return string
 * @since v3.0.0
 */

    public static function getListingSortLink($key, $heading) {
      $current = false;
      $direction = false;

      if ( !isset($_GET['sort']) ) {
        $current = 'name';
      } elseif ( ($_GET['sort'] == $key) || ($_GET['sort'] == $key . '|d') ) {
        $current = $key;
      }

      if ( $key == $current ) {
        if ( isset($_GET['sort']) ) {
          $direction = ($_GET['sort'] == $key) ? '+' : '-';
        } else {
          $direction = '+';
        }
      }

      return HTML::link(OSCOM::getLink(null, null, OSCOM::getAllGET(array('page', 'sort')) . '&sort=' . $key . ($direction == '+' ? '|d' : '')), $heading . (($key == $current) ? $direction : ''), 'title="' . (isset($_GET['sort']) && ($_GET['sort'] == $key) ? sprintf(OSCOM::getDef('listing_sort_ascendingly'), $heading) : sprintf(OSCOM::getDef('listing_sort_descendingly'), $heading)) . '" class="productListing-heading"');
    }

/**
 * Generate a product ID string value containing its product attributes combinations
 *
 * @param string $id The product ID
 * @param array $params An array of product attributes
 * @return string
 * @since v3.0.0
 */

    public static function getProductIDString($id, $params) {
      $string = (int)$id;

      if ( is_array($params) && !empty($params) ) {
        $attributes_check = true;
        $attributes_ids = array();

        foreach ( $params as $option => $value ) {
          if ( is_numeric($option) && is_numeric($value) ) {
            $attributes_ids[] = (int)$option . ':' . (int)$value;
          } else {
            $attributes_check = false;
            break;
          }
        }

        if ( $attributes_check === true ) {
          $string .= '#' . implode(';', $attributes_ids);
        }
      }

      return $string;
    }

/**
 * Generate a numeric product ID without product attribute combinations
 *
 * @param string $id The product ID
 * @access public
 */

    public static function getProductID($id) {
      if ( is_numeric($id) ) {
        return $id;
      }

      $product = explode('#', $id, 2);

      return (int)$product[0];
    }
  }
?>
