<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<?php
  if ( $OSCOM_Customer->isLoggedOn() ) {
    echo '<p>' . sprintf(OSCOM::getDef('greeting_customer'), HTML::outputProtected($OSCOM_Customer->getFirstName()), OSCOM::getLink(null, 'Products', 'New')) . '</p>';
  } else {
    echo '<p>' . sprintf(OSCOM::getDef('greeting_guest'), OSCOM::getLink(null, 'Account', 'Login', 'SSL'), OSCOM::getLink(null, 'Products', 'New')) . '</p>';
  }
?>

<p><?php echo OSCOM::getDef('index_text'); ?></p>
