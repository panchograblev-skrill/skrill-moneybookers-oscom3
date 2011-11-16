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
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<form name="search" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT); ?>" method="get"><?php echo osc_draw_hidden_field($osC_Template->getModule()); ?>

<p align="right">

<?php
  echo $osC_Language->get('operation_heading_order_id') . ' ' . osc_draw_input_field('oID') . '&nbsp;' .
       $osC_Language->get('operation_heading_customer_id') . ' ' . osc_draw_input_field('cID') . '&nbsp;' .
       $osC_Language->get('operation_heading_filter_status') . osc_draw_pull_down_menu('status', array_merge(array(array('id' => '', 'text' => $osC_Language->get('all_statuses'))), $orders_statuses)) .
       '<input type="submit" value="GO" class="operationButton" />';
?>

</p>

</form>

<?php
  $Qorders = $osC_Database->query('select o.orders_id, o.customers_ip_address, o.customers_name, o.payment_method, o.date_purchased, o.last_modified, greatest(date_purchased, coalesce(last_modified, date_purchased)) as date_sort, o.currency, o.currency_value, s.orders_status_name, ot.text as order_total from :table_orders o, :table_orders_total ot, :table_orders_status s where o.orders_id = ot.orders_id and ot.class = "total" and o.orders_status = s.orders_status_id and s.language_id = :language_id');

  if ( isset($_GET['oID']) && is_numeric($_GET['oID']) ) {
    $Qorders->appendQuery('and o.orders_id = :orders_id');
    $Qorders->bindInt(':orders_id', $_GET['oID']);
  }

  if ( isset($_GET['cID']) && is_numeric($_GET['cID']) ) {
    $Qorders->appendQuery('and o.customers_id = :customers_id');
    $Qorders->bindInt(':customers_id', $_GET['cID']);
  }

  if ( isset($_GET['status']) && is_numeric($_GET['status']) ) {
    $Qorders->appendQuery('and s.orders_status_id = :orders_status_id');
    $Qorders->bindInt(':orders_status_id', $_GET['status']);
  }

  $Qorders->appendQuery('order by date_sort desc');
  $Qorders->bindTable(':table_orders', TABLE_ORDERS);
  $Qorders->bindTable(':table_orders_total', TABLE_ORDERS_TOTAL);
  $Qorders->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
  $Qorders->bindInt(':language_id', $osC_Language->getID());
  $Qorders->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qorders->execute();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><?php echo $Qorders->getBatchTotalPages($osC_Language->get('batch_results_number_of_entries')); ?></td>
    <td align="right"><?php echo $Qorders->getBatchPageLinks('page', $osC_Template->getModule() . '&' . (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] : ''), false); ?></td>
  </tr>
</table>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php echo $osC_Language->get('table_heading_customers'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_order_total'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_date_purchased'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_status'); ?></th>
      <th width="150"><?php echo $osC_Language->get('table_heading_action'); ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th align="right" colspan="5"><?php echo '<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . $osC_Language->get('icon_trash') . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&' . (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&action=batchDelete') . '\';" />'; ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </tfoot>
  <tbody>

<?php
  while ( $Qorders->next() ) {
?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
      <td><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&' . (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&oID=' . $Qorders->valueInt('orders_id') . '&action=save'), osc_icon('orders.png') . '&nbsp;' . $Qorders->valueProtected('customers_name')); ?></td>
      <td><?php echo strip_tags($Qorders->value('order_total')); ?></td>
      <td><?php echo osC_DateTime::getShort($Qorders->value('date_purchased'), true); ?></td>
      <td><?php echo $Qorders->value('orders_status_name'); ?></td>
      <td align="right">

<?php
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&' . (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&oID=' . $Qorders->valueInt('orders_id') . '&action=save'), osc_icon('edit.png')) . '&nbsp;' .
         osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&' . (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&oID=' . $Qorders->valueInt('orders_id') . '&action=delete'), osc_icon('trash.png'));
?>

      </td>
      <td align="center"><?php echo osc_draw_checkbox_field('batch[]', $Qorders->valueInt('orders_id'), null, 'id="batch' . $Qorders->valueInt('orders_id') . '"'); ?></td>
    </tr>

<?php
  }
?>

  </tbody>
</table>

</form>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td style="opacity: 0.5; filter: alpha(opacity=50);"><?php echo '<b>' . $osC_Language->get('table_action_legend') . '</b> ' . osc_icon('edit.png') . '&nbsp;' . $osC_Language->get('icon_edit') . '&nbsp;&nbsp;' . osc_icon('trash.png') . '&nbsp;' . $osC_Language->get('icon_trash'); ?></td>
    <td align="right"><?php echo $Qorders->getBatchPagesPullDownMenu('page', $osC_Template->getModule()); ?></td>
  </tr>
</table>
