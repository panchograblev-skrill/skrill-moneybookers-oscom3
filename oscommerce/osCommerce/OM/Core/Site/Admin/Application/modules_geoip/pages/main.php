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

  $osC_DirectoryListing = new osC_DirectoryListing('includes/modules/geoip');
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
      <th><?php echo $osC_Language->get('table_heading_geoip_modules'); ?></th>
      <th width="150"><?php echo $osC_Language->get('table_heading_action'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th colspan="2">&nbsp;</th>
    </tr>
  </tfoot>
  <tbody>

<?php
  foreach ( $files as $file ) {
    include('includes/modules/geoip/' . $file['name']);

    $class = substr($file['name'], 0, strrpos($file['name'], '.'));

    if (class_exists('osC_GeoIP_' . $class)) {
      $osC_Language->loadIniFile('modules/geoip/' . $class . '.php');

      $module = 'osC_GeoIP_' . $class;
      $module = new $module();
?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
      <td><?php echo $module->getTitle(); ?></td>
      <td align="right">

<?php
      if ( $module->isInstalled() ) {
        if ( sizeof($module->getKeys()) > 0 ) {
          echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&module=' . $class . '&action=save'), osc_icon('edit.png')) . '&nbsp;';
        } else {
          echo osc_image('images/pixel_trans.gif', '', '16', '16') . '&nbsp;';
        }

        echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&module=' . $class . '&action=info'), osc_icon('info.png')) . '&nbsp;' .
             osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&module=' . $class . '&action=uninstall'), osc_icon('uninstall.png'));
      } else {
        echo osc_image('images/pixel_trans.gif', '', '16', '16') . '&nbsp;' .
             osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&module=' . $class . '&action=info'), osc_icon('info.png')) . '&nbsp;' .
             osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&module=' . $class . '&action=install'), osc_icon('install.png'));
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
    <td style="opacity: 0.5; filter: alpha(opacity=50);"><?php echo '<b>' . $osC_Language->get('table_action_legend') . '</b> ' . osc_icon('info.png') . '&nbsp;' . $osC_Language->get('icon_info') . '&nbsp;&nbsp;' . osc_icon('edit.png') . '&nbsp;' . $osC_Language->get('icon_edit') . '&nbsp;&nbsp;' . osc_icon('install.png') . '&nbsp;' . $osC_Language->get('icon_install') .  '&nbsp;&nbsp;' . osc_icon('uninstall.png') . '&nbsp;' . $osC_Language->get('icon_uninstall'); ?></td>
  </tr>
</table>
