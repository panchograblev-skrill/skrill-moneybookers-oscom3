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
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png') . ' ' . $osC_Language->get('action_heading_batch_delete_entries'); ?></div>
<div class="infoBoxContent">
  <form name="lDeleteBatch" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&fm=' . $_GET['fm'] . '&fu=' . $_GET['fu'] . '&action=batchDelete'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_batch_delete_entries'); ?></p>

<?php
  $Qlog = $osC_Database->query('select al.id, al.module, al.module_action, al.module_id, a.user_name from :table_administrators_log al, :table_administrators a where al.id in (":id") and al.administrators_id = a.id group by id order by id');
  $Qlog->bindTable(':table_administrators_log', TABLE_ADMINISTRATORS_LOG);
  $Qlog->bindTable(':table_administrators', TABLE_ADMINISTRATORS);
  $Qlog->bindRaw(':id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
  $Qlog->execute();

  $names_string = '';

  while ( $Qlog->next() ) {
    $names_string .= osc_draw_hidden_field('batch[]', $Qlog->valueInt('id')) . '<b>' . $Qlog->valueProtected('user_name') . ' &raquo; ' . $Qlog->value('module_action') . ' &raquo; ' . $Qlog->value('module') . ' &raquo; ' . $Qlog->value('module_id') . '</b><br />';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -6);
  }

  echo '<p>' . $names_string . '</p>';
?>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&fm=' . $_GET['fm'] . '&fu=' . $_GET['fu']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
