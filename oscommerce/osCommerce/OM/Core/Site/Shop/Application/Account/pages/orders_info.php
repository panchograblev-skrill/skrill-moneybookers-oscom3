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
  use osCommerce\OM\Core\Site\Shop\Address;
  use osCommerce\OM\Core\Site\Shop\Order;
  use osCommerce\OM\Core\Site\Shop\Tax;

  $order = new Order($_GET['Orders']);
?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<div class="moduleBox">
  <span style="float: right;"><h6><?php echo OSCOM::getDef('order_total_heading') . ' ' . $order->info['total']; ?></h6></span>

  <h6><?php echo OSCOM::getDef('order_date_heading') . ' ' . DateTime::getLong($order->info['date_purchased']) . ' <small>(' . $order->info['orders_status'] . ')</small>'; ?></h6>

  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="30%" valign="top">

<?php
  if ( $order->delivery != false ) {
?>

          <h6><?php echo OSCOM::getDef('order_delivery_address_title'); ?></h6>

          <p><?php echo Address::format($order->delivery, '<br />'); ?></p>

<?php
    if ( !empty($order->info['shipping_method']) ) {
?>

          <h6><?php echo OSCOM::getDef('order_shipping_method_title'); ?></h6>

          <p><?php echo $order->info['shipping_method']; ?></p>

<?php
    }
  }
?>

          <h6><?php echo OSCOM::getDef('order_billing_address_title'); ?></h6>

          <p><?php echo Address::format($order->billing, '<br />'); ?></p>

          <h6><?php echo OSCOM::getDef('order_payment_method_title'); ?></h6>

          <p><?php echo $order->info['payment_method']; ?></p>
        </td>
        <td width="70%" valign="top">
          <div style="border: 1px; border-style: solid; border-color: #CCCCCC; background-color: #FBFBFB; padding: 5px;">
            <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
  if ( count($order->info['tax_groups']) > 1 ) {
?>

              <tr>
                <td colspan="2"><h6><?php echo OSCOM::getDef('order_products_title'); ?></h6></td>
                <td align="right"><h6><?php echo OSCOM::getDef('order_tax_title'); ?></h6></td>
                <td align="right"><h6><?php echo OSCOM::getDef('order_total_title'); ?></h6></td>
              </tr>

<?php
  } else {
?>

              <tr>
                <td colspan="3"><h6><?php echo OSCOM::getDef('order_products_title'); ?></h6></td>
              </tr>

<?php
  }

  foreach ( $order->products as $product ) {
    echo '              <tr>' . "\n" .
         '                <td align="right" valign="top" width="30">' . $product['qty'] . '&nbsp;x</td>' . "\n" .
         '                <td valign="top">' . $product['name'];

    if ( isset($product['attributes']) && (count($product['attributes']) > 0) ) {
      foreach ( $product['attributes'] as $attribute ) {
        echo '<br /><nobr><small>&nbsp;<i> - ' . $attribute['option'] . ': ' . $attribute['value'] . '</i></small></nobr>';
      }
    }

    echo '</td>' . "\n";

    if ( count($order->info['tax_groups']) > 1 ) {
      echo '                <td valign="top" align="right">' . Tax::displayTaxRateValue($product['tax']) . '</td>' . "\n";
    }

    echo '                <td align="right" valign="top">' . $OSCOM_Currencies->displayPriceWithTaxRate($product['price'], $product['tax'], $product['qty'], false, $order->info['currency'], $order->info['currency_value']) . '</td>' . "\n" .
         '              </tr>' . "\n";
  }
?>

            </table>

            <p>&nbsp;</p>

            <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
  foreach ( $order->totals as $total ) {
    echo '              <tr>' . "\n" .
         '                <td align="right">' . $total['title'] . '</td>' . "\n" .
         '                <td align="right">' . $total['text'] . '</td>' . "\n" .
         '              </tr>' . "\n";
  }
?>

            </table>
          </div>
        </td>
      </tr>
    </table>
  </div>
</div>

<?php
  $Qstatus = $order->getStatusListing();

  $status = $Qstatus->fetchAll();

  if ( count($status) > 0 ) {
?>

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('order_history_heading'); ?></h6>

  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
    foreach ( $status as $s ) {
      echo '    <tr>' . "\n" .
           '      <td valign="top" width="70">' . DateTime::getShort($s['date_added']) . '</td>' . "\n" .
           '      <td valign="top" width="70">' . $s['orders_status_name'] . '</td>' . "\n" .
           '      <td valign="top">' . (strlen(HTML::outputProtected($s['comments'])) > 0 ? nl2br(HTML::outputProtected($s['comments'])) : '&nbsp;') . '</td>' . "\n" .
           '    </tr>' . "\n";
    }
?>

    </table>
  </div>
</div>

<?php
  }

// HPDL
//  if ( DOWNLOAD_ENABLED == '1' ) {
//    include('includes/modules/downloads.php');
//  }
?>


<div class="submitFormButtons">
  <?php echo HTML::button(array('href' => OSCOM::getLink(null, null, 'Orders' . (isset($_GET['page']) ? '&page=' . $_GET['page'] : ''), 'SSL'), 'icon' => 'triangle-1-w', 'title' => OSCOM::getDef('button_back'))); ?>
</div>
