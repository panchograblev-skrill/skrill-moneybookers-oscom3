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

  $specials_array = array();

  $Qspecials = $osC_Database->query('select p.products_id, p.products_price, p.products_tax_class_id, pd.products_name, s.specials_new_products_price from :table_products p left join :table_specials s on (p.products_id = s.products_id), :table_products_description pd where p.products_id = pd.products_id and pd.language_id = :language_id and p.has_children = 0 order by pd.products_name');
  $Qspecials->bindTable(':table_products', TABLE_PRODUCTS);
  $Qspecials->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
  $Qspecials->bindTable(':table_specials', TABLE_SPECIALS);
  $Qspecials->bindInt(':language_id', $osC_Language->getID());
  $Qspecials->execute();

  while ( $Qspecials->next() ) {
    if ( $Qspecials->valueDecimal('specials_new_products_price') < 1 ) {
      $specials_array[] = array('id' => $Qspecials->valueInt('products_id'),
                                'text' => $Qspecials->value('products_name') . ' (' . $osC_Currencies->format($Qspecials->value('products_price')) . ')',
                                'tax_class_id' => $Qspecials->valueInt('products_tax_class_id'));
    }
  }

  $Qtc = $osC_Database->query('select tax_class_id, tax_class_title from :table_tax_class order by tax_class_title');
  $Qtc->bindTable(':table_tax_class', TABLE_TAX_CLASS);
  $Qtc->execute();

  $tax_class_array = array();

  while ( $Qtc->next() ) {
    $tax_class_array[] = array('id' => $Qtc->valueInt('tax_class_id'),
                               'text' => $Qtc->value('tax_class_title'));
  }
?>

<script type="text/javascript"><!--
  var product_tax = new Array();
  var tax_rates = new Array();

<?php
  foreach ( $specials_array as $specials ) {
    echo '  product_tax["' . $specials['id'] . '"] = ' . $specials['tax_class_id'] . ';' . "\n";
  }

  foreach ( $tax_class_array as $tc_entry ) {
    echo '  tax_rates["' . $tc_entry['id'] . '"] = ' . $osC_Tax->getTaxRate($tc_entry['id']) . ';' . "\n";
  }
?>

  function pad(s) {
    s = s||'.';

    return (s.length>4) ? s : pad(s+'0');
  }

  function doRound(x, places) {
    return (new String(Math.round(x * Math.pow(10, places)) / Math.pow(10, places))).replace(/(\.\d*)?$/, pad);
  }

  function getTaxRate() {
    var selected_value = document.forms["special"].products_id.selectedIndex;
    var products_id = document.forms["special"].products_id[selected_value].value;
    var tax_class = product_tax[products_id];

    if ( (tax_class > 0) && (tax_rates[tax_class] > 0) ) {
      return tax_rates[tax_class];
    } else {
      return 0;
    }
  }

  function updateGross(field, evt) {
    if (evt.keyCode == 9) {
      return false;
    }

    if ((document.getElementById(field).value).indexOf('%') > -1) {
      document.getElementById(field + "_gross").value = '';
      document.getElementById(field + "_gross").disabled = true;
      return false;
    } else if (document.getElementById(field + "_gross").disabled == true) {
      document.getElementById(field + "_gross").disabled = false;
    }

    var taxRate = getTaxRate();
    var grossValue = document.getElementById(field).value;

    if (taxRate > 0) {
      grossValue = grossValue * ((taxRate / 100) + 1);
    }

    document.getElementById(field + "_gross").value = doRound(grossValue, 4);
  }

  function updateNet(field, evt) {
    if (evt.keyCode == 9) {
      return false;
    }

    if ((document.getElementById(field + "_gross").value).indexOf('%') > -1) {
      document.getElementById(field).value = document.getElementById(field + "_gross").value;
      document.getElementById(field + "_gross").value = '';
      document.getElementById(field).focus();
      document.getElementById(field + "_gross").disabled = true;
      return false;
    }

    var taxRate = getTaxRate();
    var netValue = document.getElementById(field + "_gross").value;

    if (taxRate > 0) {
      netValue = netValue / ((taxRate / 100) + 1);
    }

    document.getElementById(field).value = doRound(netValue, 4);
  }
//--></script>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('new.png') . ' ' . $osC_Language->get('action_heading_new_special'); ?></div>
<div class="infoBoxContent">
  <form name="special" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=save'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_new_special'); ?></p>

  <p><?php echo '<b>' . $osC_Language->get('field_product') . '</b><br />' . osc_draw_pull_down_menu('products_id', $specials_array); ?></p>
  <p><?php echo '<b>' . $osC_Language->get('field_price_net_percentage') . '</b><br />' . osc_draw_input_field('specials_price', null, 'onkeyup="updateGross(\'specials_price\', event)"'); ?></p>
  <p><?php echo '<b>' . $osC_Language->get('field_price_gross') . '</b><br />' . osc_draw_input_field('specials_price_gross', null, 'onkeyup="updateNet(\'specials_price\', event)"'); ?></p>
  <p><?php echo '<b>' . $osC_Language->get('field_status') . '</b><br />' . osc_draw_checkbox_field('specials_status', '1'); ?></p>
  <p><?php echo '<b>' . $osC_Language->get('field_date_start') . '</b><br />' . osc_draw_input_field('specials_start_date'); ?></p>
  <p><?php echo '<b>' . $osC_Language->get('field_date_expires') . '</b><br />' . osc_draw_input_field('specials_expires_date'); ?></p>

<script type="text/javascript"><!--
  updateGross('specials_price', false);

  $(function() {
    $("#specials_start_date").datepicker( {
      dateFormat: 'yy-mm-dd',
      changeMonth: true,
      changeYear: true
    } );

    $("#specials_expires_date").datepicker( {
      dateFormat: 'yy-mm-dd',
      changeMonth: true,
      changeYear: true
    } );
  });
//--></script>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
