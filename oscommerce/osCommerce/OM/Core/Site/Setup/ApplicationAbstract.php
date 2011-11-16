<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Setup;

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;

  abstract class ApplicationAbstract extends \osCommerce\OM\Core\ApplicationAbstract {
    public function __construct() {
      $this->initialize();

      if ( isset($_GET['action']) && !empty($_GET['action']) ) {
        $action = HTML::sanitize(basename($_GET['action']));

        if ( class_exists('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Application\\' . OSCOM::getSiteApplication() . '\\Action\\' . $action) ) {
          call_user_func(array('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Application\\' . OSCOM::getSiteApplication() . '\\Action\\' . $action, 'execute'), $this);
        }
      }
    }
  }
?>
