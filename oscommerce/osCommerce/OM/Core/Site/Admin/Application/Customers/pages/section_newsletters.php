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

<div id="sectionMenu_newsletters">
  <div class="infoBox">

<?php
  if ( $new_customer ) {
    echo '<h3>' . HTML::icon('new.png') . ' ' . OSCOM::getDef('action_heading_new_customer') . '</h3>';
  } else {
    echo '<h3>' . HTML::icon('edit.png') . ' ' . $OSCOM_ObjectInfo->getProtected('customers_name') . '</h3>';
  }
?>

    <fieldset>

<?php
  if ( ACCOUNT_NEWSLETTER == '1' ) {
?>

      <p><label for="newsletter"><?php echo OSCOM::getDef('field_newsletter_subscription'); ?></label><?php echo HTML::checkboxField('newsletter', null, ($new_customer ? null : ($OSCOM_ObjectInfo->get('customers_newsletter') == '1'))); ?></p>

<?php
  }
?>

    </fieldset>
  </div>
</div>
