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
  use osCommerce\OM\Core\Site\Admin\Application\Customers\Customers;

  $new_customer = false;

  if ( ACCOUNT_GENDER > -1 ) {
    $gender_array = array(array('id' => 'm', 'text' => OSCOM::getDef('gender_male')),
                          array('id' => 'f', 'text' => OSCOM::getDef('gender_female')));
  }

  $OSCOM_ObjectInfo = new ObjectInfo(Customers::get($_GET['id']));
?>

<script>
$(function() {
  $('#cEditForm input, #cEditForm select, #cEditForm textarea, #cEditForm fileupload').safetynet();
});
</script>

<h1><?php echo $OSCOM_Template->getIcon(32) . HTML::link(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div id="sectionMenuContainer" style="float: left; padding-bottom: 10px;">
  <span class="ui-widget-header ui-corner-all" style="padding: 10px 4px;">
    <span id="sectionMenu"><?php echo HTML::radioField('sections', array(array('id' => 'personal', 'text' => OSCOM::getDef('section_personal')), array('id' => 'password', 'text' => OSCOM::getDef('section_password')), array('id' => 'addressBook', 'text' => OSCOM::getDef('section_address_book')), array('id' => 'newsletters', 'text' => OSCOM::getDef('section_newsletters')), array('id' => 'map', 'text' => OSCOM::getDef('section_map')), array('id' => 'social', 'text' => OSCOM::getDef('section_social'))), (isset($_GET['tabIndex']) ? $_GET['tabIndex'] : null), null, ''); ?></span>
  </span>
</div>

<script>
$(function() {
  $('#sectionMenu').buttonsetTabs();
});
</script>

<form id="cEditForm" name="cEdit" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'Save&Process&id=' . $_GET['id']); ?>" method="post">

<div id="formButtons" style="float: right;"><?php echo HTML::button(array('priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::getDef('button_save'))) . ' ' . HTML::button(array('type' => 'button', 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'), 'params' => 'onclick="$.safetynet.suppressed(true); window.location.href=\'' . OSCOM::getLink() . '\';"')); ?></div>

<div style="clear: both;"></div>

<?php
// HPDL Modularize, zack zack!
  include('section_personal.php');
  include('section_password.php');
  include('section_addressBook.php');
  include('section_newsletters.php');
  include('section_map.php');
  include('section_social.php');
?>

</form>
