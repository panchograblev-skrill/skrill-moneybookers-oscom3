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

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Newsletters_Admin::getData($_GET['nID']));

  $osC_Language->loadIniFile('modules/newsletters/' . $osC_ObjectInfo->get('module') . '.php');
  include('includes/modules/newsletters/' . $osC_ObjectInfo->get('module') . '.php');

  $module_name = 'osC_Newsletter_' . $osC_ObjectInfo->get('module');
  $module = new $module_name($osC_ObjectInfo->get('title'), $osC_ObjectInfo->get('content'), $osC_ObjectInfo->get('newsletters_id'));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<?php
  if ( !isset($_POST['subaction']) ) {
    if ( $module->hasAudienceSelection() ) {
      echo $module->showAudienceSelectionForm();
    } else {
      echo $module->showConfirmation();
    }
  } elseif ( $_POST['subaction'] == 'confirm' ) {
    echo $module->showConfirmation();
  } elseif ( $_POST['subaction'] == 'execute' ) {
?>

<p><?php echo osc_image('images/ani_send_email.gif'); ?></p>

<p><?php echo '<b>' . $osC_Language->get('sending_please_wait') . '</b>'; ?></p>

<?php
    flush();

    $module->sendEmail();
?>

<p><font color="#ff0000"><b><?php echo $osC_Language->get('sending_finalized'); ?></b></font></p>

<p align="right"><?php echo '<input type="button" value="' . $osC_Language->get('button_ok') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

<?php
  }
?>
