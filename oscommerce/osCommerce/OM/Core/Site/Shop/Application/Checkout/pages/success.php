<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Shop\Address;
  use osCommerce\OM\Core\Site\Shop\Tax;

  if ( $OSCOM_Customer->isLoggedOn() ) {
    $Qglobal = $OSCOM_PDO->prepare('select global_product_notifications from :table_customers where customers_id =:customers_id');
    $Qglobal->bindInt(':customers_id', $OSCOM_Customer->getID());
    $Qglobal->execute();

    if ( $Qglobal->valueInt('global_product_notifications') !== 1 ) {
      $Qorder = $OSCOM_PDO->prepare('select orders_id from :table_orders where customers_id = :customers_id order by date_purchased desc limit 1');
      $Qorder->bindInt(':customers_id', $OSCOM_Customer->getID());
      $Qorder->execute();

      $Qproducts = $OSCOM_PDO->prepare('select products_id, products_name from :table_orders_products where orders_id = :orders_id order by products_name');
      $Qproducts->bindInt(':orders_id', $Qorder->valueInt('orders_id'));
      $Qproducts->execute();

      $products_array = array();
      while ( $Qproducts->fetch() ) {
        $products_array[] = array('id' => $Qproducts->valueInt('products_id'),
                                  'text' => $Qproducts->value('products_name'));
      }
    }
  }
?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<form name="order" action="<?php echo OSCOM::getLink(null, null, 'Success&UpdateNotifications', 'SSL'); ?>" method="post">

<div>
  <div style="padding-top: 30px;">
    <p><?php echo OSCOM::getDef('order_processed_successfully'); ?></p>

    <p>

<?php
  if ( $OSCOM_Customer->isLoggedOn() ) {
    if ( $Qglobal->valueInt('global_product_notifications') != 1 ) {
      echo OSCOM::getDef('add_selection_to_product_notifications') . '<br /><p class="productsNotifications">';

      $products_displayed = array();

      foreach ( $products_array as $product ) {
        if ( !in_array($product['id'], $products_displayed) ) {
          echo HTML::checkboxField('notify[]', $product['id']) . ' ' . $product['text'] . '<br />';

          $products_displayed[] = $product['id'];
        }
      }
    } else {
      echo sprintf(OSCOM::getDef('view_order_history'), OSCOM::getLink(null, 'Account', null, 'SSL'), OSCOM::getLink(null, 'Account', 'Orders', 'SSL')) . '<br /><br />' . sprintf(OSCOM::getDef('contact_store_owner'), OSCOM::getLink(null, 'Info', 'Contact'));
    }
  }
?>

    </p>

    <h2 style="text-align: center;"><?php echo OSCOM::getDef('thanks_for_shopping_with_us'); ?></h2>
  </div>
</div>

<div class="submitFormButtons" style="text-align: right;">
  <?php echo HTML::button(array('icon' => 'triangle-1-e', 'title' => OSCOM::getDef('button_continue'))); ?>
</div>

<?php
// HPDL
/*  if (DOWNLOAD_ENABLED == '1') {
?>

<table border="0" width="100%" cellspacing="0" cellpadding="0">

<?php
  include('includes/modules/downloads.php');
?>

</table>

<?php
  }
*/
?>

</form>
