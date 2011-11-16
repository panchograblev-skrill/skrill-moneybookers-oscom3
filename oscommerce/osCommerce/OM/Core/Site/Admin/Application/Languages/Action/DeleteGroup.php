<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Languages\Action;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Site\Admin\Application\Languages\Languages;

  class DeleteGroup {
    public static function execute(ApplicationAbstract $application) {
      if ( Languages::isGroup($_GET['id'], $_GET['group']) ) {
        $application->setPageContent('groups_delete.php');
      }
    }
  }
?>
