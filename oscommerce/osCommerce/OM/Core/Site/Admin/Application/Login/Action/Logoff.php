<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Login\Action;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;

  class Logoff {
    public static function execute(ApplicationAbstract $application) {
      unset($_SESSION[OSCOM::getSite()]);

      Registry::get('MessageStack')->add('header', OSCOM::getDef('ms_success_logged_out'), 'success');

      OSCOM::redirect(OSCOM::getLink(null, OSCOM::getDefaultSiteApplication()));
    }
  }
?>
