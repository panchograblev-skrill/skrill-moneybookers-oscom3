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

  <span style="float: right;"><?php echo HTML::button(array('href' => OSCOM::getLink(), 'priority' => 'secondary', 'icon' => 'triangle-1-w', 'title' => OSCOM::getDef('button_back'))) . ' ' . HTML::button(array('href' => OSCOM::getLink(null, null, 'InsertDefinition&id=' . $_GET['id']), 'icon' => 'plus', 'title' => OSCOM::getDef('button_insert'))); ?></span>
</form>

<div style="padding: 20px 5px 5px 5px; height: 16px;">
  <span id="batchTotalPages"></span>
  <span id="batchPageLinks"></span>
</div>

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable" id="langDefGroupDataTable">
  <thead>
    <tr>
      <th><?php echo OSCOM::getDef('table_heading_definition_groups'); ?></th>
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

<div style="padding: 2px;">
  <span id="dataTableLegend"><?php echo '<b>' . OSCOM::getDef('table_action_legend') . '</b> ' . HTML::icon('trash.png') . '&nbsp;' . OSCOM::getDef('icon_trash'); ?></span>
  <span id="batchPullDownMenu"></span>
</div>

<script type="text/javascript">
  var moduleParamsCookieName = 'oscom_admin_' + pageModule;
  var dataTablePageSetName = 'groups_page';

  var moduleParams = new Object();
  moduleParams[dataTablePageSetName] = 1;
  moduleParams['search'] = '';

  if ( $.cookie(moduleParamsCookieName) != null ) {
    moduleParams = $.secureEvalJSON($.cookie(moduleParamsCookieName));
  }

  var dataTableName = 'langDefGroupDataTable';
  var dataTableDataURL = '<?php echo OSCOM::getRPCLink(null, null, 'GetGroups&id=' . $_GET['id']); ?>';

  var groupLink = '<?php echo OSCOM::getLink(null, null, 'id=' . $_GET['id'] . '&group=GROUPCODE'); ?>';
  var groupLinkIcon = '<?php echo HTML::icon('folder.png'); ?>';

  var groupDeleteLink = '<?php echo OSCOM::getLink(null, null, 'DeleteGroup&id=' . $_GET['id'] . '&group=GROUPCODE'); ?>';
  var groupDeleteLinkIcon = '<?php echo HTML::icon('trash.png'); ?>';

  var osC_DataTable = new osC_DataTable();
  osC_DataTable.load();

  function feedDataTable(data) {
    var rowCounter = 0;

    for ( var r in data.entries ) {
      var record = data.entries[r];

      var newRow = $('#' + dataTableName)[0].tBodies[0].insertRow(rowCounter);
      newRow.id = 'row' + record.content_group;

      $('#row' + record.content_group).hover( function() { $(this).addClass('mouseOver'); }, function() { $(this).removeClass('mouseOver'); }).click(function(event) {
      }).css('cursor', 'pointer');

      var newCell = newRow.insertCell(0);
      newCell.innerHTML = groupLinkIcon + '&nbsp;<a href="' + groupLink.replace('GROUPCODE', htmlSpecialChars(record.content_group)) + '" class="parent">' + htmlSpecialChars(record.content_group) + '</a><span style="float: right;">(' + parseInt(record.total_entries) + ')</span>';

      newCell = newRow.insertCell(1);
      newCell.innerHTML = '<a href="' + groupDeleteLink.replace('GROUPCODE', htmlSpecialChars(record.content_group)) + '">' + groupDeleteLinkIcon + '</a>';
      newCell.align = 'right';

      rowCounter++;
    }
  }
</script>
