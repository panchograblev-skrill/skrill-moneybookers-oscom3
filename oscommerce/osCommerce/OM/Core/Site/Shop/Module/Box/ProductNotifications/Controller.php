<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Module\Box\ProductNotifications;

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  class Controller extends \osCommerce\OM\Core\Modules {
    var $_title,
        $_code = 'ProductNotifications',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'Box';

    public function __construct() {
      $this->_title = OSCOM::getDef('box_product_notifications_heading');
    }

    public function initialize() {
      $OSCOM_Product = ( Registry::exists('Product') ) ? Registry::get('Product') : null;
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_Language = Registry::get('Language');

      $this->_title_link = OSCOM::getLink(null, 'Account', 'Notifications', 'SSL');

      if ( isset($OSCOM_Product) && ($OSCOM_Product instanceof \osCommerce\OM\Site\Shop\Product) && $OSCOM_Product->isValid() ) {
        if ( $OSCOM_Customer->isLoggedOn() ) {
          $Qcheck = $OSCOM_PDO->prepare('select global_product_notifications from :table_customers where customers_id = :customers_id');
          $Qcheck->bindInt(':customers_id', $OSCOM_Customer->getID());
          $Qcheck->execute();

          if ( $Qcheck->valueInt('global_product_notifications') === 0 ) {
            $get_params = array();

            foreach ( $_GET as $key => $value ) {
              if ( ($key != 'action') && ($key != Registry::get('Session')->getName()) && ($key != 'x') && ($key != 'y') ) {
                $get_params[] = $key . '=' . $value;
              }
            }

            $get_params = implode($get_params, '&');

            if ( !empty($get_params) ) {
              $get_params .= '&';
            }

            $Qcheck = $OSCOM_PDO->prepare('select products_id from :table_products_notifications where products_id = :products_id and customers_id = :customers_id limit 1');
            $Qcheck->bindInt(':products_id', $OSCOM_Product->getID());
            $Qcheck->bindInt(':customers_id', $OSCOM_Customer->getID());
            $Qcheck->execute();

            $result = $Qcheck->fetch();

            if ( !empty($result) ) {
              $this->_content = '<div style="float: left; width: 55px;">' . HTML::link(OSCOM::getLink(null, null, $get_params . 'action=notify_remove', 'AUTO'), HTML::image(DIR_WS_IMAGES . 'box_products_notifications_remove.gif', sprintf(OSCOM::getDef('box_product_notifications_remove'), $OSCOM_Product->getTitle()))) . '</div>' .
                                HTML::link(OSCOM::getLink(null, null, $get_params . 'action=notify_remove', 'AUTO'), sprintf(OSCOM::getDef('box_product_notifications_remove'), $OSCOM_Product->getTitle()));
            } else {
              $this->_content = '<div style="float: left; width: 55px;">' . HTML::link(OSCOM::getLink(null, null, $get_params . 'action=notify_add', 'AUTO'), HTML::image(DIR_WS_IMAGES . 'box_products_notifications.gif', sprintf(OSCOM::getDef('box_product_notifications_add'), $OSCOM_Product->getTitle()))) . '</div>' .
                                HTML::link(OSCOM::getLink(null, null, $get_params . 'action=notify_add', 'AUTO'), sprintf(OSCOM::getDef('box_product_notifications_add'), $OSCOM_Product->getTitle()));
            }

            $this->_content .= '<div style="clear: both;"></div>';
          }
        }
      }
    }
  }
?>
