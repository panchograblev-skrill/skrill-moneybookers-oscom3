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
  if ( $OSCOM_MessageStack->exists('Create') ) {
    echo $OSCOM_MessageStack->get('Create');
  }
?>

<form name="create" action="<?php echo OSCOM::getLink(null, null, 'Create&Process', 'SSL'); ?>" method="post" onsubmit="return check_form(create);">

<div class="moduleBox">
  <em style="float: right; margin-top: 10px;"><?php echo OSCOM::getDef('form_required_information'); ?></em>

  <h6><?php echo OSCOM::getDef('my_account_title'); ?></h6>

  <div class="content">
    <ol>

<?php
  if ( ACCOUNT_GENDER > -1 ) {
    $gender_array = array(array('id' => 'm', 'text' => OSCOM::getDef('gender_male')),
                          array('id' => 'f', 'text' => OSCOM::getDef('gender_female')));
?>

      <li><?php echo HTML::label(OSCOM::getDef('field_customer_gender'), 'gender_1', null, (ACCOUNT_GENDER > 0)) . HTML::radioField('gender', $gender_array); ?></li>

<?php
  }
?>

      <li><?php echo HTML::label(OSCOM::getDef('field_customer_first_name'), 'firstname', null, true) . HTML::inputField('firstname'); ?></li>
      <li><?php echo HTML::label(OSCOM::getDef('field_customer_last_name'), 'lastname', null, true) . HTML::inputField('lastname'); ?></li>

<?php
  if ( ACCOUNT_DATE_OF_BIRTH == '1' ) {
?>

      <li><?php echo HTML::label(OSCOM::getDef('field_customer_date_of_birth'), 'dob_days', null, true) . HTML::dateSelectMenu('dob', null, false, null, null, date('Y')-1901, -5); ?></li>

<?php
  }
?>

      <li><?php echo HTML::label(OSCOM::getDef('field_customer_email_address'), 'email_address', null, true) . HTML::inputField('email_address'); ?></li>

<?php
  if ( ACCOUNT_NEWSLETTER == '1' ) {
?>

      <li><?php echo HTML::label(OSCOM::getDef('field_customer_newsletter'), 'newsletter') . HTML::checkboxField('newsletter', '1'); ?></li>

<?php
  }
?>

      <li><?php echo HTML::label(OSCOM::getDef('field_customer_password'), 'password', null, true) . HTML::passwordField('password'); ?></li>
      <li><?php echo HTML::label(OSCOM::getDef('field_customer_password_confirmation'), 'confirmation', null, true) . HTML::passwordField('confirmation'); ?></li>
    </ol>
  </div>
</div>

<?php
  if ( DISPLAY_PRIVACY_CONDITIONS == '1' ) {
?>

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('create_account_terms_heading'); ?></h6>

  <div class="content">
    <?php echo sprintf(OSCOM::getDef('create_account_terms_description'), OSCOM::getLink(null, 'Info', 'Privacy', 'AUTO')) . '<br /><br /><ol><li>' . HTML::checkboxField('privacy_conditions', array(array('id' => 1, 'text' => OSCOM::getDef('create_account_terms_confirm')))) . '</li></ol>'; ?>
  </div>
</div>

<?php
  }
?>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo HTML::button(array('icon' => 'triangle-1-e', 'title' => OSCOM::getDef('button_continue'))); ?></span>
</div>

</form>
