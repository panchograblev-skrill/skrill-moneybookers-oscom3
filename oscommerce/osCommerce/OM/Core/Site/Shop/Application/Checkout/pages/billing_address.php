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
  use osCommerce\OM\Core\Site\Shop\AddressBook;
?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists('CheckoutAddress') ) {
    echo $OSCOM_MessageStack->get('CheckoutAddress');
  }
?>

<form name="checkout_address" action="<?php echo OSCOM::getLink(null, null, 'Billing&Address&Process', 'SSL'); ?>" method="post" onsubmit="return check_form_optional(checkout_address);">

<?php
  if ( !isset($_GET['Process']) ) {
    if ( $OSCOM_Customer->hasDefaultAddress() ) {
?>

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('billing_address_title'); ?></h6>

  <div class="content">
    <div style="float: right; padding: 0px 0px 10px 20px;">
      <?php echo Address::format($OSCOM_ShoppingCart->getBillingAddress(), '<br />'); ?>
    </div>

    <div style="float: right; padding: 0px 0px 10px 20px; text-align: center;">
      <?php echo '<b>' . OSCOM::getDef('current_billing_address_title') . '</b>'; ?>
    </div>

    <?php echo OSCOM::getDef('selected_billing_destination'); ?>

    <div style="clear: both;"></div>
  </div>
</div>

<?php
    }

    if ( $OSCOM_Customer->isLoggedOn() && (AddressBook::numberOfEntries() > 1) ) {
?>

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('address_book_entries_title'); ?></h6>

  <div class="content">
    <div style="float: right; padding: 0px 0px 10px 20px; text-align: center;">
      <?php echo '<b>' . OSCOM::getDef('please_select') . '</b>'; ?>
    </div>

    <p style="margin-top: 0px;"><?php echo OSCOM::getDef('select_another_billing_destination'); ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td height="30" colspan="4">&nbsp;</td>
      </tr>

<?php
      $radio_buttons = 0;

      $Qaddresses = AddressBook::getListing();

      while ( $Qaddresses->fetch() ) {
?>

      <tr>
        <td width="10">&nbsp;</td>
        <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
       if ( $Qaddresses->valueInt('address_book_id') == $OSCOM_ShoppingCart->getBillingAddress('id') ) {
          echo '          <tr id="defaultSelected" class="moduleRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
        } else {
          echo '          <tr class="moduleRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
        }
/* HPDL osc_draw_radio_field() does not like integer default values */
?>

            <td width="10">&nbsp;</td>
            <td colspan="2"><b><?php echo $Qaddresses->valueProtected('firstname') . ' ' . $Qaddresses->valueProtected('lastname'); ?></b></td>
            <td align="right"><?php echo HTML::radioField('ab', $Qaddresses->valueInt('address_book_id'), (string)$OSCOM_ShoppingCart->getBillingAddress('id')); ?></td>
            <td width="10">&nbsp;</td>
          </tr>
          <tr>
            <td width="10">&nbsp;</td>
            <td colspan="3"><table border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td width="10">&nbsp;</td>
                <td><?php echo Address::format($Qaddresses->toArray(), ', '); ?></td>
                <td width="10">&nbsp;</td>
              </tr>
            </table></td>
            <td width="10">&nbsp;</td>
          </tr>
        </table></td>
        <td width="10">&nbsp;</td>
      </tr>

<?php
        $radio_buttons++;
      }
?>

    </table>
  </div>
</div>

<?php
    }
  }

  if ( !$OSCOM_Customer->isLoggedOn() || (AddressBook::numberOfEntries() < MAX_ADDRESS_BOOK_ENTRIES) ) {
?>

<div class="moduleBox">
  <em style="float: right; margin-top: 10px;"><?php echo OSCOM::getDef('form_required_information'); ?></em>

  <h6><?php echo OSCOM::getDef('new_billing_address_title'); ?></h6>

  <div class="content">
    <?php echo OSCOM::getDef('new_billing_address'); ?>

    <div style="margin: 10px 30px 10px 30px;">
      <?php require(OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Application/Account/pages/address_book_details.php'); ?>
    </div>
  </div>
</div>

<?php
  }
?>

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
