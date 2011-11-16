<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2009 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_ProductTypes_Admin::get($_GET['tID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->exists($osC_Template->getModule()) ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBox">
  <h3><?php echo osc_icon('trash.png') . ' ' . $osC_ObjectInfo->getProtected('title'); ?></h3>

  <form name="tDelete" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&tID=' . $osC_ObjectInfo->getInt('id') . '&action=delete'); ?>" method="post">

<?php
  if ( $osC_ObjectInfo->getInt('total_products') > 0 ) {
?>

  <p><?php echo '<b>' . sprintf(OSCOM::getDef('delete_error_product_type_in_use'), $osC_ObjectInfo->getInt('total_products')) . '</b>'; ?></p>

  <p><?php echo osc_draw_button(array('href' => osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), 'icon' => 'triangle-1-w', 'title' => OSCOM::getDef('button_back'))); ?></p>

<?php
  } else {
    $type_name = $osC_ObjectInfo->getProtected('title');

    if ( $osC_ObjectInfo->getInt('total_assignments') > 0 ) {
      $type_name .= ' (' . sprintf(OSCOM::getDef('total_assignments'), $osC_ObjectInfo->getInt('total_assignments')) . ')';
    }
?>

  <p><?php echo OSCOM::getDef('introduction_delete_product_type'); ?></p>

  <p><?php echo '<b>' . $type_name . '</b>'; ?></p>

  <p><?php echo osc_draw_hidden_field('subaction', 'confirm') . osc_draw_button(array('priority' => 'primary', 'icon' => 'trash', 'title' => OSCOM::getDef('button_delete'))) . ' ' . osc_draw_button(array('href' => osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

<?php
  }
?>

  </form>
</div>
