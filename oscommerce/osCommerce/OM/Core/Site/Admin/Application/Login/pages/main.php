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

<h1><?php echo $OSCOM_Template->getIcon(32) . HTML::link(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<div class="infoBox">
  <h3><?php echo HTML::icon('people.png') . ' ' . OSCOM::getDef('action_heading_login'); ?></h3>

  <form id="formLogin" name="login" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'Process'); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction'); ?></p>

  <fieldset>
    <p><label for="user_name"><?php echo OSCOM::getDef('field_username'); ?></label><?php echo HTML::inputField('user_name', null, 'tabindex="1"'); ?></p>
    <p><label for="user_password"><?php echo OSCOM::getDef('field_password'); ?></label><?php echo HTML::passwordField('user_password', 'tabindex="2"'); ?></p>
  </fieldset>

  <p><?php echo HTML::button(array('icon' => 'key', 'title' => OSCOM::getDef('button_login'))); ?></p>

  </form>
</div>

<script type="text/javascript">
  $('#user_name').focus();

  if (typeof webkitNotifications != 'undefined') {
    $('#formLogin').submit(function() {
      if ( webkitNotifications.checkPermission() == 1 ) {
        webkitNotifications.requestPermission();
      }
    });
  }
</script>

<?php
  if ( isset($_GET['Process']) && !empty($_POST['user_name']) && !empty($_POST['user_password']) ) {
?>

<script type="text/javascript" src="public/external/jquery/jquery.showPasswordCheckbox.js"></script>
<script type="text/javascript">
  var showPasswordText = '<?php echo addslashes(OSCOM::getDef('field_show_password')); ?>';
  $("#user_password").showPasswordCheckbox();
</script>

<?php
  }
?>
