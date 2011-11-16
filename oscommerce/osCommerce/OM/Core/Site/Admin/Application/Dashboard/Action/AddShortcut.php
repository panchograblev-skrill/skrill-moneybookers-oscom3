<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Dashboard\Action;

  use osCommerce\OM\Core\Access;
  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Admin\Application\Dashboard\Dashboard;

  class AddShortcut {
    public static function execute(ApplicationAbstract $application) {
      if ( !empty($_GET['shortcut']) ) {
        $application = HTML::sanitize($_GET['shortcut']);

        if ( OSCOM::siteApplicationExists($application) ) {
          if ( Dashboard::saveShortcut($_SESSION[OSCOM::getSite()]['id'], $application) ) {
            $_SESSION[OSCOM::getSite()]['access'] = Access::getUserLevels($_SESSION[OSCOM::getSite()]['id']);

            Registry::get('MessageStack')->add('header', OSCOM::getDef('ms_success_shortcut_added'), 'success');

            OSCOM::redirect(OSCOM::getLink(null, $application));
          }
        }
      }

      OSCOM::redirect(OSCOM::getLink());
    }
  }
?>
