<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\ObjectInfo;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Shop\AddressBook;

  if ( isset($_GET['Edit']) ) {
    $osC_oiAddress = new ObjectInfo(AddressBook::getEntry($_GET['Edit']));
  } else {
    if ( AddressBook::numberOfEntries() >= MAX_ADDRESS_BOOK_ENTRIES ) {
      $OSCOM_MessageStack->add('AddressBook', OSCOM::getDef('error_address_book_full'));
    }
  }
?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists('AddressBook') ) {
    echo $OSCOM_MessageStack->get('AddressBook');
  }

  if ( ($OSCOM_Customer->hasDefaultAddress() === false) || (isset($_GET['Create']) && (AddressBook::numberOfEntries() < MAX_ADDRESS_BOOK_ENTRIES)) || (isset($osC_oiAddress) && !empty($osC_oiAddress)) ) {
?>

<form name="address_book" action="<?php echo OSCOM::getLink(null, null, 'AddressBook&' . (isset($_GET['Edit']) ? 'Edit=' . $_GET['Edit'] : 'Create') . '&Process', 'SSL'); ?>" method="post" onsubmit="return check_form(address_book);">

<div class="moduleBox">
  <em style="float: right; margin-top: 10px;"><?php echo OSCOM::getDef('form_required_information'); ?></em>

  <h6><?php echo OSCOM::getDef('address_book_new_address_title'); ?></h6>

  <div class="content">

<?php
    include(OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Application/Account/pages/address_book_details.php');
?>

  </div>
</div>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo HTML::button(array('icon' => 'triangle-1-e', 'title' => OSCOM::getDef('button_continue'))); ?></span>

<?php
    if ( $OSCOM_NavigationHistory->hasSnapshot() ) {
      $back_link = $OSCOM_NavigationHistory->getSnapshotURL();
    } elseif ( $OSCOM_Customer->hasDefaultAddress() === false ) {
      $back_link = OSCOM::getLink(null, null, null, 'SSL');
    } else {
      $back_link = OSCOM::getLink(null, null, 'AddressBook', 'SSL');
    }

    echo HTML::button(array('href' => $back_link, 'icon' => 'triangle-1-w', 'title' => OSCOM::getDef('button_back')));
?>

</div>

</form>

<?php
  } else {
?>

<div class="submitFormButtons">
  <?php HTML::button(array('href' => OSCOM::getLink(null, null, 'AddressBook', 'SSL'), 'icon' => 'triangle-1-w', 'title' => OSCOM::getDef('button_back'))); ?>
</div>

<?php
  }
?>
