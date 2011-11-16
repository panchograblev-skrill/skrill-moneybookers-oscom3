<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;

  $products_listing = $OSCOM_Search->getResult();
?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<?php
  require(OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Application/Products/pages/product_listing.php');
?>

<div class="submitFormButtons">
  <?php echo HTML::button(array('href' => OSCOM::getLink(), 'icon' => 'triangle-1-w', 'title' => OSCOM::getDef('button_back'))); ?>
</div>
