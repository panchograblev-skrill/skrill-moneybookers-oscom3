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

  $osC_DirectoryListing = new osC_DirectoryListing('includes/modules/order_total');
  $osC_DirectoryListing->setIncludeDirectories(false);
  $files = $osC_DirectoryListing->getFiles();
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php echo $osC_Language->get('table_heading_order_total_modules'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_sort_order'); ?></th>
      <th width="150"><?php echo $osC_Language->get('table_heading_action'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th colspan="3">&nbsp;</th>
    </tr>
  </tfoot>
  <tbody>

<?php
  $installed_modules = array();

  foreach ( $files as $file ) {
    include('includes/modules/order_total/' . $file['name']);

    $class = substr($file['name'], 0, strrpos($file['name'], '.'));

    if ( class_exists('osC_OrderTotal_' . $class) ) {
      $osC_Language->injectDefinitions('modules/order_total/' . $class . '.xml');

      $module = 'osC_OrderTotal_' . $class;
      $module = new $module();
?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" <?php echo ( $module->isInstalled() && !$module->isEnabled() ? 'class="deactivatedRow"' : '') ?>>
      <td><?php echo $module->getTitle(); ?></td>
      <td><?php echo $module->getSortOrder(); ?></td>
      <td align="right">

<?php
    if ( $module->isInstalled() ) {
      echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&module=' . $module->getCode() . '&action=save'), osc_icon('edit.png')) . '&nbsp;' .
           osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&module=' . $module->getCode() . '&action=uninstall'), osc_icon('uninstall.png'));
    } else {
      echo osc_image('images/pixel_trans.gif', '', '16', '16') . '&nbsp;' .
           osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&module=' . $module->getCode() . '&action=install'), osc_icon('install.png'));
    }
?>

      </td>
    </tr>

<?php
    }
  }
?>

  </tbody>
</table>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td style="opacity: 0.5; filter: alpha(opacity=50);"><?php echo '<b>' . $osC_Language->get('table_action_legend') . '</b> ' . osc_icon('edit.png') . '&nbsp;' . $osC_Language->get('icon_edit') . '&nbsp;&nbsp;' . osc_icon('install.png') . '&nbsp;' . $osC_Language->get('icon_install') .  '&nbsp;&nbsp;' . osc_icon('uninstall.png') . '&nbsp;' . $osC_Language->get('icon_uninstall'); ?></td>
  </tr>
</table>
