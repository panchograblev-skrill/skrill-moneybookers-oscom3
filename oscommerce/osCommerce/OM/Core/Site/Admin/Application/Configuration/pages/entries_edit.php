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
  use osCommerce\OM\Core\Site\Admin\Application\Configuration\Configuration;

  $OSCOM_ObjectInfo = new ObjectInfo(Configuration::getEntry($_GET['pID']));
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . HTML::link(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }

  if ( strlen($OSCOM_ObjectInfo->get('set_function')) > 0 ) {
    $value_field = Configuration::callUserFunc($OSCOM_ObjectInfo->get('set_function'), $OSCOM_ObjectInfo->get('configuration_value'), $OSCOM_ObjectInfo->get('configuration_key'));
  } else {
    $value_field = HTML::inputField('configuration[' . $OSCOM_ObjectInfo->get('configuration_key') . ']', $OSCOM_ObjectInfo->get('configuration_value'));
  }
?>

<div class="infoBox">
  <h3><?php echo HTML::icon('edit.png') . ' ' . $OSCOM_ObjectInfo->getProtected('configuration_title'); ?></h3>

  <form name="cEdit" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'EntrySave&Process&id=' . $_GET['id']); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_edit_parameter'); ?></p>

  <fieldset>
    <p><label for="configuration[<?php echo $OSCOM_ObjectInfo->get('configuration_key'); ?>]"><?php echo $OSCOM_ObjectInfo->getProtected('configuration_title'); ?></label><?php echo $value_field; ?></p>
    <p><?php echo $OSCOM_ObjectInfo->get('configuration_description'); ?></p>
  </fieldset>

  <p><?php echo HTML::button(array('priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::getDef('button_save'))) . ' ' . HTML::button(array('href' => OSCOM::getLink(null, null, 'id=' . $_GET['id']), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>
