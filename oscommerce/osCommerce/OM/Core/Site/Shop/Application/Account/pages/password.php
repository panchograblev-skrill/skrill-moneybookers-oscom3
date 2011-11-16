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
  if ( $OSCOM_MessageStack->exists('Password') ) {
    echo $OSCOM_MessageStack->get('Password');
  }
?>

<form name="account_password" action="<?php echo OSCOM::getLink(null, null, 'Password&Process', 'SSL'); ?>" method="post" onsubmit="return check_form(account_edit);">

<div class="moduleBox">
  <em style="float: right; margin-top: 10px;"><?php echo OSCOM::getDef('form_required_information'); ?></em>

  <h6><?php echo OSCOM::getDef('my_password_title'); ?></h6>

  <div class="content">
    <ol>
      <li><?php echo HTML::label(OSCOM::getDef('field_customer_password_current'), 'password_current', null, true) . HTML::passwordField('password_current'); ?></li>
      <li><?php echo HTML::label(OSCOM::getDef('field_customer_password_new'), 'password_new', null, true) . HTML::passwordField('password_new'); ?></li>
      <li><?php echo HTML::label(OSCOM::getDef('field_customer_password_confirmation'), 'password_confirmation', null, true) . HTML::passwordField('password_confirmation'); ?></li>
    </ol>
  </div>
</div>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo HTML::button(array('icon' => 'triangle-1-e', 'title' => OSCOM::getDef('button_continue'))); ?></span>

  <?php echo HTML::button(array('href' => OSCOM::getLink(null, null, null, 'SSL'), 'icon' => 'triangle-1-w', 'title' => OSCOM::getDef('button_back'))); ?>
</div>

</form>
