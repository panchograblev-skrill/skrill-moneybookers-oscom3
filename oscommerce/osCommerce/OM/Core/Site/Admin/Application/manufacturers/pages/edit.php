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

<div class="infoBoxHeading"><?php echo osc_icon('edit.png') . ' ' . $osC_ObjectInfo->get('manufacturers_name'); ?></div>
<div class="infoBoxContent">
  <form name="mEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&mID=' . $osC_ObjectInfo->get('manufacturers_id') . '&action=save'); ?>" method="post" enctype="multipart/form-data">

  <p><?php echo $osC_Language->get('introduction_edit_manufacturer'); ?></p>

  <p><?php echo $osC_Language->get('field_name') . '<br />' . osc_draw_input_field('manufacturers_name', $osC_ObjectInfo->get('manufacturers_name')); ?></p>
  <p><?php echo osc_image('../' . DIR_WS_IMAGES . 'manufacturers/' . $osC_ObjectInfo->get('manufacturers_image'), $osC_ObjectInfo->get('manufacturers_name')) . '<br />' . DIR_WS_CATALOG . DIR_WS_IMAGES . 'manufacturers/<br /><b>' . $osC_ObjectInfo->get('manufacturers_image') . '</b>'; ?></p>
  <p><?php echo $osC_Language->get('field_image') . '<br />' . osc_draw_file_field('manufacturers_image', true); ?></p>

  <p>

<?php
  echo $osC_Language->get('field_url');

  $manufacturers_array = array();

  $Qmanufacturer = $osC_Database->query('select manufacturers_url, languages_id from :table_manufacturers_info where manufacturers_id = :manufacturers_id');
  $Qmanufacturer->bindTable(':table_manufacturers_info', TABLE_MANUFACTURERS_INFO);
  $Qmanufacturer->bindInt(':manufacturers_id', $osC_ObjectInfo->get('manufacturers_id'));
  $Qmanufacturer->execute();

  while ( $Qmanufacturer->next() ) {
    $manufacturers_array[$Qmanufacturer->valueInt('languages_id')] = $Qmanufacturer->value('manufacturers_url');
  }

  foreach ( $osC_Language->getAll() as $l ) {
    echo '<br />' . $osC_Language->showImage($l['code']) . '&nbsp;' . osc_draw_input_field('manufacturers_url[' . $l['id'] . ']', $manufacturers_array[$l['id']]);
  }
?>

  </p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
