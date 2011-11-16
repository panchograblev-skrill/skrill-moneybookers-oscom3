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
  use osCommerce\OM\Core\Site\Shop\Product;
  use osCommerce\OM\Core\Site\Shop\Products;

// create column list
  $define_list = array('PRODUCT_LIST_MODEL' => PRODUCT_LIST_MODEL,
                       'PRODUCT_LIST_NAME' => PRODUCT_LIST_NAME,
                       'PRODUCT_LIST_MANUFACTURER' => PRODUCT_LIST_MANUFACTURER,
                       'PRODUCT_LIST_PRICE' => PRODUCT_LIST_PRICE,
                       'PRODUCT_LIST_QUANTITY' => PRODUCT_LIST_QUANTITY,
                       'PRODUCT_LIST_WEIGHT' => PRODUCT_LIST_WEIGHT,
                       'PRODUCT_LIST_IMAGE' => PRODUCT_LIST_IMAGE,
                       'PRODUCT_LIST_BUY_NOW' => PRODUCT_LIST_BUY_NOW);

  asort($define_list);

  $column_list = array();

  foreach ( $define_list as $key => $value ) {
    if ($value > 0) $column_list[] = $key;
  }

  if ( (count($products_listing['entries']) > 0) && ( (PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3') ) ) {
?>

<div class="listingPageLinks">
  <span style="float: right;"><?php echo PDO::getBatchPageLinks('page', $products_listing['total'], OSCOM::getAllGET('page')); ?></span>

  <?php echo PDO::getBatchTotalPages(OSCOM::getDef('result_set_number_of_products'), (isset($_GET['page']) ? $_GET['page'] : 1), $products_listing['total']); ?>
</div>

<?php
  }
?>

<div>
  
<?php
  if ( count($products_listing['entries']) > 0 ) {
?>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>

<?php
    for ($col=0, $n=sizeof($column_list); $col<$n; $col++) {
      $lc_key = false;
      $lc_align = '';

      switch ($column_list[$col]) {
        case 'PRODUCT_LIST_MODEL':
          $lc_text = OSCOM::getDef('listing_model_heading');
          $lc_key = 'model';
          break;
        case 'PRODUCT_LIST_NAME':
          $lc_text = OSCOM::getDef('listing_products_heading');
          $lc_key = 'name';
          break;
        case 'PRODUCT_LIST_MANUFACTURER':
          $lc_text = OSCOM::getDef('listing_manufacturer_heading');
          $lc_key = 'manufacturer';
          break;
        case 'PRODUCT_LIST_PRICE':
          $lc_text = OSCOM::getDef('listing_price_heading');
          $lc_key = 'price';
          $lc_align = 'right';
          break;
        case 'PRODUCT_LIST_QUANTITY':
          $lc_text = OSCOM::getDef('listing_quantity_heading');
          $lc_key = 'quantity';
          $lc_align = 'right';
          break;
        case 'PRODUCT_LIST_WEIGHT':
          $lc_text = OSCOM::getDef('listing_weight_heading');
          $lc_key = 'weight';
          $lc_align = 'right';
          break;
        case 'PRODUCT_LIST_IMAGE':
          $lc_text = OSCOM::getDef('listing_image_heading');
          $lc_align = 'center';
          break;
        case 'PRODUCT_LIST_BUY_NOW':
          $lc_text = OSCOM::getDef('listing_buy_now_heading');
          $lc_align = 'center';
          break;
      }

      if ($lc_key !== false) {
        $lc_text = Products::getListingSortLink($lc_key, $lc_text);
      }

      echo '      <td align="' . $lc_align . '" class="productListing-heading">&nbsp;' . $lc_text . '&nbsp;</td>' . "\n";
    }
?>

    </tr>

<?php
    $rows = 0;

    foreach ( $products_listing['entries'] as $p ) {
      $OSCOM_Product = new Product($p['products_id']);

      $rows++;

      echo '    <tr class="' . ((($rows/2) == floor($rows/2)) ? 'productListing-even' : 'productListing-odd') . '">' . "\n";

      for ($col=0, $n=sizeof($column_list); $col<$n; $col++) {
        $lc_align = '';

        switch ($column_list[$col]) {
          case 'PRODUCT_LIST_MODEL':
            $lc_align = '';
            $lc_text = '&nbsp;' . $OSCOM_Product->getModel() . '&nbsp;';
            break;
          case 'PRODUCT_LIST_NAME':
            $lc_align = '';
            if (isset($_GET['manufacturers'])) {
              $lc_text = HTML::link(OSCOM::getLink(null, 'Products', $OSCOM_Product->getKeyword() . '&manufacturers=' . $_GET['manufacturers']), $OSCOM_Product->getTitle());
            } else {
              $lc_text = '&nbsp;' . HTML::link(OSCOM::getLink(null, 'Products', $OSCOM_Product->getKeyword() . ($OSCOM_Category->getID() > 0 ? '&cPath=' . $OSCOM_Category->getPath() : '')), $OSCOM_Product->getTitle()) . '&nbsp;';
            }
            break;
          case 'PRODUCT_LIST_MANUFACTURER':
            $lc_align = '';
            $lc_text = '&nbsp;';

            if ( $OSCOM_Product->hasManufacturer() ) {
              $lc_text = '&nbsp;' . HTML::link(OSCOM::getLink(null, 'Index', 'Manufacturers=' . $OSCOM_Product->getManufacturerID()), $OSCOM_Product->getManufacturer()) . '&nbsp;';
            }
            break;
          case 'PRODUCT_LIST_PRICE':
            $lc_align = 'right';
            $lc_text = '&nbsp;' . $OSCOM_Product->getPriceFormated() . '&nbsp;';
            break;
          case 'PRODUCT_LIST_QUANTITY':
            $lc_align = 'right';
            $lc_text = '&nbsp;' . $OSCOM_Product->getQuantity() . '&nbsp;';
            break;
          case 'PRODUCT_LIST_WEIGHT':
            $lc_align = 'right';
            $lc_text = '&nbsp;' . $OSCOM_Product->getWeight() . '&nbsp;';
            break;
          case 'PRODUCT_LIST_IMAGE':
            $lc_align = 'center';
            if (isset($_GET['manufacturers'])) {
              $lc_text = HTML::link(OSCOM::getLink(null, 'Products', $OSCOM_Product->getKeyword() . '&manufacturers=' . $_GET['manufacturers']), $OSCOM_Image->show($OSCOM_Product->getImage(), $OSCOM_Product->getTitle()));
            } else {
              $lc_text = '&nbsp;' . HTML::link(OSCOM::getLink(null, 'Products', $OSCOM_Product->getKeyword() . ($OSCOM_Category->getID() > 0 ? '&cPath=' . $OSCOM_Category->getPath() : '')), $OSCOM_Image->show($OSCOM_Product->getImage(), $OSCOM_Product->getTitle())) . '&nbsp;';
            }
            break;
          case 'PRODUCT_LIST_BUY_NOW':
            $lc_align = 'center';
            $lc_text = HTML::button(array('href' => OSCOM::getLink(null, 'Cart', 'Add&' . $OSCOM_Product->getKeyword()), 'icon' => 'cart', 'title' => OSCOM::getDef('button_buy_now')));
            break;
        }

        echo '      <td ' . ((empty($lc_align) === false) ? 'align="' . $lc_align . '" ' : '') . 'class="productListing-data">' . $lc_text . '</td>' . "\n";
      }

      echo '    </tr>' . "\n";
    }
?>

  </table>

<?php
  } else {
    echo OSCOM::getDef('no_products_in_category');
  }
?>

</div>

<?php
  if ( (count($products_listing['entries']) > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3')) ) {
?>

<div class="listingPageLinks">
  <span style="float: right;"><?php echo PDO::getBatchPageLinks('page', $products_listing['total'], OSCOM::getAllGET('page')); ?></span>

  <?php echo PDO::getBatchTotalPages(OSCOM::getDef('result_set_number_of_products'), (isset($_GET['page']) ? $_GET['page'] : 1), $products_listing['total']); ?>
</div>

<?php
  }
?>
