<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\HTML;

  $www_location = 'http://' . $_SERVER['HTTP_HOST'];

  if ( isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI']) ) {
    $www_location .= $_SERVER['REQUEST_URI'];
  } else {
    $www_location .= $_SERVER['SCRIPT_FILENAME'];
  }

  $www_location = substr($www_location, 0, strpos($www_location, 'index.php'));

  $db_table_types = array(array('id' => 'MySQL_Standard', 'text' => 'MySQL Standard'),
                          array('id' => 'MySQL_V5', 'text' => 'MySQL v5'));
?>

<script language="javascript" type="text/javascript">
  var dbServer;
  var dbUsername;
  var dbPassword;
  var dbName;
  var dbPort;
  var dbClass;
  var dbPrefix;

  var formSubmited = false;
  var formSuccess = false;

  function handleHttpResponse_DoImport(data) {
    if (data.result == true) {
      $('#mBoxContents').html('<p><img src="<?php echo OSCOM::getPublicSiteLink('templates/default/images/success.gif'); ?>" align="right" hspace="5" vspace="5" border="0" /><?php echo OSCOM::getDef('rpc_database_imported'); ?></p>');

      formSuccess = true;

      $('#installForm').submit();
    } else {
      $('#mBoxContents').html('<p><img src="<?php echo OSCOM::getPublicSiteLink('templates/default/images/failed.gif'); ?>" align="right" hspace="5" vspace="5" border="0" /><?php echo OSCOM::getDef('rpc_database_import_error'); ?></p>'.replace('%s', data.error_message));

      formSubmited = false;
    }
  }

  function handleHttpResponse(data) {
    if (data.result == true) {
      $('#mBoxContents').html('<p><img src="<?php echo OSCOM::getPublicSiteLink('templates/default/images/progress.gif'); ?>" align="right" hspace="5" vspace="5" border="0" /><?php echo OSCOM::getDef('rpc_database_importing'); ?></p>');

      $.post('<?php echo OSCOM::getRPCLink(null, null, 'DBImport'); ?>',
             'server=' + dbServer + '&username=' + dbUsername + '&password=' + dbPassword + '&name=' + dbName + '&port=' + dbPort + '&class=' + dbClass + '&import=0&prefix=' + dbPrefix,
             handleHttpResponse_DoImport, 'json');
    } else {
      $('#mBoxContents').html('<p><img src="<?php echo OSCOM::getPublicSiteLink('templates/default/images/failed.gif'); ?>" align="right" hspace="5" vspace="5" border="0" /><?php echo OSCOM::getDef('rpc_database_connection_error'); ?></p>'.replace('%s', data.error_message));

      formSubmited = false;
    }
  }

  function prepareDB() {
    if (formSubmited == true) {
      return false;
    }

    formSubmited = true;

    $('#mBox').css('visibility', 'visible').show();
    $('#mBoxContents').html('<p><img src="<?php echo OSCOM::getPublicSiteLink('templates/default/images/progress.gif'); ?>" align="right" hspace="5" vspace="5" border="0" /><?php echo OSCOM::getDef('rpc_database_connection_test'); ?></p>');

    dbServer = encodeURIComponent($('#DB_SERVER').val());
    dbUsername = encodeURIComponent($('#DB_SERVER_USERNAME').val());
    dbPassword = encodeURIComponent($('#DB_SERVER_PASSWORD').val());
    dbName = encodeURIComponent($('#DB_DATABASE').val());
    dbPort = encodeURIComponent($('#DB_SERVER_PORT').val());
    dbClass = encodeURIComponent($('#DB_DATABASE_CLASS').val());
    dbPrefix = encodeURIComponent($('#DB_TABLE_PREFIX').val());

    $.post('<?php echo OSCOM::getRPCLink(null, null, 'DBCheck'); ?>',
           'server=' + dbServer + '&username=' + dbUsername + '&password=' + dbPassword + '&name=' + dbName + '&port=' + dbPort + '&class=' + dbClass,
           handleHttpResponse, 'json');
  }
</script>

<div class="mainBlock">
  <div class="stepsBox">
    <ol>
      <li style="font-weight: bold;"><?php echo OSCOM::getDef('box_steps_step_1'); ?></li>
      <li><?php echo OSCOM::getDef('box_steps_step_2'); ?></li>
      <li><?php echo OSCOM::getDef('box_steps_step_3'); ?></li>
    </ol>
  </div>

  <h1><?php echo OSCOM::getDef('page_title_installation'); ?></h1>

  <p><?php echo OSCOM::getDef('text_installation'); ?></p>
</div>

<div class="contentBlock">
  <div class="infoPane">
    <h3><?php echo OSCOM::getDef('box_info_step_1_title'); ?></h3>

    <div class="infoPaneContents">
      <p><?php echo OSCOM::getDef('box_info_step_1_text'); ?></p>
    </div>
  </div>

  <div id="mBox">
    <div id="mBoxContents"></div>
  </div>

  <div class="contentPane">
    <form name="install" id="installForm" action="<?php echo OSCOM::getLink(null, null, 'step=2'); ?>" method="post">

    <h2><?php echo OSCOM::getDef('page_heading_web_server'); ?></h2>

    <table border="0" width="99%" cellspacing="0" cellpadding="5" class="inputForm">
      <tr>
        <td class="inputField"><?php echo OSCOM::getDef('param_web_address') . '<br />' . HTML::inputField('HTTP_WWW_ADDRESS', $www_location, 'class="text"'); ?></td>
        <td class="inputDescription"><?php echo OSCOM::getDef('param_web_address_description'); ?></td>
      </tr>
    </table>

    <br />

    <h2><?php echo OSCOM::getDef('page_heading_database_server'); ?></h2>

    <table border="0" width="99%" cellspacing="0" cellpadding="5" class="inputForm">
      <tr>
        <td class="inputField"><?php echo OSCOM::getDef('param_database_server') . '<br />' . HTML::inputField('DB_SERVER', null, 'class="text"'); ?></td>
        <td class="inputDescription"><?php echo OSCOM::getDef('param_database_server_description'); ?></td>
      </tr>
      <tr>
        <td class="inputField"><?php echo OSCOM::getDef('param_database_username') . '<br />' . HTML::inputField('DB_SERVER_USERNAME', null, 'class="text"'); ?></td>
        <td class="inputDescription"><?php echo OSCOM::getDef('param_database_username_description'); ?></td>
      </tr>
      <tr>
        <td class="inputField"><?php echo OSCOM::getDef('param_database_password') . '<br />' . HTML::inputField('DB_SERVER_PASSWORD', null, 'class="text"'); ?></td>
        <td class="inputDescription"><?php echo OSCOM::getDef('param_database_password_description'); ?></td>
      </tr>
      <tr>
        <td class="inputField"><?php echo OSCOM::getDef('param_database_name') . '<br />' . HTML::inputField('DB_DATABASE', null, 'class="text"'); ?></td>
        <td class="inputDescription"><?php echo OSCOM::getDef('param_database_name_description'); ?></td>
      </tr>
      <tr>
        <td class="inputField"><?php echo OSCOM::getDef('param_database_port') . '<br />' . HTML::inputField('DB_SERVER_PORT', null, 'class="text"'); ?></td>
        <td class="inputDescription"><?php echo OSCOM::getDef('param_database_port_description'); ?></td>
      </tr>
      <tr>
        <td class="inputField"><?php echo OSCOM::getDef('param_database_type') . '<br />' . HTML::selectMenu('DB_DATABASE_CLASS', $db_table_types); ?></td>
        <td class="inputDescription"><?php echo OSCOM::getDef('param_database_type_description'); ?></td>
      </tr>
      <tr>
        <td class="inputField"><?php echo OSCOM::getDef('param_database_prefix') . '<br />' . HTML::inputField('DB_TABLE_PREFIX', 'osc_', 'class="text"'); ?></td>
        <td class="inputDescription"><?php echo OSCOM::getDef('param_database_prefix_description'); ?></td>
      </tr>
    </table>

    <p align="right"><?php echo HTML::button(array('priority' => 'primary', 'icon' => 'triangle-1-e', 'title' => OSCOM::getDef('button_continue'))) . ' ' . HTML::button(array('href' => OSCOM::getLink(null, OSCOM::getDefaultSiteApplication()), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

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
