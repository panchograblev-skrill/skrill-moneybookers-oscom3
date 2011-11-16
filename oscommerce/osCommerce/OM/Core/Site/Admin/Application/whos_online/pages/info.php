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

  $osC_Currencies = new osC_Currencies();

  $osC_Tax = new osC_Tax_Admin();

  $osC_Weight = new osC_Weight();

  $osC_GeoIP = osC_GeoIP_Admin::load();

  if ( $osC_GeoIP->isInstalled() ) {
    $osC_GeoIP->activate();
  }

  $osC_ObjectInfo = new osC_ObjectInfo(osC_WhosOnline_Admin::getData($_GET['info']));

  if ( STORE_SESSIONS == 'database' ) {
    $Qsession = $osC_Database->query('select value from :table_sessions where id = :id');
    $Qsession->bindTable(':table_sessions', TABLE_SESSIONS);
    $Qsession->bindValue(':id', $osC_ObjectInfo->get('session_id'));
    $Qsession->execute();

    $session_data = trim($Qsession->value('value'));
  } else {
    if ( file_exists(OSCOM_Registry::get('Session')->getSavePath() . '/sess_' . $osC_ObjectInfo->get('session_id')) && ( filesize(OSCOM_Registry::get('Session')->getSavePath() . '/sess_' . $osC_ObjectInfo->get('session_id')) > 0 ) ) {
      $session_data = trim(file_get_contents(OSCOM_Registry::get('Session')->getSavePath() . '/sess_' . $osC_ObjectInfo->get('session_id')));
    }
  }

  $navigation = unserialize(osc_get_serialized_variable($session_data, 'osC_NavigationHistory_data', 'array'));
  $last_page = end($navigation);
  $last_page_url = $last_page['page'];

  if ( isset($last_page['get']['osCsid']) ) {
    unset($last_page['get']['osCsid']);
  }

  if ( sizeof($last_page['get']) > 0 ) {
    $last_page_url .= '?' . osc_array_to_string($last_page['get']);
  }

  $currency = unserialize(osc_get_serialized_variable($session_data, 'currency', 'string'));

  $cart = unserialize(osc_get_serialized_variable($session_data, 'osC_ShoppingCart_data', 'array'));

?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('info.png') . ' ' . $osC_ObjectInfo->get('full_name'); ?></div>
<div class="infoBoxContent">
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_session_id') . '</b>'; ?></td>
      <td width="60%"><?php echo $osC_ObjectInfo->get('session_id'); ?></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_time_online') . '</b>'; ?></td>
      <td width="60%"><?php echo gmdate('H:i:s', time() - $osC_ObjectInfo->get('time_entry')); ?></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_customer_id') . '</b>'; ?></td>
      <td width="60%"><?php echo $osC_ObjectInfo->get('customer_id'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_customer_name') . '</b>'; ?></td>
      <td width="60%"><?php echo $osC_ObjectInfo->get('full_name'); ?></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_ip_address') . '</b>'; ?></td>
      <td width="60%">

<?php
  echo $osC_ObjectInfo->get('ip_address');

  if ( $osC_GeoIP->isActive() && $osC_GeoIP->isValid($osC_ObjectInfo->get('ip_address')) ) {
    echo '<p>' . implode('<br />', $osC_GeoIP->getData($osC_ObjectInfo->get('ip_address'))) . '</p>';
  }
?>

      </td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_entry_time') . '</b>'; ?></td>
      <td width="60%"><?php echo date('H:i:s', $osC_ObjectInfo->get('time_entry')); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_last_click') . '</b>'; ?></td>
      <td width="60%"><?php echo date('H:i:s', $osC_ObjectInfo->get('time_last_click')); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_last_page_url') . '</b>'; ?></td>
      <td width="60%"><?php echo $last_page_url; ?></td>
    </tr>

<?php
  if ( !empty($cart['contents']) ) {
    echo '    <tr>' . "\n" .
         '      <td colspan="2">&nbsp;</td>' . "\n" .
         '    </tr>' . "\n" .
         '    <tr>' . "\n" .
         '      <td width="40%" valign="top"><b>' . $osC_Language->get('field_shopping_cart_contents') . '</b></td>' . "\n" .
         '      <td width="60%"><table border="0" cellspacing="0" cellpadding="2">' . "\n";

    foreach ($cart['contents'] as $product) {
      echo '        <tr>' . "\n" .
           '          <td align="right">' . $product['quantity'] . ' x</td>' . "\n" .
           '          <td>' . $product['name'] . '</td>' . "\n" .
           '        </tr>' . "\n";
    }

    echo '      </table></td>' . "\n" .
         '    </tr>' . "\n" .
         '    <tr>' . "\n" .
         '      <td width="40%"><b>' . $osC_Language->get('field_shopping_cart_total') . '</b></td>' . "\n" .
         '      <td width="60%">' . $osC_Currencies->format($cart['total_cost'], true, $currency) . '</td>' . "\n" .
         '    </tr>' . "\n";
  }
?>

  </table>

  <p align="center"><?php echo '<input type="button" value="' . $osC_Language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>
</div>

<?php
  if ( $osC_GeoIP->isActive() ) {
    $osC_GeoIP->deactivate();
  }
?>
