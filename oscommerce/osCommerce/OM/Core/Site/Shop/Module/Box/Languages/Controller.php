<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Module\Box\Languages;

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  class Controller extends \osCommerce\OM\Core\Modules {
    var $_title,
        $_code = 'Languages',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'Box';

    public function __construct() {
      $this->_title = OSCOM::getDef('box_languages_heading');
    }

    function initialize() {
      $OSCOM_Language = Registry::get('Language');

      $this->_content = '';

      $get_params = array();

      foreach ( $_GET as $key => $value ) {
        if ( ($key != 'language') && ($key != Registry::get('Session')->getName()) && ($key != 'x') && ($key != 'y') ) {
          $get_params[] = $key . '=' . $value;
        }
      }

      $get_params = implode($get_params, '&');

      if ( !empty($get_params) ) {
        $get_params .= '&';
      }

      foreach ( $OSCOM_Language->getAll() as $value ) {
        $this->_content .= ' ' . HTML::link(OSCOM::getLink(null, null, $get_params . 'language=' . $value['code'], 'AUTO'), $OSCOM_Language->showImage($value['code'])) . ' ';
      }
    }
  }
?>
