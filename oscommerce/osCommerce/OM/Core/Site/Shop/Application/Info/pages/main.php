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

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('information_title'); ?></h6>

  <div class="content">
    <ul style="padding-left: 50px;">
      <li><?php echo HTML::link(OSCOM::getLink(null, null, 'Shipping'), OSCOM::getDef('box_information_shipping')); ?></li>
      <li><?php echo HTML::link(OSCOM::getLink(null, null, 'Privacy'), OSCOM::getDef('box_information_privacy')); ?></li>
      <li><?php echo HTML::link(OSCOM::getLink(null, null, 'Conditions'), OSCOM::getDef('box_information_conditions')); ?></li>
      <li><?php echo HTML::link(OSCOM::getLink(null, null, 'Contact'), OSCOM::getDef('box_information_contact')); ?></li>
      <li><?php echo HTML::link(OSCOM::getLink(null, null, 'Sitemap'), OSCOM::getDef('box_information_sitemap')); ?></li>
    </ul>

    <div style="clear: both;"></div>
  </div>
</div>
