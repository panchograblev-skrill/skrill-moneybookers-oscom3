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

  require('includes/templates/' . $_GET['filter'] . '.php');

  $filter_id = 0;
  $templates_array = array();

  $Qtemplates = $osC_Database->query('select id, title, code from :table_templates order by title');
  $Qtemplates->bindTable(':table_templates', TABLE_TEMPLATES);
  $Qtemplates->execute();

  while ( $Qtemplates->next() ) {
    if ( $Qtemplates->value('code') == $_GET['filter'] ) {
      $filter_id = $Qtemplates->valueInt('id');
    }

    $templates_array[] = array('id' => $Qtemplates->value('code'),
                               'text' => $Qtemplates->value('title'));
  }
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&set=' . $_GET['set']), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div style="float: right;">
  <form name="template" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT); ?>" method="get"><?php echo osc_draw_hidden_field($osC_Template->getModule(), null) . osc_draw_hidden_field('set', $_GET['set']); ?>

  <?php echo osc_draw_pull_down_menu('filter', $templates_array, $filter_id) . '<input type="submit" value="GO" class="operationButton" />'; ?>

  <?php echo '<input type="button" value="' . $osC_Language->get('button_insert') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&set=' . $_GET['set'] . '&filter=' . $_GET['filter'] . '&action=save') . '\';" class="infoBoxButton" />'; ?>

  </form>
</div>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php echo $osC_Language->get('table_heading_modules'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_pages'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_page_specific'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_group'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_sort_order'); ?></th>
      <th width="150"><?php echo $osC_Language->get('table_heading_action'); ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th align="right" colspan="6"><?php echo '<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . $osC_Language->get('icon_trash') . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&set=' . $_GET['set'] . '&filter=' . $_GET['filter'] . '&action=batchDelete') . '\';" />'; ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </tfoot>
  <tbody>

<?php
  $Qlayout = $osC_Database->query('select b2p.*, b.title as box_title from :table_templates_boxes_to_pages b2p, :table_templates_boxes b where b2p.templates_id = :templates_id and b2p.templates_boxes_id = b.id and b.modules_group = :modules_group order by b2p.page_specific desc, b2p.boxes_group, b2p.sort_order, b.title');
  $Qlayout->bindTable(':table_templates_boxes_to_pages', TABLE_TEMPLATES_BOXES_TO_PAGES);
  $Qlayout->bindTable(':table_templates_boxes', TABLE_TEMPLATES_BOXES);
  $Qlayout->bindInt(':templates_id', $filter_id);
  $Qlayout->bindValue(':modules_group', $_GET['set']);
  $Qlayout->execute();

  while ( $Qlayout->next() ) {
?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
      <td onclick="document.getElementById('batch<?php echo $Qlayout->valueInt('id'); ?>').checked = !document.getElementById('batch<?php echo $Qlayout->valueInt('id'); ?>').checked;"><?php echo $Qlayout->value('box_title'); ?></td>
      <td><?php echo $Qlayout->value('content_page'); ?></td>
      <td align="center"><?php echo osc_icon(($Qlayout->valueInt('page_specific') === 1 ? 'checkbox_ticked.gif' : 'checkbox.gif'), null, null); ?></td>
      <td align="right"><?php echo $Qlayout->value('boxes_group'); ?></td>
      <td align="right"><?php echo $Qlayout->valueInt('sort_order'); ?></td>
      <td align="right">

<?php
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&set=' . $_GET['set'] . '&filter=' . $_GET['filter'] . '&lID=' . $Qlayout->valueInt('id') . '&action=save'), osc_icon('edit.png')) . '&nbsp;' .
         osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&set=' . $_GET['set'] . '&filter=' . $_GET['filter'] . '&lID=' . $Qlayout->valueInt('id') . '&action=delete'), osc_icon('trash.png'));
?>

      </td>
      <td align="center"><?php echo osc_draw_checkbox_field('batch[]', $Qlayout->valueInt('id'), null, 'id="batch' . $Qlayout->valueInt('id') . '"'); ?></td>
    </tr>

<?php
  }
?>

  </tbody>
</table>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td style="opacity: 0.5; filter: alpha(opacity=50);"><?php echo '<b>' . $osC_Language->get('table_action_legend') . '</b> ' . osc_icon('edit.png') . '&nbsp;' . $osC_Language->get('icon_edit') . '&nbsp;&nbsp;' . osc_icon('trash.png') . '&nbsp;' . $osC_Language->get('icon_trash'); ?></td>
  </tr>
</table>
