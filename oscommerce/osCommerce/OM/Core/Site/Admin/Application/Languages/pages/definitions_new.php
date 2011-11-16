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

  $groups_array = array();

  foreach ( ObjectInfo::to(Languages::getGroups($_GET['id']))->get('entries') as $value ) {
    $groups_array[] = $value['content_group'];
  }
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . HTML::link(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo HTML::icon('new.png') . ' ' . OSCOM::getDef('action_heading_new_language_definition'); ?></h3>

  <form name="lNew" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'InsertDefinition&Process&id=' . $_GET['id'] . (isset($_GET['group']) ? '&group=' . $_GET['group'] : '')); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_new_language_definition'); ?></p>

  <fieldset>
    <p><label for="key"><?php echo OSCOM::getDef('field_definition_key'); ?></label><?php echo HTML::inputField('key'); ?></p>
    <p><label><?php echo OSCOM::getDef('field_definition_value'); ?></label>

<?php
  foreach ( $OSCOM_Language->getAll() as $l ) {
    echo '<br />' . $OSCOM_Language->showImage($l['code']) . '<br />' . HTML::textareaField('value[' . $l['id'] . ']');
  }
?>

    </p>
    <p><label for="defgroup"><?php echo OSCOM::getDef('field_definition_group'); ?></label><?php echo HTML::inputField('defgroup', (isset($_GET['group']) ? $_GET['group'] : null)); ?></p>
  </fieldset>

  <p><?php echo HTML::button(array('priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::getDef('button_save'))) . ' ' . HTML::button(array('href' => OSCOM::getLink(null, null, 'id=' . $_GET['id'] . (isset($_GET['group']) ? '&group=' . $_GET['group'] : '')), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>

<script type="text/javascript">
  var suggestGroups = <?php echo json_encode($groups_array); ?>;

  $("#defgroup").autocomplete({
    source: suggestGroups,
    minLength: 0
  });
</script>
