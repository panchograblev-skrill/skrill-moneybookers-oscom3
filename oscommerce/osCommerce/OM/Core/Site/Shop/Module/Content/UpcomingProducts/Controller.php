<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Module\Content\UpcomingProducts;

  use osCommerce\OM\Core\DateTime;
  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Shop\Product;

  class Controller extends \osCommerce\OM\Core\Modules {
    var $_title,
        $_code = 'UpcomingProducts',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'Content';

    public function __construct() {
      $this->_title = OSCOM::getDef('upcoming_products_title');
    }

    public function initialize() {
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_Language = Registry::get('Language');
      $OSCOM_Currencies = Registry::get('Currencies');

      $Qupcoming = $OSCOM_PDO->prepare('select p.products_id, pa.value as date_expected from :table_products p, :table_templates_boxes tb, :table_product_attributes pa where tb.code = :code and tb.id = pa.id and to_days(str_to_date(pa.value, "%Y-%m-%d")) >= to_days(now()) and pa.products_id = p.products_id and p.products_status = :products_status order by pa.value limit :max_display_upcoming_products');
      $Qupcoming->bindValue(':code', 'DateAvailable');
      $Qupcoming->bindInt(':products_status', 1);
      $Qupcoming->bindInt(':max_display_upcoming_products', MODULE_CONTENT_UPCOMING_PRODUCTS_MAX_DISPLAY);

      if ( MODULE_CONTENT_UPCOMING_PRODUCTS_CACHE > 0 ) {
        $Qupcoming->setCache('upcoming_products-' . $OSCOM_Language->getCode() . '-' . $OSCOM_Currencies->getCode(), MODULE_CONTENT_UPCOMING_PRODUCTS_CACHE);
      }

      $Qupcoming->execute();

      $result = $Qupcoming->fetchAll();

      if ( !empty($result) ) {
        $this->_content = '<ol style="list-style: none;">';

        foreach ( $result as $r ) {
          $OSCOM_Product = new Product($r['products_id']);

          $this->_content .= '<li>' . DateTime::getLong($r['date_expected']) . ': ' . HTML::link(OSCOM::getLink(null, 'Products', $OSCOM_Product->getKeyword()), $OSCOM_Product->getTitle()) . ' ' . $OSCOM_Product->getPriceFormated(true) . '</li>';
        }

        $this->_content .= '</ol>';
      }
    }

    public function install() {
      $OSCOM_PDO = Registry::get('PDO');

      parent::install();

      $OSCOM_PDO->exec("insert into :table_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Maximum Entries To Display', 'MODULE_CONTENT_UPCOMING_PRODUCTS_MAX_DISPLAY', '10', 'Maximum number of upcoming products to display', '6', '0', now())");
      $OSCOM_PDO->exec("insert into :table_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Cache Contents', 'MODULE_CONTENT_UPCOMING_PRODUCTS_CACHE', '1440', 'Number of minutes to keep the contents cached (0 = no cache)', '6', '0', now())");
    }

    public function getKeys() {
      if ( !isset($this->_keys) ) {
        $this->_keys = array('MODULE_CONTENT_UPCOMING_PRODUCTS_MAX_DISPLAY', 'MODULE_CONTENT_UPCOMING_PRODUCTS_CACHE');
      }

      return $this->_keys;
    }
  }
?>
