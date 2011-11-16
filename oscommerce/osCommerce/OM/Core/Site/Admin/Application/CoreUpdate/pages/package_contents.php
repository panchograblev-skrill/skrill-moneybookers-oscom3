<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Admin\Application\CoreUpdate\CoreUpdate;
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . HTML::link(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<form id="liveSearchForm">
  <?php echo HTML::inputField('search', null, 'id="liveSearchField" class="searchField" placeholder="' . OSCOM::getDef('placeholder_search') . '"') . HTML::button(array('type' => 'button', 'params' => 'onclick="osC_DataTable.reset();"', 'title' => OSCOM::getDef('button_reset'))); ?>

  <span style="float: right;"><?php echo HTML::button(array('href' => OSCOM::getLink(), 'priority' => 'secondary', 'icon' => 'triangle-1-w', 'title' => OSCOM::getDef('button_back'))) . (CoreUpdate::getPackageInfo('version_from') == OSCOM::getVersion() ? ' ' . HTML::button(array('href' => OSCOM::getLink(null, null, 'Apply&Process&v=' . $_GET['v']), 'icon' => 'disk', 'title' => OSCOM::getDef('button_apply_update'))) : ''); ?></span>
</form>

<div style="padding: 20px 5px 5px 5px; height: 16px;">
  <span id="batchTotalPages"></span>
  <span id="batchPageLinks"></span>
</div>

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable" id="coreUpdateDataTable">
  <thead>
    <tr>
      <th><?php echo OSCOM::getDef('table_heading_files'); ?></th>
      <th width="100"><?php echo OSCOM::getDef('table_heading_file_writable'); ?></th>
      <th width="100"><?php echo OSCOM::getDef('table_heading_file_custom'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th colspan="3">&nbsp;</th>
    </tr>
  </tfoot>
  <tbody>
  </tbody>
</table>

<div style="padding: 2px; min-height: 16px;">
  <span id="dataTableLegend"><?php echo '<b>' . OSCOM::getDef('table_action_legend') . '</b> <span style="background-color: #ceffc8; padding: 1px 10px 1px 10px;">' . OSCOM::getDef('legend_new') . '</span> <span style="background-color: #ffebc8; padding: 1px 10px 1px 10px;">' . OSCOM::getDef('legend_modified') . '</span> <span style="background-color: #ffc8c8; padding: 1px 10px 1px 10px;">' . OSCOM::getDef('legend_to_delete') . '</span>'; ?></span>
  <span id="batchPullDownMenu"></span>
</div>

<script type="text/javascript">
  var moduleParamsCookieName = 'oscom_admin_' + pageModule;
  var dataTablePageSetName = 'page';

  var moduleParams = new Object();
  moduleParams[dataTablePageSetName] = 1;
  moduleParams['search'] = '';

  if ( $.cookie(moduleParamsCookieName) != null ) {
    moduleParams = $.secureEvalJSON($.cookie(moduleParamsCookieName));
  }

  var dataTableName = 'coreUpdateDataTable';
  var dataTableDataURL = '<?php echo OSCOM::getRPCLink(null, null, 'GetPackageContents'); ?>';

  var checkboxTickedIcon = '<?php echo HTML::icon('checkbox_ticked.gif'); ?>';
  var checkboxCrossedIcon = '<?php echo HTML::icon('checkbox_crossed.gif'); ?>';
  var checkboxIcon = '<?php echo HTML::icon('checkbox.gif'); ?>';

  var osC_DataTable = new osC_DataTable();
  osC_DataTable.load();

  function feedDataTable(data) {
    var rowCounter = 0;

    for ( var r in data.entries ) {
      var record = data.entries[r];

      var newRow = $('#' + dataTableName)[0].tBodies[0].insertRow(rowCounter);
      newRow.id = 'row' + record.key;

      $('#row' + record.key).hover( function() { $(this).addClass('mouseOver'); }, function() { $(this).removeClass('mouseOver'); }).css('cursor', 'pointer');

      var newCell = newRow.insertCell(0);
      newCell.innerHTML = htmlSpecialChars(record.name);

      var newCell = newRow.insertCell(1);
      newCell.innerHTML = record.writable == true ? checkboxTickedIcon : checkboxCrossedIcon;
      newCell.align = 'center';

      var newCell = newRow.insertCell(2);
      newCell.innerHTML = record.custom == true ? checkboxTickedIcon : checkboxIcon;
      newCell.align = 'center';

      if ( record.to_delete == true ) {
        $(newRow).children().css('backgroundColor', '#ffc8c8');
      } else if ( record.exists == true ) {
        $(newRow).children().css('backgroundColor', '#ffebc8');
      } else if ( record.exists == false ) {
        $(newRow).children().css('backgroundColor', '#ceffc8');
      }

      rowCounter++;
    }
  }
</script>
