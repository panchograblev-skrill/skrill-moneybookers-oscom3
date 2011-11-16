<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Manufacturers_Admin::getData($_GET['mID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png') . ' ' . $osC_ObjectInfo->get('manufacturers_name'); ?></div>
<div class="infoBoxContent">
  <form name="mDelete" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&mID=' . $osC_ObjectInfo->get('manufacturers_id') . '&action=delete'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_delete_manufacturer'); ?></p>

  <p>

<?php
  $manufacturer_name = $osC_ObjectInfo->get('manufacturers_name');

  if ( $osC_ObjectInfo->get('products_count') > 0 ) {
    $manufacturer_name .= ' (' . sprintf($osC_Language->get('total_entries'), $osC_ObjectInfo->get('products_count')) . ')';
  }

  echo '<b>' . $manufacturer_name . '</b>';
?>

  </p>

<?php
  if ( !osc_empty($osC_ObjectInfo->get('manufacturers_image')) ) {
    echo '  <p>' . osc_draw_checkbox_field('delete_image', null, true) . ' ' . $osC_Language->get('field_delete_image') . '</p>';
  }

  if ( $osC_ObjectInfo->get('products_count') > 0 ) {
    echo '  <p>' . osc_draw_checkbox_field('delete_products') . ' ' . $osC_Language->get('field_delete_products') . '</p>';
  }
?>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
