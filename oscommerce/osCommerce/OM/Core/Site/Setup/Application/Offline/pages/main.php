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

  <h1><?php echo OSCOM::getDef('page_title_authorization_required'); ?></h1>
</div>

<div class="contentBlock">
  <div class="contentPane" style="margin-left: 0;">
    <h2><?php echo OSCOM::getDef('page_heading_access_disabled'); ?></h2>

    <p><?php echo OSCOM::getDef('text_access_disabled'); ?></p>

    <p align="center"><?php echo HTML::button(array('href' => OSCOM::getLink(null, OSCOM::getDefaultSiteApplication()), 'priority' => 'primary', 'icon' => 'triangle-1-e', 'title' => OSCOM::getDef('button_continue'))); ?></p>
  </div>
</div>
