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

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Products_Admin::get($_GET[$osC_Template->getModule()]));

  $in_categories = array();

  $Qcategories = $osC_Database->query('select categories_id from :table_products_to_categories where products_id = :products_id');
  $Qcategories->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
  $Qcategories->bindInt(':products_id', $osC_ObjectInfo->getInt('products_id'));
  $Qcategories->execute();

  while ( $Qcategories->next() ) {
    $in_categories[] = $Qcategories->valueInt('categories_id');
  }

  $in_categories_path = '';

  foreach ( $in_categories as $category_id ) {
    $in_categories_path .= $osC_CategoryTree->getPath($category_id, 0, ' &raquo; ') . '<br />';
  }

  if ( !empty($in_categories_path) ) {
    $in_categories_path = substr($in_categories_path, 0, -6);
  }

  $categories_array = array(array('id' => '0',
                                  'text' => $osC_Language->get('top_category')));

  foreach ( $osC_CategoryTree->getArray() as $value ) {
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

<div class="infoBoxHeading"><?php echo osc_icon('copy.png') . ' ' . $osC_ObjectInfo->getProtected('products_name'); ?></div>
<div class="infoBoxContent">
  <form name="pCopy" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $osC_ObjectInfo->get('products_id') . '&cID=' . $_GET['cID'] . '&action=copy'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_copy_product'); ?></p>

  <fieldset>
    <p><?php echo '<b>' . $osC_Language->get('field_current_categories') . '</b><br />' . $in_categories_path; ?></p>

    <div><label for="new_category_id"><?php echo $osC_Language->get('field_categories'); ?></label><?php echo osc_draw_pull_down_menu('new_category_id', $categories_array); ?></div>
    <div><label for="copy_as"><?php echo $osC_Language->get('field_copy_method'); ?></label><?php echo osc_draw_radio_field('copy_as', array(array('id' => 'link', 'text' => $osC_Language->get('copy_method_link')), array('id' => 'duplicate', 'text' => $osC_Language->get('copy_method_duplicate'))), 'link', null, '<br />'); ?></div>
  </fieldset>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_copy') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&cID=' . $_GET['cID']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
