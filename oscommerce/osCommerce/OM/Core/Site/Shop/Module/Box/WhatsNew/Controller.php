<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Module\Box\WhatsNew;

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Shop\Product;

  class Controller extends \osCommerce\OM\Core\Modules {
    var $_title,
        $_code = 'WhatsNew',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'Box';

    public function __construct() {
      $this->_title = OSCOM::getDef('box_whats_new_heading');
    }

    function initialize() {
      $OSCOM_Cache = Registry::get('Cache');
      $OSCOM_Language = Registry::get('Language');
      $OSCOM_Currencies = Registry::get('Currencies');
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_Image = Registry::get('Image');

      $this->_title_link = OSCOM::getLink(null, 'Products', 'All');

      $data = array();

      if ( (BOX_WHATS_NEW_CACHE > 0) && $OSCOM_Cache->read('box-whats_new-' . $OSCOM_Language->getCode() . '-' . $OSCOM_Currencies->getCode(), BOX_WHATS_NEW_CACHE) ) {
        $data = $OSCOM_Cache->getCache();
      } else {
        $Qnew = $OSCOM_PDO->prepare('select products_id from :table_products where products_status = :products_status order by products_date_added desc limit :max_random_select_new');
        $Qnew->bindInt(':products_status', 1);
        $Qnew->bindInt(':max_random_select_new', BOX_WHATS_NEW_RANDOM_SELECT);
        $Qnew->execute();

        $result = $Qnew->fetchAll();

        if ( count($result) > 0 ) {
          $result = $result[rand(0, count($result) - 1)];

          $OSCOM_Product = new Product($result['products_id']);

          $data = $OSCOM_Product->getData();

          $data['display_price'] = $OSCOM_Product->getPriceFormated(true);
          $data['display_image'] = $OSCOM_Product->getImage();
        }

        $OSCOM_Cache->write($data);
      }

      if ( !empty($data) ) {
        $this->_content = '';

        if ( !empty($data['display_image']) ) {
          $this->_content .= HTML::link(OSCOM::getLink(null, 'Products', $data['keyword']), $OSCOM_Image->show($data['display_image'], $data['name'])) . '<br />';
        }

        $this->_content .= HTML::link(OSCOM::getLink(null, 'Products', $data['keyword']), $data['name']) . '<br />' . $data['display_price'];
      }
    }

    function install() {
      $OSCOM_PDO = Registry::get('PDO');

      parent::install();

      $OSCOM_PDO->exec("insert into :table_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Random New Product Selection', 'BOX_WHATS_NEW_RANDOM_SELECT', '10', 'Select a random new product from this amount of the newest products available', '6', '0', now())");
      $OSCOM_PDO->exec("insert into :table_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Cache Contents', 'BOX_WHATS_NEW_CACHE', '1', 'Number of minutes to keep the contents cached (0 = no cache)', '6', '0', now())");
    }

    function getKeys() {
      if ( !isset($this->_keys) ) {
        $this->_keys = array('BOX_WHATS_NEW_RANDOM_SELECT', 'BOX_WHATS_NEW_CACHE');
      }

      return $this->_keys;
    }
  }
?>
