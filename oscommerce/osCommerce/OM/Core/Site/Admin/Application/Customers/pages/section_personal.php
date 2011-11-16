<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\DateTime;
  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
?>

<div id="sectionMenu_personal">
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
  if ( ACCOUNT_GENDER > -1 ) {
?>

      <p id="genderFields"><label for="gender"><?php echo OSCOM::getDef('field_gender'); ?></label><?php echo HTML::radioField('gender', $gender_array, ($new_customer ? 'm' : $OSCOM_ObjectInfo->get('customers_gender')), null, ''); ?></p>

      <script>$('#genderFields').buttonset();</script>

<?php
  }
?>

      <p><label for="firstname"><?php echo OSCOM::getDef('field_first_name'); ?></label><?php echo HTML::inputField('firstname', ($new_customer ? null : $OSCOM_ObjectInfo->get('customers_firstname'))); ?></p>
      <p><label for="lastname"><?php echo OSCOM::getDef('field_last_name'); ?></label><?php echo HTML::inputField('lastname', ($new_customer ? null : $OSCOM_ObjectInfo->get('customers_lastname'))); ?></p>

<?php
  if ( ACCOUNT_DATE_OF_BIRTH == '1' ) {
?>

      <p><label for="dob"><?php echo OSCOM::getDef('field_date_of_birth'); ?></label><?php echo HTML::inputField('dob', ($new_customer ? null : DateTime::fromUnixTimestamp(DateTime::getTimestamp($OSCOM_ObjectInfo->get('customers_dob')), 'Y-m-d'))); ?></p>

      <script>$('#dob').datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true, yearRange: '-100:+0'});</script>

<?php
  }
?>

      <p><label for="email_address"><?php echo OSCOM::getDef('field_email_address'); ?></label><?php echo HTML::inputField('email_address', ($new_customer ? null : $OSCOM_ObjectInfo->get('customers_email_address'))); ?></p>
      <p><label for="status"><?php echo OSCOM::getDef('field_status'); ?></label><?php echo HTML::checkboxField('status', null, ($new_customer ? true : ($OSCOM_ObjectInfo->get('customers_status') == '1'))); ?></p>
    </fieldset>
  </div>
</div>
