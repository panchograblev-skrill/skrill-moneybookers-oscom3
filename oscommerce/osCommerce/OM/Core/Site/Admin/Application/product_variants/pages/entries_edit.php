<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_ProductVariants_Admin::getEntry($_GET['paeID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->exists($osC_Template->getModule()) ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('edit.png') . ' ' . $osC_ObjectInfo->getProtected('title'); ?></div>
<div class="infoBoxContent">
  <form name="paeEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page'] . '&paeID=' . $osC_ObjectInfo->getInt('id') . '&action=saveEntry'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_edit_group_entry'); ?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%" valign="top"><?php echo '<b>' . $osC_Language->get('field_group_entry_name') . '</b>'; ?></td>
      <td width="60%">

<?php
  $Qed = $osC_Database->query('select languages_id, title from :table_products_variants_values where id = :id');
  $Qed->bindTable(':table_products_variants_values', TABLE_PRODUCTS_VARIANTS_VALUES);
  $Qed->bindInt(':id', $osC_ObjectInfo->getInt('id'));
  $Qed->execute();

  $entry_names = array();

  while ( $Qed->next() ) {
    $entry_names[$Qed->valueInt('languages_id')] = $Qed->value('title');
  }

  foreach ( $osC_Language->getAll() as $l ) {
    echo $osC_Language->showImage($l['code']) . '&nbsp;' .  osc_draw_input_field('entry_name[' . $l['id'] . ']', (isset($entry_names[$l['id']]) ? $entry_names[$l['id']] : null)) . '<br />';
  }
?>

      </td>
    </tr>
    <tr>
      <td width="40%" valign="top"><?php echo '<b>' . $osC_Language->get('field_sort_order') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('sort_order', $osC_ObjectInfo->get('sort_order')); ?></td>
    </tr>
  </table>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
