<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Module\Box\ShoppingCart;

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  class Controller extends \osCommerce\OM\Core\Modules {
    var $_title,
        $_code = 'ShoppingCart',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'Box';

    public function __construct() {
      $this->_title = OSCOM::getDef('box_shopping_cart_heading');
    }

    public function initialize() {
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');
      $OSCOM_Currencies = Registry::get('Currencies');

      $this->_title_link = OSCOM::getLink(null, 'Checkout', null, 'SSL');

      if ( $OSCOM_ShoppingCart->hasContents() ) {
        $this->_content = '<table border="0" width="100%" cellspacing="0" cellpadding="0">';

        foreach ( $OSCOM_ShoppingCart->getProducts() as $products ) {
          $this->_content .= '  <tr>' .
                             '    <td align="right" valign="top">' . $products['quantity'] . '&nbsp;x&nbsp;</td>' .
                             '    <td valign="top">' . HTML::link(OSCOM::getLink(null, 'Products', $products['keyword']), $products['name']) . '</td>' .
                             '  </tr>';
        }

        $this->_content .= '</table>' .
                           '<p style="text-align: right">' . OSCOM::getDef('box_shopping_cart_subtotal') . ' ' . $OSCOM_Currencies->format($OSCOM_ShoppingCart->getSubTotal()) . '</p>';
      } else {
        $this->_content = OSCOM::getDef('box_shopping_cart_empty');
      }
    }
  }
?>
