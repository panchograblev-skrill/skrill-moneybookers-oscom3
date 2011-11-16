<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  require('includes/classes/tax.php');
  $osC_Tax = new osC_Tax_Admin();

  $osC_Order = new osC_Order($_GET['oID']);

  if ( !$osC_Order->isValid() ) {
    $osC_MessageStack->add($osC_Template->getModule(), sprintf(ERROR_ORDER_DOES_NOT_EXIST, $_GET['oID']), 'error');
  }

  $tabIndex = 0;

  if ( isset($_GET['tabIndex']) && !empty($_GET['tabIndex']) ) {
    switch ( $_GET['tabIndex'] ) {
      case 'tabProducts':
        $tabIndex = 1;
        break;

      case 'tabTransactionHistory':
        $tabIndex = 2;
        break;

      case 'tabStatusHistory':
        $tabIndex = 3;
        break;
    }
  }
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<p align="right">
  <?php echo '<input type="button" value="' . $osC_Language->get('button_orders_invoice') . '" onclick="window.open(\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&oID=' . $_GET['oID'] . '&action=invoice') . '\');" class="infoBoxButton"/> <input type="button" value="' . $osC_Language->get('button_orders_packaging_slip') . '" onclick="window.open(\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&oID=' . $_GET['oID'] . '&action=packaging_slip') . '\');" class="infoBoxButton" /> <input type="button" value="' . $osC_Language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&' . (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page']) . '\';" class="operationButton" />'; ?>
</p>

<?php
  if ( $osC_Order->isValid() ) {
?>

<script type="text/javascript">
  var tabIndex = <?php echo (int)$tabIndex; ?>;

  $(document).ready(function(){
    $("#orderTabs").tabs( { selected: tabIndex } );
  });
</script>

<div id="orderTabs">
  <ul>
    <li><?php echo osc_link_object('#section_summary_content', $osC_Language->get('section_summary')); ?></li>
    <li><?php echo osc_link_object('#section_products_content', $osC_Language->get('section_products')); ?></li>
    <li><?php echo osc_link_object('#section_transaction_history_content', $osC_Language->get('section_transaction_history')); ?></li>
    <li><?php echo osc_link_object('#section_status_history_content', $osC_Language->get('section_status_history')); ?></li>
  </ul>

  <div id="section_summary_content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="33%" valign="top">
          <fieldset style="border: 0; height: 100%;">
            <legend style="margin-left: -20px; font-weight: bold;"><?php echo osc_icon('personal.png') . ' ' . $osC_Language->get('subsection_customer'); ?></legend>

            <p><?php echo osC_Address::format($osC_Order->getCustomer(), '<br />'); ?></p>
            <p><?php echo osc_icon('telephone.png') . ' ' . $osC_Order->getCustomer('telephone') . '<br />' . osc_icon('write.png') . ' ' . $osC_Order->getCustomer('email_address'); ?></p>
          </fieldset>
        </td>
        <td width="33%" valign="top">
          <fieldset style="border: 0; height: 100%;">
            <legend style="margin-left: -20px; font-weight: bold;"><?php echo osc_icon('home.png') . ' ' . $osC_Language->get('subsection_shipping_address'); ?></legend>

            <p><?php echo osC_Address::format($osC_Order->getDelivery(), '<br />'); ?></p>
          </fieldset>
        </td>
        <td width="33%" valign="top">
          <fieldset style="border: 0; height: 100%;">
            <legend style="margin-left: -20px; font-weight: bold;"><?php echo osc_icon('bill.png') . ' ' . $osC_Language->get('subsection_billing_address'); ?></legend>

            <p><?php echo osC_Address::format($osC_Order->getBilling(), '<br />'); ?></p>
          </fieldset>
        </td>
      </tr>
      <tr>
        <td width="33%" valign="top">
          <fieldset style="border: 0; height: 100%;">
            <legend style="margin-left: -20px; font-weight: bold;"><?php echo osc_icon('payment.png') . ' ' . $osC_Language->get('subsection_payment_method'); ?></legend>

            <p><?php echo $osC_Order->getPaymentMethod(); ?></p>

<?php
    if ( $osC_Order->isValidCreditCard() ) {
?>

            <table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td><?php echo $osC_Language->get('credit_card_type'); ?></td>
                <td><?php echo $osC_Order->getCreditCardDetails('type'); ?></td>
              </tr>
              <tr>
                <td><?php echo $osC_Language->get('credit_card_owner_name'); ?></td>
                <td><?php echo $osC_Order->getCreditCardDetails('owner'); ?></td>
              </tr>
              <tr>
                <td><?php echo $osC_Language->get('credit_card_number'); ?></td>
                <td><?php echo $osC_Order->getCreditCardDetails('number'); ?></td>
              </tr>
              <tr>
                <td><?php echo $osC_Language->get('credit_card_expiry_date'); ?></td>
                <td><?php echo $osC_Order->getCreditCardDetails('expires'); ?></td>
              </tr>
            </table>

<?php
    }
?>
          </fieldset>
        </td>
        <td width="33%" valign="top">
          <fieldset style="border: 0; height: 100%;">
            <legend style="margin-left: -20px; font-weight: bold;"><?php echo osc_icon('history.png') . ' ' . $osC_Language->get('subsection_status'); ?></legend>

            <p><?php echo $osC_Order->getStatus() . '<br />' . ($osC_Order->getDateLastModified() > $osC_Order->getDateCreated() ? osC_DateTime::getShort($osC_Order->getDateLastModified(), true) : osC_DateTime::getShort($osC_Order->getDateCreated(), true)); ?></p>
            <p><?php echo $osC_Language->get('number_of_comments') . ' ' . $osC_Order->getNumberOfComments(); ?></p>
          </fieldset>
        </td>
        <td width="33%" valign="top">
          <fieldset style="border: 0; height: 100%;">
            <legend style="margin-left: -20px; font-weight: bold;"><?php echo osc_icon('calculator.png') . ' ' . $osC_Language->get('subsection_total'); ?></legend>

            <p><?php echo $osC_Order->getTotal(); ?></p>
            <p><?php echo $osC_Language->get('number_of_products') . ' ' . $osC_Order->getNumberOfProducts() . '<br />' . $osC_Language->get('number_of_items') . ' ' . $osC_Order->getNumberOfItems(); ?></p>
          </fieldset>
        </td>
      </tr>
    </table>
  </div>

  <div id="section_products_content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
      <thead>
        <tr>
          <th colspan="2"><?php echo $osC_Language->get('table_heading_products'); ?></th>
          <th><?php echo $osC_Language->get('table_heading_product_model'); ?></th>
          <th><?php echo $osC_Language->get('table_heading_tax'); ?></th>
          <th><?php echo $osC_Language->get('table_heading_price_net'); ?></th>
          <th><?php echo $osC_Language->get('table_heading_price_gross'); ?></th>
          <th><?php echo $osC_Language->get('table_heading_total_net'); ?></th>
          <th><?php echo $osC_Language->get('table_heading_total_gross'); ?></th>
        </tr>
      </thead>
      <tbody>

<?php
    foreach ( $osC_Order->getProducts() as $products ) {
?>

        <tr>
          <td valign="top" align="right"><?php echo $products['quantity'] . '&nbsp;x'; ?></td>
          <td valign="top">

<?php
      echo $products['name'];

      if ( isset($products['attributes']) && is_array($products['attributes']) && ( sizeof($products['attributes']) > 0 ) ) {
        foreach ( $products['attributes'] as $attributes ) {
          echo '<br /><nobr>&nbsp;&nbsp;&nbsp;<i>' . $attributes['option'] . ': ' . $attributes['value'] . '</i></nobr>';
        }
      }
?>

          </td>
          <td valign="top"><?php echo $products['model']; ?></td>
          <td valign="top" align="right"><?php echo $osC_Tax->displayTaxRateValue($products['tax']); ?></td>
          <td valign="top" align="right"><?php echo $osC_Currencies->format($products['price'], $osC_Order->getCurrency(), $osC_Order->getCurrencyValue()); ?></td>
          <td valign="top" align="right"><?php echo $osC_Currencies->displayPriceWithTaxRate($products['price'], $products['tax'], 1, true, $osC_Order->getCurrency(), $osC_Order->getCurrencyValue()); ?></td>
          <td valign="top" align="right"><?php echo $osC_Currencies->format($products['price'] * $products['quantity'], $osC_Order->getCurrency(), $osC_Order->getCurrencyValue()); ?></td>
          <td valign="top" align="right"><?php echo $osC_Currencies->displayPriceWithTaxRate($products['price'], $products['tax'], $products['quantity'], true, $osC_Order->getCurrency(), $osC_Order->getCurrencyValue()); ?></td>
        </tr>

<?php
    }
?>

      </tbody>
    </table>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tbody>

<?php
    foreach ( $osC_Order->getTotals() as $totals ) {
?>

        <tr>
          <td align="right"><?php echo $totals['title']; ?></td>
          <td align="right"><?php echo $totals['text']; ?></td>
        </tr>

<?php
    }
?>

      </tbody>
    </table>
  </div>

  <div id="section_transaction_history_content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
      <thead>
        <tr>
          <th width="130"><?php echo $osC_Language->get('table_heading_date_added'); ?></th>
          <th width="50"><?php echo $osC_Language->get('table_heading_status'); ?></th>
          <th width="20">&nbsp;</th>
          <th><?php echo $osC_Language->get('table_heading_comments'); ?></th>
        </tr>
      </thead>
      <tbody>

<?php
    foreach ( $osC_Order->getTransactionHistory() as $history ) {
?>

        <tr>
          <td valign="top"><?php echo osC_DateTime::getShort($history['date_added'], true); ?></td>
          <td valign="top"><?php echo ( !empty($history['status']) ) ? $history['status'] : $history['status_id']; ?></td>
          <td valign="top" align="center"><?php echo osc_icon(($history['return_status'] === 1 ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif'), null, null); ?></td>
          <td valign="top"><?php echo nl2br($history['return_value']); ?></td>
        </tr>

<?php
    }
?>

      </tbody>
    </table>

<?php
    if ( $osC_Order->hasPostTransactionActions() ) {
?>

    <br />

    <form name="transaction" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&' . (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&oID=' . $_GET['oID'] . '&action=updateTransaction'); ?>" method="post">

    <p><?php echo $osC_Language->get('field_post_transaction_actions') . ' '. osc_draw_pull_down_menu('transaction', $osC_Order->getPostTransactionActions()) . ' ' . osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_execute') . '" class="operationButton" />'; ?></p>

    </form>

<?php
    }
?>

  </div>

  <div id="section_status_history_content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
      <thead>
        <tr>
          <th><?php echo $osC_Language->get('table_heading_date_added'); ?></th>
          <th><?php echo $osC_Language->get('table_heading_status'); ?></th>
          <th><?php echo $osC_Language->get('table_heading_comments'); ?></th>
          <th align="right"><?php echo $osC_Language->get('table_heading_customer_notified'); ?></th>
        </tr>
      </thead>
      <tbody>

<?php
    foreach ( $osC_Order->getStatusHistory() as $status_history ) {
?>

        <tr>
          <td valign="top"><?php echo osC_DateTime::getShort($status_history['date_added'], true); ?></td>
          <td valign="top"><?php echo $status_history['status']; ?></td>
          <td valign="top"><?php echo nl2br($status_history['comment']); ?></td>
          <td align="right" valign="top"><?php echo osc_icon((($status_history['customer_notified'] === 1) ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif'), null, null); ?></td>
        </tr>

<?php
    }
?>
      </tbody>
    </table>

    <br />

    <form name="status" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&' . (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&oID=' . $_GET['oID'] . '&action=updateStatus'); ?>" method="post">

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><?php echo $osC_Language->get('field_status'); ?></td>
        <td><?php echo osc_draw_pull_down_menu('status', $orders_statuses, $osC_Order->getStatusID()); ?></td>
      </tr>
      <tr>
        <td valign="top"><?php echo $osC_Language->get('field_add_comment'); ?></td>
        <td><?php echo osc_draw_textarea_field('comment', null, null, null, 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td><?php echo $osC_Language->get('field_notify_customer'); ?></td>
        <td><?php echo osc_draw_checkbox_field('notify_customer', null, true); ?></td>
      </tr>
        <td><?php echo $osC_Language->get('field_notify_customer_with_comments'); ?></td>
        <td><?php echo osc_draw_checkbox_field('append_comment', null, true); ?></td>
      </tr>
      <tr>
        <td colspan="2" align="right"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_update') . '" class="operationButton" />'; ?></td>
      </tr>
    </table>

    </form>
  </div>
</div>

<?php
  }
?>
