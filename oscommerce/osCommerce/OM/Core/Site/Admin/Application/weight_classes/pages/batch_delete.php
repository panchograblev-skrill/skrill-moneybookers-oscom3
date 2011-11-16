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
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png') . ' ' . $osC_Language->get('action_heading_batch_delete_weight_classes'); ?></div>
<div class="infoBoxContent">
  <form name="wcDeleteBatch" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=batchDelete'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_batch_delete_weight_classes'); ?></p>

<?php
  $check_default_flag = false;
  $check_products_flag = false;

  $Qclasses = $osC_Database->query('select weight_class_id, weight_class_title from :table_weight_class where weight_class_id in (":weight_class_id") and language_id = :language_id order by weight_class_title');
  $Qclasses->bindTable(':table_weight_class', TABLE_WEIGHT_CLASS);
  $Qclasses->bindRaw(':weight_class_id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
  $Qclasses->bindInt(':language_id', $osC_Language->getID());
  $Qclasses->execute();

  $names_string = '';

  while ( $Qclasses->next() ) {
    if ( $Qclasses->value('weight_class_id') == SHIPPING_WEIGHT_UNIT ) {
      $check_default_flag = true;
    }

    $Qproducts = $osC_Database->query('select count(*) as total from :table_products where products_weight_class = :products_weight_class');
    $Qproducts->bindTable(':table_products', TABLE_PRODUCTS);
    $Qproducts->bindInt(':products_weight_class', $Qclasses->valueInt('weight_class_id'));
    $Qproducts->execute();

    if ( $Qproducts->valueInt('total') > 0 ) {
      $check_products_flag = true;
    }

    $names_string .= osc_draw_hidden_field('batch[]', $Qclasses->valueInt('weight_class_id')) . '<b>' . $Qclasses->value('weight_class_title') . '</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2) . osc_draw_hidden_field('subaction', 'confirm');
  }

  echo '<p>' . $names_string . '</p>';

  if ( ( $check_default_flag === true ) || ( $check_products_flag === true ) ) {
    if ( $check_default_flag === true ) {
      echo '  <p><b>' . $osC_Language->get('batch_delete_error_weight_class_prohibited') . '</b></p>';
    }

    if ( $check_products_flag === true ) {
      echo '  <p><b>' . sprintf($osC_Language->get('batch_delete_error_weight_class_in_use'), $Qproducts->valueInt('total')) . '</b></p>';
    }

    echo '  <p align="center"><input type="button" value="' . $osC_Language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" /></p>';
  } else {
    echo '  <p align="center"><input type="submit" value="' . $osC_Language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" /></p>';
  }
?>

  </form>
</div>
