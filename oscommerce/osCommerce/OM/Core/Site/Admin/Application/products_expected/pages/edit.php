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

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Products_Admin::get($_GET['pID']));

  $Qdata = $osC_Database->query('select str_to_date(pa.value, "%Y-%m-%d") as products_date_available from :table_product_attributes pa, :table_templates_boxes tb where tb.code = :code and tb.modules_group = :modules_group and tb.id = pa.id');
  $Qdata->bindTable(':table_product_attributes', TABLE_PRODUCT_ATTRIBUTES);
  $Qdata->bindTable(':table_templates_boxes', TABLE_TEMPLATES_BOXES);
  $Qdata->bindValue(':code', 'date_available');
  $Qdata->bindValue(':modules_group', 'product_attributes');
  $Qdata->execute();

  $osC_ObjectInfo->set('products_date_available', $Qdata->value('products_date_available'));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->exists($osC_Template->getModule()) ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('edit.png') . ' ' . $osC_ObjectInfo->getProtected('products_name'); ?></div>
<div class="infoBoxContent">
  <form name="pEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&pID=' . $osC_ObjectInfo->getInt('products_id') . '&action=save'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_edit_product_expected'); ?></p>

  <p><?php echo $osC_Language->get('field_date_expected') . '<br />' . osc_draw_input_field('products_date_available', $osC_ObjectInfo->get('products_date_available')); ?></p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>

<script type="text/javascript">
  $(function() {
    $("#products_date_available").datepicker( {
      dateFormat: 'yy-mm-dd',
      changeMonth: true,
      changeYear: true
    } );
  });
</script>
