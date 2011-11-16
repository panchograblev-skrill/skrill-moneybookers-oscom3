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

  <span style="float: right;"><?php echo HTML::button(array('href' => OSCOM::getLink(null, null, 'Save'), 'icon' => 'plus', 'title' => OSCOM::getDef('button_insert'))); ?></span>
</form>

<div style="padding: 20px 5px 5px 5px; height: 16px;">
  <span id="batchTotalPages"></span>
  <span id="batchPageLinks"></span>
</div>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable" id="customerDataTable">
  <thead>
    <tr>
      <th><?php echo OSCOM::getDef('table_heading_customers'); ?></th>
      <th><?php echo OSCOM::getDef('table_heading_date_created'); ?></th>
      <th width="150"><?php echo OSCOM::getDef('table_heading_action'); ?></th>
      <th align="center" width="20"><?php echo HTML::checkboxField('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th align="right" colspan="3"><?php echo '<a href="#" onclick="$(\'#dialogBatchDeleteConfirm\').dialog(\'open\'); return false;">' . HTML::icon('trash.png') . '</a>'; ?></th>
      <th align="center" width="20"><?php echo HTML::checkboxField('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </tfoot>
  <tbody>
  </tbody>
</table>

</form>

<div style="padding: 2px;">
  <span id="dataTableLegend"><?php echo '<b>' . OSCOM::getDef('table_action_legend') . '</b> ' . HTML::icon('edit.png') . '&nbsp;' . OSCOM::getDef('icon_edit') . '&nbsp;&nbsp;' . HTML::icon('trash.png') . '&nbsp;' . OSCOM::getDef('icon_trash'); ?></span>
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

  var dataTableName = 'customerDataTable';
  var dataTableDataURL = '<?php echo OSCOM::getRPCLink(null, null, 'GetAll'); ?>';

  var customerEditLink = '<?php echo OSCOM::getLink(null, null, 'Save&id=CUSTOMERID'); ?>';
  var customerEditLinkIcon = '<?php echo HTML::icon('edit.png'); ?>';

  var customerDeleteLinkIcon = '<?php echo HTML::icon('trash.png'); ?>';

  var customerGenderMaleIcon = '<?php echo HTML::icon('user_male.png'); ?>';
  var customerGenderFemaleIcon = '<?php echo HTML::icon('user_female.png'); ?>';

  var showCustomerGender = '<?php echo ACCOUNT_GENDER; ?>';

  var osC_DataTable = new osC_DataTable();
  osC_DataTable.load();

  function feedDataTable(data) {
    var rowCounter = 0;

    for ( var r in data.entries ) {
      var record = data.entries[r];

      var customerGenderIcon = '';

      if ( (showCustomerGender == '0') || (showCustomerGender == '1') ) {
        if ( record.customers_gender == 'm' ) {
          customerGenderIcon = customerGenderMaleIcon;
        } else if ( record.customers_gender == 'f' ) {
          customerGenderIcon = customerGenderFemaleIcon;
        } else {
          customerGenderIcon = '<span style="padding: 16px 16px 0 0;"></span>';
        }

        customerGenderIcon += ' ';
      }

      var newRow = $('#' + dataTableName)[0].tBodies[0].insertRow(rowCounter);
      newRow.id = 'row' + parseInt(record.customers_id);

      if ( parseInt(record.customers_status) != 1 ) {
        $('#row' + parseInt(record.customers_id)).addClass('deactivatedRow');
      }

      $('#row' + parseInt(record.customers_id)).hover( function() { $(this).addClass('mouseOver'); }, function() { $(this).removeClass('mouseOver'); }).click(function(event) {
        if (event.target.type !== 'checkbox') {
          $(':checkbox', this).trigger('click');
        }
      }).css('cursor', 'pointer');

      var newCell = newRow.insertCell(0);
      newCell.innerHTML = customerGenderIcon + htmlSpecialChars(record.customers_lastname) + ', ' + htmlSpecialChars(record.customers_firstname);

      newCell = newRow.insertCell(1);
      newCell.innerHTML = htmlSpecialChars(record.date_account_created);

      newCell = newRow.insertCell(2);
      newCell.innerHTML = '<a href="' + customerEditLink.replace('CUSTOMERID', parseInt(record.customers_id)) + '">' + customerEditLinkIcon + '</a>&nbsp;<a href="#" onclick="$(\'#dialogDeleteConfirm\').data(\'id\', ' + parseInt(record.customers_id) + ').dialog(\'open\'); return false;">' + customerDeleteLinkIcon + '</a>';
      newCell.align = 'right';

      newCell = newRow.insertCell(3);
      newCell.innerHTML = '<input type="checkbox" name="batch[]" value="' + parseInt(record.customers_id) + '" id="batch' + parseInt(record.customers_id) + '" />';
      newCell.align = 'center';

      rowCounter++;
    }
  }
</script>

<div id="dialogDeleteConfirm" title="<?php echo HTML::output(OSCOM::getDef('dialog_delete_customer_title')); ?>">
  <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span><?php echo OSCOM::getDef('dialog_delete_customer_desc'); ?></p>
</div>

<div id="dialogBatchDeleteConfirm" title="<?php echo HTML::output(OSCOM::getDef('dialog_batch_delete_customer_title')); ?>">
  <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span><?php echo OSCOM::getDef('dialog_batch_delete_customer_desc'); ?></p>
</div>

<script type="text/javascript">
$(function() {
  $('#dialogDeleteConfirm').dialog({
    autoOpen: false,
    resizable: false,
    modal: true,
    buttons: {
      '<?php echo addslashes(OSCOM::getDef('button_delete')); ?>': function() {
        window.location.href='<?php echo OSCOM::getLink(null, null, 'Delete&Process&id=CUSTOMERID'); ?>'.replace('CUSTOMERID', $(this).data('id'));
      },
      '<?php echo addslashes(OSCOM::getDef('button_cancel')); ?>': function() {
        $(this).dialog('close');
      }
    }
  });
});

$(function() {
  $('#dialogBatchDeleteConfirm').dialog({
    autoOpen: false,
    resizable: false,
    modal: true,
    buttons: {
      '<?php echo addslashes(OSCOM::getDef('button_delete')); ?>': function() {
        document.batch.action='<?php echo OSCOM::getLink(null, null, 'BatchDelete&Process'); ?>';
        document.batch.submit();
      },
      '<?php echo addslashes(OSCOM::getDef('button_cancel')); ?>': function() {
        $(this).dialog('close');
      }
    }
  });
});
</script>
