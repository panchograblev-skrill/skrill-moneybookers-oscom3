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
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('new.png') . ' ' . $osC_Language->get('action_heading_new_group_entry'); ?></div>
<div class="infoBoxContent">
  <form name="paeNew" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page'] . '&action=saveEntry'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_new_group_entry'); ?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%" valign="top"><?php echo '<b>' . $osC_Language->get('field_group_entry_name') . '</b>'; ?></td>
      <td width="60%">

<?php
  foreach ( $osC_Language->getAll() as $l ) {
    echo $osC_Language->showImage($l['code']) . '&nbsp;' .  osc_draw_input_field('entry_name[' . $l['id'] . ']') . '<br />';
  }
?>

      </td>
    </tr>
    <tr>
      <td width="40%" valign="top"><?php echo '<b>' . $osC_Language->get('field_sort_order') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('sort_order'); ?></td>
    </tr>
  </table>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
