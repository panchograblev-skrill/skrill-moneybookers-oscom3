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
  use osCommerce\OM\Core\Site\Admin\Application\Currencies\Currencies;

  $languages_array = array(array('id' => '0',
                                 'text' => OSCOM::getDef('none')));

  foreach ( ObjectInfo::to(Languages::getAll(-1))->get('entries') as $l ) {
    if ( $l['languages_id'] != $_GET['id'] ) {
      $languages_array[] = array('id' => $l['languages_id'],
                                 'text' => $l['name'] . ' (' . $l['code'] . ')');
    }
  }

  $currencies_array = array();

  foreach ( ObjectInfo::to(Currencies::getAll(-1))->get('entries') as $c ) {
    $currencies_array[] = array('id' => $c['currencies_id'],
                                'text' => $c['title']);
  }

  $OSCOM_ObjectInfo = new ObjectInfo(Languages::get($_GET['id']));
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . HTML::link(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo HTML::icon('edit.png') . ' ' . $OSCOM_ObjectInfo->getProtected('name'); ?></h3>

  <form name="lEdit" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'Save&Process&id=' . $_GET['id']); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_edit_language'); ?></p>

  <fieldset>
    <p><label for="name"><?php echo OSCOM::getDef('field_name'); ?></label><?php echo HTML::inputField('name', $OSCOM_ObjectInfo->get('name')); ?></p>
    <p><label for="code"><?php echo OSCOM::getDef('field_code'); ?></label><?php echo HTML::inputField('code', $OSCOM_ObjectInfo->get('code')); ?></p>
    <p><label for="locale"><?php echo OSCOM::getDef('field_locale'); ?></label><?php echo HTML::inputField('locale', $OSCOM_ObjectInfo->get('locale')); ?></p>
    <p><label for="charset"><?php echo OSCOM::getDef('field_character_set'); ?></label><?php echo HTML::inputField('charset', $OSCOM_ObjectInfo->get('charset')); ?></p>
    <p><label for="text_direction"><?php echo OSCOM::getDef('field_text_direction'); ?></label><?php echo HTML::selectMenu('text_direction', array(array('id' => 'ltr', 'text' => 'ltr'), array('id' => 'rtl', 'text' => 'rtl')), $OSCOM_ObjectInfo->get('text_direction')); ?></p>
    <p><label for="date_format_short"><?php echo OSCOM::getDef('field_date_format_short'); ?></label><?php echo HTML::inputField('date_format_short', $OSCOM_ObjectInfo->get('date_format_short')); ?></p>
    <p><label for="date_format_long"><?php echo OSCOM::getDef('field_date_format_long'); ?></label><?php echo HTML::inputField('date_format_long', $OSCOM_ObjectInfo->get('date_format_long')); ?></p>
    <p><label for="time_format"><?php echo OSCOM::getDef('field_time_format'); ?></label><?php echo HTML::inputField('time_format', $OSCOM_ObjectInfo->get('time_format')); ?></p>
    <p><label for="currencies_id"><?php echo OSCOM::getDef('field_currency'); ?></label><?php echo HTML::selectMenu('currencies_id', $currencies_array, $OSCOM_ObjectInfo->get('currencies_id')); ?></p>
    <p><label for="numeric_separator_decimal"><?php echo OSCOM::getDef('field_currency_separator_decimal'); ?></label><?php echo HTML::inputField('numeric_separator_decimal', $OSCOM_ObjectInfo->get('numeric_separator_decimal')); ?></p>
    <p><label for="numeric_separator_thousands"><?php echo OSCOM::getDef('field_currency_separator_thousands'); ?></label><?php echo HTML::inputField('numeric_separator_thousands', $OSCOM_ObjectInfo->get('numeric_separator_thousands')); ?></p>
    <p><label for="parent_id"><?php echo OSCOM::getDef('field_parent_language'); ?></label><?php echo HTML::selectMenu('parent_id', $languages_array, $OSCOM_ObjectInfo->get('parent_id')); ?></p>
    <p><label for="sort_order"><?php echo OSCOM::getDef('field_sort_order'); ?></label><?php echo HTML::inputField('sort_order', $OSCOM_ObjectInfo->get('sort_order')); ?></p>

<?php
    if ( $OSCOM_ObjectInfo->get('code') != DEFAULT_LANGUAGE ) {
?>

    <p><label for="default"><?php echo OSCOM::getDef('field_set_default'); ?></label><?php echo HTML::checkboxField('default'); ?></p>

<?php
    }
?>

  </fieldset>

  <p><?php echo HTML::button(array('priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::getDef('button_save'))) . ' ' . HTML::button(array('href' => OSCOM::getLink(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>
