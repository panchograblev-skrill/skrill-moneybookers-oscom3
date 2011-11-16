<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Reviews_Admin::getData($_GET['rID']));

  $rating_array = array();

  for ($i=1; $i<=5; $i++) {
    $rating_array[] = array('id' => $i, 'text' => '');
  }
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('edit.png') . ' ' . $osC_ObjectInfo->get('products_name'); ?></div>
<div class="infoBoxContent">
  <form name="review" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&rID=' . $_GET['rID'] . '&action=save'); ?>" method="post">

  <p><?php echo '<b>' . $osC_Language->get('field_product') . '</b><br />' . $osC_ObjectInfo->get('products_name'); ?></p>
  <p><?php echo '<b>' . $osC_Language->get('field_author') . '</b><br />' . osc_output_string_protected($osC_ObjectInfo->get('customers_name')); ?></p>
  <p><?php echo '<b>' . $osC_Language->get('field_date_added') . '</b><br />' . osC_DateTime::getShort($osC_ObjectInfo->get('date_added')); ?></p>
  <p><?php echo '<b>' . $osC_Language->get('field_review') . '</b><br />' . osc_draw_textarea_field('reviews_text', $osC_ObjectInfo->get('reviews_text')); ?></p>
  <p><?php echo '<b>' . $osC_Language->get('field_rating') . '</b><br />' . $osC_Language->get('rating_bad') . '&nbsp;' . osc_draw_radio_field('reviews_rating', $rating_array, $osC_ObjectInfo->get('reviews_rating')) . '&nbsp;' . $osC_Language->get('rating_good'); ?></p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
