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
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png') . ' ' . $osC_Language->get('action_heading_batch_delete_attribute_groups'); ?></div>
<div class="infoBoxContent">
  <form name="paDeleteBatch" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=batchDelete'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_batch_delete_attribute_groups'); ?></p>

<?php
  $check_products_flag = array();

  $Qgroups = $osC_Database->query('select id, title from :table_products_variants_groups where languages_id = :languages_id and id in (":id") order by title');
  $Qgroups->bindTable(':table_products_variants_groups', TABLE_PRODUCTS_VARIANTS_GROUPS);
  $Qgroups->bindInt(':languages_id', $osC_Language->getID());
  $Qgroups->bindRaw(':id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
  $Qgroups->execute();

  $names_string = '';

  while ( $Qgroups->next() ) {
    $Qproducts = $osC_Database->query('select count(*) as total_products from :table_products_variants pv, :table_products_variants_values pvv where pvv.products_variants_groups_id = :products_variants_groups_id and pvv.id = pv.products_variants_values_id');
    $Qproducts->bindTable(':table_products_variants', TABLE_PRODUCTS_VARIANTS);
    $Qproducts->bindTable(':table_products_variants_values', TABLE_PRODUCTS_VARIANTS_VALUES);
    $Qproducts->bindInt(':products_variants_groups_id', $Qgroups->valueInt('id'));
    $Qproducts->execute();

    if ( $Qproducts->valueInt('total_products') > 0 ) {
      $check_products_flag[] = $Qgroups->value('products_options_name');
    }

    $Qentries = $osC_Database->query('select count(*) as total_entries from :table_products_variants_values where products_variants_groups_id = :products_variants_groups_id');
    $Qentries->bindTable(':table_products_variants_values', TABLE_PRODUCTS_VARIANTS_VALUES);
    $Qentries->bindInt(':products_variants_groups_id', $Qgroups->valueInt('id'));
    $Qentries->execute();

    $group_name = $Qgroups->value('title');

    if ( $Qentries->valueInt('total_entries') > 0 ) {
      $group_name .= ' (' . sprintf($osC_Language->get('total_entries'), $Qentries->valueInt('total_entries')) . ')';
    }

    $names_string .= osc_draw_hidden_field('batch[]', $Qgroups->valueInt('id')) . '<b>' . $group_name . '</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2);
  }

  echo '<p>' . $names_string . '</p>';

  if ( empty($check_products_flag) ) {
?>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

<?php
  } else {
?>

  <p><b><?php echo $osC_Language->get('batch_delete_error_attribute_groups_in_use'); ?></b></p>

  <p><?php echo implode(', ', $check_products_flag); ?></p>

  <p align="center"><?php echo '<input type="button" value="' . $osC_Language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

<?php
  }
?>

  </form>
</div>
