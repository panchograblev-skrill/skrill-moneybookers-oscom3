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

  include('../includes/modules/' . $_GET['set'] . '/' . $_GET['module'] . '.php');

  $module = 'osC_' . ucfirst($_GET['set']) . '_' . $_GET['module'];

  if ( call_user_func(array($module, 'isInstalled'), $_GET['module'], $_GET['set']) === false ) {
    $osC_Language->injectDefinitions('modules/' . $_GET['set'] . '/' . $_GET['module'] . '.xml');
  }

  $module = new $module();
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&set=' . $_GET['set']), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('info.png') . ' ' . $module->getTitle(); ?></div>
<div class="infoBoxContent">
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td><?php echo $osC_Language->get('field_title'); ?></td>
      <td><?php echo $module->getTitle(); ?></td>
    </tr>
    <tr>
      <td><?php echo $osC_Language->get('field_author'); ?></td>
      <td><?php echo $module->getAuthorName(); ?> (<?php echo $module->getAuthorAddress(); ?>)</td>
    </tr>
  </table>

  <p align="center"><?php echo '<input type="button" value="' . $osC_Language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&set=' . $_GET['set']) . '\';" class="operationButton" />'; ?></p>
</div>
