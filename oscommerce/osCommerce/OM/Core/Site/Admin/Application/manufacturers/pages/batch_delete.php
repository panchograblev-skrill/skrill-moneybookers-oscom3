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

<div class="infoBoxHeading"><?php echo osc_icon('trash.png') . ' ' . $osC_Language->get('action_heading_batch_delete_manufacturers'); ?></div>
<div class="infoBoxContent">
  <form name="mDeleteBatch" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=batchDelete'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_batch_delete_manufacturers'); ?></p>

<?php
  $products_flag = false;

  $Qmanufacturers = $osC_Database->query('select manufacturers_id, manufacturers_name from :table_manufacturers where manufacturers_id in (":manufacturers_id") order by manufacturers_name');
  $Qmanufacturers->bindTable(':table_manufacturers', TABLE_MANUFACTURERS);
  $Qmanufacturers->bindRaw(':manufacturers_id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
  $Qmanufacturers->execute();

  $names_string = '';

  while ( $Qmanufacturers->next() ) {
    $Qproducts = $osC_Database->query('select count(*) as products_count from :table_products where manufacturers_id = :manufacturers_id');
    $Qproducts->bindTable(':table_products', TABLE_PRODUCTS);
    $Qproducts->bindInt(':manufacturers_id', $Qmanufacturers->valueInt('manufacturers_id'));
    $Qproducts->execute();

    $manufacturer_name = $Qmanufacturers->valueProtected('manufacturers_name');

    if ( $Qproducts->valueInt('products_count') > 0 ) {
      if ( $products_flag === false ) {
        $products_flag = true;
      }

      $manufacturer_name .= ' (' . sprintf($osC_Language->get('total_entries'), $Qproducts->valueInt('products_count')) . ')';
    }

    $names_string .= osc_draw_hidden_field('batch[]', $Qmanufacturers->valueInt('manufacturers_id')) . '<b>' . $manufacturer_name . '</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2);
  }

  echo '<p>' . $names_string . '</p>';
?>

  <p><?php echo osc_draw_checkbox_field('delete_image', null, true) . ' ' . $osC_Language->get('field_batch_delete_images'); ?></p>

<?php
  if ( $products_flag === true ) {
?>

  <p><?php echo osc_draw_checkbox_field('delete_products') . ' ' . $osC_Language->get('field_delete_products'); ?></p>

<?php
  }
?>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
