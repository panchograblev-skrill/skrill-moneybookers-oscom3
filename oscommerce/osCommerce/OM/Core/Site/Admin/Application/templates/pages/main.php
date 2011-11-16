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

  $osC_DirectoryListing = new osC_DirectoryListing('includes/templates');
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
      <th><?php echo $osC_Language->get('table_heading_templates'); ?></th>
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
    include('includes/templates/' . $file['name']);

    $code = substr($file['name'], 0, strrpos($file['name'], '.'));
    $class = 'osC_Template_' . $code;

    if ( class_exists($class) ) {
      $module = new $class();

      $module_title = $module->getTitle();

      if ( $module->getCode() == DEFAULT_TEMPLATE ) {
        $module_title .= ' (' . $osC_Language->get('default_entry') . ')';
      }
?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" <?php echo ( $module->isInstalled() && !$module->isActive() ? 'class="deactivatedRow"' : '') ?>>
      <td><?php echo $module_title; ?></td>
      <td align="right">

<?php
      if ( $module->isInstalled() && $module->isActive() ) {
        if ( $module->hasKeys() || ( $module->getCode() != DEFAULT_TEMPLATE ) ) {
          echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&template=' . $code . '&action=save'), osc_icon('edit.png')) . '&nbsp;';
        } else {
          echo osc_image('images/pixel_trans.gif', '', '16', '16') . '&nbsp;';
        }

        echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&template=' . $code . '&action=uninstall'), osc_icon('uninstall.png')) . '&nbsp;';
      } else {
        echo osc_image('images/pixel_trans.gif', '', '16', '16') . '&nbsp;' .
             osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&template=' . $code . '&action=install'), osc_icon('install.png')) . '&nbsp;';
      }

      echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&template=' . $code . '&action=info'), osc_icon('info.png'));
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
    <td style="opacity: 0.5; filter: alpha(opacity=50);"><?php echo '<b>' . $osC_Language->get('table_action_legend') . '</b> ' . osc_icon('edit.png') . '&nbsp;' . $osC_Language->get('icon_edit') . '&nbsp;&nbsp;' . osc_icon('install.png') . '&nbsp;' . $osC_Language->get('icon_install') .  '&nbsp;&nbsp;' . osc_icon('uninstall.png') . '&nbsp;' . $osC_Language->get('button_uninstall') . '&nbsp;&nbsp;' . osc_icon('info.png') . '&nbsp;' . $osC_Language->get('icon_info'); ?></td>
  </tr>
</table>
