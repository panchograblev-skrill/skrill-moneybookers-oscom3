<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop;

  use osCommerce\OM\Core\Registry;

  class RecentlyVisited {
    var $visits = array();

    public function __construct() {
      if ( !isset($_SESSION['Shop']['RecentlyVisited']) ) {
        $_SESSION['Shop']['RecentlyVisited'] = array();
      }

      $this->visits =& $_SESSION['Shop']['RecentlyVisited'];
    }

    function initialize() {
      $OSCOM_Category = Registry::get('Category');

      if ( SERVICE_RECENTLY_VISITED_SHOW_PRODUCTS == '1' ) {
        if ( Registry::exists('Product') && (Registry::get('Product') instanceof Product) ) {
          $this->setProduct(Registry::get('Product')->getMasterID());
        }
      }

      if ( (SERVICE_RECENTLY_VISITED_SHOW_CATEGORIES == '1') && ($OSCOM_Category->getID() > 0) ) {
        $this->setCategory($OSCOM_Category->getID());
      }

      if ( SERVICE_RECENTLY_VISITED_SHOW_SEARCHES == '1' ) {
        if ( Registry::exists('Search') && (Registry::get('Search') instanceof Search) && (Registry::get('Search')->hasKeywords()) ) {
          $this->setSearchQuery(Registry::get('Search')->getKeywords());
        }
      }
    }

    function setProduct($id) {
      if (isset($this->visits['products'])) {
        foreach ($this->visits['products'] as $key => $value) {
          if ($value['id'] == $id) {
            unset($this->visits['products'][$key]);
            break;
          }
        }

        if (sizeof($this->visits['products']) > (SERVICE_RECENTLY_VISITED_MAX_PRODUCTS * 2)) {
          array_pop($this->visits['products']);
        }
      } else {
        $this->visits['products'] = array();
      }

      array_unshift($this->visits['products'], array('id' => $id));
    }

    function setCategory($id) {
      if (isset($this->visits['categories'])) {
        foreach ($this->visits['categories'] as $key => $value) {
          if ($value['id'] == $id) {
            unset($this->visits['categories'][$key]);
            break;
          }
        }

        if (sizeof($this->visits['categories']) > (SERVICE_RECENTLY_VISITED_MAX_CATEGORIES * 2)) {
          array_pop($this->visits['categories']);
        }
      } else {
        $this->visits['categories'] = array();
      }

      array_unshift($this->visits['categories'], array('id' => $id));
    }

    function setSearchQuery($keywords) {
      $OSCOM_Search = Registry::get('Search');

      if (isset($this->visits['searches'])) {
        foreach ($this->visits['searches'] as $key => $value) {
          if ($value['keywords'] == $keywords) {
            unset($this->visits['searches'][$key]);
            break;
          }
        }

        if (sizeof($this->visits['searches']) > (SERVICE_RECENTLY_VISITED_MAX_SEARCHES * 2)) {
          array_pop($this->visits['searches']);
        }
      } else {
        $this->visits['searches'] = array();
      }

      array_unshift($this->visits['searches'], array('keywords' => $keywords,
                                                     'results' => $OSCOM_Search->getNumberOfResults()
                                                    ));
    }

    function hasHistory() {
      if ($this->hasProducts() || $this->hasCategories() || $this->hasSearches()) {
        return true;
      }

      return false;
    }

    function hasProducts() {
      if ( SERVICE_RECENTLY_VISITED_SHOW_PRODUCTS == '1' ) {
        if ( isset($this->visits['products']) && !empty($this->visits['products']) ) {
          foreach ($this->visits['products'] as $k => $v) {
            if ( !Product::checkEntry($v['id']) ) {
              unset($this->visits['products'][$k]);
            }
          }

          return (sizeof($this->visits['products']) > 0);
        }
      }

      return false;
    }

    function getProducts() {
      $history = array();

      if (isset($this->visits['products']) && (empty($this->visits['products']) === false)) {
        $counter = 0;

        foreach ($this->visits['products'] as $k => $v) {
          $counter++;

          $OSCOM_Product = new Product($v['id']);
          $OSCOM_Category = new Category($OSCOM_Product->getCategoryID());

          $history[] = array('name' => $OSCOM_Product->getTitle(),
                             'id' => $OSCOM_Product->getID(),
                             'keyword' => $OSCOM_Product->getKeyword(),
                             'price' => (SERVICE_RECENTLY_VISITED_SHOW_PRODUCT_PRICES == '1') ? $OSCOM_Product->getPriceFormated(true) : '',
                             'image' => $OSCOM_Product->getImage(),
                             'category_name' =>  $OSCOM_Category->getTitle(),
                             'category_path' => $OSCOM_Category->getPath()
                            );

          if ($counter == SERVICE_RECENTLY_VISITED_MAX_PRODUCTS) {
            break;
          }
        }
      }

      return $history;
    }

    function hasCategories() {
      return ( (SERVICE_RECENTLY_VISITED_SHOW_CATEGORIES == '1') && isset($this->visits['categories']) && !empty($this->visits['categories']) );
    }

    function getCategories() {
      $history = array();

      if (isset($this->visits['categories']) && (empty($this->visits['categories']) === false)) {
        $counter = 0;

        foreach ($this->visits['categories'] as $k => $v) {
          $counter++;

          $OSCOM_Category = new Category($v['id']);

          if ($OSCOM_Category->hasParent()) {
            $OSCOM_CategoryParent = new Category($OSCOM_Category->getParent());
          }

          $history[]  = array('id' => $OSCOM_Category->getID(),
                              'name' => $OSCOM_Category->getTitle(),
                              'path' => $OSCOM_Category->getPath(),
                              'image' => $OSCOM_Category->getImage(),
                              'parent_name' => ($OSCOM_Category->hasParent()) ? $OSCOM_CategoryParent->getTitle() : '',
                              'parent_id' => ($OSCOM_Category->hasParent()) ? $OSCOM_CategoryParent->getID() : ''
                             );

          if ($counter == SERVICE_RECENTLY_VISITED_MAX_CATEGORIES) {
            break;
          }
        }
      }

      return $history;
    }

    function hasSearches() {
      return ( (SERVICE_RECENTLY_VISITED_SHOW_SEARCHES == '1') && isset($this->visits['searches']) && !empty($this->visits['searches']) );
    }

    function getSearches() {
      $history = array();

      if (isset($this->visits['searches']) && (empty($this->visits['searches']) === false)) {
        $counter = 0;

        foreach ($this->visits['searches'] as $k => $v) {
          $counter++;

          $history[]  = array('keywords' => $this->visits['searches'][$k]['keywords'],
                              'results' => $this->visits['searches'][$k]['results']
                             );

          if ($counter == SERVICE_RECENTLY_VISITED_MAX_SEARCHES) {
            break;
          }
        }
      }

      return $history;
    }
  }
?>
