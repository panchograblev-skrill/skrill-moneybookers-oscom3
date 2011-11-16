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

  $modules_array = array(array('id' => '',
                               'text' => $osC_Language->get('filter_all')));

  foreach ( $_SESSION[OSCOM::getSite()]['access'] as $module ) {
    $modules_array[] = array('id' => $module,
                             'text' => $module);

  }

  $admins_array = array(array('id' => '',
                              'text' => $osC_Language->get('filter_all')));

  $Qadmins = $osC_Database->query('select id, user_name from :table_administrators order by user_name');
  $Qadmins->bindTable(':table_administrators', TABLE_ADMINISTRATORS);
  $Qadmins->execute();

  while ( $Qadmins->next() ) {
    $admins_array[] = array('id' => $Qadmins->valueInt('id'),
                            'text' => $Qadmins->valueProtected('user_name'));
  }
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div align="right">
  <form name="filter" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT); ?>" method="get"><?php echo osc_draw_hidden_field($osC_Template->getModule()); ?>

  <?php echo $osC_Language->get('operation_title_filter_modules') . ' ' . osc_draw_pull_down_menu('fm', $modules_array); ?>
  <?php echo $osC_Language->get('operation_title_filter_users') . ' ' . osc_draw_pull_down_menu('fu', $admins_array); ?>

  <input type="submit" value="GO" class="operationButton" />

  </form>
</div>

<?php
  $Qlog = $osC_Database->query('select SQL_CALC_FOUND_ROWS count(al.id) as total, al.id, al.module, al.module_action, al.module_id, al.action, a.user_name, unix_timestamp(al.datestamp) as datestamp from :table_administrators_log al, :table_administrators a where');

  if ( !empty($_GET['fm']) && in_array($_GET['fm'], $_SESSION[OSCOM::getSite()]['access']) ) {
    $Qlog->appendQuery('al.module = :module');
    $Qlog->bindValue(':module', $_GET['fm']);
  } else {
    $Qlog->appendQuery('al.module in (":modules")');
    $Qlog->bindRaw(':modules', implode('", "', $_SESSION[OSCOM::getSite()]['access']));
  }

  $Qlog->appendQuery('and');

  if ( is_numeric($_GET['fu']) ) {
    $Qlog->appendQuery('al.administrators_id = :administrators_id and');
    $Qlog->bindInt(':administrators_id', $_GET['fu']);
  }

  $Qlog->appendQuery('al.administrators_id = a.id group by al.id order by al.id desc');
  $Qlog->bindTable(':table_administrators_log', TABLE_ADMINISTRATORS_LOG);
  $Qlog->bindTable(':table_administrators', TABLE_ADMINISTRATORS);
  $Qlog->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qlog->execute();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><?php echo $Qlog->getBatchTotalPages($osC_Language->get('batch_results_number_of_entries')); ?></td>
    <td align="right"><?php echo $Qlog->getBatchPageLinks('page', $osC_Template->getModule() . '&fm=' . $_GET['fm'] . '&fu=' . $_GET['fu'], false); ?></td>
  </tr>
</table>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php echo $osC_Language->get('table_heading_module'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_id'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_type'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_user'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_date'); ?></th>
      <th width="150"><?php echo $osC_Language->get('table_heading_action'); ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th align="right" colspan="6"><?php echo '<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . $osC_Language->get('icon_trash') . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&fm=' . $_GET['fm'] . '&fu=' . $_GET['fu'] . '&action=batchDelete') . '\';" />'; ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </tfoot>
  <tbody>

<?php
  while ( $Qlog->next() ) {
?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
      <td onclick="document.getElementById('batch<?php echo $Qlog->valueInt('id'); ?>').checked = !document.getElementById('batch<?php echo $Qlog->valueInt('id'); ?>').checked;"><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&fm=' . $_GET['fm'] . '&fu=' . $_GET['fu'] . '&lID=' . $Qlog->valueInt('id') . '&action=info'), osc_icon('folder.png') . '&nbsp;' . $Qlog->value('module') . ' (' . $Qlog->valueInt('total') . ')'); ?></td>
      <td align="center"><?php echo $Qlog->valueInt('module_id'); ?></td>
      <td align="center"><?php echo $Qlog->valueProtected('module_action'); ?></td>
      <td align="right"><?php echo $Qlog->valueProtected('user_name'); ?></td>
      <td align="right"><?php echo date('d M Y H:i:s', $Qlog->value('datestamp')); ?></td>
      <td align="right">

<?php
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&fm=' . $_GET['fm'] . '&fu=' . $_GET['fu'] . '&lID=' . $Qlog->valueInt('id') . '&action=info'), osc_icon('info.png')) . '&nbsp;' .
         osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&fm=' . $_GET['fm'] . '&fu=' . $_GET['fu'] . '&lID=' . $Qlog->valueInt('id') . '&action=delete'), osc_icon('trash.png'));
?>

      </td>
      <td align="center"><?php echo osc_draw_checkbox_field('batch[]', $Qlog->valueInt('id'), null, 'id="batch' . $Qlog->valueInt('id') . '"'); ?></td>
    </tr>

<?php
  }
?>

  </tbody>
</table>

</form>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td style="opacity: 0.5; filter: alpha(opacity=50);"><?php echo '<b>' . $osC_Language->get('table_action_legend') . '</b> ' . osc_icon('info.png') . '&nbsp;' . $osC_Language->get('icon_info') . '&nbsp;&nbsp;' . osc_icon('trash.png') . '&nbsp;' . $osC_Language->get('icon_trash') ; ?></td>
    <td align="right"><?php echo $Qlog->getBatchPagesPullDownMenu('page', $osC_Template->getModule() . '&fm=' . $_GET['fm'] . '&fu=' . $_GET['fu']); ?></td>
  </tr>
</table>
