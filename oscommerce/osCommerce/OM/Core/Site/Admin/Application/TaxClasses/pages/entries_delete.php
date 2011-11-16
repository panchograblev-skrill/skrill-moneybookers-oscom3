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
  use osCommerce\OM\Core\Site\Admin\Application\TaxClasses\TaxClasses;

  $OSCOM_ObjectInfo = new ObjectInfo(TaxClasses::getEntry($_GET['rID']));
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . HTML::link(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo HTML::icon('trash.png') . ' ' . $OSCOM_ObjectInfo->get('tax_class_title') . ': ' . $OSCOM_ObjectInfo->getProtected('geo_zone_name'); ?></h3>

  <form name="rDelete" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'EntryDelete&Process&id=' . $_GET['id'] . '&rID=' . $OSCOM_ObjectInfo->getInt('tax_rates_id')); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_delete_tax_rate'); ?></p>

  <p><?php echo '<b>' . $OSCOM_ObjectInfo->getProtected('tax_class_title') . ': ' . $OSCOM_ObjectInfo->getProtected('geo_zone_name') . '</b>'; ?></p>

  <p><?php echo HTML::button(array('priority' => 'primary', 'icon' => 'trash', 'title' => OSCOM::getDef('button_delete'))) . ' ' . HTML::button(array('href' => OSCOM::getLink(null, null, 'id=' . $_GET['id']), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>
