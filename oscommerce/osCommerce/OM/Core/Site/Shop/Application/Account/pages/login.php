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
  if ( $OSCOM_MessageStack->exists('LogIn') ) {
    echo $OSCOM_MessageStack->get('LogIn');
  }
?>

<div class="moduleBox" style="width: 49%; float: right;">
  <form name="login" action="<?php echo OSCOM::getLink(null, null, 'LogIn&Process', 'SSL'); ?>" method="post">

  <h6><?php echo OSCOM::getDef('login_returning_customer_heading'); ?></h6>

  <div class="content">
    <p><?php echo OSCOM::getDef('login_returning_customer_text'); ?></p>

    <ol>
      <li><?php echo HTML::label(OSCOM::getDef('field_customer_email_address'), 'email_address') . HTML::inputField('email_address'); ?></li>
      <li><?php echo HTML::label(OSCOM::getDef('field_customer_password'), 'password') . HTML::passwordField('password'); ?></li>
    </ol>

    <p><?php echo sprintf(OSCOM::getDef('login_returning_customer_password_forgotten'), OSCOM::getLink(null, null, 'PasswordForgotten', 'SSL')); ?></p>

    <p align="right"><?php echo HTML::button(array('icon' => 'key', 'title' => OSCOM::getDef('button_sign_in'))); ?></p>
  </div>

  </form>
</div>

<div class="moduleBox" style="width: 49%;">
  <div class="outsideHeading">
    <h6><?php echo OSCOM::getDef('login_new_customer_heading'); ?></h6>
  </div>

  <div class="content">
    <p><?php echo OSCOM::getDef('login_new_customer_text'); ?></p>

    <p align="right"><?php echo HTML::button(array('href' => OSCOM::getLink(null, null, 'Create', 'SSL'), 'icon' => 'triangle-1-e', 'title' => OSCOM::getDef('button_continue'))); ?></p>
  </div>
</div>
