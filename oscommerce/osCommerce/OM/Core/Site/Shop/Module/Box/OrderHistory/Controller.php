<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Module\Box\OrderHistory;

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  class Controller extends \osCommerce\OM\Core\Modules {
    var $_title,
        $_code = 'OrderHistory',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'Box';

    public function __construct() {
      $this->_title = OSCOM::getDef('box_order_history_heading');
    }

    public function initialize() {
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_Language = Registry::get('Language');

      if ( $OSCOM_Customer->isLoggedOn() ) {
        $Qorders = $OSCOM_PDO->prepare('select distinct op.products_id from :table_orders o, :table_orders_products op, :table_products p where o.customers_id = :customers_id and o.orders_id = op.orders_id and op.products_id = p.products_id and p.products_status = 1 group by products_id order by o.date_purchased desc limit :limit');
        $Qorders->bindInt(':customers_id', $OSCOM_Customer->getID());
        $Qorders->bindInt(':limit', BOX_ORDER_HISTORY_MAX_LIST);
        $Qorders->execute();

        $result = $Qorders->fetchAll();

        if ( count($result) > 0 ) {
          $product_ids = '';

          foreach ( $result as $r ) {
            $product_ids .= $r['products_id'] . ',';
          }

          $product_ids = substr($product_ids, 0, -1);

          $Qproducts = $OSCOM_PDO->prepare('select products_id, products_name, products_keyword from :table_products_description where products_id in (' . $product_ids . ') and language_id = :language_id order by products_name');
          $Qproducts->bindInt(':language_id', $OSCOM_Language->getID());
          $Qproducts->execute();

          $this->_content = '<ol style="list-style: none; margin: 0; padding: 0;">';

          while ( $Qproducts->fetch() ) {
            $this->_content .= '<li>' . HTML::link(OSCOM::getLink(null, 'Products', $Qproducts->value('products_keyword')), $Qproducts->value('products_name')) . '</li>';
          }

          $this->_content .= '</ol>';
        }
      }
    }

    public function install() {
      $OSCOM_PDO = Registry::get('PDO');

      parent::install();

      $OSCOM_PDO->exec("insert into :table_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Maximum List Size', 'BOX_ORDER_HISTORY_MAX_LIST', '5', 'Maximum amount of products to show in the listing', '6', '0', now())");
    }

    public function getKeys() {
      if (!isset($this->_keys)) {
        $this->_keys = array('BOX_ORDER_HISTORY_MAX_LIST');
      }

      return $this->_keys;
    }
  }
?>
