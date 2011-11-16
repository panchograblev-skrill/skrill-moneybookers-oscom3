<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  include('includes/modules/statistics/' . $_GET['module'] . '.php');

  $class = 'osC_Statistics_' . str_replace(' ', '_', ucwords(str_replace('_', ' ', $_GET['module'])));

  $osC_Statistics = new $class();
  $osC_Statistics->activate();
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle() . ': ' . $osC_Statistics->getTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<p align="right"><?php echo '<input type="button" value="' . $osC_Language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton">'; ?></p>

<?php
  if ( $osC_Statistics->isBatchQuery() ) {
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><?php echo $osC_Statistics->getBatchTotalPages($osC_Language->get('batch_results_number_of_entries')); ?></td>
    <td align="right"><?php echo $osC_Statistics->getBatchPageLinks('page', $osC_Template->getModule() . '&module=' . $_GET['module'], false); ?></td>
  </tr>
</table>

<?php
  }
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>

<?php
  foreach ( $osC_Statistics->getHeader() as $header ) {
    echo '      <th>' . $header . '</th>' . "\n";
  }
?>

    </tr>
  </thead>
  <tfoot>
    <tr>
      <th colspan="<?php echo sizeof($osC_Statistics->getHeader()); ?>">&nbsp;</th>
    </tr>
  </tfoot>
  <tbody>

<?php
  foreach ( $osC_Statistics->getData() as $data ) {
    if ( !isset($columns) ) {
      $columns = sizeof($data);
    }
?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">

<?php
    for ( $i = 0; $i < $columns; $i++ ) {
      echo '      <td>' . $data[$i] . '</td>' . "\n";
    }
?>

    </tr>

<?php
  }
?>

  </tbody>
</table>

<?php
  if ( $osC_Statistics->isBatchQuery() ) {
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td align="right"><?php echo $osC_Statistics->getBatchPagesPullDownMenu('page', $osC_Template->getModule() . '&module=' , $_GET['module']); ?></td>
  </tr>
</table>

<?php
  }
?>
