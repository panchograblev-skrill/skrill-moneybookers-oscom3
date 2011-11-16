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

  include('includes/modules/shipping/' . $_GET['module'] . '.php');

  $osC_Language->injectDefinitions('modules/shipping/' . $_GET['module'] . '.xml');

  $module = 'osC_Shipping_' . $_GET['module'];
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
  <form name="mEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&module=' . $module->getCode() . '&action=save'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_edit_shipping_module'); ?></p>

<?php
  $keys = '';

  foreach ( $module->getKeys() as $key ) {
    $Qkey = $osC_Database->query('select configuration_title, configuration_value, configuration_description, use_function, set_function from :table_configuration where configuration_key = :configuration_key');
    $Qkey->bindTable(':table_configuration', TABLE_CONFIGURATION);
    $Qkey->bindValue(':configuration_key', $key);
    $Qkey->execute();

    $keys .= '<b>' . $Qkey->value('configuration_title') . '</b><br />' . $Qkey->value('configuration_description') . '<br />';

    if ( !osc_empty($Qkey->value('set_function')) ) {
      $keys .= osc_call_user_func($Qkey->value('set_function'), $Qkey->value('configuration_value'), $key);
    } else {
      $keys .= osc_draw_input_field('configuration[' . $key . ']', $Qkey->value('configuration_value'));
    }

    $keys .= '<br /><br />';
  }

  $keys = substr($keys, 0, strrpos($keys, '<br /><br />'));
?>

  <p><?php echo $keys; ?></p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
