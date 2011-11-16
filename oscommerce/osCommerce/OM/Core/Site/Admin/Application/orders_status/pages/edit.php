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

  $osC_ObjectInfo = new osC_ObjectInfo(osC_OrdersStatus_Admin::getData($_GET['osID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('edit.png') . ' ' . $osC_ObjectInfo->get('orders_status_name'); ?></div>
<div class="infoBoxContent">
  <form name="osEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&osID=' . $osC_ObjectInfo->get('orders_status_id') . '&action=save'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_edit_order_status'); ?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_name') . '</b>'; ?></td>
      <td width="60%">

<?php
  $Qsd = $osC_Database->query('select language_id, orders_status_name from :table_orders_status where orders_status_id = :orders_status_id');
  $Qsd->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
  $Qsd->bindInt(':orders_status_id', $osC_ObjectInfo->get('orders_status_id'));
  $Qsd->execute();

  $status_name = array();

  while ( $Qsd->next() ) {
    $status_name[$Qsd->valueInt('language_id')] = $Qsd->value('orders_status_name');
  }

  foreach ( $osC_Language->getAll() as $l ) {
    echo $osC_Language->showImage($l['code']) . '&nbsp;' . osc_draw_input_field('name[' . $l['id'] . ']', (isset($status_name[$l['id']]) ? $status_name[$l['id']] : null)) . '<br />';
  }
?>

      </td>
    </tr>

<?php
    if ( $osC_ObjectInfo->get('orders_status_id') != DEFAULT_ORDERS_STATUS_ID ) {
?>

    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_set_as_default') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_checkbox_field('default'); ?></td>
    </tr>

<?php
    }
?>

  </table>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
