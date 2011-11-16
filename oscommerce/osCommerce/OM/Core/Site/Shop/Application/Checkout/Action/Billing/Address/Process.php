<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Application\Checkout\Action\Billing\Address;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Shop\AddressBook;

  class Process {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_MessageStack = Registry::get('MessageStack');
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');

      global $entry_state_has_zones; // HPDL

// process a new shipping address
      if ( !empty($_POST['firstname']) && !empty($_POST['lastname']) && !empty($_POST['street_address']) ) {
        $address_array = array('id' => 0,
                               'zone_id' => 0);

        $error = false;

        if ( ACCOUNT_GENDER > -1 ) {
          if ( isset($_POST['gender']) && ((ACCOUNT_GENDER == 0) || in_array($_POST['gender'], array('m', 'f'))) ) {
            if ( in_array($_POST['gender'], array('m', 'f')) ) {
              $address_array['gender'] = $_POST['gender'];
            }
          } else {
            $OSCOM_MessageStack->add('CheckoutAddress', OSCOM::getDef('field_customer_gender_error'));

            $error = true;
          }
        }

        if ( isset($_POST['firstname']) && (strlen(trim($_POST['firstname'])) >= ACCOUNT_FIRST_NAME) ) {
          $address_array['firstname'] = trim($_POST['firstname']);
        } else {
          $OSCOM_MessageStack->add('CheckoutAddress', sprintf(OSCOM::getDef('field_customer_first_name_error'), ACCOUNT_FIRST_NAME));

          $error = true;
        }

        if ( isset($_POST['lastname']) && (strlen(trim($_POST['lastname'])) >= ACCOUNT_LAST_NAME) ) {
          $address_array['lastname'] = trim($_POST['lastname']);
        } else {
          $OSCOM_MessageStack->add('CheckoutAddress', sprintf(OSCOM::getDef('field_customer_last_name_error'), ACCOUNT_LAST_NAME));

          $error = true;
        }

        if ( ACCOUNT_COMPANY > -1 ) {
          if ( isset($_POST['company']) && ((ACCOUNT_COMPANY == 0) || (strlen(trim($_POST['company'])) >= ACCOUNT_COMPANY)) ) {
            $address_array['company'] = trim($_POST['company']);
          } else {
            $OSCOM_MessageStack->add('CheckoutAddress', sprintf(OSCOM::getDef('field_customer_company_error'), ACCOUNT_COMPANY));

            $error = true;
          }
        }

        if ( isset($_POST['street_address']) && (strlen(trim($_POST['street_address'])) >= ACCOUNT_STREET_ADDRESS) ) {
          $address_array['street_address'] = trim($_POST['street_address']);
        } else {
          $OSCOM_MessageStack->add('CheckoutAddress', sprintf(OSCOM::getDef('field_customer_street_address_error'), ACCOUNT_STREET_ADDRESS));

          $error = true;
        }

        if ( ACCOUNT_SUBURB > -1 ) {
          if ( isset($_POST['suburb']) && ((ACCOUNT_SUBURB == 0) || (strlen(trim($_POST['suburb'])) >= ACCOUNT_SUBURB)) ) {
            $address_array['suburb'] = trim($_POST['suburb']);
          } else {
            $OSCOM_MessageStack->add('CheckoutAddress', sprintf(OSCOM::getDef('field_customer_suburb_error'), ACCOUNT_SUBURB));

            $error = true;
          }
        }

        if ( ACCOUNT_POST_CODE > -1 ) {
          if ( isset($_POST['postcode']) && ((ACCOUNT_POST_CODE == 0) || (strlen(trim($_POST['postcode'])) >= ACCOUNT_POST_CODE)) ) {
            $address_array['postcode'] = trim($_POST['postcode']);
          } else {
            $OSCOM_MessageStack->add('CheckoutAddress', sprintf(OSCOM::getDef('field_customer_post_code_error'), ACCOUNT_POST_CODE));

            $error = true;
          }
        }

        if ( isset($_POST['city']) && (strlen(trim($_POST['city'])) >= ACCOUNT_CITY) ) {
          $address_array['city'] = trim($_POST['city']);
        } else {
          $OSCOM_MessageStack->add('CheckoutAddress', sprintf(OSCOM::getDef('field_customer_city_error'), ACCOUNT_CITY));

          $error = true;
        }

        if ( ACCOUNT_STATE > -1 ) {
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
              $address_array['zone_id'] = $Qzone->valueInt('zone_id');
            } else {
              $Qzone = $OSCOM_PDO->prepare('select zone_id from :table_zones where zone_country_id = :zone_country_id and zone_name like :zone_name');
              $Qzone->bindInt(':zone_country_id', $_POST['country']);
              $Qzone->bindValue(':zone_name', $_POST['state'] . '%');
              $Qzone->execute();

              if ( $Qzone->fetch() !== false ) {
                $address_array['zone_id'] = $Qzone->valueInt('zone_id');
              } else {
                $OSCOM_MessageStack->add('CheckoutAddress', OSCOM::getDef('field_customer_state_select_pull_down_error'));

                $error = true;
              }
            }
          } else {
            if ( isset($_POST['state']) && ((ACCOUNT_STATE == 0) || (strlen(trim($_POST['state'])) >= ACCOUNT_STATE)) ) {
              $address_array['state'] = trim($_POST['state']);
            } else {
              $OSCOM_MessageStack->add('CheckoutAddress', sprintf(OSCOM::getDef('field_customer_state_error'), ACCOUNT_STATE));

              $error = true;
            }
          }
        }

        if ( isset($_POST['country']) && is_numeric($_POST['country']) && ($_POST['country'] > 0) ) {
          $address_array['country_id'] = (int)$_POST['country'];
        } else {
          $OSCOM_MessageStack->add('CheckoutAddress', OSCOM::getDef('field_customer_country_error'));

          $error = true;
        }

        if ( ACCOUNT_TELEPHONE > -1 ) {
          if ( isset($_POST['telephone']) && ((ACCOUNT_TELEPHONE == 0) || (strlen(trim($_POST['telephone'])) >= ACCOUNT_TELEPHONE)) ) {
            $address_array['telephone'] = trim($_POST['telephone']);
          } else {
            $OSCOM_MessageStack->add('CheckoutAddress', sprintf(OSCOM::getDef('field_customer_telephone_number_error'), ACCOUNT_TELEPHONE));

            $error = true;
          }
        }

        if ( ACCOUNT_FAX > -1 ) {
          if ( isset($_POST['fax']) && ((ACCOUNT_FAX == 0) || (strlen(trim($_POST['fax'])) >= ACCOUNT_FAX)) ) {
            $address_array['fax'] = trim($_POST['fax']);
          } else {
            $OSCOM_MessageStack->add('CheckoutAddress', sprintf(OSCOM::getDef('field_customer_fax_number_error'), ACCOUNT_FAX));

            $error = true;
          }
        }

        if ( $error === false ) {
          if ( $OSCOM_Customer->isLoggedOn() ) {
            $Qab = $OSCOM_PDO->prepare('insert into :table_address_book (customers_id, entry_gender, entry_company, entry_firstname, entry_lastname, entry_street_address, entry_suburb, entry_postcode, entry_city, entry_state, entry_country_id, entry_zone_id, entry_telephone, entry_fax) values (:customers_id, :entry_gender, :entry_company, :entry_firstname, :entry_lastname, :entry_street_address, :entry_suburb, :entry_postcode, :entry_city, :entry_state, :entry_country_id, :entry_zone_id, :entry_telephone, :entry_fax)');
            $Qab->bindInt(':customers_id', $OSCOM_Customer->getID());
            $Qab->bindValue(':entry_gender', (isset($address_array['gender']) ? $address_array['gender'] : ''));
            $Qab->bindValue(':entry_company', (isset($address_array['company']) ? $address_array['company'] : ''));
            $Qab->bindValue(':entry_firstname', $address_array['firstname']);
            $Qab->bindValue(':entry_lastname', $address_array['lastname']);
            $Qab->bindValue(':entry_street_address', $address_array['street_address']);
            $Qab->bindValue(':entry_suburb', (isset($address_array['suburb']) ? $address_array['suburb'] : ''));
            $Qab->bindValue(':entry_postcode', (isset($address_array['postcode']) ? $address_array['postcode'] : ''));
            $Qab->bindValue(':entry_city', $address_array['city']);
            $Qab->bindValue(':entry_state', ($address_array['zone_id'] > 0 ? '' : $address_array['state']));
            $Qab->bindInt(':entry_country_id', $address_array['country_id']);
            $Qab->bindInt(':entry_zone_id', $address_array['zone_id']);
            $Qab->bindValue(':entry_telephone', (isset($address_array['telephone']) ? $address_array['telephone'] : ''));
            $Qab->bindValue(':entry_fax', (isset($address_array['fax']) ? $address_array['fax'] : ''));
            $Qab->execute();

            if ( $Qab->rowCount() === 1 ) {
              $address_book_id = $OSCOM_PDO->lastInsertId();

              if ( !$OSCOM_Customer->hasDefaultAddress() ) {
                $Qcustomer = $OSCOM_PDO->prepare('update :table_customers set customers_default_address_id = :customers_default_address_id where customers_id = :customers_id');
                $Qcustomer->bindInt(':customers_default_address_id', $address_book_id);
                $Qcustomer->bindInt(':customers_id', $OSCOM_Customer->getID());
                $Qcustomer->execute();

                $OSCOM_Customer->setCountryID($address_array['country_id']);
                $OSCOM_Customer->setZoneID($address_array['zone_id']);
                $OSCOM_Customer->setDefaultAddressID($address_book_id);
              }

              $OSCOM_ShoppingCart->setBillingAddress($address_book_id);
              $OSCOM_ShoppingCart->resetBillingMethod();

              OSCOM::redirect(OSCOM::getLink(null, null, 'Confirm', 'SSL'));
            } else {
              $OSCOM_MessageStack->add('CheckoutAddress', 'Error inserting into address book table.');
            }
          } else {
            $OSCOM_Customer->setGender((isset($address_array['gender']) ? $address_array['gender'] : null));
            $OSCOM_Customer->setFirstName($address_array['firstname']);
            $OSCOM_Customer->setLastName($address_array['lastname']);

            $OSCOM_Customer->setCountryID($address_array['country_id']);
            $OSCOM_Customer->setZoneID($address_array['zone_id']);

            $OSCOM_ShoppingCart->setBillingAddress($address_array);
            $OSCOM_ShoppingCart->resetBillingMethod();

            OSCOM::redirect(OSCOM::getLink(null, null, null, 'SSL'));
          }
        }
// process the selected shipping destination
      } elseif ( isset($_POST['ab']) && is_numeric($_POST['ab']) ) {
        if ( AddressBook::checkEntry($_POST['ab']) ) {
          $OSCOM_ShoppingCart->setBillingAddress($_POST['ab']);
          $OSCOM_ShoppingCart->resetBillingMethod();

          OSCOM::redirect(OSCOM::getLink(null, null, 'Billing', 'SSL'));
        } else {
          OSCOM::redirect(OSCOM::getLink(null, null, 'Billing&Address', 'SSL'));
        }
      } else {
        OSCOM::redirect(OSCOM::getLink(null, null, 'Confirm', 'SSL'));
      }
    }
  }
?>
