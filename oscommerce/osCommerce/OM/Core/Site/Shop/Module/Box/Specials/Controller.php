<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Module\Box\Specials;

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  class Controller extends \osCommerce\OM\Core\Modules {
    var $_title,
        $_code = 'Specials',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'Box';

    public function __construct() {
      $this->_title = OSCOM::getDef('box_specials_heading');
    }

    public function initialize() {
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_Cache = Registry::get('Cache');
      $OSCOM_Language = Registry::get('Language');
      $OSCOM_Currencies = Registry::get('Currencies');
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_Image = Registry::get('Image');

      $this->_title_link = OSCOM::getLink(null, 'Products', 'Specials');

      if ( $OSCOM_Service->isStarted('Specials') ) {
        if ( (BOX_SPECIALS_CACHE > 0) && $OSCOM_Cache->read('box-specials-' . $OSCOM_Language->getCode() . '-' . $OSCOM_Currencies->getCode(), BOX_SPECIALS_CACHE)) {
          $data = $OSCOM_Cache->getCache();
        } else {
          $Qspecials = $OSCOM_PDO->prepare('select p.products_id, p.products_price, p.products_tax_class_id, pd.products_name, pd.products_keyword, s.specials_new_products_price, i.image from :table_products p left join :table_products_images i on (p.products_id = i.products_id and i.default_flag = :default_flag), :table_products_description pd, :table_specials s where s.status = 1 and s.products_id = p.products_id and p.products_status = 1 and p.products_id = pd.products_id and pd.language_id = :language_id order by s.specials_date_added desc limit :max_random_select_specials');
          $Qspecials->bindInt(':default_flag', 1);
          $Qspecials->bindInt(':language_id', $OSCOM_Language->getID());
          $Qspecials->bindInt(':max_random_select_specials', BOX_SPECIALS_RANDOM_SELECT);
          $Qspecials->execute();

          $data = $Qspecials->fetchAll();

          if ( count($data) > 0 ) {
            $data = $result[rand(0, count($result) - 1)];

            $data['products_price'] = '<s>' . $OSCOM_Currencies->displayPrice($data['products_price'], $data['products_tax_class_id']) . '</s>&nbsp;<span class="productSpecialPrice">' . $OSCOM_Currencies->displayPrice($data['specials_new_products_price'], $data['products_tax_class_id']) . '</span>';

            $OSCOM_Cache->write($data);
          }
        }

        if ( !empty($data) ) {
          $this->_content = '';

          if ( !empty($data['image']) ) {
            $this->_content = HTML::link(OSCOM::getLink(null, 'Products', $data['products_keyword']), $OSCOM_Image->show($data['image'], $data['products_name'])) . '<br />';
          }

          $this->_content .= HTML::link(OSCOM::getLink(null, 'Products', $data['products_keyword']), $data['products_name']) . '<br />' . $data['products_price'];
        }
      }
    }

    public function install() {
      $OSCOM_PDO = Registry::get('PDO');

      parent::install();

      $OSCOM_PDO->exec("insert into :table_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Random Product Specials Selection', 'BOX_SPECIALS_RANDOM_SELECT', '10', 'Select a random product on special from this amount of the newest products on specials available', '6', '0', now())");
      $OSCOM_PDO->exec("insert into :table_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Cache Contents', 'BOX_SPECIALS_CACHE', '1', 'Number of minutes to keep the contents cached (0 = no cache)', '6', '0', now())");
    }

    public function getKeys() {
      if (!isset($this->_keys)) {
        $this->_keys = array('BOX_SPECIALS_RANDOM_SELECT', 'BOX_SPECIALS_CACHE');
      }

      return $this->_keys;
    }
  }
?>
