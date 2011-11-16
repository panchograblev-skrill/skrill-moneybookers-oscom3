<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Module\Box\Categories;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  class Controller extends \osCommerce\OM\Core\Modules {
    var $_title,
        $_code = 'Categories',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'Box';

    public function __construct() {
      $this->_title = OSCOM::getDef('box_categories_heading');
    }

    public function initialize() {
      $OSCOM_CategoryTree = Registry::get('CategoryTree');
      $OSCOM_Category = Registry::get('Category');

      $OSCOM_CategoryTree->reset();
      $OSCOM_CategoryTree->setCategoryPath($OSCOM_Category->getPath(), '<b>', '</b>');
      $OSCOM_CategoryTree->setParentGroupString('', '');
      $OSCOM_CategoryTree->setParentString('', '-&gt;');
      $OSCOM_CategoryTree->setChildString('', '<br />');
      $OSCOM_CategoryTree->setSpacerString('&nbsp;', 2);
      $OSCOM_CategoryTree->setShowCategoryProductCount((BOX_CATEGORIES_SHOW_PRODUCT_COUNT == '1') ? true : false);

      $this->_content = $OSCOM_CategoryTree->getTree();
    }

    function install() {
      $OSCOM_PDO = Registry::get('PDO');

      parent::install();

      $OSCOM_PDO->exec("insert into :table_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Show Product Count', 'BOX_CATEGORIES_SHOW_PRODUCT_COUNT', '1', 'Show the amount of products each category has', '6', '0', 'osc_cfg_use_get_boolean_value', 'osc_cfg_set_boolean_value(array(1, -1))', now())");
    }

    function getKeys() {
      if (!isset($this->_keys)) {
        $this->_keys = array('BOX_CATEGORIES_SHOW_PRODUCT_COUNT');
      }

      return $this->_keys;
    }
  }
?>
