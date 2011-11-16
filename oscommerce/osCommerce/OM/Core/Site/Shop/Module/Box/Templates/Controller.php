<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Module\Box\Templates;

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Template;

  class Controller extends \osCommerce\OM\Core\Modules {
    var $_title,
        $_code = 'Templates',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'Box';

    public function __construct() {
      $this->_title = OSCOM::getDef('box_templates_heading');
    }

    public function initialize() {
      $OSCOM_Session = Registry::get('Session');

      $data = array();

      foreach ( Template::getTemplates() as $template ) {
        $data[] = array('id' => $template['code'],
                        'text' => $template['title']);
      }

      if ( count($data) > 1 ) {
        $hidden_get_params = '';

        foreach ( $_GET as $key => $value ) {
          if ( ($key != 'template') && ($key != $OSCOM_Session->getName()) && ($key != 'x') && ($key != 'y') ) {
            $hidden_get_params .= HTML::hiddenField($key, $value);
          }
        }

        $this->_content = '<form name="templates" action="' . OSCOM::getLink(null, null, null, 'AUTO', false) . '" method="get">' .
                          $hidden_get_params . HTML::selectMenu('template', $data, $_SESSION['template']['code'], 'onchange="this.form.submit();" style="width: 100%"') . HTML::hiddenSessionIDField() .
                          '</form>';
      }
    }
  }
?>
