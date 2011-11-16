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

<div style="float: right;"><?php echo HTML::link(OSCOM::getLink(null, null, $OSCOM_Product->getKeyword()), $OSCOM_Image->show($OSCOM_Product->getImage(), $OSCOM_Product->getTitle(), 'hspace="5" vspace="5"', 'mini')); ?></div>

<h1><?php echo $OSCOM_Template->getPageTitle() . ($OSCOM_Product->hasModel() ? '<br /><span class="smallText">' . $OSCOM_Product->getModel() . '</span>' : ''); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists('TellAFriend') ) {
    echo $OSCOM_MessageStack->get('TellAFriend');
  }
?>

<form name="tell_a_friend" action="<?php echo OSCOM::getLink(null, null, 'TellAFriend&Process&' . $OSCOM_Product->getKeyword()); ?>" method="post">

<div class="moduleBox">
  <em style="float: right; margin-top: 10px;"><?php echo OSCOM::getDef('form_required_information'); ?></em>

  <h6><?php echo OSCOM::getDef('customer_details_title'); ?></h6>

  <div class="content">
    <ol>
      <li><?php echo HTML::label(OSCOM::getDef('field_tell_a_friend_customer_name'), 'from_name', null, true) . HTML::inputField('from_name', ($OSCOM_Customer->isLoggedOn() ? $OSCOM_Customer->getName() : null)); ?></li>
      <li><?php echo HTML::label(OSCOM::getDef('field_tell_a_friend_customer_email_address'), 'from_email_address', null, true) . HTML::inputField('from_email_address', ($OSCOM_Customer->isLoggedOn() ? $OSCOM_Customer->getEmailAddress() : null)); ?></li>
    </ol>
  </div>
</div>

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('friend_details_title'); ?></h6>

  <div class="content">
    <ol>
      <li><?php echo HTML::label(OSCOM::getDef('field_tell_a_friend_friends_name'), 'to_name', null, true) . HTML::inputField('to_name'); ?></li>
      <li><?php echo HTML::label(OSCOM::getDef('field_tell_a_friend_friends_email_address'), 'to_email_address', null, true) . HTML::inputField('to_email_address'); ?></li>
    </ol>
  </div>
</div>

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('tell_a_friend_message'); ?></h6>

  <div class="content">
    <ol>
      <li><?php echo HTML::textareaField('message', null, 40, 8, 'style="width: 98%;"'); ?></li>
    </ol>
  </div>
</div>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo HTML::button(array('icon' => 'triangle-1-e', 'title' => OSCOM::getDef('button_continue'))); ?></span>

  <?php echo HTML::button(array('href' => OSCOM::getLink(null, null, $OSCOM_Product->getKeyword()), 'icon' => 'triangle-1-w', 'title' => OSCOM::getDef('button_back'))); ?>
</div>

</form>
