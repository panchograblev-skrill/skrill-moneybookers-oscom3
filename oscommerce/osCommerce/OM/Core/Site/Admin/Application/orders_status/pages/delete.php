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

<div class="infoBoxHeading"><?php echo osc_icon('trash.png') . ' ' . $osC_ObjectInfo->get('orders_status_name'); ?></div>
<div class="infoBoxContent">
  <form name="osDelete" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&osID=' . $osC_ObjectInfo->get('orders_status_id') . '&action=delete'); ?>" method="post">

<?php
/*
  $Qorders = $osC_Database->query('select count(*) as total from :table_orders where orders_status = :orders_status');
  $Qorders->bindTable(':table_orders', TABLE_ORDERS);
  $Qorders->bindInt(':orders_status', $osC_ObjectInfo->get('orders_status_id'));
  $Qorders->execute();

  $Qhistory = $osC_Database->query('select count(*) as total from :table_orders_status_history where orders_status_id = :orders_status_id group by orders_id');
  $Qhistory->bindTable(':table_orders_status_history', TABLE_ORDERS_STATUS_HISTORY);
  $Qhistory->bindInt(':orders_status_id', $osC_ObjectInfo->get('orders_status_id'));
  $Qhistory->execute();

  if ( ( $osC_ObjectInfo->get('orders_status_id') == DEFAULT_ORDERS_STATUS_ID ) || ( $Qorders->valueInt('total') > 0)  || ( $Qhistory->valueInt('total') > 0 ) ) {
    if ( $osC_ObjectInfo->get('orders_status_id') == DEFAULT_ORDERS_STATUS_ID ) {
      echo '  <p><b>' . $osC_Language->get('delete_error_order_status_prohibited') . '</b></p>';
    }

    if ( $Qorders->valueInt('total') > 0 ) {
      echo '  <p><b>' . sprintf($osC_Language->get('delete_error_order_status_in_use'), $Qorders->valueInt('total')) . '</b></p>';
    }

    if ( $Qhistory->valueInt('total') > 0 ) {
      echo '  <p><b>' . sprintf($osC_Language->get('delete_error_order_status_used'), $Qhistory->valueInt('total')) . '</b></p>';
    }

    echo '  <p align="center"><input type="button" value="' . $osC_Language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" /></p>';
  } else {
*/
?>

  <p><?php echo $osC_Language->get('introduction_delete_order_status'); ?></p>

  <p><?php echo '<b>' . $osC_ObjectInfo->get('orders_status_name') . '</b>'; ?></p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

<?php
//  }
?>

  </form>
</div>
