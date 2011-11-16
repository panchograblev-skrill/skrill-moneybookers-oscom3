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

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo HTML::icon('new.png') . ' ' . OSCOM::getDef('action_heading_new_currency'); ?></h3>

  <form name="cNew" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'Save&Process'); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_new_currency'); ?></p>

  <fieldset>
    <p><label for="title"><?php echo OSCOM::getDef('field_title'); ?></label><?php echo HTML::inputField('title'); ?></p>
    <p><label for="code"><?php echo OSCOM::getDef('field_code'); ?></label><?php echo HTML::inputField('code'); ?></p>
    <p><label for="symbol_left"><?php echo OSCOM::getDef('field_symbol_left'); ?></label><?php echo HTML::inputField('symbol_left'); ?></p>
    <p><label for="symbol_right"><?php echo OSCOM::getDef('field_symbol_right'); ?></label><?php echo HTML::inputField('symbol_right'); ?></p>
    <p><label for="decimal_places"><?php echo OSCOM::getDef('field_decimal_places'); ?></label><?php echo HTML::inputField('decimal_places'); ?></p>
    <p><label for="value"><?php echo OSCOM::getDef('field_currency_value'); ?></label><?php echo HTML::inputField('value'); ?></p>
    <p><label for="default"><?php echo OSCOM::getDef('field_set_default'); ?></label><?php echo HTML::checkboxField('default'); ?></p>
  </fieldset>

  <p><?php echo HTML::button(array('priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::getDef('button_save'))) . ' ' . HTML::button(array('href' => OSCOM::getLink(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>
