<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2009 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_ProductTypes_Admin::getAssignments($_GET[$osC_Template->getModule()], $_GET['aID']));

  $modules_array = array();

  foreach ( osC_ProductTypes_Admin::getModules() as $module ) {
    $modules_array[$module['id']] = $module['title'];
  }

  $activated_modules_array = array();

  foreach ( $osC_ObjectInfo->get('modules') as $module ) {
    $activated_modules_array[] = $module['module'];
  }
?>

<style type="text/css">
#modulesInstalled, #modulesAvailable {
  list-style-type: none;
  margin-left: 15px;
  padding: 10px 5px;
  width: 60%;
}

#modulesInstalled {
  border: 1px dashed #4F8A10;
  background-color: #DFF2BF;
}

#modulesAvailable {
  border: 1px dashed #D8000C;
  background-color: #FFBABA;
}

#modulesInstalled li, #modulesAvailable li {
  margin: 0 3px 3px 3px;
  padding: 4px;
  padding-left: 20px;
  height: 18px;
  text-align: left;
}
</style>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->exists($osC_Template->getModule()) ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBox">
  <h3><?php echo osc_icon('edit.png') . ' ' . $osC_ObjectInfo->getProtected('action_title'); ?></h3>

  <form name="tEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . (int)$_GET[$osC_Template->getModule()] . '&aID=' . $osC_ObjectInfo->get('action') . '&action=entry_save'); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_edit_assignments'); ?></p>

  <fieldset id="containment">
    <p><label>Active Modules:</label><ul id="modulesInstalled" class="connectedList">

<?php
  foreach ( $activated_modules_array as $id ) {
    echo '<li id="' . $id . '" class="ui-state-default fg-button fg-button-icon-left" onmouseover="$(this).addClass(\'ui-state-highlight\');" onmouseout="$(this).removeClass(\'ui-state-highlight\');"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>' . $modules_array[$id] . '</li>';
  }
?>

    </ul></p>

    <p><label>Available Modules:</label><ul id="modulesAvailable" class="connectedList">

<?php
  foreach ( $modules_array as $id => $title ) {
    if ( !in_array($id, $activated_modules_array) ) {
      echo '<li id="' . $id . '" class="ui-state-default fg-button fg-button-icon-left" onmouseover="$(this).addClass(\'ui-state-highlight\');" onmouseout="$(this).removeClass(\'ui-state-highlight\');"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>' . $title . '</li>';
    }
  }
?>

    </ul></p>
  </fieldset>

  <p><?php echo osc_draw_hidden_field('modules', implode(',', $activated_modules_array), 'id="modules"') . osc_draw_hidden_field('subaction', 'confirm') . osc_draw_button(array('priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::getDef('button_save'))) . ' ' . osc_draw_button(array('href' => osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()]), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>

<script type="text/javascript">
  $('#modulesInstalled, #modulesAvailable').sortable({
    containment: '#containment',
    axis: 'y',
    connectWith: '.connectedList',
    update: function(event, ui) {
      $('#modules').val( $('#modulesInstalled').sortable('toArray') );
    }
  }).disableSelection();
</script>
