<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Application\Account\Action\AddressBook\Edit;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Shop\AddressBook;

  class Process {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_MessageStack = Registry::get('MessageStack');
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_Customer = Registry::get('Customer');

      global $entry_state_has_zones; // HPDL (used in template)

      $data = array();

      if ( ACCOUNT_GENDER >= 0 ) {
        if ( isset($_POST['gender']) && (($_POST['gender'] == 'm') || ($_POST['gender'] == 'f')) ) {
          $data['gender'] = $_POST['gender'];
        } else {
          $OSCOM_MessageStack->add('AddressBook', OSCOM::getDef('field_customer_gender_error'));
        }
      }

      if ( isset($_POST['firstname']) && (strlen(trim($_POST['firstname'])) >= ACCOUNT_FIRST_NAME) ) {
        $data['firstname'] = $_POST['firstname'];
      } else {
        $OSCOM_MessageStack->add('AddressBook', sprintf(OSCOM::getDef('field_customer_first_name_error'), ACCOUNT_FIRST_NAME));
      }

      if ( isset($_POST['lastname']) && (strlen(trim($_POST['lastname'])) >= ACCOUNT_LAST_NAME) ) {
        $data['lastname'] = $_POST['lastname'];
      } else {
        $OSCOM_MessageStack->add('AddressBook', sprintf(OSCOM::getDef('field_customer_last_name_error'), ACCOUNT_LAST_NAME));
      }

      if ( ACCOUNT_COMPANY > -1 ) {
        if ( isset($_POST['company']) && (strlen(trim($_POST['company'])) >= ACCOUNT_COMPANY) ) {
          $data['company'] = $_POST['company'];
        } else {
          $OSCOM_MessageStack->add('AddressBook', sprintf(OSCOM::getDef('field_customer_company_error'), ACCOUNT_COMPANY));
        }
      }

      if ( isset($_POST['street_address']) && (strlen(trim($_POST['street_address'])) >= ACCOUNT_STREET_ADDRESS) ) {
        $data['street_address'] = $_POST['street_address'];
      } else {
        $OSCOM_MessageStack->add('AddressBook', sprintf(OSCOM::getDef('field_customer_street_address_error'), ACCOUNT_STREET_ADDRESS));
      }

      if ( ACCOUNT_SUBURB >= 0 ) {
        if ( isset($_POST['suburb']) && (strlen(trim($_POST['suburb'])) >= ACCOUNT_SUBURB) ) {
          $data['suburb'] = $_POST['suburb'];
        } else {
          $OSCOM_MessageStack->add('AddressBook', sprintf(OSCOM::getDef('field_customer_suburb_error'), ACCOUNT_SUBURB));
        }
      }

      if ( ACCOUNT_POST_CODE > -1 ) {
        if ( isset($_POST['postcode']) && (strlen(trim($_POST['postcode'])) >= ACCOUNT_POST_CODE) ) {
          $data['postcode'] = $_POST['postcode'];
        } else {
          $OSCOM_MessageStack->add('AddressBook', sprintf(OSCOM::getDef('field_customer_post_code_error'), ACCOUNT_POST_CODE));
        }
      }

      if ( isset($_POST['city']) && (strlen(trim($_POST['city'])) >= ACCOUNT_CITY) ) {
        $data['city'] = $_POST['city'];
      } else {
        $OSCOM_MessageStack->add('AddressBook', sprintf(OSCOM::getDef('field_customer_city_error'), ACCOUNT_CITY));
      }

      if ( ACCOUNT_STATE >= 0 ) {
        $Qcheck = $OSCOM_PDO->prepare('select zone_id from :table_zones where zone_country_id = :zone_country_id limit 1');
        $Qcheck->bindInt(':zone_country_id', $_POST['country']);
        $Qcheck->execute();

        $entry_state_has_zones = ($Qcheck->fetch() !== false);

        if ( $entry_state_has_zones === true ) {
          $Qzone = $OSCOM_PDO->prepare('select zone_id from :table_zones where zone_country_id = :zone_country_id and zone_code like :zone_code');
          $Qzone->bindInt(':zone_country_id', $_POST['country']);
          $Qzone->bindValue(':zone_code', $_POST['state']);
          $Qzone->execute();

          if ( $Qzone->fetch() !== false ) {
            $data['zone_id'] = $Qzone->valueInt('zone_id');
          } else {
            $Qzone = $OSCOM_PDO->prepare('select zone_id from :table_zones where zone_country_id = :zone_country_id and zone_name like :zone_name');
            $Qzone->bindInt(':zone_country_id', $_POST['country']);
            $Qzone->bindValue(':zone_name', $_POST['state'] . '%');
            $Qzone->execute();

            if ( $Qzone->fetch() !== false ) {
              $data['zone_id'] = $Qzone->valueInt('zone_id');
            } else {
              $OSCOM_MessageStack->add('AddressBook', OSCOM::getDef('field_customer_state_select_pull_down_error'));
            }
          }
        } else {
          if ( strlen(trim($_POST['state'])) >= ACCOUNT_STATE ) {
            $data['state'] = $_POST['state'];
          } else {
            $OSCOM_MessageStack->add('AddressBook', sprintf(OSCOM::getDef('field_customer_state_error'), ACCOUNT_STATE));
          }
        }
      } else {
        if ( strlen(trim($_POST['state'])) >= ACCOUNT_STATE ) {
          $data['state'] = $_POST['state'];
        } else {
          $OSCOM_MessageStack->add('AddressBook', sprintf(OSCOM::getDef('field_customer_state_error'), ACCOUNT_STATE));
        }
      }

      if ( isset($_POST['country']) && is_numeric($_POST['country']) && ($_POST['country'] >= 1) ) {
        $data['country'] = $_POST['country'];
      } else {
        $OSCOM_MessageStack->add('AddressBook', OSCOM::getDef('field_customer_country_error'));
      }

      if ( ACCOUNT_TELEPHONE >= 0 ) {
        if ( isset($_POST['telephone']) && (strlen(trim($_POST['telephone'])) >= ACCOUNT_TELEPHONE) ) {
          $data['telephone'] = $_POST['telephone'];
        } else {
          $OSCOM_MessageStack->add('AddressBook', sprintf(OSCOM::getDef('field_customer_telephone_number_error'), ACCOUNT_TELEPHONE));
        }
      }

      if ( ACCOUNT_FAX >= 0 ) {
        if ( isset($_POST['fax']) && (strlen(trim($_POST['fax'])) >= ACCOUNT_FAX) ) {
          $data['fax'] = $_POST['fax'];
        } else {
          $OSCOM_MessageStack->add('AddressBook', sprintf(OSCOM::getDef('field_customer_fax_number_error'), ACCOUNT_FAX));
        }
      }

      if ( ($OSCOM_Customer->hasDefaultAddress() === false) || (isset($_POST['primary']) && ($_POST['primary'] == 'on')) ) {
        $data['primary'] = true;
      }

      if ( $OSCOM_MessageStack->size('AddressBook') === 0 ) {
        if ( AddressBook::saveEntry($data, $_GET['Edit']) ) {
          $OSCOM_MessageStack->add('AddressBook', OSCOM::getDef('success_address_book_entry_updated'), 'success');
        }

        OSCOM::redirect(OSCOM::getLink(null, null, 'AddressBook', 'SSL'));
      }
    }
  }
?>
