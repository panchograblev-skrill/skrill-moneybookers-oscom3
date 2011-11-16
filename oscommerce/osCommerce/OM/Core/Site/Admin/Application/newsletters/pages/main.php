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
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<p align="right"><?php echo '<input type="button" value="' . $osC_Language->get('button_insert') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=save') . '\';" class="infoBoxButton" />'; ?></p>

<?php
  $Qnewsletters = $osC_Database->query('select newsletters_id, title, length(content) as content_length, module, date_added, date_sent, status, locked from :table_newsletters order by date_added desc');
  $Qnewsletters->bindTable(':table_newsletters', TABLE_NEWSLETTERS);
  $Qnewsletters->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qnewsletters->execute();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><?php echo $Qnewsletters->getBatchTotalPages($osC_Language->get('batch_results_number_of_entries')); ?></td>
    <td align="right"><?php echo $Qnewsletters->getBatchPageLinks('page', $osC_Template->getModule(), false); ?></td>
  </tr>
</table>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php echo $osC_Language->get('table_heading_newsletters'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_size'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_module'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_sent'); ?></th>
      <th width="150"><?php echo $osC_Language->get('table_heading_action'); ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th align="right" colspan="5"><?php echo '<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . $osC_Language->get('icon_trash') . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=batchDelete') . '\';" />'; ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </tfoot>
  <tbody>

<?php
  while ( $Qnewsletters->next() ) {
    $newsletter_module_class = 'osC_Newsletter_' . $Qnewsletters->value('module');

    if ( !class_exists($newsletter_module_class) ) {
      $osC_Language->loadIniFile('modules/newsletters/' . $Qnewsletters->value('module') . '.php');
      include('includes/modules/newsletters/' . $Qnewsletters->value('module') . '.php');

      $$newsletter_module_class = new $newsletter_module_class();
    }
?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
      <td><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&nID=' . $Qnewsletters->valueInt('newsletters_id') . '&action=preview'), osc_icon('newsletters.png') . '&nbsp;' . $Qnewsletters->value('title')); ?></td>
      <td align="right"><?php echo number_format($Qnewsletters->valueInt('content_length')); ?></td>
      <td align="right"><?php echo $$newsletter_module_class->getTitle(); ?></td>
      <td align="center"><?php echo osc_icon(($Qnewsletters->valueInt('status') === 1) ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif', null, null); ?></td>
      <td align="right">

<?php
    if ( $Qnewsletters->valueInt('status') === 1 ) {
      echo osc_image('images/pixel_trans.gif', '', '16', '16') . '&nbsp;' .
           osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&nID=' . $Qnewsletters->valueInt('newsletters_id') . '&action=log'), osc_icon('log.png')) . '&nbsp;';
    } else {
      echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&nID=' . $Qnewsletters->valueInt('newsletters_id') . '&action=save'), osc_icon('edit.png')) . '&nbsp;' .
           osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&nID=' . $Qnewsletters->valueInt('newsletters_id') . '&action=send'), osc_icon('email_send.png')) . '&nbsp;';
    }

    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&nID=' . $Qnewsletters->valueInt('newsletters_id') . '&action=delete') , osc_icon('trash.png'));
?>

      </td>
      <td align="center"><?php echo osc_draw_checkbox_field('batch[]', $Qnewsletters->valueInt('newsletters_id'), null, 'id="batch' . $Qnewsletters->valueInt('newsletters_id') . '"'); ?></td>
    </tr>

<?php
  }
?>

  </tbody>
</table>

</form>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td style="opacity: 0.5; filter: alpha(opacity=50);"><?php echo '<b>' . $osC_Language->get('table_action_legend') . '</b> ' . osc_icon('newsletters.png') . '&nbsp;' . $osC_Language->get('icon_newsletters') . '&nbsp;&nbsp;' . osc_icon('edit.png') . '&nbsp;' . $osC_Language->get('icon_edit') . '&nbsp;&nbsp;' . osc_icon('email_send.png') . '&nbsp;' . $osC_Language->get('icon_email_send') . '&nbsp;&nbsp;' . osc_icon('log.png') . '&nbsp;' . $osC_Language->get('icon_log') . '&nbsp;&nbsp;' . osc_icon('trash.png') . '&nbsp;' . $osC_Language->get('icon_trash'); ?></td>
    <td align="right"><?php echo $Qnewsletters->getBatchPagesPullDownMenu('page', $osC_Template->getModule()); ?></td>
  </tr>
</table>
