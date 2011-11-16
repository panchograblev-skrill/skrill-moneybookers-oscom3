<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Categories\Action;

  use osCommerce\OM\Core\ApplicationAbstract;

/**
 * @since v3.0.2
 */

  class BatchMove {
    public static function execute(ApplicationAbstract $application) {
      $application->setPageContent('batch_move.php');
    }
  }
?>
