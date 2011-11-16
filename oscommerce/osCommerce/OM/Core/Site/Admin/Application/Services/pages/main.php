<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . HTML::link(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<form id="liveSearchForm">
  <?php echo HTML::inputField('search', null, 'id="liveSearchField" class="searchField" placeholder="' . OSCOM::getDef('placeholder_search') . '"') . HTML::button(array('type' => 'button', 'params' => 'onclick="osC_DataTable.reset();"', 'title' => OSCOM::getDef('button_reset'))); ?>

  <span style="float: right;"><?php echo HTML::button(array('href' => OSCOM::getLink(null, null, 'Install'), 'icon' => 'plus', 'title' => OSCOM::getDef('button_install'))); ?></span>
</form>

<div style="padding: 20px 5px 5px 5px; height: 16px;">
  <span id="batchTotalPages"></span>
  <span id="batchPageLinks"></span>
</div>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable" id="servicesDataTable">
  <thead>
    <tr>
      <th><?php echo OSCOM::getDef('table_heading_service_modules'); ?></th>
      <th width="150"><?php echo OSCOM::getDef('table_heading_action'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th colspan="2">&nbsp;</th>
    </tr>
  </tfoot>
  <tbody>
  </tbody>
</table>

</form>

<div style="padding: 5px;">
  <span id="dataTableLegend"><?php echo '<b>' . OSCOM::getDef('table_action_legend') . '</b> ' . HTML::icon('edit.png') . '&nbsp;' . OSCOM::getDef('icon_edit') . '&nbsp;&nbsp;' . HTML::icon('uninstall.png') . '&nbsp;' . OSCOM::getDef('icon_uninstall'); ?></span>
  <span id="batchPullDownMenu"></span>
</div>

<script>
  var moduleParamsCookieName = 'oscom_admin_' + pageModule;
  var dataTablePageSetName = 'page';

  var moduleParams = new Object();
  moduleParams[dataTablePageSetName] = 1;
  moduleParams['search'] = '';

  if ( $.cookie(moduleParamsCookieName) != null ) {
    moduleParams = $.secureEvalJSON($.cookie(moduleParamsCookieName));
  }

  var dataTableName = 'servicesDataTable';
  var dataTableDataURL = '<?php echo OSCOM::getRPCLink(null, null, 'GetInstalled'); ?>';

  var smEditLink = '<?php echo OSCOM::getLink(null, null, 'Save&code=SMCODE'); ?>';
  var smEditLinkIcon = '<?php echo HTML::icon('edit.png'); ?>';

  var smUninstallLinkIcon = '<?php echo HTML::icon('uninstall.png'); ?>';

  var osC_DataTable = new osC_DataTable();
  osC_DataTable.load();

  function feedDataTable(data) {
    var rowCounter = 0;

    for ( var r in data.entries ) {
      var record = data.entries[r];

      var newRow = $('#' + dataTableName)[0].tBodies[0].insertRow(rowCounter);
      newRow.id = 'row' + record.code;

      $('#row' + record.code).hover( function() { $(this).addClass('mouseOver'); }, function() { $(this).removeClass('mouseOver'); }).css('cursor', 'pointer');

      var newCell = newRow.insertCell(0);
      newCell.innerHTML = htmlSpecialChars(record.title);

      var actions = '';

      if ( record.has_keys == true ) {
        actions += '<a href="' + smEditLink.replace('SMCODE', htmlSpecialChars(record.code)) + '">' + smEditLinkIcon + '</a>';
      } else {
        actions += '<span style="padding: 8px;"></span>';
      }

      if ( record.uninstallable == true ) {
        actions += '&nbsp;<a href="#" onclick="$(\'#dialogUninstallConfirm\').data(\'code\', \'' + record.code + '\').dialog(\'open\'); return false;">' + smUninstallLinkIcon + '</a>';
      } else {
        actions += '&nbsp;<span style="padding: 8px;"></span>';
      }

      newCell = newRow.insertCell(1);
      newCell.innerHTML = actions;
      newCell.align = 'right';

      rowCounter++;
    }
  }
</script>

<div id="dialogUninstallConfirm" title="<?php echo HTML::output(OSCOM::getDef('dialog_uninstall_module_title')); ?>">
  <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span><?php echo OSCOM::getDef('dialog_uninstall_module_desc'); ?></p>
</div>

<script>
$(function() {
  $('#dialogUninstallConfirm').dialog({
    autoOpen: false,
    resizable: false,
    modal: true,
    buttons: {
      '<?php echo addslashes(OSCOM::getDef('button_uninstall')); ?>': function() {
        window.location.href='<?php echo OSCOM::getLink(null, null, 'Uninstall&Process&code=SMCODE'); ?>'.replace('SMCODE', $(this).data('code'));
      },
      '<?php echo addslashes(OSCOM::getDef('button_cancel')); ?>': function() {
        $(this).dialog('close');
      }
    }
  });
});
</script>
