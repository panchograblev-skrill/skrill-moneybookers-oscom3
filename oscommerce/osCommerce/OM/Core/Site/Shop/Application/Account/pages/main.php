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
  if ( $OSCOM_MessageStack->exists('Account') ) {
    echo $OSCOM_MessageStack->get('Account');
  }
?>

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('my_account_title'); ?></h6>

  <div class="content">
    <ul style="padding-left: 50px;">
      <li><?php echo HTML::link(OSCOM::getLink(null, null, 'Edit', 'SSL'), OSCOM::getDef('my_account_information')); ?></li>
      <li><?php echo HTML::link(OSCOM::getLink(null, null, 'AddressBook', 'SSL'), OSCOM::getDef('my_account_address_book')); ?></li>
      <li><?php echo HTML::link(OSCOM::getLink(null, null, 'Password', 'SSL'), OSCOM::getDef('my_account_password')); ?></li>
    </ul>

    <div style="clear: both;"></div>
  </div>
</div>

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('my_orders_title'); ?></h6>

  <div class="content">
    <ul style="padding-left: 50px;">
      <li><?php echo HTML::link(OSCOM::getLink(null, null, 'Orders', 'SSL'), OSCOM::getDef('my_orders_view')); ?></li>
    </ul>

    <div style="clear: both;"></div>
  </div>
</div>

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('my_notifications_title'); ?></h6>

  <div class="content">
    <ul style="padding-left: 50px;">
      <li><?php echo HTML::link(OSCOM::getLink(null, null, 'Newsletters', 'SSL'), OSCOM::getDef('my_notifications_newsletters')); ?></li>
      <li><?php echo HTML::link(OSCOM::getLink(null, null, 'Notifications', 'SSL'), OSCOM::getDef('my_notifications_products')); ?></li>
    </ul>

    <div style="clear: both;"></div>
  </div>
</div>
