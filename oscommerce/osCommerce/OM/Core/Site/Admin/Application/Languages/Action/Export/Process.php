<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Languages\Action\Export;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Site\Admin\Application\Languages\Languages;

  class Process {
    public static function execute(ApplicationAbstract $application) {
      $data = array('id' => $_GET['id'],
                    'groups' => $_POST['groups'],
                    'include_data' => (isset($_POST['include_data']) && ($_POST['include_data'] == 'on')));

      Languages::export($data);
    }
  }
?>
