<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop;

  use osCommerce\OM\Core\Registry;

  class Search extends Products {

/**
 * @since v3.0.0
 */

    protected $_period_min_year;

/**
 * @since v3.0.0
 */

    protected $_period_max_year;

/**
 * @since v3.0.0
 */

    protected $_date_from;

/**
 * @since v3.0.0
 */

    protected $_date_to;

/**
 * @since v3.0.0
 */

    protected $_price_from;

/**
 * @since v3.0.0
 */

    protected $_price_to;

/**
 * @since v3.0.0
 */

    protected $_keywords;

/**
 * @since v3.0.1
 */

    protected $_result;

/**
 * @since v3.0.0
 */

    public function __construct() {
      $OSCOM_PDO = Registry::get('PDO');

      $Qproducts = $OSCOM_PDO->query('select min(year(products_date_added)) as min_year, max(year(products_date_added)) as max_year from :table_products limit 1');
      $Qproducts->execute();

      $this->_period_min_year = $Qproducts->valueInt('min_year');
      $this->_period_max_year = $Qproducts->valueInt('max_year');
    }

/**
 * @since v3.0.0
 */

    public function getMinYear() {
      return $this->_period_min_year;
    }

/**
 * @since v3.0.0
 */

    public function getMaxYear() {
      return $this->_period_max_year;
    }

/**
 * @since v3.0.0
 */

    public function getDateFrom() {
      return $this->_date_from;
    }

/**
 * @since v3.0.0
 */

    public function getDateTo() {
      return $this->_date_to;
    }

/**
 * @since v3.0.0
 */

    public function getPriceFrom() {
      return $this->_price_from;
    }

/**
 * @since v3.0.0
 */

    public function getPriceTo() {
      return $this->_price_to;
    }

/**
 * @since v3.0.0
 */

    public function getKeywords() {
      return $this->_keywords;
    }

/**
 * @since v3.0.0
 */

    public function getNumberOfResults() {
      return $this->_result['total'];
    }

/**
 * @since v3.0.0
 */

    public function hasDateSet($flag = null) {
      if ($flag == 'from') {
        return isset($this->_date_from);
      } elseif ($flag == 'to') {
        return isset($this->_date_to);
      }

      return isset($this->_date_from) && isset($this->_date_to);
    }

/**
 * @since v3.0.0
 */

    public function hasPriceSet($flag = null) {
      if ($flag == 'from') {
        return isset($this->_price_from);
      } elseif ($flag == 'to') {
        return isset($this->_price_to);
      }

      return isset($this->_price_from) && isset($this->_price_to);
    }

/**
 * @since v3.0.0
 */

    public function hasKeywords() {
      return isset($this->_keywords) && !empty($this->_keywords);
    }

/**
 * @since v3.0.0
 */

    public function setDateFrom($timestamp) {
      $this->_date_from = $timestamp;
    }

/**
 * @since v3.0.0
 */

    public function setDateTo($timestamp) {
      $this->_date_to = $timestamp;
    }

/**
 * @since v3.0.0
 */

    public function setPriceFrom($price) {
      $this->_price_from = $price;
    }

/**
 * @since v3.0.0
 */

    public function setPriceTo($price) {
      $this->_price_to = $price;
    }

/**
 * @since v3.0.0
 */

    public function setKeywords($keywords) {
      $terms = explode(' ', trim($keywords));

      $terms_array = array();

      $counter = 0;

      foreach ($terms as $word) {
        $counter++;

        if ($counter > 5) {
          break;
        } elseif (!empty($word)) {
          if (!in_array($word, $terms_array)) {
            $terms_array[] = $word;
          }
        }
      }

      $this->_keywords = implode(' ', $terms_array);
    }

/**
 * @since v3.0.0
 */

    public function execute() {
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_Language = Registry::get('Language');
      $OSCOM_CategoryTree = Registry::get('CategoryTree');
      $OSCOM_Currencies = Registry::get('Currencies');

      $result = array();

      if ( $this->hasPriceSet('from') ) {
        if ( $OSCOM_Currencies->exists($_SESSION['currency']) ) {
          $this->_price_from = $this->_price_from / $OSCOM_Currencies->value($_SESSION['currency']);
        }
      }

      if ( $this->hasPriceSet('to') ) {
        if ( $OSCOM_Currencies->exists($_SESSION['currency']) ) {
          $this->_price_to = $this->_price_to / $OSCOM_Currencies->value($_SESSION['currency']);
        }
      }

      $sql_query = 'select SQL_CALC_FOUND_ROWS distinct p.*, pd.*, m.*, i.image, if(s.status, s.specials_new_products_price, null) as specials_new_products_price, if(s.status, s.specials_new_products_price, p.products_price) as final_price';

      if ( ($this->hasPriceSet('from') || $this->hasPriceSet('to')) && (DISPLAY_PRICE_WITH_TAX == '1') ) {
        $sql_query .= ', sum(tr.tax_rate) as tax_rate';
      }

      $sql_query .= ' from :table_products p left join :table_manufacturers m using(manufacturers_id) left join :table_specials s on (p.products_id = s.products_id) left join :table_products_images i on (p.products_id = i.products_id and i.default_flag = :default_flag)';

      if ( ($this->hasPriceSet('from') || $this->hasPriceSet('to')) && (DISPLAY_PRICE_WITH_TAX == '1') ) {
        $sql_query .= ' left join :table_tax_rates tr on p.products_tax_class_id = tr.tax_class_id left join :table_zones_to_geo_zones gz on tr.tax_zone_id = gz.geo_zone_id and (gz.zone_country_id is null or gz.zone_country_id = 0 or gz.zone_country_id = :zone_country_id) and (gz.zone_id is null or gz.zone_id = 0 or gz.zone_id = :zone_id)';
      }

      $sql_query .= ', :table_products_description pd, :table_categories c, :table_products_to_categories p2c where p.products_status = 1 and p.products_id = pd.products_id and pd.language_id = :language_id and p.products_id = p2c.products_id and p2c.categories_id = c.categories_id';

      if ( $this->hasCategory() ) {
        if ( $this->isRecursive() ) {
          $subcategories_array = array($this->_category);

          $sql_query .= ' and p2c.products_id = p.products_id and p2c.products_id = pd.products_id and p2c.categories_id in (' . implode(',', $OSCOM_CategoryTree->getChildren($this->_category, $subcategories_array)) . ')';
        } else {
          $sql_query .= ' and p2c.products_id = p.products_id and p2c.products_id = pd.products_id and pd.language_id = :language_id_c and p2c.categories_id = :categories_id';
        }
      }

      if ( $this->hasManufacturer() ) {
        $sql_query .= ' and m.manufacturers_id = :manufacturers_id';
      }

      if ( $this->hasKeywords() ) {
        foreach ( explode(' ', $this->_keywords) as $keyword ) {
          $sql_query .= ' and (pd.products_name like :keyword_name or pd.products_description like :keyword_description)';
        }
      }

      if ( $this->hasDateSet('from') ) {
        $sql_query .= ' and p.products_date_added >= :products_date_added_from';
      }

      if ( $this->hasDateSet('to') ) {
        $sql_query .= ' and p.products_date_added <= :products_date_added_to';
      }

      if ( DISPLAY_PRICE_WITH_TAX == '1' ) {
        if ( $this->_price_from > 0 ) {
          $sql_query .= ' and (if(s.status, s.specials_new_products_price, p.products_price) * if(gz.geo_zone_id is null, 1, 1 + (tr.tax_rate / 100) ) >= :price_from)';
        }

        if ( $this->_price_to > 0 ) {
          $sql_query .= ' and (if(s.status, s.specials_new_products_price, p.products_price) * if(gz.geo_zone_id is null, 1, 1 + (tr.tax_rate / 100) ) <= :price_to)';
        }
      } else {
        if ( $this->_price_from > 0 ) {
          $sql_query .= ' and (if(s.status, s.specials_new_products_price, p.products_price) >= :price_from)';
        }

        if ( $this->_price_to > 0 ) {
          $sql_query .= ' and (if(s.status, s.specials_new_products_price, p.products_price) <= :price_to)';
        }
      }

      if ( ($this->hasPriceSet('from') || $this->hasPriceSet('to')) && (DISPLAY_PRICE_WITH_TAX == '1') ) {
        $sql_query .= ' group by p.products_id, tr.tax_priority';
      }

      $sql_query .= ' order by';

      if ( isset($this->_sort_by) ) {
        $sql_query .= ' ' . $this->_sort_by . ' ' . (($this->_sort_by_direction == '-') ? 'desc' : '') . ', pd.products_name';
      } else {
        $sql_query .= ' pd.products_name ' . (($this->_sort_by_direction == '-') ? 'desc' : '');
      }

      $sql_query .= ' limit :batch_pageset, :batch_max_results; select found_rows();';

      $Qlisting = $OSCOM_PDO->prepare($sql_query);
      $Qlisting->bindInt(':default_flag', 1);

      if ( ($this->hasPriceSet('from') || $this->hasPriceSet('to')) && (DISPLAY_PRICE_WITH_TAX == '1') ) {
        if ( $OSCOM_Customer->isLoggedOn() ) {
          $customer_country_id = $OSCOM_Customer->getCountryID();
          $customer_zone_id = $OSCOM_Customer->getZoneID();
        } else {
          $customer_country_id = STORE_COUNTRY;
          $customer_zone_id = STORE_ZONE;
        }

        $Qlisting->bindInt(':zone_country_id', $customer_country_id);
        $Qlisting->bindInt(':zone_id', $customer_zone_id);
      }

      $Qlisting->bindInt(':language_id', $OSCOM_Language->getID());

      if ( $this->hasCategory() ) {
        if ( !$this->isRecursive() ) {
          $Qlisting->bindInt(':language_id_c', $OSCOM_Language->getID());
          $Qlisting->bindInt(':categories_id', $this->_category);
        }
      }

      if ( $this->hasManufacturer() ) {
        $Qlisting->bindInt(':manufacturers_id', $this->_manufacturer);
      }

      if ( $this->hasKeywords() ) {
        foreach ( explode(' ', $this->_keywords) as $keyword ) {
          $Qlisting->bindValue(':keyword_name', '%' . $keyword . '%');
          $Qlisting->bindValue(':keyword_description', '%' . $keyword . '%');
        }
      }

      if ( $this->hasDateSet('from') ) {
        $Qlisting->bindValue(':products_date_added_from', date('Y-m-d H:i:s', $this->_date_from));
      }

      if ( $this->hasDateSet('to') ) {
        $Qlisting->bindValue(':products_date_added_to', date('Y-m-d H:i:s', $this->_date_to));
      }


      if ( DISPLAY_PRICE_WITH_TAX == '1' ) {
        if ( $this->_price_from > 0 ) {
          $Qlisting->bindValue(':price_from', $this->_price_from);
        }

        if ( $this->_price_to > 0 ) {
          $Qlisting->bindValue(':price_to', $this->_price_to);
        }
      } else {
        if ( $this->_price_from > 0 ) {
          $Qlisting->bindValue(':price_from', $this->_price_from);
        }

        if ( $this->_price_to > 0 ) {
          $Qlisting->bindValue(':price_to', $this->_price_to);
        }
      }

      $Qlisting->bindInt(':batch_pageset', $OSCOM_PDO->getBatchFrom((isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1), MAX_DISPLAY_SEARCH_RESULTS));
      $Qlisting->bindInt(':batch_max_results', MAX_DISPLAY_SEARCH_RESULTS);
      $Qlisting->execute();

      $result['entries'] = $Qlisting->fetchAll();

      $Qlisting->nextRowset();

      $result['total'] = $Qlisting->fetchColumn();

      $this->_result = $result;
    }

/**
 * @since v3.0.1
 */

    public function getResult() {
      if ( !isset($this->_result) ) {
        $this->execute();
      }

      return $this->_result;
    }
  }
?>
