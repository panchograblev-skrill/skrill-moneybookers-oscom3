<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\PDO;
  use osCommerce\OM\Core\Site\Shop\Specials;

  $specials_listing = Specials::getListing();
?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<div style="overflow: auto;">

<?php
  foreach ( $specials_listing['entries'] as $s ) {
    echo '<span style="width: 33%; float: left; text-align: center;">';

    if ( !empty($s['image']) ) {
      echo HTML::link(OSCOM::getLink(null, null, $s['products_keyword']), $OSCOM_Image->show($s['image'], $s['products_name'])) . '<br />';
    }

    echo HTML::link(OSCOM::getLink(null, null, $s['products_keyword']), $s['products_name']) . '<br />' .
         '<s>' . $OSCOM_Currencies->displayPrice($s['products_price'], $s['products_tax_class_id']) . '</s> <span class="productSpecialPrice">' . $OSCOM_Currencies->displayPrice($s['specials_new_products_price'], $s['products_tax_class_id']) . '</span>' .
         '</span>' . "\n";
  }
?>

</div>

<div class="listingPageLinks">
  <span style="float: right;"><?php echo PDO::getBatchPageLinks('page', $specials_listing['total'], OSCOM::getAllGET('page')); ?></span>

  <?php echo PDO::getBatchTotalPages(OSCOM::getDef('result_set_number_of_products'), (isset($_GET['page']) ? $_GET['page'] : 1), $specials_listing['total']); ?>
</div>
