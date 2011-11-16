<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;

  $services = array(array('id' => 'oanda',
                          'text' => 'Oanda (http://www.oanda.com)'),
                    array('id' => 'xe',
                          'text' => 'XE (http://www.xe.com)'));
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . HTML::link(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo HTML::icon('update.png') . ' ' . OSCOM::getDef('action_heading_update_rates'); ?></h3>

  <form name="cUpdate" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'UpdateRates&Process'); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_update_exchange_rates'); ?></p>

  <fieldset>
    <p><?php echo HTML::radioField('service', $services, null, null, '<br />'); ?></p>
  </fieldset>

  <p><?php echo OSCOM::getDef('service_terms_agreement'); ?></p>

  <p><?php echo HTML::button(array('priority' => 'primary', 'icon' => 'refresh', 'title' => OSCOM::getDef('button_update'))) . ' ' . HTML::button(array('href' => OSCOM::getLink(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>
