<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $categories_array = array();

  foreach ( $osC_CategoryTree->getArray() as $value ) {
    $categories_array[] = array('id' => end(explode('_', $value['id'])),
                                'text' => $value['title']);
  }
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->exists($osC_Template->getModule()) ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div style="padding-bottom: 10px;">
  <span><form id="liveSearchForm"><input type="text" id="liveSearchField" name="search" class="searchField fieldTitleAsDefault" title="Search.." /><input type="button" value="Reset" class="operationButton" onclick="osC_DataTable.reset();" /></form></span>
  <span><form name="filter" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT); ?>" method="get"><?php echo osc_draw_hidden_field($osC_Template->getModule()); ?><?php echo osc_draw_pull_down_menu('cID', array_merge(array(array('id' => '', 'text' => $osC_Language->get('top_category'))), $categories_array)); ?><input type="submit" value="Filter" class="operationButton" /></form></span>
  <span style="float: right;"><?php echo '<input type="button" value="' . $osC_Language->get('button_insert') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&cID=' . $current_category_id . '&action=save') . '\';" class="infoBoxButton" />'; ?></span>
</div>

<div style="padding: 2px; height: 16px;">
  <span id="batchTotalPages"></span>
  <span id="batchPageLinks"></span>
</div>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable" id="productsDataTable">
  <thead>
    <tr>
      <th><?php echo $osC_Language->get('table_heading_products'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_price'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_quantity'); ?></th>
      <th width="150"><?php echo $osC_Language->get('table_heading_action'); ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th align="right" colspan="4"><?php echo '<input type="image" src="' . osc_icon_raw('copy.png') . '" title="' . $osC_Language->get('icon_copy') . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&cID=' . $current_category_id . '&action=batch_copy') . '\';" />&nbsp;<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . $osC_Language->get('icon_trash') . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&cID=' . $current_category_id . '&action=batch_delete') . '\';" />'; ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </tfoot>
  <tbody>
  </tbody>
</table>

</form>

<div style="padding: 2px;">
  <span id="dataTableLegend"><?php echo '<b>' . $osC_Language->get('table_action_legend') . '</b> ' . osc_icon('edit.png') . '&nbsp;' . $osC_Language->get('icon_edit') . '&nbsp;&nbsp;' . osc_icon('copy.png') . '&nbsp;' . $osC_Language->get('icon_copy') . '&nbsp;&nbsp;' . osc_icon('trash.png') . '&nbsp;' . $osC_Language->get('icon_trash'); ?></span>
  <span id="batchPullDownMenu"></span>
</div>

<script type="text/javascript"><!--
  var moduleParamsCookieName = 'oscadmin_module_' + pageModule;

  var moduleParams = new Object();
  moduleParams.page = 1;
  moduleParams.search = '';

  if ( $.cookie(moduleParamsCookieName) != null ) {
    var p = $.secureEvalJSON($.cookie(moduleParamsCookieName));
    moduleParams.page = parseInt(p.page);
    moduleParams.search = String(p.search);
  }

  var dataTableName = 'productsDataTable';
  var dataTableDataURL = '<?php echo osc_href_link_admin('rpc.php', $osC_Template->getModule() . '&cID=' . $current_category_id . '&action=getAll'); ?>';

  var productLink = '<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=PRODUCTID&cID=' . $current_category_id . '&action=preview'); ?>';
  var productLinkIcon = '<?php echo osc_icon('products.png'); ?>';
  var productVariantLinkIcon = '<?php echo osc_icon('attach.png'); ?>';

  var productEditLink = '<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=PRODUCTID&cID=' . $current_category_id . '&action=save'); ?>';
  var productEditLinkIcon = '<?php echo osc_icon('edit.png'); ?>';

  var productCopyLink = '<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=PRODUCTID&cID=' . $current_category_id . '&action=copy'); ?>';
  var productCopyLinkIcon = '<?php echo osc_icon('copy.png'); ?>';

  var productDeleteLink = '<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=PRODUCTID&cID=' . $current_category_id . '&action=delete'); ?>';
  var productDeleteLinkIcon = '<?php echo osc_icon('trash.png'); ?>';

  var osC_DataTable = new osC_DataTable();
  osC_DataTable.load();

  function feedDataTable(data) {
    var rowCounter = 0;

    for ( var r in data.entries ) {
      var record = data.entries[r];

      var newRow = $('#' + dataTableName)[0].tBodies[0].insertRow(rowCounter);
      newRow.id = 'row' + parseInt(record.products_id);

      $('#row' + parseInt(record.products_id)).mouseover( function() { rowOverEffect(this); }).mouseout( function() { rowOutEffect(this); }).click(function(event) {
        if (event.target.type !== 'checkbox') {
          $(':checkbox', this).trigger('click');
        }
      }).css('cursor', 'pointer');

      if ( parseInt(record.has_children) == 1 ) {
        var useProductLinkIcon = productVariantLinkIcon;
      } else {
        var useProductLinkIcon = productLinkIcon;
      }

      var newCell = newRow.insertCell(0);
      newCell.innerHTML = '<a href="' + productLink.replace('PRODUCTID', parseInt(record.products_id)) + '">' + useProductLinkIcon + '&nbsp;' + htmlSpecialChars(record.products_name) + '</a>';

      newCell = newRow.insertCell(1);
      newCell.innerHTML = htmlSpecialChars(record.products_price_formatted);

      newCell = newRow.insertCell(2);
      newCell.innerHTML = htmlSpecialChars(record.products_quantity);

      newCell = newRow.insertCell(3);
      newCell.innerHTML = '<a href="' + productEditLink.replace('PRODUCTID', parseInt(record.products_id)) + '">' + productEditLinkIcon + '</a>&nbsp;<a href="' + productCopyLink.replace('PRODUCTID', parseInt(record.products_id)) + '">' + productCopyLinkIcon + '</a>&nbsp;<a href="' + productDeleteLink.replace('PRODUCTID', parseInt(record.products_id)) + '">' + productDeleteLinkIcon + '</a>';
      newCell.align = 'right';

      newCell = newRow.insertCell(4);
      newCell.innerHTML = '<input type="checkbox" name="batch[]" value="' + parseInt(record.products_id) + '" id="batch' + parseInt(record.products_id) + '" />';
      newCell.align = 'center';

      rowCounter++;
    }
  }
//--></script>
