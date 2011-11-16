<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Admin\Application\Configuration\Configuration;
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . HTML::link(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo HTML::icon('edit.png') . ' ' . OSCOM::getDef('action_heading_batch_edit_configuration_parameters'); ?></h3>

  <form name="cEditBatch" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'BatchSaveEntries&Process&id=' . $_GET['id']); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_batch_edit_configuration_parameters'); ?></p>

  <fieldset>

<?php
  $Qcfg = $OSCOM_PDO->query('select configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, use_function, set_function from :table_configuration where configuration_id in (' . implode(',', array_unique(array_filter($_POST['batch'], 'is_numeric'))) . ')');
  $Qcfg->execute();

  while ( $Qcfg->fetch() ) {
    if ( strlen($Qcfg->value('set_function')) > 0 ) {
      $value_field = Configuration::callUserFunc($Qcfg->value('set_function'), $Qcfg->value('configuration_value'), $Qcfg->value('configuration_key'));
    } else {
      $value_field = HTML::inputField('configuration[' . $Qcfg->value('configuration_key') . ']', $Qcfg->value('configuration_value'));
    }
?>

    <p><label for="configuration[<?php echo $Qcfg->valueProtected('configuration_key'); ?>]"><?php echo $Qcfg->valueProtected('configuration_title'); ?></label><?php echo $value_field . HTML::hiddenField('batch[]', $Qcfg->valueInt('configuration_id')); ?></p>

    <p><?php echo $Qcfg->value('configuration_description'); ?></p>

<?php
  }
?>

  </fieldset>

  <p><?php echo HTML::button(array('priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::getDef('button_save'))) . ' ' . HTML::button(array('href' => OSCOM::getLink(null, null, 'id=' . $_GET['id']), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>
