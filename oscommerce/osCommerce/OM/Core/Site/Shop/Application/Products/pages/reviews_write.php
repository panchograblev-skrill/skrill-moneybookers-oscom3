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

<div style="clear: both;"></div>

<?php
  if ( $OSCOM_MessageStack->exists('Reviews') ) {
    echo $OSCOM_MessageStack->get('Reviews');
  }
?>

<form name="reviews_write" action="<?php echo OSCOM::getLink(null, null, 'Reviews&Process&' . $OSCOM_Product->getID()); ?>" method="post" onsubmit="return checkForm(this);">

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('new_review_title'); ?></h6>

  <div class="content">
    <ol>

<?php
  if ( $OSCOM_Customer->isLoggedOn() === false ) {
?>

      <li><?php echo HTML::label(ENTRY_NAME, 'customer_name') . HTML::inputField('customer_name'); ?></li>
      <li><?php echo HTML::label(OSCOM::getDef('field_customer_email_address'), 'customer_email_address') . HTML::inputField('customer_email_address'); ?></li>

<?php
  }
?>

      <li><?php echo HTML::textareaField('review', null, null, 15, 'style="width: 98%;"'); ?></li>
      <li><?php echo OSCOM::getDef('field_review_rating') . ' ' . OSCOM::getDef('review_lowest_rating_title') . ' ' . HTML::radioField('rating', array('1', '2', '3', '4', '5')) . ' ' . OSCOM::getDef('review_highest_rating_title'); ?></li>
    </ol>
  </div>
</div>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo HTML::button(array('icon' => 'triangle-1-e', 'title' => OSCOM::getDef('button_continue'))); ?></span>

  <?php echo HTML::button(array('href' => OSCOM::getLink(null, null, 'Reviews&' . $OSCOM_Product->getID()), 'icon' => 'triangle-1-w', 'title' => OSCOM::getDef('button_back'))); ?>
</div>

</form>
