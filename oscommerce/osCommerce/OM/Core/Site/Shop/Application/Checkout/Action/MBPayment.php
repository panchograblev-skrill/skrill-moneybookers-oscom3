<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Application\Checkout\Action;

  use osCommerce\OM\Core\ApplicationAbstract;

  /**
   * @author Pancho Grablev <pancho.grablev@skrill.com>
   */
  class MBPayment {
    public static function execute(ApplicationAbstract $application) {

      $application->setPageContent('mb_payment.php');
    }
  }
?>
