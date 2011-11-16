<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\ObjectInfo;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Admin\Application\ZoneGroups\ZoneGroups;

  $zones_array = array();

  foreach ( ObjectInfo::to(ZoneGroups::getAll(-1))->get('entries') as $group ) {
    $zones_array[] = array('id' => $group['geo_zone_id'],
                           'text' => $group['geo_zone_name']);
  }
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . HTML::link(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo HTML::icon('new.png') . ' ' . OSCOM::getDef('action_heading_new_tax_rate'); ?></h3>

  <form name="rNew" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'EntrySave&Process&id=' . $_GET['id']); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_new_tax_rate'); ?></p>

  <fieldset>
    <p><label for="tax_zone_id"><?php echo OSCOM::getDef('field_tax_rate_zone_group'); ?></label><?php echo HTML::selectMenu('tax_zone_id', $zones_array); ?></p>
    <p><label for="tax_rate"><?php echo OSCOM::getDef('field_tax_rate'); ?></label><?php echo HTML::inputField('tax_rate'); ?></p>
    <p><label for="tax_description"><?php echo OSCOM::getDef('field_tax_rate_description'); ?></label><?php echo HTML::inputField('tax_description'); ?></p>
    <p><label for="tax_priority"><?php echo OSCOM::getDef('field_tax_rate_priority'); ?></label><?php echo HTML::inputField('tax_priority'); ?></p>
  </fieldset>

  <p><?php echo HTML::button(array('priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::getDef('button_save'))) . ' ' . HTML::button(array('href' => OSCOM::getLink(null, null, 'id=' . $_GET['id']), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>
