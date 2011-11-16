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

  $osC_Order = new osC_Order($_GET['oID']);
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td class="pageHeading"><?php echo nl2br(STORE_NAME_ADDRESS); ?></td>
        <td class="pageHeading" align="right"><?php echo osc_image('../images/store_logo.jpg', STORE_NAME); ?></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td><b><?php echo $osC_Language->get('subsection_billing_address'); ?></b></td>
          </tr>
          <tr>
            <td><?php echo osC_Address::format($osC_Order->getBilling(), '<br />'); ?></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><?php echo $osC_Order->getCustomer('telephone'); ?></td>
          </tr>
          <tr>
            <td><?php echo '<a href="mailto:' . $osC_Order->getCustomer('email_address') . '"><u>' . $osC_Order->getCustomer('email_address') . '</u></a>'; ?></td>
          </tr>
        </table></td>
        <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td><b><?php echo $osC_Language->get('subsection_shipping_address'); ?></b></td>
          </tr>
          <tr>
            <td><?php echo osC_Address::format($osC_Order->getDelivery(), '<br />'); ?></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><table border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td><b><?php echo $osC_Language->get('subsection_payment_method'); ?></b></td>
        <td><?php echo $osC_Order->getPaymentMethod(); ?></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
      <thead>
        <tr>
          <th colspan="2"><?php echo $osC_Language->get('table_heading_products'); ?></th>
          <th><?php echo $osC_Language->get('table_heading_product_model'); ?></th>
        </tr>
      </thead>
      <tbody>
<?php
    foreach ($osC_Order->getProducts() as $product) {
      echo '        <tr>' . "\n" .
           '          <td valign="top" align="right">' . $product['quantity'] . '&nbsp;x</td>' . "\n" .
           '          <td valign="top">' . $product['name'];

      if (isset($product['attributes']) && (sizeof($product['attributes']) > 0)) {
        foreach ($product['attributes'] as $attribute) {
          echo '<br /><nobr>&nbsp;&nbsp;&nbsp;' . $attribute['option'] . ': ' . $attribute['value'] . '</nobr>';
        }
      }

      echo '          </td>' . "\n" .
           '          <td valign="top">' . $product['model'] . '</td>' . "\n";
           '        </tr>' . "\n";
    }
?>
      </tbody>
    </table></td>
  </tr>
</table>
