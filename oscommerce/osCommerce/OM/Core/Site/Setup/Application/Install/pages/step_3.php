<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\DirectoryListing;
  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
?>

<div class="mainBlock">
  <div class="stepsBox">
    <ol>
      <li><?php echo OSCOM::getDef('box_steps_step_1'); ?></li>
      <li><?php echo OSCOM::getDef('box_steps_step_2'); ?></li>
      <li style="font-weight: bold;"><?php echo OSCOM::getDef('box_steps_step_3'); ?></li>
    </ol>
  </div>

  <h1><?php echo OSCOM::getDef('page_title_installation'); ?></h1>

  <p><?php echo OSCOM::getDef('text_installation'); ?></p>
</div>

<div class="contentBlock">
  <div class="infoPane">
    <h3><?php echo OSCOM::getDef('box_info_step_3_title'); ?></h3>

    <div class="infoPaneContents">
      <p><?php echo OSCOM::getDef('box_info_step_3_text'); ?></p>
    </div>
  </div>

  <div class="contentPane">
    <h2><?php echo OSCOM::getDef('page_heading_finished'); ?></h2>

<?php
  $http_url = parse_url($_POST['HTTP_WWW_ADDRESS']);
  $http_server = $http_url['scheme'] . '://' . $http_url['host'];
  $http_dir_ws = $http_url['path'];

  if ( isset($http_url['port']) && !empty($http_url['port']) ) {
    $http_server .= ':' . $http_url['port'];
  }

  if ( substr($http_dir_ws, -1) != '/' ) {
    $http_dir_ws .= '/';
  }

  $http_cookie_domain = '';

  if ( (substr_count($http_url['host'], '.') > 1) && !filter_var($http_url['host'], FILTER_VALIDATE_IP) ) {
    $http_cookie_domain = $http_url['host'];
  }

  $dir_fs_document_root = OSCOM_PUBLIC_BASE_DIRECTORY;

  $DL_Cache = new DirectoryListing(OSCOM::BASE_DIRECTORY . 'Work/Cache');
  $DL_Cache->setIncludeDirectories(false);
  $DL_Cache->setCheckExtension('cache');

  foreach ( $DL_Cache->getFiles() as $files ) {
    @unlink($DL_Cache->getDirectory() . '/' . $files['name']);
  }

  $db_class = str_replace('_', '\\', $_POST['DB_DATABASE_CLASS']);

  $file_contents = <<<EOT
[OSCOM]
bootstrap_file = "index.php"
default_site = "Shop"
time_zone = "{$_POST['CFG_TIME_ZONE']}"
dir_fs_public = "{$dir_fs_document_root}public/"

[Admin]
enable_ssl = "false"
http_server = "$http_server"
https_server = "$http_server"
http_cookie_domain = "$http_cookie_domain"
https_cookie_domain = "$http_cookie_domain"
http_cookie_path = "$http_dir_ws"
https_cookie_path = "$http_dir_ws"
dir_ws_http_server = "$http_dir_ws"
dir_ws_https_server = "$http_dir_ws"
db_server = "{$_POST['DB_SERVER']}"
db_server_username = "{$_POST['DB_SERVER_USERNAME']}"
db_server_password = "{$_POST['DB_SERVER_PASSWORD']}"
db_server_port = "{$_POST['DB_SERVER_PORT']}"
db_database = "{$_POST['DB_DATABASE']}"
db_driver = "$db_class"
db_table_prefix = "{$_POST['DB_TABLE_PREFIX']}"
db_server_persistent_connections = "false"
store_sessions = "Database"

[Shop]
enable_ssl = "false"
http_server = "$http_server"
https_server = "$http_server"
http_cookie_domain = "$http_cookie_domain"
https_cookie_domain = "$http_cookie_domain"
http_cookie_path = "$http_dir_ws"
https_cookie_path = "$http_dir_ws"
dir_ws_http_server = "$http_dir_ws"
dir_ws_https_server = "$http_dir_ws"
product_images_http_server = ""
product_images_https_server = ""
product_images_dir_ws_http_server = "{$http_dir_ws}public/products/"
product_images_dir_ws_https_server = "{$http_dir_ws}public/products/"
db_server = "{$_POST['DB_SERVER']}"
db_server_username = "{$_POST['DB_SERVER_USERNAME']}"
db_server_password = "{$_POST['DB_SERVER_PASSWORD']}"
db_server_port = "{$_POST['DB_SERVER_PORT']}"
db_database = "{$_POST['DB_DATABASE']}"
db_driver = "$db_class"
db_table_prefix = "{$_POST['DB_TABLE_PREFIX']}"
db_server_persistent_connections = "false"
store_sessions = "Database"

[Setup]
offline = "true"
EOT;

  if ( is_writable(OSCOM::BASE_DIRECTORY . 'Config/settings.ini') ) {
    file_put_contents(OSCOM::BASE_DIRECTORY . 'Config/settings.ini', $file_contents);
?>

    <p><?php echo OSCOM::getDef('text_successful_installation'); ?></p>

<?php
  } else {
?>

    <form name="install" action="<?php echo OSCOM::getLink(null, null, 'step=3'); ?>" method="post">

    <div class="noticeBox">
      <p><?php echo sprintf(OSCOM::getDef('error_configuration_file_not_writeable'), OSCOM::BASE_DIRECTORY . 'Config/settings.ini'); ?></p>

      <p align="right"><?php echo HTML::button(array('icon' => 'refresh', 'title' => OSCOM::getDef('button_retry'))); ?></p>

      <p><?php echo OSCOM::getDef('error_configuration_file_alternate_method'); ?></p>

      <?php echo HTML::textareaField('contents', $file_contents, 60, 5, 'readonly="readonly" style="width: 100%; height: 120px;"', false); ?>
    </div>

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

    <p><?php echo OSCOM::getDef('text_go_to_shop_after_cfg_file_is_saved'); ?></p>

<?php
  }
?>

    <br />

    <p align="center"><?php echo HTML::button(array('href' => $http_server . $http_dir_ws . 'index.php?Shop', 'icon' => 'cart', 'title' => OSCOM::getDef('button_shop'))) . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . HTML::button(array('href' => $http_server . $http_dir_ws . 'index.php?Admin', 'icon' => 'gear', 'title' => OSCOM::getDef('button_admin'))); ?></p>
  </div>
</div>
