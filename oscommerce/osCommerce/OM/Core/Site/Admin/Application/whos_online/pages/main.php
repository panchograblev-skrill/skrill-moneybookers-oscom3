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

  $osC_Currencies = new osC_Currencies();

  $osC_Tax = new osC_Tax_Admin();

  $osC_Weight = new osC_Weight();

  $osC_GeoIP = osC_GeoIP_Admin::load();

  if ( $osC_GeoIP->isInstalled() ) {
    $osC_GeoIP->activate();
  }

  $xx_mins_ago = time() - 900;

// remove entries that have expired
  $Qdelete = $osC_Database->query('delete from :table_whos_online where time_last_click < :time_last_click');
  $Qdelete->bindTable(':table_whos_online', TABLE_WHOS_ONLINE);
  $Qdelete->bindValue(':time_last_click', $xx_mins_ago);
  $Qdelete->execute();
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }

  $Qwho = $osC_Database->query('select customer_id, full_name, ip_address, time_entry, time_last_click, session_id from :table_whos_online order by time_last_click desc');
  $Qwho->bindTable(':table_whos_online', TABLE_WHOS_ONLINE);
  $Qwho->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qwho->execute();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><?php echo $Qwho->getBatchTotalPages($osC_Language->get('batch_results_number_of_entries')); ?></td>
    <td align="right"><?php echo $Qwho->getBatchPageLinks('page', $osC_Template->getModule(), false); ?></td>
  </tr>
</table>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th width="22">&nbsp;</th>
      <th><?php echo $osC_Language->get('table_heading_online'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_customers'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_date_last_click'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_last_page_url'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_shopping_cart_total'); ?></th>
      <th width="150"><?php echo $osC_Language->get('table_heading_action'); ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th align="right" colspan="7"><?php echo '<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . $osC_Language->get('icon_trash') . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=batchDelete') . '\';" />'; ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </tfoot>
  <tbody>

<?php
  while ( $Qwho->next() ) {
    if (STORE_SESSIONS == 'database') {
      $Qsession = $osC_Database->query('select value from :table_sessions where id = :id');
      $Qsession->bindTable(':table_sessions', TABLE_SESSIONS);
      $Qsession->bindValue(':id', $Qwho->value('session_id'));
      $Qsession->execute();

      $session_data = trim($Qsession->value('value'));
    } else {
      if ( file_exists(OSCOM_Registry::get('Session')->getSavePath() . '/sess_' . $Qwho->value('session_id')) && ( filesize(OSCOM_Registry::get('Session')->getSavePath() . '/sess_' . $Qwho->value('session_id')) > 0 ) ) {
        $session_data = trim(file_get_contents(OSCOM_Registry::get('Session')->getSavePath() . '/sess_' . $Qwho->value('session_id')));
      }
    }

    $navigation = unserialize(osc_get_serialized_variable($session_data, 'osC_NavigationHistory_data', 'array'));
    $last_page = end($navigation);

    $currency = unserialize(osc_get_serialized_variable($session_data, 'currency', 'string'));

    $cart = unserialize(osc_get_serialized_variable($session_data, 'osC_ShoppingCart_data', 'array'));
?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
      <td align="center">

<?php
    if ( $osC_GeoIP->isActive() && $osC_GeoIP->isValid($Qwho->value('ip_address')) ) {
      echo osc_image('../images/worldflags/' . $osC_GeoIP->getCountryISOCode2($Qwho->value('ip_address')) . '.png', $osC_GeoIP->getCountryName($Qwho->value('ip_address')) . ', ' . $Qwho->value('ip_address'), 18, 12);
    } else {
      echo osc_image('images/pixel_trans.gif', $Qwho->value('ip_address'), 18, 12);
    }
?>

      </td>
      <td><?php echo gmdate('H:i:s', time() - $Qwho->value('time_entry')); ?></td>
      <td><?php echo $Qwho->value('full_name') . ' (' . $Qwho->valueInt('customer_id') . ')'; ?></td>
      <td><?php echo date('H:i:s', $Qwho->value('time_last_click')); ?></td>
      <td><?php echo $last_page['page']; ?></td>
      <td><?php echo $osC_Currencies->format($cart['total_cost'], true, $currency); ?></td>
      <td align="right">

<?php
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&info=' . $Qwho->value('session_id') . '&action=info'), osc_icon('info.png')) . '&nbsp;' .
        osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&info=' . $Qwho->value('session_id') . '&action=delete'), osc_icon('trash.png'));
?>

      </td>
      <td align="center"><?php echo osc_draw_checkbox_field('batch[]', $Qwho->value('session_id'), null, 'id="batch' . $Qwho->value('session_id') . '"'); ?></td>
    </tr>

<?php
  }
?>

  </tbody>
</table>

</form>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td style="opacity: 0.5; filter: alpha(opacity=50);"><?php echo '<b>' . $osC_Language->get('table_action_legend') . '</b> ' . osc_icon('info.png') . '&nbsp;' . $osC_Language->get('icon_info') . '&nbsp;&nbsp;' . osc_icon('trash.png') . '&nbsp;' . $osC_Language->get('icon_trash'); ?></td>
    <td align="right"><?php echo $Qwho->getBatchPagesPullDownMenu('page', $osC_Template->getModule()); ?></td>
  </tr>
</table>

<?php
  if ( $osC_GeoIP->isActive() ) {
    $osC_GeoIP->deactivate();
  }
?>
