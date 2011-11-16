<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Module\Box\Currencies;

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  class Controller extends \osCommerce\OM\Core\Modules {
    var $_title,
        $_code = 'Currencies',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'Box';

    public function __construct() {
      $this->_title = OSCOM::getDef('box_currencies_heading');
    }

    public function initialize() {
      $OSCOM_Currencies = Registry::get('Currencies');

      $data = array();

      foreach ( $OSCOM_Currencies->getData() as $key => $value ) {
        $data[] = array('id' => $key,
                        'text' => $value['title']);
      }

      if ( count($data) > 1 ) {
        $hidden_get_params = '';

        foreach ( $_GET as $key => $value ) {
          if ( ($key != 'currency') && ($key != Registry::get('Session')->getName()) && ($key != 'x') && ($key != 'y') ) {
            $hidden_get_params .= HTML::hiddenField($key, $value);
          }
        }

        $this->_content = '<form name="currencies" action="' . OSCOM::getLink(null, null, null, 'AUTO', false) . '" method="get">' .
                          $hidden_get_params .
                          HTML::selectMenu('currency', $data, $_SESSION['currency'], 'onchange="this.form.submit();" style="width: 100%"') .
                          HTML::hiddenSessionIDField() .
                          '</form>';
      }
    }
  }
?>
