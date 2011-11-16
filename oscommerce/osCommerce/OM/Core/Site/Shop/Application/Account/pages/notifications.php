<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;

// HPDL Should be moved to the customers class!
  $Qglobal = $OSCOM_PDO->prepare('select global_product_notifications from :table_customers where customers_id = :customers_id');
  $Qglobal->bindInt(':customers_id', $OSCOM_Customer->getID());
  $Qglobal->execute();
?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<form name="account_notifications" action="<?php echo OSCOM::getLink(null, null, 'Notifications&Process', 'SSL'); ?>" method="post">

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('newsletter_product_notifications'); ?></h6>

  <div class="content">
    <?php echo OSCOM::getDef('newsletter_product_notifications_description'); ?>
  </div>
</div>

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('newsletter_product_notifications_global'); ?></h6>

  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="30"><?php echo HTML::checkboxField('product_global', '1', $Qglobal->value('global_product_notifications')); ?></td>
        <td><b><?php echo HTML::label(OSCOM::getDef('newsletter_product_notifications_global'), 'product_global'); ?></b></td>
      </tr>
      <tr>
        <td width="30">&nbsp;</td>
        <td><?php echo OSCOM::getDef('newsletter_product_notifications_global_description'); ?></td>
      </tr>
    </table>
  </div>
</div>

<?php
  if ( $Qglobal->valueInt('global_product_notifications') !== 1 ) {
?>

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('newsletter_product_notifications_products'); ?></h6>

  <div class="content">

<?php
    if ( $OSCOM_Customer->hasProductNotifications() ) {
?>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td colspan="2"><?php echo OSCOM::getDef('newsletter_product_notifications_products_description'); ?></td>
      </tr>

<?php
      $Qproducts = $OSCOM_Customer->getProductNotifications();
      $counter = 0;

      while ( $Qproducts->next() ) {
        $counter++;
?>

      <tr>
        <td width="30"><?php echo HTML::checkboxField('products[' . $counter . ']', $Qproducts->valueInt('products_id'), true); ?></td>
        <td><b><?php echo HTML::label($Qproducts->value('products_name'), 'products[' . $counter . ']'); ?></b></td>
      </tr>

<?php
      }
?>

    </table>

<?php
    } else {
      echo OSCOM::getDef('newsletter_product_notifications_products_none');
    }
?>

  </div>
</div>

<?php
  }
?>

<div class="submitFormButtons" style="text-align: right;">
  <?php echo HTML::button(array('icon' => 'triangle-1-e', 'title' => OSCOM::getDef('button_continue'))); ?>
</div>

</form>
