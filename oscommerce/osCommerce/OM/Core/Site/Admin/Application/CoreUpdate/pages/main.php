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

  <span style="float: right;"><?php echo HTML::selectMenu('logs', $OSCOM_Application->getLogList(), null, 'onchange="viewLog();"'); ?></span>
</form>

<div style="padding: 20px 5px 5px 5px; height: 16px;">
  <span id="batchTotalPages"></span>
  <span id="batchPageLinks"></span>
</div>

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable" id="coreUpdateDataTable">
  <thead>
    <tr>
      <th><?php echo OSCOM::getDef('table_heading_release_version'); ?></th>
      <th><?php echo OSCOM::getDef('table_heading_release_date'); ?></th>
      <th width="150"><?php echo OSCOM::getDef('table_heading_action'); ?></th>
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

<div style="padding: 5px;">
  <span id="dataTableLegend"><?php echo '<b>' . OSCOM::getDef('table_action_legend') . '</b> ' . HTML::icon('newsletters.png', OSCOM::getDef('icon_view_announcement')) . '&nbsp;' . OSCOM::getDef('icon_view_announcement') . ' ' . HTML::icon('folder_contents.png', OSCOM::getDef('icon_view_contents')) . '&nbsp;' . OSCOM::getDef('icon_view_contents'); ?></span>
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
  var dataTableDataURL = '<?php echo OSCOM::getRPCLink(null, null, 'GetAvailablePackages'); ?>';

  var announcementIcon = '<?php echo HTML::icon('newsletters.png', OSCOM::getDef('icon_view_announcement')); ?>';

  var updateInstallLink = '<?php echo OSCOM::getLink(null, null, 'Apply&v=VCODE'); ?>';
  var updateInstallLinkIcon = '<?php echo HTML::icon('folder_contents.png', OSCOM::getDef('icon_view_contents')); ?>';

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
      newCell.innerHTML = htmlSpecialChars(record.version);

      var newCell = newRow.insertCell(1);
      newCell.innerHTML = htmlSpecialChars(record.date);

      var actions = '';

      if ( record.announcement ) {
        actions += '<a href="' + record.announcement + '" target="_blank">' + announcementIcon + '</a>';
      }

      if ( record.update_package ) {
        if ( record.announcement ) {
          actions += '&nbsp;'
        }

        actions += '<a href="' + updateInstallLink.replace('VCODE', htmlSpecialChars(record.version)) + '">' + updateInstallLinkIcon + '</a>';
      }

      newCell = newRow.insertCell(2);
      newCell.innerHTML = actions;
      newCell.align = 'right';

      rowCounter++;
    }

    if ( (rowCounter == 0) && (moduleParams['search'] == '') ) {
      var newRow = $('#' + dataTableName)[0].tBodies[0].insertRow(rowCounter);
      newRow.id = 'row0';

      $('#row0').hover( function() { $(this).addClass('mouseOver'); }, function() { $(this).removeClass('mouseOver'); }).css('cursor', 'pointer');

      var newCell = newRow.insertCell(0);
      newCell.colSpan = 3;
      newCell.innerHTML = '<?php echo HTML::icon('tick.png') . ' '; ?>' + htmlSpecialChars("<?php echo OSCOM::getDef('up_to_date'); ?>");
    }
  }

  function viewLog() {
    window.location.href = '<?php echo OSCOM::getLink(null, null, 'ViewLog&log=LOGID'); ?>'.replace('LOGID', $('#logs option:selected').val());
  }
</script>
