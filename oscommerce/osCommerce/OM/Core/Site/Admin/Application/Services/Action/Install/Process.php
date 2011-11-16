<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Services\Action\Install;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Admin\Application\Services\Services;

/**
 * @since v3.0.2
 */

  class Process {
    public static function execute(ApplicationAbstract $application) {
      $data = HTML::sanitize(basename($_GET['code']));

      if ( Services::install($data) ) {
        $class = 'osCommerce\\OM\\Core\\Site\\Admin\\Module\\Service\\' . $data;
        $OSCOM_SM = new $class();

        if ( $OSCOM_SM->hasKeys() ) {
          OSCOM::redirect(OSCOM::getLink(null, null, 'Save&code=' . $data));
        } else {
          Registry::get('MessageStack')->add(null, OSCOM::getDef('ms_success_action_performed'), 'success');

          OSCOM::redirect(OSCOM::getLink());
        }
      } else {
        Registry::get('MessageStack')->add(null, OSCOM::getDef('ms_error_action_not_performed'), 'error');

        OSCOM::redirect(OSCOM::getLink());
      }
    }
  }
?>
