<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Currencies\Action;

  use osCommerce\OM\Core\ApplicationAbstract;

  class UpdateRates {
    public static function execute(ApplicationAbstract $application) {
      $application->setPageContent('update_rates.php');
    }
  }
?>
