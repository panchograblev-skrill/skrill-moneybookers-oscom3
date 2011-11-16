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
  use osCommerce\OM\Core\Site\Admin\Application\Languages\Languages;

  $OSCOM_ObjectInfo = new ObjectInfo(Languages::getDefinition($_GET['dID']));
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . HTML::link(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo HTML::icon('edit.png') . ' ' . HTML::outputProtected($_GET['group']); ?></h3>

  <form name="lDefine" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'EditDefinition&Process&id=' . $_GET['id'] . '&group=' . $_GET['group']); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_edit_language_definitions'); ?></p>

  <fieldset>
    <p><label for="def[<?php echo $OSCOM_ObjectInfo->getProtected('definition_key'); ?>]"><?php echo $OSCOM_ObjectInfo->getProtected('definition_key'); ?></label><?php echo HTML::textareaField('def[' . $OSCOM_ObjectInfo->get('definition_key') . ']', $OSCOM_ObjectInfo->get('definition_value')); ?></p>
  </fieldset>

  <p><?php echo HTML::button(array('priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::getDef('button_save'))) . ' ' . HTML::button(array('href' => OSCOM::getLink(null, null, 'id=' . $_GET['id'] . '&group=' . $_GET['group']), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>
