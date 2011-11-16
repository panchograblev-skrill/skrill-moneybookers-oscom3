<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_AdministratorsLog_Admin::getData($_GET['lID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>
<p align="right"><?php echo '<input type="button" value="' . $osC_Language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&fm=' . $_GET['fm'] . '&fu=' . $_GET['fu']) . '\';" class="operationButton" />'; ?></p>

<div class="infoBoxHeading"><?php echo osc_icon('info.png') . ' ' . $osC_ObjectInfo->get('user_name') . ' &raquo; ' . $osC_ObjectInfo->get('module_action') . ' &raquo; ' . $osC_ObjectInfo->get('module') . ' &raquo; ' . $osC_ObjectInfo->get('module_id'); ?></div>
<div class="infoBoxContent">
  <p><?php echo '<b>' . $osC_Language->get('field_date') . '</b> ' . date('d M Y H:i:s', $osC_ObjectInfo->get('datestamp')); ?></p>
</div>

<br />

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php echo $osC_Language->get('table_heading_fields'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_value_old'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_value_new'); ?></th>
    </tr>
  </thead>
  <tbody>

<?php
  $Qentries = $osC_Database->query('select action, field_key, old_value, new_value from :table_administrators_log where id = :id');
  $Qentries->bindTable(':table_administrators_log', TABLE_ADMINISTRATORS_LOG);
  $Qentries->bindInt(':id', $osC_ObjectInfo->get('id'));
  $Qentries->execute();

  while ( $Qentries->next() ) {
    switch ( $Qentries->value('action') ) {
      case 'delete':
        $bgColor = '#E23832';

        break;

      case 'insert':
        $bgColor = '#96E97A';

        break;

      default:
        $bgColor = '#FFC881';

        break;
    }
?>

    <tr>
      <td valign="top" style="background-color: <?php echo $bgColor; ?>;"><?php echo $Qentries->valueProtected('field_key'); ?></td>
      <td valign="top" style="background-color: <?php echo $bgColor; ?>;"><?php echo nl2br($Qentries->valueProtected('old_value')); ?></td>
      <td valign="top" style="background-color: <?php echo $bgColor; ?>;"><?php echo nl2br($Qentries->valueProtected('new_value')); ?></td>
    </tr>

<?php
  }
?>

  </tbody>
</table>
