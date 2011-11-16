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

  $osC_ObjectInfo = new osC_ObjectInfo(osC_AdministratorsLog_Admin::getData($_GET['lID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png') . ' ' . $osC_ObjectInfo->get('user_name') . ' &raquo; ' . $osC_ObjectInfo->get('module_action') . ' &raquo; ' . $osC_ObjectInfo->get('module') . ' &raquo; ' . $osC_ObjectInfo->get('module_id'); ?></div>
<div class="infoBoxContent">
  <form name="lDelete" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&fm=' . $_GET['fm'] . '&fu=' . $_GET['fu'] . '&lID=' . $osC_ObjectInfo->get('id') . '&action=delete'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_delete_entry'); ?></p>

  <p><?php echo '<b>' . $osC_ObjectInfo->get('user_name') . ' &raquo; ' . $osC_ObjectInfo->get('module_action') . ' &raquo; ' . $osC_ObjectInfo->get('module') . ' &raquo; ' . $osC_ObjectInfo->get('module_id') . '</b>'; ?></p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&fm=' . $_GET['fm'] . '&fu=' . $_GET['fu']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
