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

<div class="mainBlock">
  <ul style="list-style-type: none; padding: 5px; margin: 0px; display: inline; float: right;">
    <li style="font-weight: bold; display: inline;"><?php echo OSCOM::getDef('title_language'); ?></li>

<?php
  foreach ( $OSCOM_Language->getAll() as $available_language ) {
?>

    <li style="display: inline;"><?php echo '<a href="' . OSCOM::getLink(null, null, 'language=' . $available_language['code']) . '">' . $OSCOM_Language->showImage($available_language['code']) . '</a>'; ?></li>

<?php      
  }
?>

  </ul>

  <h1><?php echo OSCOM::getDef('page_title_welcome'); ?></h1>

  <p><?php echo OSCOM::getDef('text_welcome'); ?></p>
</div>

<div class="contentBlock">
  <div class="infoPane">
    <h3><?php echo OSCOM::getDef('box_server_title'); ?></h3>

    <div class="infoPaneContents">
      <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
          <td><b><?php echo OSCOM::getDef('box_server_php_version'); ?></b></td>
          <td align="right"><?php echo phpversion(); ?></td>
          <td align="right" width="25"><img src="<?php echo OSCOM::getPublicSiteLink('templates/default/images/' . ((version_compare(PHP_VERSION, '5.3') === 1) ? 'tick.gif' : 'cross.gif')); ?>" alt="" border="0" width="16" height="16" /></td>
        </tr>
      </table>

      <br />

      <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
          <td><b><?php echo OSCOM::getDef('box_server_php_settings'); ?></b></td>
          <td align="right"></td>
          <td align="right" width="25"></td>
        </tr>
        <tr>
          <td><?php echo OSCOM::getDef('box_server_register_globals'); ?></td>
          <td align="right"><?php echo (((int)ini_get('register_globals') === 0) ? OSCOM::getDef('box_server_off') : OSCOM::getDef('box_server_on')); ?></td>
          <td align="right"><img src="<?php echo OSCOM::getPublicSiteLink('templates/default/images/' . (((int)ini_get('register_globals') === 0) ? 'tick.gif' : 'cross.gif')); ?>" alt="" border="0" width="16" height="16" /></td>
        </tr>
        <tr>
          <td><?php echo OSCOM::getDef('box_server_magic_quotes_gpc'); ?></td>
          <td align="right"><?php echo (((int)ini_get('magic_quotes_gpc') === 0) ? OSCOM::getDef('box_server_off') : OSCOM::getDef('box_server_on')); ?></td>
          <td align="right"><img src="<?php echo OSCOM::getPublicSiteLink('templates/default/images/' . (((int)ini_get('magic_quotes_gpc') === 0) ? 'tick.gif' : 'cross.gif')); ?>" alt="" border="0" width="16" height="16" /></td>
        </tr>
        <tr>
          <td><?php echo OSCOM::getDef('box_server_file_uploads'); ?></td>
          <td align="right"><?php echo (((int)ini_get('file_uploads') === 0) ? OSCOM::getDef('box_server_off') : OSCOM::getDef('box_server_on')); ?></td>
          <td align="right"><img src="<?php echo OSCOM::getPublicSiteLink('templates/default/images/' . (((int)ini_get('file_uploads') === 1) ? 'tick.gif' : 'cross.gif')); ?>" alt="" border="0" width="16" height="16" /></td>
        </tr>
        <tr>
          <td><?php echo OSCOM::getDef('box_server_session_auto_start'); ?></td>
          <td align="right"><?php echo (((int)ini_get('session.auto_start') === 0) ? OSCOM::getDef('box_server_off') : OSCOM::getDef('box_server_on')); ?></td>
          <td align="right"><img src="<?php echo OSCOM::getPublicSiteLink('templates/default/images/' . (((int)ini_get('session.auto_start') === 0) ? 'tick.gif' : 'cross.gif')); ?>" alt="" border="0" width="16" height="16" /></td>
        </tr>
        <tr>
          <td><?php echo OSCOM::getDef('box_server_session_use_trans_sid'); ?></td>
          <td align="right"><?php echo (((int)ini_get('session.use_trans_sid') === 0) ? OSCOM::getDef('box_server_off') : OSCOM::getDef('box_server_on')); ?></td>
          <td align="right"><img src="<?php echo OSCOM::getPublicSiteLink('templates/default/images/' . (((int)ini_get('session.use_trans_sid') === 0) ? 'tick.gif' : 'cross.gif')); ?>" alt="" border="0" width="16" height="16" /></td>
        </tr>
      </table>

      <br />

      <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
          <td><b><?php echo OSCOM::getDef('box_server_php_extensions'); ?></b></td>
          <td align="right" width="25"></td>
        </tr>
        <tr>
          <td><?php echo OSCOM::getDef('box_server_pdo_mysql'); ?></td>
          <td align="right"><img src="<?php echo OSCOM::getPublicSiteLink('templates/default/images/' . (extension_loaded('pdo_mysql') ? 'tick.gif' : 'cross.gif')); ?>" alt="" border="0" width="16" height="16" /></td>
        </tr>
        <tr>
          <td><?php echo OSCOM::getDef('box_server_pdo_sqlite'); ?></td>
          <td align="right"><img src="<?php echo OSCOM::getPublicSiteLink('templates/default/images/' . (extension_loaded('pdo_sqlite') ? 'tick.gif' : 'cross.gif')); ?>" alt="" border="0" width="16" height="16" /></td>
        </tr>
        <tr>
          <td><?php echo OSCOM::getDef('box_server_phar'); ?></td>
          <td align="right"><img src="<?php echo OSCOM::getPublicSiteLink('templates/default/images/' . (extension_loaded('phar') ? 'tick.gif' : 'cross.gif')); ?>" alt="" border="0" width="16" height="16" /></td>
        </tr>
        <tr>
          <td><?php echo OSCOM::getDef('box_server_gd'); ?></td>
          <td align="right"><img src="<?php echo OSCOM::getPublicSiteLink('templates/default/images/' . (extension_loaded('gd') ? 'tick.gif' : 'cross.gif')); ?>" alt="" border="0" width="16" height="16" /></td>
        </tr>
        <tr>
          <td><?php echo OSCOM::getDef('box_server_curl'); ?></td>
          <td align="right"><img src="<?php echo OSCOM::getPublicSiteLink('templates/default/images/' . (extension_loaded('curl') ? 'tick.gif' : 'cross.gif')); ?>" alt="" border="0" width="16" height="16" /></td>
        </tr>
        <tr>
          <td><?php echo OSCOM::getDef('box_server_openssl'); ?></td>
          <td align="right"><img src="<?php echo OSCOM::getPublicSiteLink('templates/default/images/' . (extension_loaded('openssl') ? 'tick.gif' : 'cross.gif')); ?>" alt="" border="0" width="16" height="16" /></td>
        </tr>
      </table>
    </div>
  </div>

  <div class="contentPane">
    <h2><?php echo OSCOM::getDef('page_heading_server_requirements'); ?></h2>

    <ul>
      <li>PHP v5.3+ (with PDO MySQL extension)</li>
      <li>MySQL v4.1.13+ or v5.0.7+</li>
    </ul>

    <h2><?php echo OSCOM::getDef('page_heading_installation_type'); ?></h2>

<?php
  if ( file_exists(OSCOM::BASE_DIRECTORY . 'config.php') && !is_writeable(OSCOM::BASE_DIRECTORY . 'config.php') ) {
?>

    <div class="noticeBox">
      <p><?php echo sprintf(OSCOM::getDef('error_configuration_file_not_writeable'), OSCOM::BASE_DIRECTORY . 'config.php'); ?></p>
      <p><?php echo OSCOM::getDef('error_configuration_file_alternate_method'); ?></p>
    </div>

    <br />

<?php
  }
?>

    <p><?php echo OSCOM::getDef('text_installation_type'); ?></p>

    <p align="center"><?php echo HTML::button(array('href' => OSCOM::getLink(null, 'Install'), 'priority' => 'primary', 'icon' => 'triangle-1-e', 'title' => OSCOM::getDef('button_new_install'))); ?></p>
  </div>
</div>
