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

  $Qp = $osC_Database->query('select p.products_id, p.products_quantity, p.products_price, p.products_model, p.products_weight, p.products_weight_class, p.products_date_added, p.products_last_modified, p.products_status, p.products_tax_class_id, p.manufacturers_id, i.image from :table_products p left join :table_products_images i on (p.products_id = i.products_id and default_flag = :default_flag) where p.products_id = :products_id');
  $Qp->bindTable(':table_products', TABLE_PRODUCTS);
  $Qp->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
  $Qp->bindInt(':products_id', $_GET[$osC_Template->getModule()]);
  $Qp->bindInt(':default_flag', 1);
  $Qp->execute();

  $Qpd = $osC_Database->query('select products_name, products_description, products_url, language_id from :table_products_description where products_id = :products_id');
  $Qpd->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
  $Qpd->bindInt(':products_id', $_GET[$osC_Template->getModule()]);
  $Qpd->execute();

  $pd_extra = array();
  while ( $Qpd->next() ) {
    $pd_extra['products_name'][$Qpd->valueInt('language_id')] = $Qpd->valueProtected('products_name');
    $pd_extra['products_description'][$Qpd->valueInt('language_id')] = $Qpd->value('products_description');
    $pd_extra['products_url'][$Qpd->valueInt('language_id')] = $Qpd->valueProtected('products_url');
  }

  $osC_ObjectInfo = new osC_ObjectInfo(array_merge($Qp->toArray(), $pd_extra));

  $products_name = $osC_ObjectInfo->get('products_name');
  $products_description = $osC_ObjectInfo->get('products_description');
  $products_url = $osC_ObjectInfo->get('products_url');
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->exists($osC_Template->getModule()) ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div style="background-color: #fff3e7;">

<?php
  foreach ( $osC_Language->getAll() as $l ) {
    echo '<span id="lang_' . $l['code'] . '"' . (($l['code'] == $osC_Language->getCode()) ? ' class="highlight"' : '') . '><a href="javascript:toggleDivBlocks(\'pName_\', \'pName_' . $l['code'] . '\'); toggleClass(\'lang_\', \'lang_' . $l['code'] . '\', \'highlight\', \'span\');">' . $osC_Language->showImage($l['code']) . '</a></span>&nbsp;&nbsp;';
  }
?>

</div>

<?php
  foreach ( $osC_Language->getAll() as $l ) {
?>

<div id="pName_<?php echo $l['code']; ?>" <?php echo (($l['code'] != $osC_Language->getCode()) ? ' style="display: none;"' : ''); ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td><h1><?php echo osc_output_string_protected($products_name[$l['id']]) . (!osc_empty($osC_ObjectInfo->get('products_model')) ? '<br /><span>' . $osC_ObjectInfo->getProtected('products_model') . '</span>': ''); ?></h1></td>
      <td align="right"><h1><?php echo $osC_Currencies->format($osC_ObjectInfo->get('products_price')); ?></h1></td>
    </tr>
  </table>

  <p><?php echo $osC_Image->show($osC_ObjectInfo->get('image'), $products_name[$l['id']], 'align="right" hspace="5" vspace="5"', 'product_info') . $products_description[$l['id']]; ?></p>

<?php
    if ( !empty($products_url[$l['id']]) ) {
      echo '<p>' . sprintf($osC_Language->get('more_product_information'), osc_output_string_protected($products_url[$l['id']])) . '</p>';
    }
?>

<?php
// HPDL
//    if ($osC_ObjectInfo->get('products_date_available') > date('Y-m-d')) {
//      echo '<p align="center">' . sprintf($osC_Language->get('product_date_available'), osC_DateTime::getLong($osC_ObjectInfo->get('products_date_available'))) . '</p>';
//    } else {
      echo '<p align="center">' . sprintf($osC_Language->get('product_date_added'), osC_DateTime::getLong($osC_ObjectInfo->get('products_date_added'))) . '</p>';
//    }
?>

</div>

<?php
  }
?>

<p align="right"><?php echo '<input type="button" value="' . $osC_Language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&cID=' . $_GET['cID']) . '\';" class="operationButton" />'; ?></p>
