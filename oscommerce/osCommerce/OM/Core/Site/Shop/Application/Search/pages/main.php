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

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists('Search') ) {
    echo $OSCOM_MessageStack->get('Search');
  }
?>

<form name="search" action="<?php echo OSCOM::getLink(null, null, null, 'NONSSL', false); ?>" method="get" onsubmit="return check_form(this);">

<?php
  echo HTML::hiddenField('Search', null);
?>

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('search_criteria_title'); ?></h6>

  <div class="content">
    <?php echo HTML::inputField('Q', null, 'style="width: 99%;"'); ?>
  </div>
</div>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo HTML::button(array('icon' => 'search', 'title' => OSCOM::getDef('button_search'))); ?></span>

  <?php echo HTML::link('javascript:popupWindow(\'' . OSCOM::getLink(null, null, 'Help') . '\');', OSCOM::getDef('search_help_tips')); ?>
</div>

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('advanced_search_heading'); ?></h6>

  <div class="content">
    <ol>
      <li>

<?php
  echo HTML::label(OSCOM::getDef('field_search_categories'), 'category');

  $OSCOM_CategoryTree->setSpacerString('&nbsp;', 2);

  $categories_array = array(array('id' => '',
                                  'text' => OSCOM::getDef('filter_all_categories')));

  foreach ( $OSCOM_CategoryTree->buildBranchArray(0) as $category ) {
    $categories_array[] = array('id' => $category['id'],
                                'text' => $category['title']);
  }

  echo HTML::selectMenu('category', $categories_array);
?>

      </li>
      <li><?php echo HTML::checkboxField('recursive', array(array('id' => '1', 'text' => OSCOM::getDef('field_search_recursive'))), true); ?></li>
      <li>

<?php
  echo HTML::label(OSCOM::getDef('field_search_manufacturers'), 'manufacturer');

  $manufacturers_array = array(array('id' => '', 'text' => OSCOM::getDef('filter_all_manufacturers')));

  $Qmanufacturers = $OSCOM_PDO->query('select manufacturers_id, manufacturers_name from :table_manufacturers order by manufacturers_name');
  $Qmanufacturers->execute();

  while ( $Qmanufacturers->fetch() ) {
    $manufacturers_array[] = array('id' => $Qmanufacturers->valueInt('manufacturers_id'),
                                   'text' => $Qmanufacturers->value('manufacturers_name'));
  }

  echo HTML::selectMenu('manufacturer', $manufacturers_array);
?>

      </li>
      <li><?php echo HTML::label(OSCOM::getDef('field_search_price_from'), 'pfrom') . HTML::inputField('pfrom'); ?></li>
      <li><?php echo HTML::label(OSCOM::getDef('field_search_price_to'), 'pto') . HTML::inputField('pto'); ?></li>
      <li><?php echo HTML::label(OSCOM::getDef('field_search_date_from'), 'datefrom_days') . HTML::dateSelectMenu('datefrom', null, false, null, null, date('Y') - $OSCOM_Search->getMinYear(), 0); ?></li>
      <li><?php echo HTML::label(OSCOM::getDef('field_search_date_to'), 'dateto_days') . HTML::dateSelectMenu('dateto', null, null, null, null, date('Y') - $OSCOM_Search->getMaxYear(), 0); ?></li>
    </ol>
  </div>
</div>

<?php
  echo HTML::hiddenSessionIDField();
?>

</form>
