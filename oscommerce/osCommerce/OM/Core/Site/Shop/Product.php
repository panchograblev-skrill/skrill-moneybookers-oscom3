<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Shop\Products;

  class Product {
    protected $_data = array();

    public function __construct($id) {
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_Language = Registry::get('Language');
      $OSCOM_Service = Registry::get('Service');

      if ( !empty($id) ) {
        if ( is_numeric($id) ) {
          $Qproduct = $OSCOM_PDO->prepare('select products_id as id, parent_id, products_quantity as quantity, products_price as price, products_model as model, products_tax_class_id as tax_class_id, products_types_id as type_id, products_weight as weight, products_weight_class as weight_class_id, products_date_added as date_added, manufacturers_id, has_children from :table_products where products_id = :products_id and products_status = :products_status');
          $Qproduct->bindInt(':products_id', $id);
          $Qproduct->bindInt(':products_status', 1);
          $Qproduct->execute();

          if ( $Qproduct->fetch() !== false ) {
            $this->_data = $Qproduct->toArray();

            $this->_data['master_id'] = $Qproduct->valueInt('id');

            if ( $Qproduct->valueInt('parent_id') > 0 ) {
              $Qmaster = $OSCOM_PDO->prepare('select products_id, has_children from :table_products where products_id = :products_id and products_status = :products_status');
              $Qmaster->bindInt(':products_id', $Qproduct->valueInt('parent_id'));
              $Qmaster->bindInt(':products_status', 1);
              $Qmaster->execute();

              if ( $Qmaster->fetch() !== false ) {
                $this->_data['master_id'] = $Qmaster->valueInt('products_id');
              } else { // master product is disabled so invalidate the product variant
                $this->_data = array();
              }
            }

            if ( !empty($this->_data) ) {
              $Qdesc = $OSCOM_PDO->prepare('select products_name as name, products_description as description, products_keyword as keyword, products_tags as tags, products_url as url from :table_products_description where products_id = :products_id and language_id = :language_id');
              $Qdesc->bindInt(':products_id', $this->_data['master_id']);
              $Qdesc->bindInt(':language_id', $OSCOM_Language->getID());
              $Qdesc->execute();

              $this->_data = array_merge($this->_data, $Qdesc->fetch());
            }
          }
        } else {
          $Qproduct = $OSCOM_PDO->prepare('select p.products_id as id, p.parent_id, p.products_quantity as quantity, p.products_price as price, p.products_model as model, p.products_tax_class_id as tax_class_id, p.products_types_id as type_id, p.products_weight as weight, p.products_weight_class as weight_class_id, p.products_date_added as date_added, p.manufacturers_id, p.has_children, pd.products_name as name, pd.products_description as description, pd.products_keyword as keyword, pd.products_tags as tags, pd.products_url as url from :table_products p, :table_products_description pd where pd.products_keyword = :products_keyword and pd.language_id = :language_id and pd.products_id = p.products_id and p.products_status = :products_status');
          $Qproduct->bindValue(':products_keyword', $id);
          $Qproduct->bindInt(':language_id', $OSCOM_Language->getID());
          $Qproduct->bindInt(':products_status', 1);
          $Qproduct->execute();

          if ( $Qproduct->fetch() !== false ) {
            $this->_data = $Qproduct->toArray();

            $this->_data['master_id'] = $Qproduct->valueInt('id');
          }
        }

        if ( !empty($this->_data) ) {
          $Qimages = $OSCOM_PDO->prepare('select id, image, default_flag from :table_products_images where products_id = :products_id order by sort_order');
          $Qimages->bindInt(':products_id', $this->_data['master_id']);
          $Qimages->execute();

          $this->_data['images'] = $Qimages->fetchAll();

          $Qcategory = $OSCOM_PDO->prepare('select categories_id from :table_products_to_categories where products_id = :products_id limit 1');
          $Qcategory->bindInt(':products_id', $this->_data['master_id']);
          $Qcategory->execute();

          $this->_data['category_id'] = $Qcategory->valueInt('categories_id');

          if ( $this->_data['type_id'] > 0 ) {
            $this->_data['type_assignments'] = array();

            $Qtypes = $OSCOM_PDO->prepare('select action, module from :table_product_types_assignments where types_id = :types_id order by action, sort_order, module');
            $Qtypes->bindInt(':types_id', $this->_data['type_id']);
            $Qtypes->execute();

            while ( $Qtypes->fetch() ) {
              $this->_data['type_assignments'][$Qtypes->value('action')][] = $Qtypes->value('module');
            }
          }

          if ( (int)$this->_data['has_children'] === 1 ) {
            $this->_data['variants'] = array();

            $Qsubproducts = $OSCOM_PDO->prepare('select * from :table_products where parent_id = :parent_id and products_status = :products_status');
            $Qsubproducts->bindInt(':parent_id', $this->_data['master_id']);
            $Qsubproducts->bindInt(':products_status', 1);
            $Qsubproducts->execute();

            while ( $Qsubproducts->fetch() ) {
              $this->_data['variants'][$Qsubproducts->valueInt('products_id')]['data'] = array('price' => $Qsubproducts->value('products_price'),
                                                                                               'tax_class_id' => $Qsubproducts->valueInt('products_tax_class_id'),
                                                                                               'model' => $Qsubproducts->value('products_model'),
                                                                                               'quantity' => $Qsubproducts->value('products_quantity'),
                                                                                               'weight' => $Qsubproducts->value('products_weight'),
                                                                                               'weight_class_id' => $Qsubproducts->valueInt('products_weight_class'),
                                                                                               'availability_shipping' => 1);

              $Qvariants = $OSCOM_PDO->prepare('select pv.default_combo, pvg.id as group_id, pvg.title as group_title, pvg.module, pvv.id as value_id, pvv.title as value_title, pvv.sort_order as value_sort_order from :table_products_variants pv, :table_products_variants_groups pvg, :table_products_variants_values pvv where pv.products_id = :products_id and pv.products_variants_values_id = pvv.id and pvv.languages_id = :languages_id_pvv and pvv.products_variants_groups_id = pvg.id and pvg.languages_id = :languages_id_pvg order by pvg.sort_order, pvg.title');
              $Qvariants->bindInt(':products_id', $Qsubproducts->valueInt('products_id'));
              $Qvariants->bindInt(':languages_id_pvv', $OSCOM_Language->getID());
              $Qvariants->bindInt(':languages_id_pvg', $OSCOM_Language->getID());
              $Qvariants->execute();

              while ( $Qvariants->fetch() ) {
                $this->_data['variants'][$Qsubproducts->valueInt('products_id')]['values'][$Qvariants->valueInt('group_id')][$Qvariants->valueInt('value_id')] = array('value_id' => $Qvariants->valueInt('value_id'),
                                                                                                                                                                       'group_title' => $Qvariants->value('group_title'),
                                                                                                                                                                       'value_title' => $Qvariants->value('value_title'),
                                                                                                                                                                       'sort_order' => $Qvariants->value('value_sort_order'),
                                                                                                                                                                       'default' => (bool)$Qvariants->valueInt('default_combo'),
                                                                                                                                                                       'module' => $Qvariants->value('module'));
              }
            }
          }

          $this->_data['attributes'] = array();

          $Qattributes = $OSCOM_PDO->prepare('select tb.code, pa.value from :table_product_attributes pa, :table_templates_boxes tb where pa.products_id = :products_id and pa.languages_id in (0, :languages_id) and pa.id = tb.id');
          $Qattributes->bindInt(':products_id', $this->_data['master_id']);
          $Qattributes->bindInt(':languages_id', $OSCOM_Language->getID());
          $Qattributes->execute();

          while ( $Qattributes->fetch() ) {
            $this->_data['attributes'][$Qattributes->value('code')] = $Qattributes->value('value');
          }

          if ( $OSCOM_Service->isStarted('Reviews') ) {
            $Qavg = $OSCOM_PDO->prepare('select avg(reviews_rating) as rating from :table_reviews where products_id = :products_id and languages_id = :languages_id and reviews_status = 1');
            $Qavg->bindInt(':products_id', $this->_data['master_id']);
            $Qavg->bindInt(':languages_id', $OSCOM_Language->getID());
            $Qavg->execute();

            $this->_data['reviews_average_rating'] = round($Qavg->value('rating'));
          }
        }
      }
    }

    public function isValid() {
      return !empty($this->_data);
    }

    public function getData($key = null) {
      if ( isset($this->_data[$key]) ) {
        return $this->_data[$key];
      }

      return $this->_data;
    }

    public function getID() {
      return $this->_data['id'];
    }

    public function getMasterID() {
      return $this->_data['master_id'];
    }

    public function getTitle() {
      return $this->_data['name'];
    }

    public function getDescription() {
      return $this->_data['description'];
    }

    public function hasModel() {
      return (isset($this->_data['model']) && !empty($this->_data['model']));
    }

    public function getModel() {
      return $this->_data['model'];
    }

    public function hasKeyword() {
      return (isset($this->_data['keyword']) && !empty($this->_data['keyword']));
    }

    public function getKeyword() {
      return $this->_data['keyword'];
    }

    public function hasTags() {
      return (isset($this->_data['tags']) && !empty($this->_data['tags']));
    }

    public function getTags() {
      return $this->_data['tags'];
    }

    public function getPrice($with_special = false) {
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_Currencies = Registry::get('Currencies');

      if ( ($with_special === true) && $OSCOM_Service->isStarted('Specials') && ($new_price = Registry::get('Specials')->getPrice($this->_data['id'])) ) {
        $price = $OSCOM_Currencies->displayPriceRaw($new_price, $this->_data['tax_class_id']);
      } else {
        if ( $this->hasVariants() ) {
          $price = $OSCOM_Currencies->displayPriceRaw($this->getVariantMinPrice(), $this->_data['tax_class_id']);
        } else {
          $price = $OSCOM_Currencies->displayPriceRaw($this->_data['price'], $this->_data['tax_class_id']);
        }
      }

      return $price;
    }

    public function getPriceFormated($with_special = false) {
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_Currencies = Registry::get('Currencies');

      if ( ($with_special === true) && $OSCOM_Service->isStarted('Specials') && ($new_price = Registry::get('Specials')->getPrice($this->_data['id'])) ) {
        $price = '<s>' . $OSCOM_Currencies->displayPrice($this->_data['price'], $this->_data['tax_class_id']) . '</s> <span class="productSpecialPrice">' . $OSCOM_Currencies->displayPrice($new_price, $this->_data['tax_class_id']) . '</span>';
      } else {
        if ( $this->hasVariants() ) {
          $price = 'from&nbsp;' . $OSCOM_Currencies->displayPrice($this->getVariantMinPrice(), $this->_data['tax_class_id']);
        } else {
          $price = $OSCOM_Currencies->displayPrice($this->_data['price'], $this->_data['tax_class_id']);
        }
      }

      return $price;
    }

    public function getVariantMinPrice() {
      $price = null;

      foreach ( $this->_data['variants'] as $variant ) {
        if ( ($price === null) || ($variant['data']['price'] < $price) ) {
          $price = $variant['data']['price'];
        }
      }

      return ( $price !== null ) ? $price : 0;
    }

    public function getVariantMaxPrice() {
      $price = 0;

      foreach ( $this->_data['variants'] as $variant ) {
        if ( $variant['data']['price'] > $price ) {
          $price = $variant['data']['price'];
        }
      }

      return $price;
    }

    public function getQuantity() {
      $quantity = $this->_data['quantity'];

      if ( $this->hasVariants() ) {
        $quantity = 0;

        foreach ( $this->_data['variants'] as $variants ) {
          $quantity += $variants['data']['quantity'];
        }
      }

      return $quantity;
    }

    public function getWeight() {
      $OSCOM_Weight = Registry::get('Weight');

      $weight = 0;

      if ( $this->hasVariants() ) {
        foreach ( $this->_data['variants'] as $subproduct_id => $variants ) {
          foreach ( $variants['values'] as $group_id => $values ) {
            foreach ( $values as $value_id => $data ) {
              if ( $data['default'] === true ) {
                $weight = $OSCOM_Weight->display($variants['data']['weight'], $variants['data']['weight_class_id']);

                break 3;
              }
            }
          }
        }
      } else {
        $weight = $OSCOM_Weight->display($this->_data['weight'], $this->_data['weight_class_id']);
      }

      return $weight;
    }

    public function hasClass() {
      return ( $this->_data['type_id'] > 0 );
    }

// $action mixed, string = action, array $key $value = action + single module
    public function isTypeActionAllowed($action, $stop_at_module = null, $execute_onfail = true) {
      $action_call = $action;
      $module_call = null;

      $return_value = null;

      if ( is_array($action) ) {
        $action_call = $action[0];
        $module_call = $action[1];
      }

      if ( isset($this->_data['type_assignments'][$action_call]) ) {
        foreach ( $this->_data['type_assignments'][$action_call] as $module ) {
          if ( $module == $stop_at_module ) {
            break;
          }

          if ( is_null($module_call) || ($module_call == $module) ) {
            if ( class_exists('osCommerce\\OM\\Core\\Site\\Shop\\Module\\ProductType\\' . $module) ) {
              if ( !call_user_func(array('osCommerce\\OM\\Core\\Site\\Shop\\Module\\ProductType\\' . $module, 'isValid'), $this) ) {
                if ( ($execute_onfail === true) && method_exists('osCommerce\\OM\\Core\\Site\\Shop\\Module\\ProductType\\' . $module, 'onFail') ) {
                  call_user_func(array('osCommerce\\OM\\Core\\Site\\Shop\\Module\\ProductType\\' . $module, 'onFail'), $this);
                }

                $return_value = false;

                break;
              } else {
                $return_value = true;

                if ( $module_call == $module ) {
                  break;
                }
              }
            }
          }
        }
      }

      if ( !isset($return_value) ) {
        $return_value = true;
      }

      return $return_value;
    }

    public function hasManufacturer() {
      return ( $this->_data['manufacturers_id'] > 0 );
    }

    public function getManufacturer() {
      $OSCOM_Manufacturer = new Manufacturer($this->_data['manufacturers_id']);

      return $OSCOM_Manufacturer->getTitle();
    }

    public function getManufacturerID() {
      return $this->_data['manufacturers_id'];
    }

    public function getCategoryID() {
      return $this->_data['category_id'];
    }

    public function getImages() {
      return $this->_data['images'];
    }

    public function hasImage() {
      foreach ( $this->_data['images'] as $image ) {
        if ( $image['default_flag'] == '1' ) {
          return true;
        }
      }
    }

    public function getImage() {
      foreach ( $this->_data['images'] as $image ) {
        if ( $image['default_flag'] == '1' ) {
          return $image['image'];
        }
      }
    }

    public function hasURL() {
      return (isset($this->_data['url']) && !empty($this->_data['url']));
    }

    public function getURL() {
      return $this->_data['url'];
    }

    public function getDateAvailable() {
// HPDL
      return false; //$this->_data['date_available'];
    }

    public function getDateAdded() {
      return $this->_data['date_added'];
    }

    public function hasVariants() {
      return (isset($this->_data['variants']) && !empty($this->_data['variants']));
    }

    public function getVariants($filter_duplicates = true) {
      if ( $filter_duplicates === true ) {
        $values_array = array();

        foreach ( $this->_data['variants'] as $product_id => $variants ) {
          foreach ( $variants['values'] as $group_id => $values ) {
            foreach ( $values as $value_id => $value ) {
              if ( !isset($values_array[$group_id]) ) {
                $values_array[$group_id]['group_id'] = $group_id;
                $values_array[$group_id]['title'] = $value['group_title'];
                $values_array[$group_id]['module'] = $value['module'];
              }

              $value_exists = false;

              if ( isset($values_array[$group_id]['data']) ) {
                foreach ( $values_array[$group_id]['data'] as $data ) {
                  if ( $data['id'] == $value_id ) {
                    $value_exists = true;

                    break;
                  }
                }
              }

              if ( $value_exists === false ) {
                $values_array[$group_id]['data'][] = array('id' => $value_id,
                                                           'text' => $value['value_title'],
                                                           'default' => $value['default'],
                                                           'sort_order' => $value['sort_order']);
              } elseif ( $value['default'] === true ) {
                foreach ( $values_array[$group_id]['data'] as &$existing_data ) {
                  if ( $existing_data['id'] == $value_id ) {
                    $existing_data['default'] = true;

                    break;
                  }
                }
              }
            }
          }
        }

        foreach ( $values_array as $group_id => &$value ) {
          usort($value['data'], function ($a, $b) {
            if ( $a['sort_order'] == $b['sort_order'] ) {
              return strnatcasecmp($a['text'], $b['text']);
            }

            return ( $a['sort_order'] < $b['sort_order'] ) ? -1 : 1;
          });
        }

        return $values_array;
      }

      return $this->_data['variants'];
    }

    public function variantExists($variant) {
      return is_numeric($this->getProductVariantID($variant));
    }

    public function getProductVariantID($variant) {
      $_product_id = false;

      $_size = sizeof($variant);

      foreach ( $this->_data['variants'] as $product_id => $variants ) {
        if ( sizeof($variants['values']) === $_size ) {
          $_array = array();

          foreach ( $variants['values'] as $group_id => $value ) {
            foreach ( $value as $value_id => $value_data ) {
              if ( is_array($variant[$group_id]) && array_key_exists($value_id, $variant[$group_id]) ) {
                $_array[$group_id][$value_id] = $variant[$group_id][$value_id];
              } else {
                $_array[$group_id] = $value_id;
              }
            }
          }

          if ( sizeof(array_diff_assoc($_array, $variant)) === 0 ) {
            $_product_id = $product_id;

            break;
          }
        }
      }

      return $_product_id;
    }

    public function hasAttribute($code) {
      return isset($this->_data['attributes'][$code]);
    }

    public function getAttribute($code) {
      if ( !class_exists('osC_ProductAttributes_' . $code) ) {
        if ( file_exists(DIR_FS_CATALOG . 'includes/modules/product_attributes/' . basename($code) . '.php') ) {
          include(DIR_FS_CATALOG . 'includes/modules/product_attributes/' . basename($code) . '.php');
        }
      }

      if ( class_exists('osC_ProductAttributes_' . $code) ) {
        return call_user_func(array('osC_ProductAttributes_' . $code, 'getValue'), $this->_data['attributes'][$code]);
      }
    }

    public static function checkID($id) {
      $OSCOM_Session = Registry::get('Session');

      return ( (preg_match('/^[0-9]+(#?([0-9]+:?[0-9]+)+(;?([0-9]+:?[0-9]+)+)*)*$/', $id) || preg_match('/^[a-zA-Z0-9 -_]*$/', $id)) && ($id != $OSCOM_Session->getName()) );
    }

    public static function checkEntry($id) {
      if ( self::checkID($id) === false ) {
        return false;
      }

      $OSCOM_PDO = Registry::get('PDO');

      $sql_query = 'select p.products_id from :table_products p';

      if ( is_numeric($id) ) {
        $sql_query .= ' where p.products_id = :products_id';
      } else {
        $sql_query .= ', :table_products_description pd where pd.products_keyword = :products_keyword and pd.products_id = p.products_id';
      }

      $sql_query .= ' and p.products_status = 1 limit 1';

      $Qproduct = $OSCOM_PDO->prepare($sql_query);

      if ( is_numeric($id) ) {
        $Qproduct->bindInt(':products_id', $id);
      } else {
        $Qproduct->bindValue(':products_keyword', $id);
      }

      $Qproduct->execute();

      $result = $Qproduct->fetch();

      return ( ($result !== false) && (count($result) === 1) );
    }

    public function incrementCounter() {
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_Language = Registry::get('Language');

      $Qupdate = $OSCOM_PDO->prepare('update :table_products_description set products_viewed = products_viewed+1 where products_id = :products_id and language_id = :language_id');
      $Qupdate->bindInt(':products_id', Products::getProductID($this->_data['id']));
      $Qupdate->bindInt(':language_id', $OSCOM_Language->getID());
      $Qupdate->execute();
    }

    public function numberOfImages() {
      return count($this->_data['images']);
    }
  }
?>
