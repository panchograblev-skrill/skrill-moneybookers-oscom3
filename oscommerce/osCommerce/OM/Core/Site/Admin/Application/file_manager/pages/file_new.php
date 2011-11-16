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

  $writeable = true;

  if ( !is_writeable($_SESSION['fm_directory']) ) {
    $writeable = false;

    $osC_MessageStack->add($osC_Template->getModule(), sprintf($osC_Language->get('ms_error_directory_not_writable'), $_SESSION['fm_directory']), 'warning');
  }
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('new.png') . ' ' . $osC_Language->get('action_heading_new_file'); ?></div>
<div class="infoBoxContent">
  <form name="file_manager" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=save'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_new_file'); ?></p>

  <p><?php echo '<b>' . osc_output_string_protected($_SESSION['fm_directory']) . '</b>'; ?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_file_name') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('filename'); ?></td>
    </tr>
    <tr>
      <td width="40%" valign="top"><?php echo '<b>' . $osC_Language->get('field_file_contents') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_textarea_field('contents', null, 80, 20, 'style="width: 100%;"' . (($writeable === true) ? '' : ' readonly="readonly"')); ?></td>
    </tr>
  </table>

  <p align="center">

<?php
  if ( $writeable === true ) {
    echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" />';
  } else {
    echo '<input type="button" value="' . $osC_Language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" />';
  }
?>

  </p>

  </form>
</div>
