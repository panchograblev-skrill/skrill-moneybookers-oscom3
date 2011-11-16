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

  include('includes/modules/image/' . $_GET['module'] . '.php');

  $class = 'osC_Image_Admin_' . $_GET['module'];

  $osC_Images = new $class();
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle() . ': ' . $osC_Images->getTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<p align="right"><?php echo '<input type="button" value="' . $osC_Language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" />'; ?></p>

<?php
  if ( !isset($_POST['subaction']) && $osC_Images->hasParameters() ) {
?>

<div class="infoBoxHeading"><?php echo osc_icon('edit.png') . ' ' . $osC_Images->getTitle(); ?></div>
<div class="infoBoxContent">
  <form name="iEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&module=' . $osC_Images->getModuleCode()); ?>" method="post">

  <p><?php echo $osC_Images->getTitle(); ?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
    foreach ( $osC_Images->getParameters() as $params ) {
?>

    <tr>
      <td width="40%"><?php echo '<b>' . $params['key'] . '</b>'; ?></td>
      <td width="60%"><?php echo $params['field']; ?></td>
    </tr>

<?php
    }
?>

  </table>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_execute') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>

<?php
  } else {
    $osC_Images->activate();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>

<?php
    foreach ( $osC_Images->getHeader() as $header ) {
      echo '      <th>' . $header . '</th>';
    }
?>

    </tr>
  </thead>
  <tfoot>
    <tr>
      <th colspan="<?php echo sizeof($osC_Images->getHeader()); ?>">&nbsp;</th>
    </tr>
  </tfoot>
  <tbody>

<?php
    foreach ( $osC_Images->getData() as $data ) {
      if ( !isset($columns) ) {
        $columns = sizeof($data);
      }

      echo '    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">';

      for ( $i = 0; $i < $columns; $i++ ) {
        echo '      <td>' . $data[$i] . '</td>';
      }

      echo '    </tr>';
    }
?>

  </tbody>
</table>

<?php
  }
?>
