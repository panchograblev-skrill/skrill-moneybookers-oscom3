<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop;

  use osCommerce\OM\Core\DateTime;
  use osCommerce\OM\Core\Hash;
  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Shop\ProductVariants;

  class ShoppingCart {
    protected $_contents = array();
    protected $_sub_total = 0;
    protected $_total = 0;
    protected $_weight = 0;
    protected $_tax = 0;
    protected $_tax_groups = array();
    protected $_content_type;
    protected $_products_in_stock = true;

    public function __construct() {
      if ( !isset($_SESSION['osC_ShoppingCart_data']) ) {
        $_SESSION['osC_ShoppingCart_data'] = array('contents' => array(),
                                                   'sub_total_cost' => 0,
                                                   'total_cost' => 0,
                                                   'total_weight' => 0,
                                                   'tax' => 0,
                                                   'tax_groups' => array(),
                                                   'shipping_boxes_weight' => 0,
                                                   'shipping_boxes' => 1,
                                                   'shipping_address' => array('zone_id' => STORE_ZONE,
                                                                               'country_id' => STORE_COUNTRY),
                                                   'shipping_method' => array(),
                                                   'billing_address' => array('zone_id' => STORE_ZONE,
                                                                              'country_id' => STORE_COUNTRY),
                                                   'billing_method' => array(),
                                                   'shipping_quotes' => array(),
                                                   'order_totals' => array());

        $this->resetShippingAddress();
        $this->resetBillingAddress();
      }

      $this->_contents =& $_SESSION['osC_ShoppingCart_data']['contents'];
      $this->_sub_total =& $_SESSION['osC_ShoppingCart_data']['sub_total_cost'];
      $this->_total =& $_SESSION['osC_ShoppingCart_data']['total_cost'];
      $this->_weight =& $_SESSION['osC_ShoppingCart_data']['total_weight'];
      $this->_tax =& $_SESSION['osC_ShoppingCart_data']['tax'];
      $this->_tax_groups =& $_SESSION['osC_ShoppingCart_data']['tax_groups'];
      $this->_shipping_boxes_weight =& $_SESSION['osC_ShoppingCart_data']['shipping_boxes_weight'];
      $this->_shipping_boxes =& $_SESSION['osC_ShoppingCart_data']['shipping_boxes'];
      $this->_shipping_address =& $_SESSION['osC_ShoppingCart_data']['shipping_address'];
      $this->_shipping_method =& $_SESSION['osC_ShoppingCart_data']['shipping_method'];
      $this->_billing_address =& $_SESSION['osC_ShoppingCart_data']['billing_address'];
      $this->_billing_method =& $_SESSION['osC_ShoppingCart_data']['billing_method'];
      $this->_shipping_quotes =& $_SESSION['osC_ShoppingCart_data']['shipping_quotes'];
      $this->_order_totals =& $_SESSION['osC_ShoppingCart_data']['order_totals'];
    }

    public function refresh() {
      if ( !isset($_SESSION['cartID']) ) {
        $this->_calculate();
      }
    }

    public function hasContents() {
      return !empty($this->_contents);
    }

    public function synchronizeWithDatabase() {
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_Language = Registry::get('Language');
      $OSCOM_Service = Registry::get('Service');

      if ( !$OSCOM_Customer->isLoggedOn() ) {
        return false;
      }

      foreach ( $this->_contents as $item_id => $data ) {
        $db_action = 'check';

        if ( isset($data['variants']) ) {
          foreach ( $data['variants'] as $variant ) {
            if ( $variant['has_custom_value'] === true ) {
              $db_action = 'insert';

              break;
            }
          }
        }

        if ( $db_action == 'check' ) {
          $Qproduct = $OSCOM_PDO->prepare('select item_id, quantity from :table_shopping_carts where customers_id = :customers_id and products_id = :products_id');
          $Qproduct->bindInt(':customers_id', $OSCOM_Customer->getID());
          $Qproduct->bindInt(':products_id', $data['id']);
          $Qproduct->execute();

          if ( $Qproduct->fetch() !== false ) {
            $Qupdate = $OSCOM_PDO->prepare('update :table_shopping_carts set quantity = :quantity where customers_id = :customers_id and item_id = :item_id');
            $Qupdate->bindInt(':quantity', $data['quantity'] + $Qproduct->valueInt('quantity'));
            $Qupdate->bindInt(':customers_id', $OSCOM_Customer->getID());
            $Qupdate->bindInt(':item_id', $Qproduct->valueInt('item_id'));
            $Qupdate->execute();
          } else {
            $db_action = 'insert';
          }
        }

        if ( $db_action == 'insert') {
          $Qid = $OSCOM_PDO->prepare('select max(item_id) as item_id from :table_shopping_carts where customers_id = :customers_id');
          $Qid->bindInt(':customers_id', $OSCOM_Customer->getID());
          $Qid->execute();

          $db_item_id = $Qid->valueInt('item_id') + 1;

          $Qnew = $OSCOM_PDO->prepare('insert into :table_shopping_carts (customers_id, item_id, products_id, quantity, date_added) values (:customers_id, :item_id, :products_id, :quantity, now())');
          $Qnew->bindInt(':customers_id', $OSCOM_Customer->getID());
          $Qnew->bindInt(':item_id', $db_item_id);
          $Qnew->bindInt(':products_id', $data['id']);
          $Qnew->bindInt(':quantity', $data['quantity']);
          $Qnew->execute();

          if ( isset($data['variants']) ) {
            foreach ( $data['variants'] as $variant ) {
              if ( $variant['has_custom_value'] === true ) {
                $Qnew = $OSCOM_PDO->prepare('insert into :table_shopping_carts_custom_variants_values (shopping_carts_item_id, customers_id, products_id, products_variants_values_id, products_variants_values_text) values (:shopping_carts_item_id, :customers_id, :products_id, :products_variants_values_id, :products_variants_values_text)');
                $Qnew->bindInt(':shopping_carts_item_id', $db_item_id);
                $Qnew->bindInt(':customers_id', $OSCOM_Customer->getID());
                $Qnew->bindInt(':products_id', $data['id']);
                $Qnew->bindInt(':products_variants_values_id', $variant['value_id']);
                $Qnew->bindValue(':products_variants_values_text', $variant['value_title']);
                $Qnew->execute();
              }
            }
          }
        }
      }

// reset per-session cart contents, but not the database contents
      $this->reset();

      $_delete_array = array();

      $Qproducts = $OSCOM_PDO->prepare('select sc.item_id, sc.products_id, sc.quantity, sc.date_added, p.parent_id, p.products_price, p.products_model, p.products_tax_class_id, p.products_weight, p.products_weight_class, p.products_status from :table_shopping_carts sc, :table_products p where sc.customers_id = :customers_id and sc.products_id = p.products_id order by sc.date_added desc');
      $Qproducts->bindInt(':customers_id', $OSCOM_Customer->getID());
      $Qproducts->execute();

      while ( $Qproducts->fetch() ) {
        if ( $Qproducts->valueInt('products_status') === 1 ) {
          $Qdesc = $OSCOM_PDO->prepare('select products_name, products_keyword from :table_products_description where products_id = :products_id and language_id = :language_id');
          $Qdesc->bindInt(':products_id', ($Qproducts->valueInt('parent_id') > 0) ? $Qproducts->valueInt('parent_id') : $Qproducts->valueInt('products_id'));
          $Qdesc->bindInt(':language_id', $OSCOM_Language->getID());
          $Qdesc->execute();

          $Qimage = $OSCOM_PDO->prepare('select image from :table_products_images where products_id = :products_id and default_flag = :default_flag');
          $Qimage->bindInt(':products_id', ($Qproducts->valueInt('parent_id') > 0) ? $Qproducts->valueInt('parent_id') : $Qproducts->valueInt('products_id'));
          $Qimage->bindInt(':default_flag', 1);
          $Qimage->execute();

          $price = $Qproducts->value('products_price');

          if ( $OSCOM_Service->isStarted('Specials') ) {
            if ( $new_price = Registry::get('Specials')->getPrice($Qproducts->valueInt('products_id')) ) {
              $price = $new_price;
            }
          }

          $this->_contents[$Qproducts->valueInt('item_id')] = array('item_id' => $Qproducts->valueInt('item_id'),
                                                                    'id' => $Qproducts->valueInt('products_id'),
                                                                    'parent_id' => $Qproducts->valueInt('parent_id'),
                                                                    'model' => $Qproducts->value('products_model'),
                                                                    'name' => $Qdesc->value('products_name'),
                                                                    'keyword' => $Qdesc->value('products_keyword'),
                                                                    'image' => ($Qimage->fetch() !== false) ? $Qimage->value('image') : '',
                                                                    'price' => $price,
                                                                    'quantity' => $Qproducts->valueInt('quantity'),
                                                                    'weight' => $Qproducts->value('products_weight'),
                                                                    'tax_class_id' => $Qproducts->valueInt('products_tax_class_id'),
                                                                    'date_added' => DateTime::getShort($Qproducts->value('date_added')),
                                                                    'weight_class_id' => $Qproducts->valueInt('products_weight_class'));

          if ( $Qproducts->valueInt('parent_id') > 0 ) {
            $Qcheck = $OSCOM_PDO->prepare('select products_status from :table_products where products_id = :products_id');
            $Qcheck->bindInt(':products_id', $Qproducts->valueInt('parent_id'));
            $Qcheck->execute();

            if ( $Qcheck->valueInt('products_status') === 1 ) {
              $Qvariant = $OSCOM_PDO->prepare('select pvg.id as group_id, pvg.title as group_title, pvg.module, pvv.id as value_id, pvv.title as value_title from :table_products_variants pv, :table_products_variants_values pvv, :table_products_variants_groups pvg where pv.products_id = :products_id and pv.products_variants_values_id = pvv.id and pvv.languages_id = :languages_id_pvv and pvv.products_variants_groups_id = pvg.id and pvg.languages_id = :languages_id_pvg');
              $Qvariant->bindInt(':products_id', $Qproducts->valueInt('products_id'));
              $Qvariant->bindInt(':languages_id_pvv', $OSCOM_Language->getID());
              $Qvariant->bindInt(':languages_id_pvg', $OSCOM_Language->getID());
              $Qvariant->execute();

              $variants = $Qvariant->fetchAll();

              if ( count($variants) > 0 ) {
                foreach ( $variants as $v ) {
                  $group_title = ProductVariants::getGroupTitle($v['module'], $v);
                  $value_title = $v['value_title'];
                  $has_custom_value = false;

                  $Qcvv = $OSCOM_PDO->prepare('select products_variants_values_text from :table_shopping_carts_custom_variants_values where customers_id = :customers_id and shopping_carts_item_id = :shopping_carts_item_id and products_id = :products_id and products_variants_values_id = :products_variants_values_id');
                  $Qcvv->bindInt(':customers_id', $OSCOM_Customer->getID());
                  $Qcvv->bindInt(':shopping_carts_item_id', $Qproducts->valueInt('item_id'));
                  $Qcvv->bindInt(':products_id', $Qproducts->valueInt('products_id'));
                  $Qcvv->bindInt(':products_variants_values_id', $v['value_id']);
                  $Qcvv->execute();

                  if ( $Qcvv->fetch() !== false ) {
                    $value_title = $Qcvv->value('products_variants_values_text');
                    $has_custom_value = true;
                  }

                  $this->_contents[$Qproducts->valueInt('item_id')]['variants'][] = array('group_id' => $v['group_id'],
                                                                                          'value_id' => $v['value_id'],
                                                                                          'group_title' => $group_title,
                                                                                          'value_title' => $value_title,
                                                                                          'has_custom_value' => $has_custom_value);
                }
              } else {
                $_delete_array[] = $Qproducts->valueInt('item_id');
              }
            } else {
              $_delete_array[] = $Qproducts->valueInt('item_id');
            }
          }
        } else {
          $_delete_array[] = $Qproducts->valueInt('item_id');
        }
      }

      if ( !empty($_delete_array) ) {
        foreach ( $_delete_array as $id ) {
          unset($this->_contents[$id]);
        }

        $Qdelete = $OSCOM_PDO->prepare('delete from :table_shopping_carts where customers_id = :customers_id and item_id in ("' . implode('", "', $_delete_array) . '")');
        $Qdelete->bindInt(':customers_id', $OSCOM_Customer->getID());
        $Qdelete->execute();

        $Qdelete = $OSCOM_PDO->prepare('delete from :table_shopping_carts_custom_variants_values where customers_id = :customers_id and shopping_carts_item_id in ("' . implode('", "', $_delete_array) . '")');
        $Qdelete->bindInt(':customers_id', $OSCOM_Customer->getID());
        $Qdelete->execute();
      }

      $this->_cleanUp();
      $this->_calculate();
    }

    public function reset($reset_database = false) {
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_PDO = Registry::get('PDO');

      if ( ($reset_database === true) && $OSCOM_Customer->isLoggedOn() ) {
        $Qdelete = $OSCOM_PDO->prepare('delete from :table_shopping_carts where customers_id = :customers_id');
        $Qdelete->bindInt(':customers_id', $OSCOM_Customer->getID());
        $Qdelete->execute();

        $Qdelete = $OSCOM_PDO->prepare('delete from :table_shopping_carts_custom_variants_values where customers_id = :customers_id');
        $Qdelete->bindInt(':customers_id', $OSCOM_Customer->getID());
        $Qdelete->execute();
      }

      $this->_contents = array();
      $this->_sub_total = 0;
      $this->_total = 0;
      $this->_weight = 0;
      $this->_tax = 0;
      $this->_tax_groups = array();
      $this->_content_type = null;

      $this->resetShippingAddress();
      $this->resetShippingMethod();
      $this->resetBillingAddress();
      $this->resetBillingMethod();

      if ( isset($_SESSION['cartID']) ) {
        unset($_SESSION['cartID']);
      }
    }

    public function add($product_id, $quantity = null) {
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_Language = Registry::get('Language');

      if ( !is_numeric($product_id) ) {
        return false;
      }

      $OSCOM_Product = new Product($product_id);

      if ( $OSCOM_Product->isValid() ) {
        if ( $this->exists($product_id) ) {
          $item_id = $this->getBasketID($product_id);

          if ( !is_numeric($quantity) ) {
            $quantity = $this->getQuantity($item_id) + 1;
          }

          $this->_contents[$item_id]['quantity'] = $quantity;

          if ( $OSCOM_Customer->isLoggedOn() ) {
            $Qupdate = $OSCOM_PDO->prepare('update :table_shopping_carts set quantity = :quantity where customers_id = :customers_id and item_id = :item_id');
            $Qupdate->bindInt(':quantity', $quantity);
            $Qupdate->bindInt(':customers_id', $OSCOM_Customer->getID());
            $Qupdate->bindInt(':item_id', $item_id);
            $Qupdate->execute();
          }
        } else {
          if ( !is_numeric($quantity) ) {
            $quantity = 1;
          }

          if ( $OSCOM_Customer->isLoggedOn() ) {
            $Qid = $OSCOM_PDO->prepare('select max(item_id) as item_id from :table_shopping_carts where customers_id = :customers_id');
            $Qid->bindInt(':customers_id', $OSCOM_Customer->getID());
            $Qid->execute();

            $item_id = $Qid->valueInt('item_id') + 1;
          } else {
            if ( empty($this->_contents) ) {
              $item_id = 1;
            } else {
              $item_id = max(array_keys($this->_contents)) + 1;
            }
          }

          $this->_contents[$item_id] = array('item_id' => $item_id,
                                             'id' => $product_id,
                                             'parent_id' => (int)$OSCOM_Product->getData('parent_id'),
                                             'name' => $OSCOM_Product->getTitle(),
                                             'model' => $OSCOM_Product->getModel(),
                                             'keyword' => $OSCOM_Product->getKeyword(),
                                             'image' => $OSCOM_Product->getImage(),
                                             'price' => $OSCOM_Product->getPrice(true),
                                             'quantity' => $quantity,
                                             'weight' => $OSCOM_Product->getData('weight'),
                                             'tax_class_id' => $OSCOM_Product->getData('tax_class_id'),
                                             'date_added' => DateTime::getShort(DateTime::getNow()),
                                             'weight_class_id' => $OSCOM_Product->getData('weight_class_id'));

          if ( $OSCOM_Customer->isLoggedOn() ) {
            $Qnew = $OSCOM_PDO->prepare('insert into :table_shopping_carts (customers_id, item_id, products_id, quantity, date_added) values (:customers_id, :item_id, :products_id, :quantity, now())');
            $Qnew->bindInt(':customers_id', $OSCOM_Customer->getID());
            $Qnew->bindInt(':item_id', $item_id);
            $Qnew->bindInt(':products_id', $product_id);
            $Qnew->bindInt(':quantity', $quantity);
            $Qnew->execute();
          }

          if ( $OSCOM_Product->getData('parent_id') > 0 ) {
            $Qvariant = $OSCOM_PDO->prepare('select pvg.id as group_id, pvg.title as group_title, pvg.module, pvv.id as value_id, pvv.title as value_title from :table_products_variants pv, :table_products_variants_values pvv, :table_products_variants_groups pvg where pv.products_id = :products_id and pv.products_variants_values_id = pvv.id and pvv.languages_id = :languages_id_pvv and pvv.products_variants_groups_id = pvg.id and pvg.languages_id = :languages_id_pvg');
            $Qvariant->bindInt(':products_id', $product_id);
            $Qvariant->bindInt(':languages_id_pvv', $OSCOM_Language->getID());
            $Qvariant->bindInt(':languages_id_pvg', $OSCOM_Language->getID());
            $Qvariant->execute();

            while ( $Qvariant->fetch() ) {
              $group_title = ProductVariants::getGroupTitle($Qvariant->value('module'), $Qvariant->toArray());
              $value_title = ProductVariants::getValueTitle($Qvariant->value('module'), $Qvariant->toArray());
              $has_custom_value = ProductVariants::hasCustomValue($Qvariant->value('module'));

              $this->_contents[$item_id]['variants'][] = array('group_id' => $Qvariant->valueInt('group_id'),
                                                               'value_id' => $Qvariant->valueInt('value_id'),
                                                               'group_title' => $group_title,
                                                               'value_title' => $value_title,
                                                               'has_custom_value' => $has_custom_value);

              if ( $OSCOM_Customer->isLoggedOn() && ($has_custom_value === true) ) {
                $Qnew = $OSCOM_PDO->prepare('insert into :table_shopping_carts_custom_variants_values (shopping_carts_item_id, customers_id, products_id, products_variants_values_id, products_variants_values_text) values (:shopping_carts_item_id, :customers_id, :products_id, :products_variants_values_id, :products_variants_values_text)');
                $Qnew->bindInt(':shopping_carts_item_id', $item_id);
                $Qnew->bindInt(':customers_id', $OSCOM_Customer->getID());
                $Qnew->bindInt(':products_id', $product_id);
                $Qnew->bindInt(':products_variants_values_id', $Qvariant->valueInt('value_id'));
                $Qnew->bindValue(':products_variants_values_text', $value_title);
                $Qnew->execute();
              }
            }
          }
        }

        $this->_cleanUp();
        $this->_calculate();
      }
    }

    public function numberOfItems() {
      $total = 0;

      foreach ( $this->_contents as $product ) {
        $total += $product['quantity'];
      }

      return $total;
    }

    public function getBasketID($product_id) {
      foreach ( $this->_contents as $item_id => $product ) {
        if ( $product['id'] == $product_id ) {
          return $item_id;
        }
      }
    }

    public function getQuantity($item_id) {
      return ( isset($this->_contents[$item_id]) ) ? $this->_contents[$item_id]['quantity'] : 0;
    }

    public function exists($product_id) {
      foreach ( $this->_contents as $product ) {
        if ( $product['id'] == $product_id ) {
          if ( isset($product['variants']) ) {
            foreach ( $product['variants'] as $variant ) {
              if ( $variant['has_custom_value'] === true ) {
                return false;
              }
            }
          }

          return true;
        }
      }

      return false;
    }

    public function update($item_id, $quantity) {
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_PDO = Registry::get('PDO');

      if ( !is_numeric($quantity) ) {
        $quantity = $this->getQuantity($item_id) + 1;
      }

      $this->_contents[$item_id]['quantity'] = $quantity;

      if ( $OSCOM_Customer->isLoggedOn() ) {
        $Qupdate = $OSCOM_PDO->prepare('update :table_shopping_carts set quantity = :quantity where customers_id = :customers_id and item_id = :item_id');
        $Qupdate->bindInt(':quantity', $quantity);
        $Qupdate->bindInt(':customers_id', $OSCOM_Customer->getID());
        $Qupdate->bindInt(':item_id', $item_id);
        $Qupdate->execute();
      }

      $this->_cleanUp();
      $this->_calculate();
    }

    public function remove($item_id) {
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_PDO = Registry::get('PDO');

      unset($this->_contents[$item_id]);

      if ( $OSCOM_Customer->isLoggedOn() ) {
        $Qdelete = $OSCOM_PDO->prepare('delete from :table_shopping_carts where customers_id = :customers_id and item_id = :item_id');
        $Qdelete->bindInt(':customers_id', $OSCOM_Customer->getID());
        $Qdelete->bindInt(':item_id', $item_id);
        $Qdelete->execute();

        $Qdelete = $OSCOM_PDO->prepare('delete from :table_shopping_carts_custom_variants_values where customers_id = :customers_id and shopping_carts_item_id = :shopping_carts_item_id');
        $Qdelete->bindInt(':customers_id', $OSCOM_Customer->getID());
        $Qdelete->bindInt(':shopping_carts_item_id', $item_id);
        $Qdelete->execute();
      }

      $this->_calculate();
    }

    public function getProducts() {
      static $_is_sorted = false;

      if ( $_is_sorted === false ) {
        $_is_sorted = true;

        uasort($this->_contents, function ($a, $b) {
          if ( $a['date_added'] == $b['date_added'] ) {
            return strnatcasecmp($a['name'], $b['name']);
          }

          return ($a['date_added'] > $b['date_added']) ? -1 : 1;
        });
      }

      return $this->_contents;
    }

    public function getSubTotal() {
      return $this->_sub_total;
    }

    public function getTotal() {
      return $this->_total;
    }

    public function getWeight() {
      return $this->_weight;
    }

    public function generateCartID($length = 5) {
      return Hash::getRandomString($length, 'digits');
    }

    public function getCartID() {
      return $_SESSION['cartID'];
    }

    public function getContentType() {
      $OSCOM_PDO = Registry::get('PDO');

      $this->_content_type = 'physical';

      if ( (DOWNLOAD_ENABLED == '1') && $this->hasContents() ) {
        foreach ( $this->_contents as $product_id => $data ) {
/* HPDL
          if (isset($data['attributes'])) {
            foreach ($data['attributes'] as $value) {
              $Qcheck = $OSCOM_PDO->prepare('select count(*) as total from :table_products_attributes pa, :table_products_attributes_download pad where pa.products_id = :products_id and pa.options_values_id = :options_values_id and pa.products_attributes_id = pad.products_attributes_id');
              $Qcheck->bindInt(':products_id', $products_id);
              $Qcheck->bindInt(':options_values_id', $value['options_values_id']);
              $Qcheck->execute();

              if ($Qcheck->valueInt('total') > 0) {
                switch ($this->_content_type) {
                  case 'physical':
                    $this->_content_type = 'mixed';

                    return $this->_content_type;
                    break;
                  default:
                    $this->_content_type = 'virtual';
                    break;
                }
              } else {
                switch ($this->_content_type) {
                  case 'virtual':
                    $this->_content_type = 'mixed';

                    return $this->_content_type;
                    break;
                  default:
                    $this->_content_type = 'physical';
                    break;
                }
              }
            }
          } else {
*/
            switch ( $this->_content_type ) {
              case 'virtual':
                $this->_content_type = 'mixed';

                break 2;

              default:
                $this->_content_type = 'physical';

                break;
            }
//          }
        }
      }

      return $this->_content_type;
    }

    public function isVariant($item_id) {
      return isset($this->_contents[$item_id]['variants']) && !empty($this->_contents[$item_id]['variants']);
    }

    public function getVariant($item_id) {
      if ( isset($this->_contents[$item_id]['variants']) && !empty($this->_contents[$item_id]['variants']) ) {
        return $this->_contents[$item_id]['variants'];
      }
    }

    public function isInStock($item_id) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qstock = $OSCOM_PDO->prepare('select products_quantity from :table_products where products_id = :products_id');
      $Qstock->bindInt(':products_id', $this->_contents[$item_id]['id']);
      $Qstock->execute();

      if ( ($Qstock->valueInt('products_quantity') - $this->_contents[$item_id]['quantity']) >= 0 ) {
        return true;
      } elseif ( $this->_products_in_stock === true ) {
        $this->_products_in_stock = false;
      }

      return false;
    }

    public function hasStock() {
      return $this->_products_in_stock;
    }

    public function hasShippingAddress() {
      return isset($this->_shipping_address['id']);
    }

/* param $address mixed int for address book entry, or array containing address data */
    public function setShippingAddress($address) {
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_PDO = Registry::get('PDO');

      $previous_address = false;

      if ( isset($this->_shipping_address['id']) ) {
        $previous_address = $this->getShippingAddress();
      }

      if ( $OSCOM_Customer->isLoggedOn() && is_numeric($address) ) {
        $Qaddress = $OSCOM_PDO->prepare('select ab.*, z.zone_code, z.zone_name, c.countries_name, c.countries_iso_code_2, c.countries_iso_code_3, c.address_format from :table_address_book ab left join :table_zones z on (ab.entry_zone_id = z.zone_id) left join :table_countries c on (ab.entry_country_id = c.countries_id) where ab.customers_id = :customers_id and ab.address_book_id = :address_book_id');
        $Qaddress->bindInt(':customers_id', $OSCOM_Customer->getID());
        $Qaddress->bindInt(':address_book_id', $address);
        $Qaddress->execute();

        if ( $Qaddress->fetch() !== false ) {
          $this->_shipping_address = array('id' => (int)$address,
                                           'firstname' => $Qaddress->valueProtected('entry_firstname'),
                                           'lastname' => $Qaddress->valueProtected('entry_lastname'),
                                           'gender' => $Qaddress->valueProtected('entry_gender'),
                                           'company' => $Qaddress->valueProtected('entry_company'),
                                           'street_address' => $Qaddress->valueProtected('entry_street_address'),
                                           'suburb' => $Qaddress->valueProtected('entry_suburb'),
                                           'city' => $Qaddress->valueProtected('entry_city'),
                                           'postcode' => $Qaddress->valueProtected('entry_postcode'),
                                           'state' => (strlen($Qaddress->valueProtected('entry_state')) > 0) ? $Qaddress->valueProtected('entry_state') : $Qaddress->valueProtected('zone_name'),
                                           'zone_id' => $Qaddress->valueInt('entry_zone_id'),
                                           'zone_code' => $Qaddress->value('zone_code'),
                                           'country_id' => $Qaddress->valueInt('entry_country_id'),
                                           'country_title' => $Qaddress->valueProtected('countries_name'),
                                           'country_iso_code_2' => $Qaddress->valueProtected('countries_iso_code_2'),
                                           'country_iso_code_3' => $Qaddress->valueProtected('countries_iso_code_3'),
                                           'format' => $Qaddress->value('address_format'),
                                           'telephone' => $Qaddress->valueProtected('entry_telephone'),
                                           'fax' => $Qaddress->valueProtected('entry_fax'));

        }
      } else {
        $this->_shipping_address = array('id' => 0,
                                         'firstname' => HTML::outputProtected($address['firstname']),
                                         'lastname' => HTML::outputProtected($address['lastname']),
                                         'gender' => HTML::outputProtected($address['gender']),
                                         'company' => HTML::outputProtected($address['company']),
                                         'street_address' => HTML::outputProtected($address['street_address']),
                                         'suburb' => HTML::outputProtected($address['suburb']),
                                         'city' => HTML::outputProtected($address['city']),
                                         'postcode' => HTML::outputProtected($address['postcode']),
                                         'state' => (isset($address['state']) && !empty($address['state']) ? HTML::outputProtected($address['state']) : HTML::outputProtected(Address::getZoneName($address['zone_id']))),
                                         'zone_id' => (int)$address['zone_id'],
                                         'zone_code' => HTML::outputProtected(Address::getZoneCode($address['zone_id'])),
                                         'country_id' => HTML::outputProtected($address['country_id']),
                                         'country_title' => HTML::outputProtected(Address::getCountryName($address['country_id'])),
                                         'country_iso_code_2' => HTML::outputProtected(Address::getCountryIsoCode2($address['country_id'])),
                                         'country_iso_code_3' => HTML::outputProtected(Address::getCountryIsoCode3($address['country_id'])),
                                         'format' => Address::getFormat($address['country_id']),
                                         'telephone' => HTML::outputProtected($address['telephone']),
                                         'fax' => HTML::outputProtected($address['fax']));
      }

      if ( is_array($previous_address) && ( ($previous_address['id'] != $this->_shipping_address['id']) || ($previous_address['country_id'] != $this->_shipping_address['country_id']) || ($previous_address['zone_id'] != $this->_shipping_address['zone_id']) || ($previous_address['state'] != $this->_shipping_address['state']) || ($previous_address['postcode'] != $this->_shipping_address['postcode']) ) ) {
        $this->_calculate();
      }
    }

    public function getShippingAddress($key = null) {
      if ( empty($key) ) {
        return $this->_shipping_address;
      }

      return $this->_shipping_address[$key];
    }

    public function resetShippingAddress() {
      $OSCOM_Customer = Registry::get('Customer');

      $this->_shipping_address = array('zone_id' => STORE_ZONE,
                                       'country_id' => STORE_COUNTRY);

      if ( $OSCOM_Customer->isLoggedOn() && $OSCOM_Customer->hasDefaultAddress() ) {
        $this->setShippingAddress($OSCOM_Customer->getDefaultAddressID());
      }
    }

    public function setShippingMethod($shipping_array, $calculate_total = true) {
      $this->_shipping_method = $shipping_array;

      if ( $calculate_total === true ) {
        $this->_calculate(false);
      }
    }

    public function getShippingMethod($key = null) {
      if ( empty($key) ) {
        return $this->_shipping_method;
      }

      return $this->_shipping_method[$key];
    }

    public function resetShippingMethod() {
      $this->_shipping_method = array();

      $this->_calculate();
    }

    public function hasShippingMethod() {
      return !empty($this->_shipping_method);
    }

    public function hasBillingAddress() {
      return isset($this->_billing_address['id']);
    }

/* param $address mixed int for address book entry, or array containing address data */
    public function setBillingAddress($address) {
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_PDO = Registry::get('PDO');

      $previous_address = false;

      if ( isset($this->_billing_address['id']) ) {
        $previous_address = $this->getBillingAddress();
      }

      if ( $OSCOM_Customer->isLoggedOn() && is_numeric($address) ) {
        $Qaddress = $OSCOM_PDO->prepare('select ab.*, z.zone_code, z.zone_name, c.countries_name, c.countries_iso_code_2, c.countries_iso_code_3, c.address_format from :table_address_book ab left join :table_zones z on (ab.entry_zone_id = z.zone_id) left join :table_countries c on (ab.entry_country_id = c.countries_id) where ab.customers_id = :customers_id and ab.address_book_id = :address_book_id');
        $Qaddress->bindInt(':customers_id', $OSCOM_Customer->getID());
        $Qaddress->bindInt(':address_book_id', $address);
        $Qaddress->execute();

        if ( $Qaddress->fetch() !== false ) {
          $this->_billing_address = array('id' => (int)$address,
                                          'firstname' => $Qaddress->valueProtected('entry_firstname'),
                                          'lastname' => $Qaddress->valueProtected('entry_lastname'),
                                          'gender' => $Qaddress->valueProtected('entry_gender'),
                                          'company' => $Qaddress->valueProtected('entry_company'),
                                          'street_address' => $Qaddress->valueProtected('entry_street_address'),
                                          'suburb' => $Qaddress->valueProtected('entry_suburb'),
                                          'city' => $Qaddress->valueProtected('entry_city'),
                                          'postcode' => $Qaddress->valueProtected('entry_postcode'),
                                          'state' => (strlen($Qaddress->valueProtected('entry_state')) > 0) ? $Qaddress->valueProtected('entry_state') : $Qaddress->valueProtected('zone_name'),
                                          'zone_id' => $Qaddress->valueInt('entry_zone_id'),
                                          'zone_code' => $Qaddress->value('zone_code'),
                                          'country_id' => $Qaddress->valueInt('entry_country_id'),
                                          'country_title' => $Qaddress->valueProtected('countries_name'),
                                          'country_iso_code_2' => $Qaddress->valueProtected('countries_iso_code_2'),
                                          'country_iso_code_3' => $Qaddress->valueProtected('countries_iso_code_3'),
                                          'format' => $Qaddress->value('address_format'),
                                          'telephone' => $Qaddress->valueProtected('entry_telephone'),
                                          'fax' => $Qaddress->valueProtected('entry_fax'));

        }
      } else {
        $this->_billing_address = array('id' => 0,
                                        'firstname' => HTML::outputProtected($address['firstname']),
                                        'lastname' => HTML::outputProtected($address['lastname']),
                                        'gender' => HTML::outputProtected($address['gender']),
                                        'company' => HTML::outputProtected($address['company']),
                                        'street_address' => HTML::outputProtected($address['street_address']),
                                        'suburb' => HTML::outputProtected($address['suburb']),
                                        'city' => HTML::outputProtected($address['city']),
                                        'postcode' => HTML::outputProtected($address['postcode']),
                                        'state' => (isset($address['state']) && !empty($address['state']) ? HTML::outputProtected($address['state']) : HTML::outputProtected(Address::getZoneName($address['zone_id']))),
                                        'zone_id' => (int)$address['zone_id'],
                                        'zone_code' => HTML::outputProtected(Address::getZoneCode($address['zone_id'])),
                                        'country_id' => HTML::outputProtected($address['country_id']),
                                        'country_title' => HTML::outputProtected(Address::getCountryName($address['country_id'])),
                                        'country_iso_code_2' => HTML::outputProtected(Address::getCountryIsoCode2($address['country_id'])),
                                        'country_iso_code_3' => HTML::outputProtected(Address::getCountryIsoCode3($address['country_id'])),
                                        'format' => Address::getFormat($address['country_id']),
                                        'telephone' => HTML::outputProtected($address['telephone']),
                                        'fax' => HTML::outputProtected($address['fax']));
      }

      if ( is_array($previous_address) && ( ($previous_address['id'] != $this->_billing_address['id']) || ($previous_address['country_id'] != $this->_billing_address['country_id']) || ($previous_address['zone_id'] != $this->_billing_address['zone_id']) || ($previous_address['state'] != $this->_billing_address['state']) || ($previous_address['postcode'] != $this->_billing_address['postcode']) ) ) {
        $this->_calculate();
      }
    }

    public function getBillingAddress($key = null) {
      if ( empty($key) ) {
        return $this->_billing_address;
      }

      return $this->_billing_address[$key];
    }

    public function resetBillingAddress() {
      $OSCOM_Customer = Registry::get('Customer');

      $this->_billing_address = array('zone_id' => STORE_ZONE,
                                      'country_id' => STORE_COUNTRY);

      if ( $OSCOM_Customer->isLoggedOn() && $OSCOM_Customer->hasDefaultAddress() ) {
        $this->setBillingAddress($OSCOM_Customer->getDefaultAddressID());
      }
    }

    public function setBillingMethod($billing_array) {
      $this->_billing_method = $billing_array;

      $this->_calculate();
    }

    public function getBillingMethod($key = null) {
      if ( empty($key) ) {
        return $this->_billing_method;
      }

      return $this->_billing_method[$key];
    }

    public function resetBillingMethod() {
      $this->_billing_method = array();

      $this->_calculate();
    }

    public function hasBillingMethod() {
      return !empty($this->_billing_method);
    }

    public function getTaxingAddress($id = null) {
      if ( $this->getContentType() == 'virtual' ) {
        return $this->getBillingAddress($id);
      }

      return $this->getShippingAddress($id);
    }

    public function addTaxAmount($amount) {
      $this->_tax += $amount;
    }

    public function numberOfTaxGroups() {
      return sizeof($this->_tax_groups);
    }

    public function addTaxGroup($group, $amount) {
      if ( isset($this->_tax_groups[$group]) ) {
        $this->_tax_groups[$group] += $amount;
      } else {
        $this->_tax_groups[$group] = $amount;
      }
    }

    public function getTaxGroups() {
      return $this->_tax_groups;
    }

    public function addToTotal($amount) {
      $this->_total += $amount;
    }

    public function getOrderTotals() {
      return $this->_order_totals;
    }

    public function getShippingBoxesWeight() {
      return $this->_shipping_boxes_weight;
    }

    public function numberOfShippingBoxes() {
      return $this->_shipping_boxes;
    }

    private function _cleanUp() {
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_PDO = Registry::get('PDO');

      foreach ( $this->_contents as $item_id => $data ) {
        if ( $data['quantity'] < 1 ) {
          unset($this->_contents[$item_id]);

          if ( $OSCOM_Customer->isLoggedOn() ) {
            $Qdelete = $OSCOM_PDO->prepare('delete from :table_shopping_carts where customers_id = :customers_id and item_id = :item_id');
            $Qdelete->bindInt(':customers_id', $OSCOM_Customer->getID());
            $Qdelete->bindInt(':item_id', $item_id);
            $Qdelete->execute();

            $Qdelete = $OSCOM_PDO->prepare('delete from :table_shopping_carts_custom_variants_values where customers_id = :customers_id and shopping_carts_item_id = :shopping_carts_item_id');
            $Qdelete->bindInt(':customers_id', $OSCOM_Customer->getID());
            $Qdelete->bindInt(':shopping_carts_item_id', $item_id);
            $Qdelete->execute();
          }
        }
      }
    }

    private function _calculate($set_shipping = true) {
      $OSCOM_Weight = Registry::get('Weight');
      $OSCOM_Tax = Registry::get('Tax');
      $OSCOM_Currencies = Registry::get('Currencies');

      $this->_sub_total = 0;
      $this->_total = 0;
      $this->_weight = 0;
      $this->_tax = 0;
      $this->_tax_groups = array();
      $this->_shipping_boxes_weight = 0;
      $this->_shipping_boxes = 0;
      $this->_shipping_quotes = array();
      $this->_order_totals = array();

      $_SESSION['cartID'] = $this->generateCartID();

      if ( $this->hasContents() ) {
        foreach ( $this->_contents as $data ) {
          $products_weight = $OSCOM_Weight->convert($data['weight'], $data['weight_class_id'], SHIPPING_WEIGHT_UNIT);
          $this->_weight += $products_weight * $data['quantity'];

          $tax = $OSCOM_Tax->getTaxRate($data['tax_class_id'], $this->getTaxingAddress('country_id'), $this->getTaxingAddress('zone_id'));
          $tax_description = $OSCOM_Tax->getTaxRateDescription($data['tax_class_id'], $this->getTaxingAddress('country_id'), $this->getTaxingAddress('zone_id'));

          $shown_price = $OSCOM_Currencies->addTaxRateToPrice($data['price'], $tax, $data['quantity']);

          $this->_sub_total += $shown_price;
          $this->_total += $shown_price;

          if ( DISPLAY_PRICE_WITH_TAX == '1' ) {
            $tax_amount = $shown_price - ($shown_price / (($tax < 10) ? '1.0' . str_replace('.', '', $tax) : '1.' . str_replace('.', '', $tax)));
          } else {
            $tax_amount = ($tax / 100) * $shown_price;

            $this->_total += $tax_amount;
          }

          $this->_tax += $tax_amount;

          if ( isset($this->_tax_groups[$tax_description]) ) {
            $this->_tax_groups[$tax_description] += $tax_amount;
          } else {
            $this->_tax_groups[$tax_description] = $tax_amount;
          }
        }

        $this->_shipping_boxes_weight = $this->_weight;
        $this->_shipping_boxes = 1;

        if ( SHIPPING_BOX_WEIGHT >= ($this->_shipping_boxes_weight * SHIPPING_BOX_PADDING/100) ) {
          $this->_shipping_boxes_weight = $this->_shipping_boxes_weight + SHIPPING_BOX_WEIGHT;
        } else {
          $this->_shipping_boxes_weight = $this->_shipping_boxes_weight + ($this->_shipping_boxes_weight * SHIPPING_BOX_PADDING/100);
        }

        if ( $this->_shipping_boxes_weight > SHIPPING_MAX_WEIGHT ) { // Split into many boxes
          $this->_shipping_boxes = ceil($this->_shipping_boxes_weight / SHIPPING_MAX_WEIGHT);
          $this->_shipping_boxes_weight = $this->_shipping_boxes_weight / $this->_shipping_boxes;
        }

        if ( $set_shipping === true ) {
          if ( !$this->hasShippingMethod() || ($this->getShippingMethod('is_cheapest') === true) ) {
            $OSCOM_Shipping = new Shipping();
            $this->setShippingMethod($OSCOM_Shipping->getCheapestQuote(), false);
          } else {
            $OSCOM_Shipping = new Shipping($this->getShippingMethod('id'));
            $this->setShippingMethod($OSCOM_Shipping->getQuote(), false);
          }

          Registry::set('Shipping', $OSCOM_Shipping, true);
        }

        $OSCOM_OrderTotal = new OrderTotal();
        $this->_order_totals = $OSCOM_OrderTotal->getResult();

        Registry::set('OrderTotal', $OSCOM_OrderTotal, true);
      }
    }
  }
?>
