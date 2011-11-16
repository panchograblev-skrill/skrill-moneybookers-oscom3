<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Admin\Application\Languages\Languages;

  $languages_array = array();

  foreach ( Languages::getDirectoryListing() as $directory ) {
    $languages_array[] = array('id' => $directory,
                               'text' => $directory);
  }
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . HTML::link(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo HTML::icon('new.png') . ' ' . OSCOM::getDef('action_heading_import_language'); ?></h3>

  <form name="lImport" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'Import&Process'); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_import_language'); ?></p>

  <fieldset>
    <p><label for="language_import"><?php echo OSCOM::getDef('field_language_selection'); ?></label><?php echo HTML::selectMenu('language_import', $languages_array); ?></p>
    <p><label for="import_type"><?php echo OSCOM::getDef('field_import_type'); ?></label><br /><?php echo HTML::radioField('import_type', array(array('id' => 'add', 'text' => OSCOM::getDef('only_add_new_records')), array('id' => 'update', 'text' => OSCOM::getDef('only_update_existing_records')), array('id' => 'replace', 'text' => OSCOM::getDef('replace_all'))), 'add', null, '<br />'); ?></p>
  </fieldset>

  <p><?php echo HTML::button(array('priority' => 'primary', 'icon' => 'triangle-1-se', 'title' => OSCOM::getDef('button_import'))) . ' ' . HTML::button(array('href' => OSCOM::getLink(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>
