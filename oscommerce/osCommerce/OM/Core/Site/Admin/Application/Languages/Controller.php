<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Languages;

  use osCommerce\OM\Core\OSCOM;

  class Controller extends \osCommerce\OM\Core\Site\Admin\ApplicationAbstract {
    protected $_group = 'configuration';
    protected $_icon = 'languages.png';
    protected $_sort_order = 400;

    protected function initialize() {
      $this->_title = OSCOM::getDef('app_title');
    }

    protected function process() {
      $this->_page_title = OSCOM::getDef('heading_title');

      if ( isset($_GET['id']) && is_numeric($_GET['id']) && Languages::exists($_GET['id']) ) {
        $this->_page_contents = 'groups.php';
        $this->_page_title .= ': ' . Languages::get($_GET['id'], 'name');

        if ( isset($_GET['group']) && Languages::isGroup($_GET['id'], $_GET['group']) ) {
          $this->_page_contents = 'definitions.php';
          $this->_page_title .= ': ' . $_GET['group'];
        }
      }
    }
  }
?>
