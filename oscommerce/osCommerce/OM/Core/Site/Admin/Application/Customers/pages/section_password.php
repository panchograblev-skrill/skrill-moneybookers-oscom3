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

<div id="sectionMenu_password">
  <div class="infoBox">

<?php
  if ( $new_customer ) {
    echo '<h3>' . HTML::icon('new.png') . ' ' . OSCOM::getDef('action_heading_new_customer') . '</h3>';
  } else {
    echo '<h3>' . HTML::icon('edit.png') . ' ' . $OSCOM_ObjectInfo->getProtected('customers_name') . '</h3>';
  }
?>

    <fieldset>
      <p><label for="password"><?php echo OSCOM::getDef('field_new_password'); ?></label><?php echo HTML::passwordField('password'); ?></p>
      <p><label for="confirmation"><?php echo OSCOM::getDef('field_new_password_confirmation'); ?></label><?php echo HTML::passwordField('confirmation'); ?></p>
    </fieldset>
  </div>
</div>
