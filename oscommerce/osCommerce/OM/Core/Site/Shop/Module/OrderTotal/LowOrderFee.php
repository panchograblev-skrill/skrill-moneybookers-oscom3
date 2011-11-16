<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Module\OrderTotal;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  class LowOrderFee extends \osCommerce\OM\Core\Site\Shop\OrderTotal {
    var $output;

    var $_title,
        $_code = 'LowOrderFee',
        $_status = false,
        $_sort_order;

    public function __construct() {
      $this->output = array();

      $this->_title = OSCOM::getDef('order_total_loworderfee_title');
      $this->_description = OSCOM::getDef('order_total_loworderfee_description');
      $this->_status = (defined('MODULE_ORDER_TOTAL_LOWORDERFEE_STATUS') && (MODULE_ORDER_TOTAL_LOWORDERFEE_STATUS == 'true') ? true : false);
      $this->_sort_order = (defined('MODULE_ORDER_TOTAL_LOWORDERFEE_SORT_ORDER') ? MODULE_ORDER_TOTAL_LOWORDERFEE_SORT_ORDER : null);
    }

    function process() {
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');
      $OSCOM_Tax = Registry::get('Tax');
      $OSCOM_Currencies = Registry::get('Currencies');

      if ( MODULE_ORDER_TOTAL_LOWORDERFEE_LOW_ORDER_FEE == 'true' ) {
        switch ( MODULE_ORDER_TOTAL_LOWORDERFEE_DESTINATION ) {
          case 'national':
            if ( $OSCOM_ShoppingCart->getShippingAddress('country_id') == STORE_COUNTRY ) {
              $pass = true;
            }
            break;

          case 'international':
            if ( $OSCOM_ShoppingCart->getShippingAddress('country_id') != STORE_COUNTRY ) {
              $pass = true;
            }
            break;

          case 'both':
            $pass = true;
            break;

          default:
            $pass = false;
        }

        if ( ($pass == true) && ($OSCOM_ShoppingCart->getSubTotal() < MODULE_ORDER_TOTAL_LOWORDERFEE_ORDER_UNDER) ) {
          $tax = $OSCOM_Tax->getTaxRate(MODULE_ORDER_TOTAL_LOWORDERFEE_TAX_CLASS, $OSCOM_ShoppingCart->getTaxingAddress('country_id'), $OSCOM_ShoppingCart->getTaxingAddress('zone_id'));
          $tax_description = $OSCOM_Tax->getTaxRateDescription(MODULE_ORDER_TOTAL_LOWORDERFEE_TAX_CLASS, $OSCOM_ShoppingCart->getTaxingAddress('country_id'), $OSCOM_ShoppingCart->getTaxingAddress('zone_id'));

          $OSCOM_ShoppingCart->addTaxAmount($OSCOM_Tax->calculate(MODULE_ORDER_TOTAL_LOWORDERFEE_FEE, $tax));
          $OSCOM_ShoppingCart->addTaxGroup($tax_description, $OSCOM_Tax->calculate(MODULE_ORDER_TOTAL_LOWORDERFEE_FEE, $tax));
          $OSCOM_ShoppingCart->addToTotal(MODULE_ORDER_TOTAL_LOWORDERFEE_FEE + $OSCOM_Tax->calculate(MODULE_ORDER_TOTAL_LOWORDERFEE_FEE, $tax));

          $this->output[] = array('title' => $this->_title . ':',
                                  'text' => $OSCOM_Currencies->displayPriceWithTaxRate(MODULE_ORDER_TOTAL_LOWORDERFEE_FEE, $tax),
                                  'value' => $OSCOM_Currencies->addTaxRateToPrice(MODULE_ORDER_TOTAL_LOWORDERFEE_FEE, $tax));
        }
      }
    }
  }
?>
