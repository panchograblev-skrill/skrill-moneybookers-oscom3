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

  include('includes/templates/' . $_GET['template'] . '.php');

  $module = 'osC_Template_' . $_GET['template'];
  $module = new $module();
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('edit.png') . ' ' . $module->getTitle(); ?></div>
<div class="infoBoxContent">
  <form name="tEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&template=' . $module->getCode() . '&action=save'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_edit_template'); ?></p>

<?php
  $keys = '';

  foreach ( $module->getKeys() as $key => $value ) {
    $keys .= '<b>' . $value['title'] . '</b><br />' . $value['description'] . '<br />';

    if ( !empty($value['set_function']) ) {
      $keys .= osc_call_user_func($value['set_function'], $value['value'], $key);
    } else {
      $keys .= osc_draw_input_field('configuration[' . $key . ']', $value['value']);
    }

    $keys .= '<br /><br />';
  }

  $keys = substr($keys, 0, strrpos($keys, '<br /><br />'));
?>

  <p><?php echo $keys; ?></p>

<?php
  if ( $module->getCode() != DEFAULT_TEMPLATE ) {
?>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_set_as_default') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_checkbox_field('default'); ?></td>
    </tr>
  </table>

<?php
  }
?>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
