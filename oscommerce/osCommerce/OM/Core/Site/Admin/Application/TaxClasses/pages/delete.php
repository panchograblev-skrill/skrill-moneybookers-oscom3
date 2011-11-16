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

  $OSCOM_ObjectInfo = new ObjectInfo(TaxClasses::get($_GET['id']));
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . HTML::link(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo HTML::icon('trash.png') . ' ' . $OSCOM_ObjectInfo->getProtected('tax_class_title'); ?></h3>

  <form name="tcDelete" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'Delete&Process&id=' . $OSCOM_ObjectInfo->getInt('tax_class_id')); ?>" method="post">

<?php
  if ( TaxClasses::hasProducts($OSCOM_ObjectInfo->getInt('tax_class_id')) ) {
?>

  <p><?php echo '<b>' . sprintf(OSCOM::getDef('delete_warning_tax_class_in_use'), TaxClasses::getNumberOfProducts($OSCOM_ObjectInfo->getInt('tax_class_id'))) . '</b>'; ?></p>

  <p><?php echo HTML::button(array('href' => OSCOM::getLink(), 'icon' => 'triangle-1-w', 'title' => OSCOM::getDef('button_back'))); ?></p>

<?php
  } else {
?>

  <p><?php echo OSCOM::getDef('introduction_delete_tax_class'); ?></p>

  <p><?php echo '<b>' . $OSCOM_ObjectInfo->get('tax_class_title') . ' (' . sprintf(OSCOM::getDef('total_entries'), $OSCOM_ObjectInfo->getInt('total_tax_rates')) . ')</b>'; ?></p>

  <p><?php echo HTML::button(array('priority' => 'primary', 'icon' => 'trash', 'title' => OSCOM::getDef('button_delete'))) . ' ' . HTML::button(array('href' => OSCOM::getLink(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

<?php
  }
?>

  </form>
</div>
