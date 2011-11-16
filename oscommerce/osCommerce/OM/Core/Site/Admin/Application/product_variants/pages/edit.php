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

  $modules_array = array();

  $osC_DirectoryListing = new osC_DirectoryListing('../includes/modules/variants');
  $osC_DirectoryListing->setIncludeDirectories(false);
  $osC_DirectoryListing->setCheckExtension('php');

  foreach ( $osC_DirectoryListing->getFiles() as $file ) {
    $module = substr($file['name'], 0, strrpos($file['name'], '.'));

    $modules_array[] = array('id' => $module,
                             'text' => $module);
  }

  $osC_ObjectInfo = new osC_ObjectInfo(osC_ProductVariants_Admin::getData($_GET['paID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('edit.png') . ' ' . $osC_ObjectInfo->get('title'); ?></div>
<div class="infoBoxContent">
  <form name="paEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&paID=' . $osC_ObjectInfo->get('id') . '&action=save'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_edit_attribute_group'); ?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%" valign="top"><?php echo '<b>' . $osC_Language->get('field_group_name') . '</b>'; ?></td>
      <td width="60%">

<?php
  $Qgd = $osC_Database->query('select languages_id, title from :table_products_variants_groups where id = :id');
  $Qgd->bindTable(':table_products_variants_groups', TABLE_PRODUCTS_VARIANTS_GROUPS);
  $Qgd->bindInt(':id', $osC_ObjectInfo->get('id'));
  $Qgd->execute();

  $group_names = array();

  while ($Qgd->next()) {
    $group_names[$Qgd->valueInt('languages_id')] = $Qgd->value('title');
  }

  foreach ($osC_Language->getAll() as $l) {
    echo $osC_Language->showImage($l['code']) . '&nbsp;' .  osc_draw_input_field('group_name[' . $l['id'] . ']', (isset($group_names[$l['id']]) ? $group_names[$l['id']] : null)) . '<br />';
  }
?>

      </td>
    </tr>
    <tr>
      <td width="40%" valign="top"><?php echo '<b>' . $osC_Language->get('field_display_module') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_pull_down_menu('module', $modules_array, $osC_ObjectInfo->get('module')); ?></td>
    </tr>
    <tr>
      <td width="40%" valign="top"><?php echo '<b>' . $osC_Language->get('field_sort_order') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('sort_order', $osC_ObjectInfo->get('sort_order')); ?></td>
    </tr>
  </table>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
