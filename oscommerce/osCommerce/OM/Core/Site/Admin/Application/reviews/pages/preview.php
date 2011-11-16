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
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<p align="right"><?php echo '<input type="button" value="' . $osC_Language->get('button_back') . '" class="operationButton" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';">'; ?></p>

<p><?php echo '<b>' . $osC_Language->get('field_product') . '</b> ' . $osC_ObjectInfo->get('products_name') . '<br /><b>' . $osC_Language->get('field_author') . '</b> ' . osc_output_string_protected($osC_ObjectInfo->get('customers_name')) . '<br /><br /><b>' . $osC_Language->get('field_date_added') . '</b> ' . osC_DateTime::getShort($osC_ObjectInfo->get('date_added')); ?></p>

<p><?php echo '<b>' . $osC_Language->get('field_review') . '</b><br />' . nl2br(osc_output_string_protected($osC_ObjectInfo->get('reviews_text'))); ?></p>

<p><?php echo '<b>' . $osC_Language->get('field_rating') . '</b>&nbsp;' . osc_image('../images/stars_' . $osC_ObjectInfo->get('reviews_rating') . '.png', sprintf($osC_Language->get('rating_from_5_stars'), $osC_ObjectInfo->get('reviews_rating'))) . '&nbsp;[' . sprintf($osC_Language->get('rating_from_5_stars'), $osC_ObjectInfo->get('reviews_rating')) . ']'; ?></p>

<?php
  if ( defined('SERVICE_REVIEW_ENABLE_MODERATION') && (SERVICE_REVIEW_ENABLE_MODERATION != -1) ) {
    echo '<p align="right"><input type="button" value="' . $osC_Language->get('button_approve') . '" class="operationButton" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&rID=' . $_GET['rID'] . '&action=rApprove') . '\';"> <input type="button" value="' . $osC_Language->get('button_reject') . '" class="operationButton" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&rID=' . $_GET['rID'] . '&action=rReject') . '\';"></p>';
  }
?>
