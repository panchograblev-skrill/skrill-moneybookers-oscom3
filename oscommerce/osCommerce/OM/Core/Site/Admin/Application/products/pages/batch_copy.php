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

  $categories_array = array(array('id' => '0',
                                  'text' => $osC_Language->get('top_category')));

  foreach ($osC_CategoryTree->getArray() as $value) {
    $categories_array[] = array('id' => $value['id'],
                                'text' => $value['title']);
  }
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->exists($osC_Template->getModule()) ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('copy.png') . ' ' . $osC_Language->get('action_heading_batch_copy_products'); ?></div>
<div class="infoBoxContent">
  <form name="pBatchCopy" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&cID=' . $_GET['cID'] . '&action=batch_copy'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_batch_copy_products'); ?></p>

<?php
  $Qproducts = $osC_Database->query('select products_id, products_name from :table_products_description where products_id in (":products_id") and language_id = :language_id order by products_name');
  $Qproducts->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
  $Qproducts->bindRaw(':products_id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
  $Qproducts->bindInt(':language_id', $osC_Language->getID());
  $Qproducts->execute();

  $names_string = '';

  while ($Qproducts->next()) {
    $names_string .= osc_draw_hidden_field('batch[]', $Qproducts->valueInt('products_id')) . '<b>' . $Qproducts->value('products_name') . '</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2) . osc_draw_hidden_field('subaction', 'confirm');
  }

  echo '<p>' . $names_string . '</p>';
?>

  <p><?php echo '<b>' . $osC_Language->get('field_categories') . '</b><br />' . osc_draw_pull_down_menu('new_category_id', $categories_array); ?></p>

  <p><?php echo '<b>' . $osC_Language->get('field_copy_method') . '</b><br />' . osc_draw_radio_field('copy_as', array(array('id' => 'link', 'text' => $osC_Language->get('copy_method_link')), array('id' => 'duplicate', 'text' => $osC_Language->get('copy_method_duplicate'))), 'link', null, '<br />'); ?></p>

  <p align="center"><?php echo '<input type="submit" value="' . $osC_Language->get('button_copy') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&cID=' . $_GET['cID']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
