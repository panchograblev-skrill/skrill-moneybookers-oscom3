<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . HTML::link(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo HTML::icon('edit.png') . ' ' . OSCOM::getDef('action_heading_batch_edit_cards'); ?></h3>

  <form name="ccEditBatch" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'BatchSave&Process'); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_batch_edit_cards'); ?></p>

<?php
  $Qcc = $OSCOM_PDO->query('select id, credit_card_name from :table_credit_cards where id in ("' . implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))) . '") order by credit_card_name');
  $Qcc->execute();

  $names_string = '';

  while ( $Qcc->fetch() ) {
    $names_string .= HTML::hiddenField('batch[]', $Qcc->valueInt('id')) . '<b>' . $Qcc->valueProtected('credit_card_name') . '</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2);
  }

  echo '<p>' . $names_string . '</p>';

  echo '<p>' . HTML::radioField('type', array(array('id' => 'activate', 'text' => OSCOM::getDef('activate')), array('id' => 'deactivate', 'text' => OSCOM::getDef('deactivate'))), 'activate') . '</p>';
?>

  <p><?php echo HTML::button(array('priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::getDef('button_save'))) . ' ' . HTML::button(array('href' => OSCOM::getLink(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>
