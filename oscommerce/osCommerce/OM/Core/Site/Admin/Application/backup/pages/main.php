<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $osC_DirectoryListing = new osC_DirectoryListing(DIR_FS_BACKUP);
  $osC_DirectoryListing->setIncludeDirectories(false);
  $osC_DirectoryListing->setCheckExtension('zip');
  $osC_DirectoryListing->setCheckExtension('sql');
  $osC_DirectoryListing->setCheckExtension('gz');
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<p align="right"><?php echo '<input type="button" value="' . $osC_Language->get('button_backup') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=backup') . '\';" class="infoBoxButton" />&nbsp;<input type="button" value="' . $osC_Language->get('button_restore') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=restoreLocal') . '\';" class="infoBoxButton" />'; ?></p>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php echo $osC_Language->get('table_heading_backups'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_date'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_file_size'); ?></th>
      <th width="150"><?php echo $osC_Language->get('table_heading_action'); ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th align="right" colspan="4"><?php echo '<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . $osC_Language->get('icon_trash') . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=batchDelete') . '\';" />'; ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </tfoot>
  <tbody>

<?php
  foreach ( $osC_DirectoryListing->getFiles() as $file ) {
?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
      <td><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&file=' . $file['name'] . '&action=download'), osc_icon('download.png') . '&nbsp;' . $file['name']); ?></td>
      <td><?php echo osC_DateTime::getShort(osC_DateTime::fromUnixTimestamp(filemtime(DIR_FS_BACKUP . $file['name'])), true); ?></td>
      <td><?php echo number_format(filesize(DIR_FS_BACKUP . $file['name'])); ?> bytes</td>
      <td align="right">

<?php
      echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&file=' . $file['name'] . '&action=restore'), osc_icon('restore.png')) . '&nbsp;' .
           osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&file=' . $file['name'] . '&action=delete'), osc_icon('trash.png'));
?>

      </td>
      <td align="center"><?php echo osc_draw_checkbox_field('batch[]', $file['name'], null, 'id="batch' . addslashes($file['name']) . '"'); ?></td>
    </tr>

<?php
  }
?>

  </tbody>
</table>

</form>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td style="opacity: 0.5; filter: alpha(opacity=50);"><?php echo '<b>' . $osC_Language->get('table_action_legend') . '</b> ' . osc_icon('download.png') . '&nbsp;' . $osC_Language->get('icon_download') . '&nbsp;&nbsp;' . osc_icon('restore.png') . '&nbsp;' . $osC_Language->get('icon_restore') .  '&nbsp;&nbsp;' . osc_icon('trash.png') . '&nbsp;' . $osC_Language->get('icon_trash'); ?></td>
  </tr>
</table>

<p><?php echo $osC_Language->get('backup_location') . ' ' . DIR_FS_BACKUP; ?></p>

<?php
  if ( defined('DB_LAST_RESTORE') ) {
?>

<p><?php echo $osC_Language->get('last_restoration_date') . ' ' . DB_LAST_RESTORE . ' ' . osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=forget'), $osC_Language->get('forget_restoration_date')); ?></p>

<?php
  }
?>
