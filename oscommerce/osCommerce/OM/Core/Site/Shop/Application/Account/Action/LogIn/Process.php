<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Application\Account\Action\LogIn;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Shop\Account;
  use osCommerce\OM\Core\OSCOM;

  class Process {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_NavigationHistory = Registry::get('NavigationHistory');
      $OSCOM_MessageStack = Registry::get('MessageStack');

      if ( !empty($_POST['email_address']) && !empty($_POST['password']) && Account::logIn($_POST['email_address'], $_POST['password']) ) {
        $OSCOM_NavigationHistory->removeCurrentPage();

        if ( $OSCOM_NavigationHistory->hasSnapshot() ) {
          $OSCOM_NavigationHistory->redirectToSnapshot();
        } else {
          OSCOM::redirect(OSCOM::getLink(null, OSCOM::getDefaultSiteApplication(), null, 'AUTO'));
        }
      }

      $OSCOM_MessageStack->add('LogIn', OSCOM::getDef('error_login_no_match'));
    }
  }
?>
