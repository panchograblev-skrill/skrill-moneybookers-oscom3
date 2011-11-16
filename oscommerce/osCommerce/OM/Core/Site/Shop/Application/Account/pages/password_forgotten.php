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
  if ( $OSCOM_MessageStack->exists('PasswordForgotten') ) {
    echo $OSCOM_MessageStack->get('PasswordForgotten');
  }
?>

<form name="password_forgotten" action="<?php echo OSCOM::getLink(null, null, 'PasswordForgotten&Process', 'SSL'); ?>" method="post" onsubmit="return check_form(password_forgotten);">

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('password_forgotten_heading'); ?></h6>

  <div class="content">
    <p><?php echo OSCOM::getDef('password_forgotten'); ?></p>

    <ol>
      <li><?php echo HTML::label(OSCOM::getDef('field_customer_email_address'), 'email_address') . HTML::inputField('email_address'); ?></li>
    </ol>
  </div>
</div>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo HTML::button(array('icon' => 'triangle-1-e', 'title' => OSCOM::getDef('button_continue'))); ?></span>

  <?php echo HTML::button(array('href' => OSCOM::getLink(null, null, null, 'SSL'), 'icon' => 'triangle-1-w', 'title' => OSCOM::getDef('button_back'))); ?>
</div>

</form>
