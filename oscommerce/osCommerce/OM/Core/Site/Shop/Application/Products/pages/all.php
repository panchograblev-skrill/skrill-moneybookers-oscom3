<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\DateTime;
  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\PDO;
  use osCommerce\OM\Core\Site\Shop\Product;
  use osCommerce\OM\Core\Site\Shop\Products;
?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
  $OSCOM_Products = new Products();
  $OSCOM_Products->setSortBy('date_added', '-');

  $products_listing = $OSCOM_Products->execute();

  if ( $products_listing['total'] > 0 ) {
    foreach ( $products_listing['entries'] as $p ) {
      $OSCOM_Product = new Product($p['products_id']);
?>

  <tr>
    <td width="<?php echo $OSCOM_Image->getWidth('thumbnails') + 10; ?>" valign="top" align="center">

<?php
      if ( $OSCOM_Product->hasImage() ) {
        echo HTML::link(OSCOM::getLink(null, null, $OSCOM_Product->getKeyword()), $OSCOM_Image->show($OSCOM_Product->getImage(), $OSCOM_Product->getTitle()));
      }
?>

    </td>
    <td valign="top"><?php echo HTML::link(OSCOM::getLink(null, null, $OSCOM_Product->getKeyword()), '<b><u>' . $OSCOM_Product->getTitle() . '</u></b>') . '<br />' . OSCOM::getDef('date_added') . ' ' . DateTime::getLong($OSCOM_Product->getDateAdded()) . '<br />' . OSCOM::getDef('manufacturer') . ' ' . $OSCOM_Product->getManufacturer() . '<br /><br />' . OSCOM::getDef('price') . ' ' . $OSCOM_Product->getPriceFormated(); ?></td>
    <td align="right" valign="middle"><?php echo HTML::button(array('href' => OSCOM::getLink(null, 'Cart', 'Add&' . $OSCOM_Product->getKeyword()), 'icon' => 'cart', 'title' => OSCOM::getDef('button_add_to_cart'))); ?></td>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
  </tr>

<?php
    }
  } else {
?>

  <tr>
    <td><?php echo OSCOM::getDef('no_new_products'); ?></td>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
  </tr>

<?php
  }
?>

</table>

<div class="listingPageLinks">
  <span style="float: right;"><?php echo PDO::getBatchPageLinks('page', $products_listing['total'], OSCOM::getAllGET('page')); ?></span>

  <?php echo PDO::getBatchTotalPages(OSCOM::getDef('result_set_number_of_products'), (isset($_GET['page']) ? $_GET['page'] : 1), $products_listing['total']); ?>
</div>
