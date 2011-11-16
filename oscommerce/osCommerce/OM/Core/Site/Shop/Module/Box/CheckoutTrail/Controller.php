<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Module\Box\CheckoutTrail;

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  class Controller extends \osCommerce\OM\Core\Modules {
    var $_title,
        $_code = 'CheckoutTrail',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'Box';

    public function __construct() {
      $this->_title = OSCOM::getDef('box_ordering_steps_heading');
    }

    public function initialize() {
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');
      $OSCOM_Template = Registry::get('Template');

      $steps = array();

      if ( $OSCOM_ShoppingCart->getContentType() != 'virtual' ) {
        $steps[] = array('title' => OSCOM::getDef('box_ordering_steps_delivery'),
                         'code' => 'shipping',
                         'active' => (($OSCOM_Template->getModule() == 'Shipping') || ($OSCOM_Template->getModule() == 'ShippingAddress') ? true : false));
      }

      $steps[] = array('title' => OSCOM::getDef('box_ordering_steps_payment'),
                       'code' => 'payment',
                       'active' => (($OSCOM_Template->getModule() == 'Payment') || ($OSCOM_Template->getModule() == 'PaymentAddress') ? true : false));

      $steps[] = array('title' => OSCOM::getDef('box_ordering_steps_confirmation'),
                       'code' => 'confirmation',
                       'active' => ($OSCOM_Template->getModule() == 'Confirmation' ? true : false));

      $steps[] = array('title' => OSCOM::getDef('box_ordering_steps_complete'),
                       'active' => ($OSCOM_Template->getModule() == 'Success' ? true : false));


      $content = HTML::image('templates/' . $OSCOM_Template->getCode() . '/images/icons/32x32/checkout_preparing_to_ship.gif') . '<br />';

      $counter = 0;

      foreach ( $steps as $step ) {
        $counter++;

        $content .= '<span style="white-space: nowrap;">&nbsp;' . HTML::image('templates/' . $OSCOM_Template->getCode() . '/images/icons/24x24/checkout_' . $counter . ($step['active'] === true ? '_on' : '') . '.gif', $step['title'], 24, 24, 'align="absmiddle"');

        if ( isset($step['code']) ) {
          $content .= HTML::link(OSCOM::getLink(null, 'Checkout', $step['code'], 'SSL'), $step['title'], 'class="boxCheckoutTrail' . ($step['active'] === true ? 'Active' : '') . '"');
        } else {
          $content .= '<span class="boxCheckoutTrail' . ($step['active'] === true ? 'Active' : '') . '">' . $step['title'] . '</span>';
        }

        $content .= '</span><br />';
      }

      $content .= HTML::image('templates/' . $OSCOM_Template->getCode() . '/images/icons/32x32/checkout_ready_to_ship.gif');

      $this->_content = $content;
    }
  }
?>
