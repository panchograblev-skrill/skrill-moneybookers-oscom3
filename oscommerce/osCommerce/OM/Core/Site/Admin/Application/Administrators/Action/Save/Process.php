<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Administrators\Action\Save;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Site\Admin\Application\Administrators\Administrators;
  use osCommerce\OM\Core\Access;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;

  class Process {
    public static function execute(ApplicationAbstract $application) {
      $data = array('id' => (isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : null),
                    'username' => $_POST['user_name'],
                    'password' => $_POST['user_password'],
                    'modules' => (isset($_POST['modules']) ? $_POST['modules'] : null));

      switch ( Administrators::save($data) ) {
        case 1:
          if ( isset($_GET['id']) && is_numeric($_GET['id']) && ($_GET['id'] == $_SESSION[OSCOM::getSite()]['id']) ) {
            $_SESSION[OSCOM::getSite()]['access'] = Access::getUserLevels($_GET['id']);
          }

          Registry::get('MessageStack')->add(null, OSCOM::getDef('ms_success_action_performed'), 'success');

          OSCOM::redirect(OSCOM::getLink());

          break;

        case -1:
          Registry::get('MessageStack')->add(null, OSCOM::getDef('ms_error_action_not_performed'), 'error');

          OSCOM::redirect(OSCOM::getLink());

          break;

        case -2:
          Registry::get('MessageStack')->add(null, OSCOM::getDef('ms_error_username_already_exists'), 'error');

          break;
      }
    }
  }
?>
