<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Login\Action;

  use osCommerce\OM\Core\Access;
  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Admin\Application\Login\Login;

  class Process {
    public static function execute(ApplicationAbstract $application) {
      $data = array('username' => $_POST['user_name'],
                    'password' => $_POST['user_password']);

      if ( Login::isValidCredentials($data) ) {
        Registry::get('Session')->recreate();

        $admin = Login::getAdmin($data['username']);

        $_SESSION[OSCOM::getSite()]['id'] = (int)$admin['id'];
        $_SESSION[OSCOM::getSite()]['username'] = $admin['user_name'];
        $_SESSION[OSCOM::getSite()]['access'] = Access::getUserLevels($admin['id']);

        $to_application = OSCOM::getDefaultSiteApplication();

        if ( isset($_SESSION[OSCOM::getSite()]['redirect_origin']) ) {
          $to_application = $_SESSION[OSCOM::getSite()]['redirect_origin'];

          unset($_SESSION[OSCOM::getSite()]['redirect_origin']);
        }

        OSCOM::redirect(OSCOM::getLink(null, $to_application));
      } else {
        Registry::get('MessageStack')->add('header', OSCOM::getDef('ms_error_login_invalid'), 'error');
      }
    }
  }
?>
