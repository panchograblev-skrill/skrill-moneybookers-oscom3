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
?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<form name="checkout_address" action="<?php echo OSCOM::getLink(null, null, 'Shipping&Process', 'SSL'); ?>" method="post">

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('shipping_address_title'); ?></h6>

  <div class="content">
    <div style="float: right; padding: 0px 0px 10px 20px;">
      <?php echo Address::format($OSCOM_ShoppingCart->getShippingAddress(), '<br />'); ?>
    </div>

    <div style="float: right; padding: 0px 0px 10px 20px; text-align: center;">
      <?php echo '<b>' . OSCOM::getDef('current_shipping_address_title') . '</b>'; ?>
    </div>

    <?php echo OSCOM::getDef('choose_shipping_destination'). '<br /><br />' . HTML::button(array('href' => OSCOM::getLink(null, null, 'Shipping&Address', 'SSL'), 'icon' => 'home', 'title' => OSCOM::getDef('button_change_address'))); ?>

    <div style="clear: both;"></div>
  </div>
</div>

<?php
  if ( $OSCOM_Shipping->hasQuotes() ) {
?>

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('shipping_method_title'); ?></h6>

  <div class="content">

<?php
    if ( $OSCOM_Shipping->numberOfQuotes() > 1 ) {
?>

    <div style="float: right; padding: 0px 0px 10px 20px; text-align: center;">
      <?php echo '<b>' . OSCOM::getDef('please_select') . '</b>'; ?>
    </div>

    <p style="margin-top: 0px;"><?php echo OSCOM::getDef('choose_shipping_method'); ?></p>

<?php
    } else {
?>

    <p style="margin-top: 0px;"><?php echo OSCOM::getDef('only_one_shipping_method_available'); ?></p>

<?php
    }
?>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
    $radio_buttons = 0;
    foreach ( $OSCOM_Shipping->getQuotes() as $quotes ) {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td width="10">&nbsp;</td>
            <td colspan="3"><b><?php echo $quotes['module']; ?></b>&nbsp;<?php if (isset($quotes['icon']) && !empty($quotes['icon'])) { echo $quotes['icon']; } ?></td>
            <td width="10">&nbsp;</td>
          </tr>
<?php
      if ( isset($quotes['error']) ) {
?>
          <tr>
            <td width="10">&nbsp;</td>
            <td colspan="3"><?php echo $quotes['error']; ?></td>
            <td width="10">&nbsp;</td>
          </tr>
<?php
      } else {
        foreach ( $quotes['methods'] as $methods ) {
          if ( $quotes['id'] . '_' . $methods['id'] == $OSCOM_ShoppingCart->getShippingMethod('id') ) {
            echo '          <tr id="defaultSelected" class="moduleRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
          } else {
            echo '          <tr class="moduleRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
          }
?>
            <td width="10">&nbsp;</td>
            <td width="75%"><?php echo $methods['title']; ?></td>
<?php
          if ( ($OSCOM_Shipping->numberOfQuotes() > 1) || (count($quotes['methods']) > 1) ) {
?>
            <td><?php echo $OSCOM_Currencies->displayPrice($methods['cost'], $quotes['tax_class_id']); ?></td>
            <td align="right"><?php echo HTML::radioField('shipping_mod_sel', $quotes['id'] . '_' . $methods['id'], $OSCOM_ShoppingCart->getShippingMethod('id')); ?></td>
<?php
          } else {
?>
            <td align="right" colspan="2"><?php echo $OSCOM_Currencies->displayPrice($methods['cost'], $quotes['tax_class_id']) . HTML::hiddenField('shipping_mod_sel', $quotes['id'] . '_' . $methods['id']); ?></td>
<?php
          }
?>
            <td width="10">&nbsp;</td>
          </tr>
<?php
          $radio_buttons++;
        }
      }
?>
        </table></td>
      </tr>
<?php
    }
?>

    </table>
  </div>
</div>

<?php
  }
?>

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('add_comment_to_order_title'); ?></h6>

  <div class="content">
    <?php echo HTML::textareaField('comments', (isset($_SESSION['comments']) ? $_SESSION['comments'] : null), null, null, 'style="width: 98%;"'); ?>
  </div>
</div>

<br />

<div class="moduleBox">
  <div class="content">
    <div style="float: right;">
      <?php echo HTML::button(array('icon' => 'triangle-1-e', 'title' => OSCOM::getDef('button_continue'))); ?>
    </div>

    <?php echo '<b>' . OSCOM::getDef('continue_checkout_procedure_title') . '</b><br />' . OSCOM::getDef('continue_checkout_procedure_to_payment'); ?>
  </div>
</div>

</form>
