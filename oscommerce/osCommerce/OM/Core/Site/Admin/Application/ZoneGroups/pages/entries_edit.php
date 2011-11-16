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
  use osCommerce\OM\Core\Site\Admin\Application\ZoneGroups\ZoneGroups;
  use osCommerce\OM\Core\Site\Shop\Address;

  $OSCOM_ObjectInfo = new ObjectInfo(ZoneGroups::getEntry($_GET['zID']));

  $countries_array = array(array('id' => '',
                                 'text' => OSCOM::getDef('all_countries')));

  foreach ( Address::getCountries() as $country ) {
    $countries_array[] = array('id' => $country['id'],
                               'text' => $country['name']);
  }

  $zones_array = array(array('id' => '',
                             'text' => OSCOM::getDef('all_zones')));

  if ( $OSCOM_ObjectInfo->get('zone_country_id') > 0 ) {
    foreach ( Address::getZones($OSCOM_ObjectInfo->get('zone_country_id')) as $zone ) {
      $zones_array[] = array('id' => $zone['id'],
                             'text' => $zone['name']);
    }
  }
?>

<script type="text/javascript">
  function update_zone(theForm) {
    var NumState = theForm.zone_id.options.length;
    var SelectedCountry = "";

    while(NumState > 0) {
      NumState--;
      theForm.zone_id.options[NumState] = null;
    }

    SelectedCountry = theForm.zone_country_id.options[theForm.zone_country_id.selectedIndex].value;

<?php echo ZoneGroups::getJSList('SelectedCountry', 'theForm', 'zone_id'); ?>
  }
</script>

<h1><?php echo $OSCOM_Template->getIcon(32) . HTML::link(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo HTML::icon('edit.png') . ' ' . $OSCOM_ObjectInfo->getProtected('countries_name') . ': ' . $OSCOM_ObjectInfo->getProtected('zone_name'); ?></h3>

  <form name="zEdit" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'EntrySave&Process&id=' . $_GET['id'] . '&zID=' . $OSCOM_ObjectInfo->getInt('association_id')); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_edit_zone_entry'); ?></p>

  <fieldset>
    <p><label for="zone_country_id"><?php echo OSCOM::getDef('field_country'); ?></label><?php echo HTML::selectMenu('zone_country_id', $countries_array, $OSCOM_ObjectInfo->get('zone_country_id'), 'onchange="update_zone(this.form);"'); ?></p>
    <p><label for="zone_id"><?php echo OSCOM::getDef('field_zone'); ?></label><?php echo HTML::selectMenu('zone_id', $zones_array, $OSCOM_ObjectInfo->get('zone_id')); ?></p>
  </fieldset>

  <p><?php echo HTML::button(array('priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::getDef('button_save'))) . ' ' . HTML::button(array('href' => OSCOM::getLink(null, null, 'id=' . $_GET['id']), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>
