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

<h1><?php echo $OSCOM_Template->getIcon(32) . HTML::object(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo HTML::icon('trash.png') . ' ' . OSCOM::getDef('action_heading_batch_delete_tax_rates'); ?></h3>

  <form name="rDeleteBatch" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'BatchDeleteEntries&Process&id=' . $_GET['id']); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_batch_delete_tax_rates'); ?></p>

<?php
  $Qentries = $OSCOM_PDO->query('select tax_rates_id, tax_description from :table_tax_rates where tax_rates_id in ("' . implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))) . '") order by tax_description');
  $Qentries->execute();

  $names_string = '';

  while ( $Qentries->fetch() ) {
    $names_string .= HTML::hiddenField('batch[]', $Qentries->valueInt('tax_rates_id')) . '<b>' . $Qentries->valueProtected('tax_description') . '</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2);
  }

  echo '<p>' . $names_string . '</p>';

  echo '<p>' . HTML::button(array('priority' => 'primary', 'icon' => 'trash', 'title' => OSCOM::getDef('button_delete'))) . ' ' . HTML::button(array('href' => OSCOM::getLink(null, null, 'id=' . $_GET['id']), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))) . '</p>';
?>

  </form>
</div>
