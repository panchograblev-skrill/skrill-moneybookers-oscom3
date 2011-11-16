<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Languages\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class Import {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $error = false;
      $add_category_and_product_placeholders = true;

      $OSCOM_PDO->beginTransaction();

      if ( isset($data['id']) ) {
        $add_category_and_product_placeholders = false;

        $Qlanguage = $OSCOM_PDO->prepare('update :table_languages set name = :name, code = :code, locale = :locale, charset = :charset, date_format_short = :date_format_short, date_format_long = :date_format_long, time_format = :time_format, text_direction = :text_direction, currencies_id = :currencies_id, numeric_separator_decimal = :numeric_separator_decimal, numeric_separator_thousands = :numeric_separator_thousands, parent_id = :parent_id where languages_id = :languages_id');
        $Qlanguage->bindInt(':languages_id', $data['id']);
      } else {
        $Qlanguage = $OSCOM_PDO->prepare('insert into :table_languages (name, code, locale, charset, date_format_short, date_format_long, time_format, text_direction, currencies_id, numeric_separator_decimal, numeric_separator_thousands, parent_id) values (:name, :code, :locale, :charset, :date_format_short, :date_format_long, :time_format, :text_direction, :currencies_id, :numeric_separator_decimal, :numeric_separator_thousands, :parent_id)');
      }

      $Qlanguage->bindValue(':name', $data['name']);
      $Qlanguage->bindValue(':code', $data['code']);
      $Qlanguage->bindValue(':locale', $data['locale']);
      $Qlanguage->bindValue(':charset', $data['charset']);
      $Qlanguage->bindValue(':date_format_short', $data['date_format_short']);
      $Qlanguage->bindValue(':date_format_long', $data['date_format_long']);
      $Qlanguage->bindValue(':time_format', $data['time_format']);
      $Qlanguage->bindValue(':text_direction', $data['text_direction']);
      $Qlanguage->bindInt(':currencies_id', $data['currencies_id']);
      $Qlanguage->bindValue(':numeric_separator_decimal', $data['numeric_separator_decimal']);
      $Qlanguage->bindValue(':numeric_separator_thousands', $data['numeric_separator_thousands']);
      $Qlanguage->bindInt(':parent_id', $data['parent_id']);
      $Qlanguage->execute();

      if ( $Qlanguage->isError() ) {
        $error = true;
      } else {
        if ( !isset($data['id']) ) {
          $data['id'] = $OSCOM_PDO->lastInsertId();
        }

        if ( $data['import_type'] == 'replace' ) {
          $Qdel =  $OSCOM_PDO->prepare('delete from :table_languages_definitions where languages_id = :languages_id');
          $Qdel->bindInt(':languages_id', $data['id']);
          $Qdel->execute();

          if ( $Qdel->isError() ) {
            $error = true;
          }
        }
      }

      if ( $error === false ) {
        foreach ( $data['definitions'] as $def ) {
          $insert = false;
          $update = false;

          if ( $data['import_type'] == 'replace' ) {
            $insert = true;
          } else {
            $Qcheck = $OSCOM_PDO->prepare('select definition_key, content_group from :table_languages_definitions where definition_key = :definition_key and languages_id = :languages_id and content_group = :content_group');
            $Qcheck->bindValue(':definition_key', $def['key']);
            $Qcheck->bindInt(':languages_id', $data['id']);
            $Qcheck->bindValue(':content_group', $def['group']);
            $Qcheck->execute();

            if ( $Qcheck->fetch() !== false ) {
              if ( $data['import_type'] == 'update' ) {
                $update = true;
              }
            } elseif ( $data['import_type'] == 'add' ) {
              $insert = true;
            }
          }

          if ( ($insert === true) || ($update === true) ) {
            if ( $insert === true ) {
              $Qdef = $OSCOM_PDO->prepare('insert into :table_languages_definitions (languages_id, content_group, definition_key, definition_value) values (:languages_id, :content_group, :definition_key, :definition_value)');
            } else {
              $Qdef = $OSCOM_PDO->prepare('update :table_languages_definitions set content_group = :content_group, definition_key = :definition_key, definition_value = :definition_value where definition_key = :definition_key and languages_id = :languages_id and content_group = :content_group');
              $Qdef->bindValue(':definition_key', $def['key']);
              $Qdef->bindValue(':content_group', $def['group']);
            }

            $Qdef->bindInt(':languages_id', $data['id']);
            $Qdef->bindValue(':content_group', $def['group']);
            $Qdef->bindValue(':definition_key', $def['key']);
            $Qdef->bindValue(':definition_value', $def['value']);
            $Qdef->execute();

            if ( $Qdef->isError() ) {
              $error = true;
              break;
            }
          }
        }
      }

      if ( $add_category_and_product_placeholders === true ) {
        if ( $error === false ) {
          $Qcategories = $OSCOM_PDO->prepare('select categories_id, categories_name from :table_categories_description where language_id = :language_id');
          $Qcategories->bindInt(':language_id', $data['default_language_id']);
          $Qcategories->execute();

          while ( $Qcategories->fetch() ) {
            $Qinsert = $OSCOM_PDO->prepare('insert into :table_categories_description (categories_id, language_id, categories_name) values (:categories_id, :language_id, :categories_name)');
            $Qinsert->bindInt(':categories_id', $Qcategories->valueInt('categories_id'));
            $Qinsert->bindInt(':language_id', $data['id']);
            $Qinsert->bindValue(':categories_name', $Qcategories->value('categories_name'));
            $Qinsert->execute();

            if ( $Qinsert->isError() ) {
              $error = true;
              break;
            }
          }
        }

        if ( $error === false ) {
          $Qproducts = $OSCOM_PDO->prepare('select products_id, products_name, products_description, products_keyword, products_tags, products_url from :table_products_description where language_id = :language_id');
          $Qproducts->bindInt(':language_id', $data['default_language_id']);
          $Qproducts->execute();

          while ( $Qproducts->fetch() ) {
            $Qinsert = $OSCOM_PDO->prepare('insert into :table_products_description (products_id, language_id, products_name, products_description, products_keyword, products_tags, products_url) values (:products_id, :language_id, :products_name, :products_description, :products_keyword, :products_tags, :products_url)');
            $Qinsert->bindInt(':products_id', $Qproducts->valueInt('products_id'));
            $Qinsert->bindInt(':language_id', $data['id']);
            $Qinsert->bindValue(':products_name', $Qproducts->value('products_name'));
            $Qinsert->bindValue(':products_description', $Qproducts->value('products_description'));
            $Qinsert->bindValue(':products_keyword', $Qproducts->value('products_keyword'));
            $Qinsert->bindValue(':products_tags', $Qproducts->value('products_tags'));
            $Qinsert->bindValue(':products_url', $Qproducts->value('products_url'));
            $Qinsert->execute();

            if ( $Qinsert->isError() ) {
              $error = true;
              break;
            }
          }
        }

        if ( $error === false ) {
          $Qattributes = $OSCOM_PDO->prepare('select id, products_id, value from :table_product_attributes where languages_id = :languages_id');
          $Qattributes->bindInt(':languages_id', $data['default_language_id']);
          $Qattributes->execute();

          while ( $Qattributes->fetch() ) {
            $Qinsert = $OSCOM_PDO->prepare('insert into :table_product_attributes (id, products_id, languages_id, value) values (:id, :products_id, :languages_id, :value)');
            $Qinsert->bindInt(':id', $Qattributes->valueInt('id'));
            $Qinsert->bindInt(':products_id', $Qattributes->valueInt('products_id'));
            $Qinsert->bindInt(':languages_id', $data['id']);
            $Qinsert->bindValue(':value', $Qattributes->value('value'));
            $Qinsert->execute();

            if ( $Qinsert->isError() ) {
              $error = true;
              break;
            }
          }
        }

        if ( $error === false ) {
          $Qgroups = $OSCOM_PDO->prepare('select id, title, sort_order, module from :table_products_variants_groups where languages_id = :languages_id');
          $Qgroups->bindInt(':languages_id', $data['default_language_id']);
          $Qgroups->execute();

          while ( $Qgroups->fetch() ) {
            $Qinsert = $OSCOM_PDO->prepare('insert into :table_products_variants_groups (id, languages_id, title, sort_order, module) values (:id, :languages_id, :title, :sort_order, :module)');
            $Qinsert->bindInt(':id', $Qgroups->valueInt('id'));
            $Qinsert->bindInt(':languages_id', $data['id']);
            $Qinsert->bindValue(':title', $Qgroups->value('title'));
            $Qinsert->bindInt(':sort_order', $Qgroups->valueInt('sort_order'));
            $Qinsert->bindValue(':module', $Qgroups->value('module'));
            $Qinsert->execute();

            if ( $Qinsert->isError() ) {
              $error = true;
              break;
            }
          }
        }

        if ( $error === false ) {
          $Qvalues = $OSCOM_PDO->prepare('select id, products_variants_groups_id, title, sort_order from :table_products_variants_values where languages_id = :languages_id');
          $Qvalues->bindInt(':languages_id', $data['default_language_id']);
          $Qvalues->execute();

          while ( $Qvalues->fetch() ) {
            $Qinsert = $OSCOM_PDO->prepare('insert into :table_products_variants_values (id, languages_id, products_variants_groups_id, title, sort_order) values (:id, :languages_id, :products_variants_groups_id, :title, :sort_order)');
            $Qinsert->bindInt(':id', $Qvalues->valueInt('id'));
            $Qinsert->bindInt(':languages_id', $data['id']);
            $Qinsert->bindInt(':products_variants_groups_id', $Qvalues->valueInt('products_variants_groups_id'));
            $Qinsert->bindValue(':title', $Qvalues->value('title'));
            $Qinsert->bindInt(':sort_order', $Qvalues->valueInt('sort_order'));
            $Qinsert->execute();

            if ( $Qinsert->isError() ) {
              $error = true;
              break;
            }
          }
        }

        if ( $error === false ) {
          $Qmanufacturers = $OSCOM_PDO->prepare('select manufacturers_id, manufacturers_url from :table_manufacturers_info where languages_id = :languages_id');
          $Qmanufacturers->bindInt(':languages_id', $data['default_language_id']);
          $Qmanufacturers->execute();

          while ( $Qmanufacturers->fetch() ) {
            $Qinsert = $OSCOM_PDO->prepare('insert into :table_manufacturers_info (manufacturers_id, languages_id, manufacturers_url) values (:manufacturers_id, :languages_id, :manufacturers_url)');
            $Qinsert->bindInt(':manufacturers_id', $Qmanufacturers->valueInt('manufacturers_id'));
            $Qinsert->bindInt(':languages_id', $data['id']);
            $Qinsert->bindValue(':manufacturers_url', $Qmanufacturers->value('manufacturers_url'));
            $Qinsert->execute();

            if ( $Qinsert->isError() ) {
              $error = true;
              break;
            }
          }
        }

        if ( $error === false ) {
          $Qstatus = $OSCOM_PDO->prepare('select orders_status_id, orders_status_name from :table_orders_status where language_id = :language_id');
          $Qstatus->bindInt(':language_id', $data['default_language_id']);
          $Qstatus->execute();

          while ( $Qstatus->fetch() ) {
            $Qinsert = $OSCOM_PDO->prepare('insert into :table_orders_status (orders_status_id, language_id, orders_status_name) values (:orders_status_id, :language_id, :orders_status_name)');
            $Qinsert->bindInt(':orders_status_id', $Qstatus->valueInt('orders_status_id'));
            $Qinsert->bindInt(':language_id', $data['id']);
            $Qinsert->bindValue(':orders_status_name', $Qstatus->value('orders_status_name'));
            $Qinsert->execute();

            if ( $Qinsert->isError() ) {
              $error = true;
              break;
            }
          }
        }

        if ( $error === false ) {
          $Qstatus = $OSCOM_PDO->prepare('select id, status_name from :table_orders_transactions_status where language_id = :language_id');
          $Qstatus->bindInt(':language_id', $data['default_language_id']);
          $Qstatus->execute();

          while ( $Qstatus->fetch() ) {
            $Qinsert = $OSCOM_PDO->prepare('insert into :table_orders_transactions_status (id, language_id, status_name) values (:id, :language_id, :status_name)');
            $Qinsert->bindInt(':id', $Qstatus->valueInt('id'));
            $Qinsert->bindInt(':language_id', $data['id']);
            $Qinsert->bindValue(':status_name', $Qstatus->value('status_name'));
            $Qinsert->execute();

            if ( $Qinsert->isError() ) {
              $error = true;
              break;
            }
          }
        }

        if ( $error === false ) {
          $Qstatus = $OSCOM_PDO->prepare('select id, title, css_key from :table_shipping_availability where languages_id = :languages_id');
          $Qstatus->bindInt(':languages_id', $data['default_language_id']);
          $Qstatus->execute();

          while ( $Qstatus->fetch() ) {
            $Qinsert = $OSCOM_PDO->prepare('insert into :table_shipping_availability (id, languages_id, title, css_key) values (:id, :languages_id, :title, :css_key)');
            $Qinsert->bindInt(':id', $Qstatus->valueInt('id'));
            $Qinsert->bindInt(':languages_id', $data['id']);
            $Qinsert->bindValue(':title', $Qstatus->value('title'));
            $Qinsert->bindValue(':css_key', $Qstatus->value('css_key'));
            $Qinsert->execute();

            if ( $Qinsert->isError() ) {
              $error = true;
              break;
            }
          }
        }

        if ( $error === false ) {
          $Qstatus = $OSCOM_PDO->prepare('select weight_class_id, weight_class_key, weight_class_title from :table_weight_classes where language_id = :language_id');
          $Qstatus->bindInt(':language_id', $data['default_language_id']);
          $Qstatus->execute();

          while ( $Qstatus->fetch() ) {
            $Qinsert = $OSCOM_PDO->prepare('insert into :table_weight_classes (weight_class_id, weight_class_key, language_id, weight_class_title) values (:weight_class_id, :weight_class_key, :language_id, :weight_class_title)');
            $Qinsert->bindInt(':weight_class_id', $Qstatus->valueInt('weight_class_id'));
            $Qinsert->bindValue(':weight_class_key', $Qstatus->value('weight_class_key'));
            $Qinsert->bindInt(':language_id', $data['id']);
            $Qinsert->bindValue(':weight_class_title', $Qstatus->value('weight_class_title'));
            $Qinsert->execute();

            if ( $Qinsert->isError() ) {
              $error = true;
              break;
            }
          }
        }

        if ( $error === false ) {
          $Qgroup = $OSCOM_PDO->prepare('select id, title, code, size_width, size_height, force_size from :table_products_images_groups where language_id = :language_id');
          $Qgroup->bindInt(':language_id', $data['default_language_id']);
          $Qgroup->execute();

          while ( $Qgroup->fetch() ) {
            $Qinsert = $OSCOM_PDO->prepare('insert into :table_products_images_groups (id, language_id, title, code, size_width, size_height, force_size) values (:id, :language_id, :title, :code, :size_width, :size_height, :force_size)');
            $Qinsert->bindInt(':id', $Qgroup->valueInt('id'));
            $Qinsert->bindInt(':language_id', $data['id']);
            $Qinsert->bindValue(':title', $Qgroup->value('title'));
            $Qinsert->bindValue(':code', $Qgroup->value('code'));
            $Qinsert->bindInt(':size_width', $Qgroup->value('size_width'));
            $Qinsert->bindInt(':size_height', $Qgroup->value('size_height'));
            $Qinsert->bindInt(':force_size', $Qgroup->value('force_size'));
            $Qinsert->execute();

            if ( $Qinsert->isError() ) {
              $error = true;
              break;
            }
          }
        }
      }

      if ( $error === false ) {
        $OSCOM_PDO->commit();

        return true;
      } else {
        $OSCOM_PDO->rollBack();
      }

      return false;
    }
  }
?>
