<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Module\Payment;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Shop\Order;

  class COD extends \osCommerce\OM\Core\Site\Shop\PaymentModuleAbstract {
    protected function initialize() {
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');

      $this->_title = OSCOM::getDef('payment_cod_title');
      $this->_method_title = OSCOM::getDef('payment_cod_method_title');
      $this->_status = (MODULE_PAYMENT_COD_STATUS == '1') ? true : false;
      $this->_sort_order = MODULE_PAYMENT_COD_SORT_ORDER;

      if ( $this->_status === true ) {
        if ( (int)MODULE_PAYMENT_COD_ORDER_STATUS_ID > 0 ) {
          $this->order_status = MODULE_PAYMENT_COD_ORDER_STATUS_ID;
        }

        if ( (int)MODULE_PAYMENT_COD_ZONE > 0 ) {
          $check_flag = false;

          $Qcheck = $OSCOM_PDO->prepare('select zone_id from :table_zones_to_geo_zones where geo_zone_id = :geo_zone_id and zone_country_id = :zone_country_id order by zone_id');
          $Qcheck->bindInt(':geo_zone_id', MODULE_PAYMENT_COD_ZONE);
          $Qcheck->bindInt(':zone_country_id', $OSCOM_ShoppingCart->getBillingAddress('country_id'));
          $Qcheck->execute();

          while ( $Qcheck->fetch() ) {
            if ( $Qcheck->valueInt('zone_id') < 1 ) {
              $check_flag = true;
              break;
            } elseif ( $Qcheck->valueInt('zone_id') == $OSCOM_ShoppingCart->getBillingAddress('zone_id') ) {
              $check_flag = true;
              break;
            }
          }

          if ( $check_flag === false ) {
            $this->_status = false;
          }
        }
      }
    }

    public function process() {
      $this->_order_id = Order::insert();
      Order::process($this->_order_id, $this->_order_status);
    }
  }
?>
