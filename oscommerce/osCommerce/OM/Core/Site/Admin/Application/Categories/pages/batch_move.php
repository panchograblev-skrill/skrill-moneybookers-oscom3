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

<script>
$(function() {
  $('#cMoveBatchForm input, #cMoveBatchForm select, #cMoveBatchForm textarea, #cMoveBatchForm fileupload').safetynet();
});
</script>

<h1><?php echo $OSCOM_Template->getIcon(32) . HTML::link(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<form id="cMoveBatchForm" name="cMoveBatch" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'BatchMove&Process&cid=' . $OSCOM_Application->getCurrentCategoryID()); ?>" method="post">

<div id="formButtons" style="float: right;"><?php echo HTML::button(array('priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::getDef('button_save'))) . ' ' . HTML::button(array('type' => 'button', 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'), 'params' => 'onclick="$.safetynet.suppressed(true); window.location.href=\'' . OSCOM::getLink(null, null, 'cid=' . $OSCOM_Application->getCurrentCategoryID()) . '\';"')); ?></div>

<div style="clear: both;"></div>

<div class="infoBox">
  <h3><?php echo HTML::icon('move.png') . ' ' . OSCOM::getDef('action_heading_batch_move_categories'); ?></h3>

  <p><?php echo OSCOM::getDef('introduction_batch_move_categories'); ?></p>

  <fieldset>

<?php
  $categories = '';

  foreach ( $_POST['batch'] as $c ) {
    $categories .= HTML::hiddenField('batch[]', $c) . '<b>' . $OSCOM_CategoryTree->getData($c, 'name') . '</b>, ';
  }

  if ( !empty($categories) ) {
    $categories = substr($categories, 0, -2);
  }

  echo '<p>' . $categories . '</p>';
?>

    <p><label for="parent_id"><?php echo OSCOM::getDef('field_parent_category'); ?></label><?php echo HTML::selectMenu('parent_id', array_merge(array(array('id' => '0', 'text' => OSCOM::getDef('top_category'))), $OSCOM_Application->getCategoryList()), $OSCOM_Application->getCurrentCategoryID()); ?></p>
  </fieldset>
</div>

</form>
