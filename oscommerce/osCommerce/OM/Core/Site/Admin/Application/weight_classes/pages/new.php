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
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('new.png') . ' ' . $osC_Language->get('action_heading_new_weight_class'); ?></div>
<div class="infoBoxContent">
  <form name="wcNew" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=save'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_new_weight_class'); ?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_title_and_code') . '</b>'; ?></td>
      <td width="60%">

<?php
  foreach ( $osC_Language->getAll() as $l ) {
    echo $osC_Language->showImage($l['code']) . '&nbsp;' . osc_draw_input_field('name[' . $l['id'] . ']') . osc_draw_input_field('key[' . $l['id'] . ']', null, 'size="4"') . '<br />';
  }
?>

      </td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_rules') . '</b>'; ?></td>
      <td width="60%">
        <table border="0" cellspacing="0" cellpadding="2">

<?php
  $Qrules = $osC_Database->query('select weight_class_id, weight_class_title from :table_weight_classes where language_id = :language_id order by weight_class_title');
  $Qrules->bindTable(':table_weight_classes', TABLE_WEIGHT_CLASS);
  $Qrules->bindInt(':language_id', $osC_Language->getID());
  $Qrules->execute();

  while ( $Qrules->next() ) {
?>

          <tr>
            <td><?php echo $Qrules->value('weight_class_title') . ':'; ?></td>
            <td><?php echo osc_draw_input_field('rules[' . $Qrules->valueInt('weight_class_id') . ']'); ?></td>
          </tr>

<?php
  }
?>

        </table>
      </td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_set_as_default') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_checkbox_field('default'); ?></td>
    </tr>
  </table>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
