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

  $OSCOM_ObjectInfo = new ObjectInfo(ZoneGroups::get($_GET['id']));
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . HTML::link(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo HTML::icon('trash.png') . ' ' . $OSCOM_ObjectInfo->getProtected('geo_zone_name'); ?></h3>

  <form name="zDelete" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'Delete&Process&id=' . $OSCOM_ObjectInfo->getInt('geo_zone_id')); ?>" method="post">

<?php
  if ( ZoneGroups::hasTaxRates($OSCOM_ObjectInfo->getInt('geo_zone_id')) ) {
?>

  <p><?php echo '<b>' . sprintf(OSCOM::getDef('delete_warning_group_in_use_tax_rate'), ZoneGroups::getNumberOfTaxRates($OSCOM_ObjectInfo->getInt('geo_zone_id'))) . '</b>'; ?></p>

  <p><?php echo HTML::button(array('href' => OSCOM::getLink(), 'icon' => 'triangle-1-w', 'title' => OSCOM::getDef('button_back'))); ?></p>

<?php
  } else {
?>

  <p><?php echo OSCOM::getDef('introduction_delete_zone_group'); ?></p>

  <p><?php echo '<b>' . $OSCOM_ObjectInfo->getProtected('geo_zone_name') . ' (' . sprintf(OSCOM::getDef('total_entries'), $OSCOM_ObjectInfo->getInt('total_entries')) . ')</b>'; ?></p>

  <p><?php echo HTML::button(array('priority' => 'primary', 'icon' => 'trash', 'title' => OSCOM::getDef('button_delete'))) . ' ' . HTML::button(array('href' => OSCOM::getLink(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

<?php
  }
?>

  </form>
</div>
