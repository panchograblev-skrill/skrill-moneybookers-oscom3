<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\HTML;
?>

<script language="javascript" type="text/javascript">
  var dbServer = "<?php echo $_POST['DB_SERVER']; ?>";
  var dbUsername = "<?php echo $_POST['DB_SERVER_USERNAME']; ?>";
  var dbPassword = "<?php echo $_POST['DB_SERVER_PASSWORD']; ?>";
  var dbName = "<?php echo $_POST['DB_DATABASE']; ?>";
  var dbPort = "<?php echo $_POST['DB_SERVER_PORT']; ?>";
  var dbClass = "<?php echo $_POST['DB_DATABASE_CLASS']; ?>";
  var dbPrefix = "<?php echo $_POST['DB_TABLE_PREFIX']; ?>";

  var shopName;
  var shopOwnerName;
  var shopOwnerEmail;
  var adminUsername;
  var adminPassword;

  var formSubmited = false;
  var formSuccess = false;

  function handleHttpResponse_ImportSample(data) {
    if (data.result == true) {
      $('#mBoxContents').html('<p><img src="<?php echo OSCOM::getPublicSiteLink('templates/default/images/success.gif'); ?>" align="right" hspace="5" vspace="5" border="0" /><?php echo OSCOM::getDef('rpc_database_sample_data_imported'); ?></p>');

      formSuccess = true;

      $('#installForm').submit();
    } else {
      $('#mBoxContents').html('<p><img src="<?php echo OSCOM::getPublicSiteLink('templates/default/images/failed.gif'); ?>" align="right" hspace="5" vspace="5" border="0" /><?php echo OSCOM::getDef('rpc_database_sample_data_import_error'); ?></p>'.replace('%s', data.error_message));

      formSubmited = false;
    }
  }

  function handleHttpResponse(data) {
    if (data.result == true) {
      if ( $('#DB_INSERT_SAMPLE_DATA').attr('checked') ) {
        $('#mBoxContents').html('<p><img src="<?php echo OSCOM::getPublicSiteLink('templates/default/images/progress.gif'); ?>" align="right" hspace="5" vspace="5" border="0" /><?php echo OSCOM::getDef('rpc_database_sample_data_importing'); ?></p>');

        $.post('<?php echo OSCOM::getRPCLink(null, null, 'DBImportSample'); ?>',
               'server=' + dbServer + '&username=' + dbUsername + '&password=' + dbPassword + '&name=' + dbName + '&port=' + dbPort + '&class=' + dbClass + '&prefix=' + dbPrefix,
               handleHttpResponse_ImportSample, 'json');
      } else {
        formSuccess = true;

        $('#installForm').submit();
      }
    } else {
      $('#mBoxContents').html('<p><img src="<?php echo OSCOM::getPublicSiteLink('templates/default/images/failed.gif'); ?>" align="right" hspace="5" vspace="5" border="0" /><?php echo OSCOM::getDef('rpc_database_store_configuration_error'); ?></p>'.replace('%s', data.error_message));

      formSubmited = false;
    }
  }

  function prepareDB() {
    if (formSubmited == true) {
      return false;
    }

    formSubmited = true;

    $('#mBox').css('visibility', 'visible').show();
    $('#mBoxContents').html('<p><img src="<?php echo OSCOM::getPublicSiteLink('templates/default/images/progress.gif'); ?>" align="right" hspace="5" vspace="5" border="0" /><?php echo OSCOM::getDef('rpc_database_store_configuration'); ?></p>');

    shopName = encodeURIComponent($('#CFG_STORE_NAME').val());
    shopOwnerName = encodeURIComponent($('#CFG_STORE_OWNER_NAME').val());
    shopOwnerEmail = encodeURIComponent($('#CFG_STORE_OWNER_EMAIL_ADDRESS').val());
    adminUsername = encodeURIComponent($('#CFG_ADMINISTRATOR_USERNAME').val());
    adminPassword = encodeURIComponent($('#CFG_ADMINISTRATOR_PASSWORD').val());

    $.post('<?php echo OSCOM::getRPCLink(null, null, 'DBConfigureShop'); ?>',
           'server=' + dbServer + '&username=' + dbUsername + '&password=' + dbPassword + '&name=' + dbName + '&port=' + dbPort + '&class=' + dbClass + '&prefix=' + dbPrefix + '&shop_name=' + shopName + '&shop_owner_name=' + shopOwnerName + '&shop_owner_email=' + shopOwnerEmail + '&admin_username=' + adminUsername + '&admin_password=' + adminPassword,
           handleHttpResponse, 'json');
  }

</script>

<div class="mainBlock">
  <div class="stepsBox">
    <ol>
      <li><?php echo OSCOM::getDef('box_steps_step_1'); ?></li>
      <li style="font-weight: bold;"><?php echo OSCOM::getDef('box_steps_step_2'); ?></li>
      <li><?php echo OSCOM::getDef('box_steps_step_3'); ?></li>
    </ol>
  </div>

  <h1><?php echo OSCOM::getDef('page_title_installation'); ?></h1>

  <p><?php echo OSCOM::getDef('text_installation'); ?></p>
</div>

<div class="contentBlock">
  <div class="infoPane">
    <h3><?php echo OSCOM::getDef('box_info_step_2_title'); ?></h3>

    <div class="infoPaneContents">
      <p><?php echo OSCOM::getDef('box_info_step_2_text'); ?></p>
    </div>
  </div>

  <div id="mBox">
    <div id="mBoxContents"></div>
  </div>

  <div class="contentPane">
    <form name="install" id="installForm" action="<?php echo OSCOM::getLink(null, null, 'step=3'); ?>" method="post">

    <h2><?php echo OSCOM::getDef('page_heading_store_settings'); ?></h2>

    <table border="0" width="99%" cellspacing="0" cellpadding="5" class="inputForm">
      <tr>
        <td class="inputField"><?php echo OSCOM::getDef('param_store_name') . '<br />' . HTML::inputField('CFG_STORE_NAME', null, 'class="text"'); ?></td>
        <td class="inputDescription"><?php echo OSCOM::getDef('param_store_name_description'); ?></td>
      </tr>
      <tr>
        <td class="inputField"><?php echo OSCOM::getDef('param_store_owner_name') . '<br />' . HTML::inputField('CFG_STORE_OWNER_NAME', null, 'class="text"'); ?></td>
        <td class="inputDescription"><?php echo OSCOM::getDef('param_store_owner_name_description'); ?></td>
      </tr>
      <tr>
        <td class="inputField"><?php echo OSCOM::getDef('param_store_owner_email_address') . '<br />' . HTML::inputField('CFG_STORE_OWNER_EMAIL_ADDRESS', null, 'class="text"'); ?></td>
        <td class="inputDescription"><?php echo OSCOM::getDef('param_store_owner_email_address_description'); ?></td>
      </tr>
      <tr>
        <td class="inputField"><?php echo OSCOM::getDef('param_administrator_username') . '<br />' . HTML::inputField('CFG_ADMINISTRATOR_USERNAME', null, 'class="text"'); ?></td>
        <td class="inputDescription"><?php echo OSCOM::getDef('param_administrator_username_description'); ?></td>
      </tr>
      <tr>
        <td class="inputField"><?php echo OSCOM::getDef('param_administrator_password') . '<br />' . HTML::inputField('CFG_ADMINISTRATOR_PASSWORD', null, 'class="text"'); ?></td>
        <td class="inputDescription"><?php echo OSCOM::getDef('param_administrator_password_description'); ?></td>
      </tr>
      <tr>
        <td class="inputField"><?php echo OSCOM::getDef('param_time_zone') . '<br />' . HTML::timeZoneSelectMenu('CFG_TIME_ZONE', (ini_get('date.timezone') ?: null)); ?></td>
        <td class="inputDescription"><?php echo OSCOM::getDef('param_time_zone_description'); ?></td>
      </tr>
      <tr>
        <td class="inputField"><?php echo HTML::checkboxField('DB_INSERT_SAMPLE_DATA', 'true', true) . '&nbsp;' . OSCOM::getDef('param_database_import_sample_data'); ?></td>
        <td class="inputDescription"><?php echo OSCOM::getDef('param_database_import_sample_data_description'); ?></td>
      </tr>
    </table>

    <p align="right"><?php echo HTML::button(array('priority' => 'primary', 'icon' => 'triangle-1-e', 'title' => OSCOM::getDef('button_continue'))) . ' ' . HTML::button(array('href' => OSCOM::getLink(null, OSCOM::getDefaultSiteApplication()), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

<?php
  foreach ( $_POST as $key => $value ) {
    if ( ($key != 'x') && ($key != 'y') ) {
      if ( is_array($value) ) {
        for ( $i=0, $n=count($value); $i<$n; $i++ ) {
          echo HTML::hiddenField($key . '[]', $value[$i]);
        }
      } else {
        echo HTML::hiddenField($key, $value);
      }
    }
  }
?>

    </form>
  </div>
</div>

<script type="text/javascript">
  $("#installForm").submit(function(e) {
    if ( formSuccess == false ) {
      e.preventDefault();

      prepareDB();
    }
  });
</script>
