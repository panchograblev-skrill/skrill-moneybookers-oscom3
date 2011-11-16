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

  $OSCOM_ObjectInfo = new ObjectInfo(ZoneGroups::getEntry($_GET['zID']));
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . HTML::link(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo HTML::icon('trash.png') . ' ' . $OSCOM_ObjectInfo->getProtected('countries_name') . ': ' . $OSCOM_ObjectInfo->getProtected('zone_name'); ?></h3>

  <form name="zDelete" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'EntryDelete&Process&id=' . $_GET['id'] . '&zID=' . $OSCOM_ObjectInfo->getInt('association_id')); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_delete_zone_entry'); ?></p>

  <p><?php echo '<b>' . $OSCOM_ObjectInfo->getProtected('countries_name') . ': ' . $OSCOM_ObjectInfo->getProtected('zone_name') . '</b>'; ?></p>

  <p><?php echo HTML::button(array('priority' => 'primary', 'icon' => 'trash', 'title' => OSCOM::getDef('button_delete'))) . ' ' . HTML::button(array('href' => OSCOM::getLink(null, null, 'id=' . $_GET['id']), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>
