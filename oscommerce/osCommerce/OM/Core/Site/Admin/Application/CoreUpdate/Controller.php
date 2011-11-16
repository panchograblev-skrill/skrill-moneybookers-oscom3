<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\CoreUpdate;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Admin\Application\CoreUpdate\CoreUpdate;

  class Controller extends \osCommerce\OM\Core\Site\Admin\ApplicationAbstract {
    protected $_group = 'tools';
    protected $_icon = 'coreupdate.png';
    protected $_sort_order = 5;

    protected function initialize() {
      $this->_title = OSCOM::getDef('app_title');
    }

    protected function process() {
      $this->_page_title = OSCOM::getDef('heading_title');
    }

/**
 * @since v3.0.2
 */

    public function getLogList() {
      $array = array(array('id' => '',
                           'text' => OSCOM::getDef('select_log_to_view'),
                           'params' => 'disabled="disabled"'));

      foreach ( CoreUpdate::getLogs() as $f ) {
        $array[] = array('id' => substr($f, 0, -4),
                         'text' => substr($f, 0, -4));
      }

      return $array;
    }
  }
?>
