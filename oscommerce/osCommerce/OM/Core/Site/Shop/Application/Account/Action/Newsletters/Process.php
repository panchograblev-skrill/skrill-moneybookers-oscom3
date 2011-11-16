<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Application\Account\Action\Newsletters;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;

  class Process {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_MessageStack = Registry::get('MessageStack');

      if ( isset($_POST['newsletter_general']) && is_numeric($_POST['newsletter_general']) ) {
        $newsletter_general = (int)$_POST['newsletter_general'];
      } else {
        $newsletter_general = 0;
      }

// HPDL Should be moved to the customers class!
      $Qnewsletter = $OSCOM_PDO->prepare('select customers_newsletter from :table_customers where customers_id = :customers_id');
      $Qnewsletter->bindInt(':customers_id', $OSCOM_Customer->getID());
      $Qnewsletter->execute();

      if ( $newsletter_general !== $Qnewsletter->valueInt('customers_newsletter') ) {
        $newsletter_general = (($Qnewsletter->value('customers_newsletter') == '1') ? '0' : '1');

        $Qupdate = $OSCOM_PDO->prepare('update :table_customers set customers_newsletter = :customers_newsletter where customers_id = :customers_id');
        $Qupdate->bindInt(':customers_newsletter', $newsletter_general);
        $Qupdate->bindInt(':customers_id', $OSCOM_Customer->getID());
        $Qupdate->execute();

        if ( $Qupdate->rowCount() === 1 ) {
          $OSCOM_MessageStack->add('Account', OSCOM::getDef('success_newsletter_updated'), 'success');
        }
      }

      OSCOM::redirect(OSCOM::getLink(null, null, null, 'SSL'));
    }
  }
?>
