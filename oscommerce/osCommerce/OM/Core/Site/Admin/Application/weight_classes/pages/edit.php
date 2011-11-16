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

  $osC_ObjectInfo = new osC_ObjectInfo(osC_WeightClasses_Admin::getData($_GET['wcID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('edit.png') . ' ' . $osC_ObjectInfo->get('weight_class_title'); ?></div>
<div class="infoBoxContent">
  <form name="wcEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&wcID=' . $osC_ObjectInfo->get('weight_class_id') . '&action=save'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_edit_weight_class'); ?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_title_and_code') . '</b>'; ?></td>
      <td width="60%">

<?php
  $Qwc = $osC_Database->query('select language_id, weight_class_key, weight_class_title from :table_weight_classes where weight_class_id = :weight_class_id');
  $Qwc->bindTable(':table_weight_classes', TABLE_WEIGHT_CLASS);
  $Qwc->bindInt(':weight_class_id', $osC_ObjectInfo->get('weight_class_id'));
  $Qwc->execute();

  $classes_array = array();

  while ( $Qwc->next() ) {
    $classes_array[$Qwc->valueInt('language_id')] = array('key' => $Qwc->value('weight_class_key'),
                                                          'title' => $Qwc->value('weight_class_title'));
  }

  foreach ( $osC_Language->getAll() as $l ) {
    echo $osC_Language->showImage($l['code']) . '&nbsp;' . osc_draw_input_field('name[' . $l['id'] . ']', $classes_array[$l['id']]['title']) . osc_draw_input_field('key[' . $l['id'] . ']', $classes_array[$l['id']]['key'], 'size="4"') . '<br />';
  }
?>

      </td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_rules') . '</b>'; ?></td>
      <td width="60%">
        <table border="0" cellspacing="0" cellpadding="2">

<?php
  $Qrules = $osC_Database->query('select r.weight_class_to_id, r.weight_class_rule, c.weight_class_title from :table_weight_classes_rules r, :table_weight_classes c where r.weight_class_from_id = :weight_class_from_id and r.weight_class_to_id != :weight_class_to_id and r.weight_class_to_id = c.weight_class_id and c.language_id = :language_id order by c.weight_class_title');
  $Qrules->bindTable(':table_weight_classes_rules', TABLE_WEIGHT_CLASS_RULES);
  $Qrules->bindTable(':table_weight_classes', TABLE_WEIGHT_CLASS);
  $Qrules->bindInt(':weight_class_from_id', $osC_ObjectInfo->get('weight_class_id'));
  $Qrules->bindInt(':weight_class_to_id', $osC_ObjectInfo->get('weight_class_id'));
  $Qrules->bindInt(':language_id', $osC_Language->getID());
  $Qrules->execute();

  while ( $Qrules->next() ) {
?>

          <tr>
            <td><?php echo $Qrules->value('weight_class_title') . ':'; ?></td>
            <td><?php echo osc_draw_input_field('rules[' . $Qrules->valueInt('weight_class_to_id') . ']', $Qrules->value('weight_class_rule')); ?></td>
          </tr>

<?php
  }
?>

        </table>
      </td>
    </tr>

<?php
  if ( $osC_ObjectInfo->get('weight_class_id') != SHIPPING_WEIGHT_UNIT ) {
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
