<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Setup\Application\Install;

  use osCommerce\OM\Core\OSCOM;

  class Controller extends \osCommerce\OM\Core\Site\Setup\ApplicationAbstract {
    protected function initialize() {
      $this->_page_contents = 'step_1.php';
      $this->_page_title = OSCOM::getDef('page_title_installation');

      if ( isset($_GET['step']) && is_numeric($_GET['step']) ) {
        switch ( $_GET['step'] ) {
          case '2':
            $this->_page_contents = 'step_2.php';
            break;

          case '3':
            $this->_page_contents = 'step_3.php';
            break;
        }
      }
    }
  }
?>
