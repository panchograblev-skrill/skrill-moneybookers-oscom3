<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;

  if ( $OSCOM_NavigationHistory->hasSnapshot() ) {
    $origin_href = $OSCOM_NavigationHistory->getSnapshotURL();
    $OSCOM_NavigationHistory->resetSnapshot();
  } else {
    $origin_href = OSCOM::getLink(null, OSCOM::getDefaultSiteApplication());
  }
?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<div>
  <div style="padding-top: 30px;">
    <p><?php echo sprintf(OSCOM::getDef('success_account_created'), OSCOM::getLink(null, 'Info', 'Contact')); ?></p>
  </div>
</div>

<div class="submitFormButtons" style="text-align: right;">
  <?php echo HTML::button(array('href' => $origin_href, 'icon' => 'triangle-1-e', 'title' => OSCOM::getDef('button_continue'))); ?>
</div>
