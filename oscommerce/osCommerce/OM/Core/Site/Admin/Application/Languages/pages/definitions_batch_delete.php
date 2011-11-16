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
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . HTML::link(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo HTML::icon('trash.png') . ' ' . HTML::outputProtected($_GET['group']); ?></h3>

  <form name="lDeleteBatch" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'BatchDeleteDefinitions&Process&id=' . $_GET['id'] . '&group=' . $_GET['group']); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_batch_delete_language_definitions'); ?></p>

  <fieldset>

<?php
  $names_string = '';

  foreach ( $_POST['batch'] as $id ) {
    $OSCOM_ObjectInfo = new ObjectInfo(Languages::getDefinition($id));

    $names_string .= HTML::hiddenField('batch[]', $OSCOM_ObjectInfo->getInt('id')) . '<b>' . $OSCOM_ObjectInfo->getProtected('definition_key') . '</b><br />';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -6);
  }

  echo '<p>' . $names_string . '</p>';
?>

  </fieldset>

  <p><?php echo HTML::button(array('priority' => 'primary', 'icon' => 'trash', 'title' => OSCOM::getDef('button_delete'))) . ' ' . HTML::button(array('href' => OSCOM::getLink(null, null, 'id=' . $_GET['id'] . '&group=' . $_GET['group']), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>
