<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Module\Content\NewProducts;

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Shop\Product;

  class Controller extends \osCommerce\OM\Core\Modules {
    var $_title,
        $_code = 'NewProducts',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'Content';

    public function __construct() {
      $this->_title = OSCOM::getDef('new_products_title');
    }

    public function initialize() {
      $OSCOM_Cache = Registry::get('Cache');
      $OSCOM_Language = Registry::get('Language');
      $OSCOM_Currencies = Registry::get('Currencies');
      $OSCOM_Category = Registry::get('Category');
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_Image = Registry::get('Image');

      $data = array();

      if ( (MODULE_CONTENT_NEW_PRODUCTS_CACHE > 0) && $OSCOM_Cache->read('new_products-' . $OSCOM_Language->getCode() . '-' . $OSCOM_Currencies->getCode() . '-' . $OSCOM_Category->getID(), MODULE_CONTENT_NEW_PRODUCTS_CACHE) ) {
        $data = $OSCOM_Cache->getCache();
      } else {
        if ( $OSCOM_Category->getID() < 1 ) {
          $Qproducts = $OSCOM_PDO->prepare('select products_id from :table_products where products_status = :products_status and parent_id is null order by products_date_added desc limit :max_display_new_products');
        } else {
          $Qproducts = $OSCOM_PDO->prepare('select distinct p2c.products_id from :table_products p, :table_products_to_categories p2c, :table_categories c where c.parent_id = :category_parent_id and c.categories_id = p2c.categories_id and p2c.products_id = p.products_id and p.products_status = :products_status and p.parent_id is null order by p.products_date_added desc limit :max_display_new_products');
          $Qproducts->bindInt(':category_parent_id', $OSCOM_Category->getID());
        }

        $Qproducts->bindInt(':products_status', 1);
        $Qproducts->bindInt(':max_display_new_products', MODULE_CONTENT_NEW_PRODUCTS_MAX_DISPLAY);
        $Qproducts->execute();

        while ( $Qproducts->fetch() ) {
          $OSCOM_Product = new Product($Qproducts->valueInt('products_id'));

          $data[$OSCOM_Product->getID()] = $OSCOM_Product->getData();
          $data[$OSCOM_Product->getID()]['display_price'] = $OSCOM_Product->getPriceFormated(true);
          $data[$OSCOM_Product->getID()]['display_image'] = $OSCOM_Product->getImage();
        }

        $OSCOM_Cache->write($data);
      }

      if ( !empty($data) ) {
        $this->_content = '<div style="overflow: auto; height: 100%;">';

        foreach ( $data as $product ) {
          $this->_content .= '<span style="width: 33%; float: left; text-align: center;">' .
                             HTML::link(OSCOM::getLink(null, 'Products', $product['keyword']), $OSCOM_Image->show($product['display_image'], $product['name'])) . '<br />' .
                             HTML::link(OSCOM::getLink(null, 'Products', $product['keyword']), $product['name']) . '<br />' .
                             $product['display_price'] .
                             '</span>';
        }

        $this->_content .= '</div>';
      }
    }

    function install() {
      $OSCOM_PDO = Registry::get('PDO');

      parent::install();

      $OSCOM_PDO->exec("insert into :table_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Maximum Entries To Display', 'MODULE_CONTENT_NEW_PRODUCTS_MAX_DISPLAY', '9', 'Maximum number of new products to display', '6', '0', now())");
      $OSCOM_PDO->exec("insert into :table_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Cache Contents', 'MODULE_CONTENT_NEW_PRODUCTS_CACHE', '60', 'Number of minutes to keep the contents cached (0 = no cache)', '6', '0', now())");
    }

    function getKeys() {
      if ( !isset($this->_keys) ) {
        $this->_keys = array('MODULE_CONTENT_NEW_PRODUCTS_MAX_DISPLAY', 'MODULE_CONTENT_NEW_PRODUCTS_CACHE');
      }

      return $this->_keys;
    }
  }
?>
