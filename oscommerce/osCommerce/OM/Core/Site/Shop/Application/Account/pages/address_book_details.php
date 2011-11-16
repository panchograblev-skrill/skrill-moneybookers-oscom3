<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Shop\Address;
?>

<ol>

<?php
  if ( ACCOUNT_GENDER > -1 ) {
    $gender_array = array(array('id' => 'm', 'text' => OSCOM::getDef('gender_male')),
                          array('id' => 'f', 'text' => OSCOM::getDef('gender_female')));
?>

  <li><?php echo HTML::label(OSCOM::getDef('field_customer_gender'), 'gender_1', null, (ACCOUNT_GENDER > 0)) . HTML::radioField('gender', $gender_array, (isset($osC_oiAddress) && $osC_oiAddress->exists('gender') ? $osC_oiAddress->get('gender') : (!$OSCOM_Customer->hasDefaultAddress() ? $OSCOM_Customer->getGender() : null))); ?></li>

<?php
  }
?>

  <li><?php echo HTML::label(OSCOM::getDef('field_customer_first_name'), 'firstname', null, true) . HTML::inputField('firstname', (isset($osC_oiAddress) && $osC_oiAddress->exists('firstname') ? $osC_oiAddress->get('firstname') : (!$OSCOM_Customer->hasDefaultAddress() ? $OSCOM_Customer->getFirstName() : null))); ?></tli>
  <li><?php echo HTML::label(OSCOM::getDef('field_customer_last_name'), 'lastname', null, true) . HTML::inputField('lastname', (isset($osC_oiAddress) && $osC_oiAddress->exists('lastname') ? $osC_oiAddress->get('lastname') : (!$OSCOM_Customer->hasDefaultAddress() ? $OSCOM_Customer->getLastName() : null))); ?></li>

<?php
  if ( ACCOUNT_COMPANY > -1 ) {
?>

  <li><?php echo HTML::label(OSCOM::getDef('field_customer_company'), 'company', null, (ACCOUNT_COMPANY > 0)) . HTML::inputField('company', (isset($osC_oiAddress) && $osC_oiAddress->exists('company') ? $osC_oiAddress->get('company') : null)); ?></li>

<?php
  }
?>

  <li><?php echo HTML::label(OSCOM::getDef('field_customer_street_address'), 'street_address', null, true) . HTML::inputField('street_address', (isset($osC_oiAddress) && $osC_oiAddress->exists('street_address') ? $osC_oiAddress->get('street_address') : null)); ?></li>

<?php
  if ( ACCOUNT_SUBURB > -1 ) {
?>

  <li><?php echo HTML::label(OSCOM::getDef('field_customer_suburb'), 'suburb', null, (ACCOUNT_SUBURB > 0)) . HTML::inputField('suburb', (isset($osC_oiAddress) && $osC_oiAddress->exists('suburb') ? $osC_oiAddress->get('suburb') : null)); ?></li>

<?php
  }

  if ( ACCOUNT_POST_CODE > -1 ) {
?>

  <li><?php echo HTML::label(OSCOM::getDef('field_customer_post_code'), 'postcode', null, (ACCOUNT_POST_CODE > 0)) . HTML::inputField('postcode', (isset($osC_oiAddress) && $osC_oiAddress->exists('postcode') ? $osC_oiAddress->get('postcode') : null)); ?></li>

<?php
  }
?>

  <li><?php echo HTML::label(OSCOM::getDef('field_customer_city'), 'city', null, true) . HTML::inputField('city', (isset($osC_oiAddress) && $osC_oiAddress->exists('city') ? $osC_oiAddress->get('city') : null)); ?></li>

<?php
  if ( ACCOUNT_STATE > -1 ) {
?>

  <li>

<?php
    echo HTML::label(OSCOM::getDef('field_customer_state'), 'state', null, (ACCOUNT_STATE > 0));

    if ( isset($entry_state_has_zones) ) { // HPDL
      if ( $entry_state_has_zones === true ) {
        $Qzones = $OSCOM_PDO->prepare('select zone_name from :table_zones where zone_country_id = :zone_country_id order by zone_name');
        $Qzones->bindInt(':zone_country_id', $_POST['country']);
        $Qzones->execute();

        $zones_array = array();
        while ( $Qzones->fetch() ) {
          $zones_array[] = array('id' => $Qzones->value('zone_name'),
                                 'text' => $Qzones->value('zone_name'));
        }

        echo HTML::selectMenu('state', $zones_array);
      } else {
        echo HTML::inputField('state');
      }
    } else {
      $zone = null;

      if ( isset($osC_oiAddress) ) {
        if ( $osC_oiAddress->exists('zone_id') && ($osC_oiAddress->getInt('zone_id') > 0) ) {
          $zone = Address::getZoneName($osC_oiAddress->getInt('zone_id'));
        } elseif ( $osC_oiAddress->exists('state') ) {
          $zone = $osC_oiAddress->get('state');
        }
      }

      echo HTML::inputField('state', $zone);
    }
?>

  </li>

<?php
  }
?>

  <li>

<?php
  echo HTML::label(OSCOM::getDef('field_customer_country'), 'country', null, true);

  $countries_array = array(array('id' => '',
                                 'text' => OSCOM::getDef('pull_down_default')));

  foreach ( Address::getCountries() as $country ) {
    $countries_array[] = array('id' => $country['id'],
                               'text' => $country['name']);
  }

  echo HTML::selectMenu('country', $countries_array, (isset($osC_oiAddress) && $osC_oiAddress->exists('country_id') ? $osC_oiAddress->getInt('country_id') : STORE_COUNTRY));
?>

  </li>

<?php
  if ( ACCOUNT_TELEPHONE > -1 ) {
?>

  <li><?php echo HTML::label(OSCOM::getDef('field_customer_telephone_number'), 'telephone', null, (ACCOUNT_TELEPHONE > 0)) . HTML::inputField('telephone', (isset($osC_oiAddress) && $osC_oiAddress->exists('telephone') ? $osC_oiAddress->get('telephone') : null)); ?></li>

<?php
  }

  if ( ACCOUNT_FAX > -1 ) {
?>

  <li><?php echo HTML::label(OSCOM::getDef('field_customer_fax_number'), 'fax', null, (ACCOUNT_FAX > 0)) . HTML::inputField('fax', (isset($osC_oiAddress) && $osC_oiAddress->exists('fax') ? $osC_oiAddress->get('fax') : null)); ?></li>

<?php
  }

  if ( $OSCOM_Customer->hasDefaultAddress() && ((isset($_GET['Edit']) && ($OSCOM_Customer->getDefaultAddressID() != $_GET['Edit'])) || isset($_GET['Create'])) ) {
?>

  <li><?php echo HTML::checkboxField('primary', array(array('id' => 'on', 'text' => OSCOM::getDef('set_as_primary'))), false); ?></li>

<?php
  }
?>

</ol>
