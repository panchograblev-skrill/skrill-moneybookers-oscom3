<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Customers\Action\Save;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Admin\Application\Customers\Customers;

/**
 * @since v3.0.2
 */

  class Process {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_MessageStack = Registry::get('MessageStack');

      $error = false;

      $data = array('id' => (isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : null),
                    'gender' => (isset($_POST['gender']) ? $_POST['gender'] : ''),
                    'firstname' => $_POST['firstname'],
                    'lastname' => $_POST['lastname'],
                    'dob_day' => (isset($_POST['dob']) ? substr($_POST['dob'], 8, 2) : ''),
                    'dob_month' => (isset($_POST['dob']) ? substr($_POST['dob'], 5, 2) : ''),
                    'dob_year' => (isset($_POST['dob']) ? substr($_POST['dob'], 0, 4) : ''),
                    'email_address' => $_POST['email_address'],
                    'password' => $_POST['password'],
                    'newsletter' => (isset($_POST['newsletter']) && ($_POST['newsletter'] == 'on') ? '1' : '0'),
                    'status' => (isset($_POST['status']) && ($_POST['status'] == 'on') ? '1' : '0'));

      if ( ACCOUNT_GENDER > 0 ) {
        if ( ($data['gender'] != 'm') && ($data['gender'] != 'f') ) {
          $OSCOM_MessageStack->add(null, OSCOM::getDef('ms_error_gender'), 'error');
          $error = true;
        }
      }

      if ( strlen(trim($data['firstname'])) < ACCOUNT_FIRST_NAME ) {
        $OSCOM_MessageStack->add(null, sprintf(OSCOM::getDef('ms_error_first_name'), ACCOUNT_FIRST_NAME), 'error');
        $error = true;
      }

      if ( strlen(trim($data['lastname'])) < ACCOUNT_LAST_NAME ) {
        $OSCOM_MessageStack->add(null, sprintf(OSCOM::getDef('ms_error_last_name'), ACCOUNT_LAST_NAME), 'error');
        $error = true;
      }

      if ( ACCOUNT_DATE_OF_BIRTH == '1' ) {
        if ( !checkdate($data['dob_month'], $data['dob_day'], $data['dob_year']) ) {
          $OSCOM_MessageStack->add(null, OSCOM::getDef('ms_error_date_of_birth'), 'error');
          $error = true;
        }
      }

      if ( strlen(trim($data['email_address'])) < ACCOUNT_EMAIL_ADDRESS ) {
        $OSCOM_MessageStack->add(null, sprintf(OSCOM::getDef('ms_error_email_address'), ACCOUNT_EMAIL_ADDRESS), 'error');
        $error = true;
      } elseif ( filter_var($data['email_address'], FILTER_VALIDATE_EMAIL) === false ) {
        $OSCOM_MessageStack->add(null, OSCOM::getDef('ms_error_email_address_invalid'), 'error');
        $error = true;
      } elseif ( Customers::emailAddressExists($data['email_address'], (isset($_GET['id']) ? $_GET['id'] : null)) ) {
        $OSCOM_MessageStack->add(null, OSCOM::getDef('ms_error_email_address_exists'), 'error');
        $error = true;
      }

      if ( ( !isset($_GET['id']) || !empty($data['password']) ) && (strlen(trim($data['password'])) < ACCOUNT_PASSWORD) ) {
        $OSCOM_MessageStack->add(null, sprintf(OSCOM::getDef('ms_error_password'), ACCOUNT_PASSWORD), 'error');
        $error = true;
      } elseif ( !empty($_POST['confirmation']) && (trim($data['password']) != trim($_POST['confirmation'])) ) {
        $OSCOM_MessageStack->add(null, OSCOM::getDef('ms_error_password_confirmation_invalid'), 'error');
        $error = true;
      }

      if ( $error === false ) {
        if ( Customers::save($data) === false ) {
          $error = true;
        }
      }

      if ( $error === false ) {
        if ( isset($_GET['id']) && is_numeric($_GET['id']) ) {
          $customer_id = $_GET['id'];
        } else {
          $customer_data = OSCOM::callDB('Admin\Customers\Get', array('email_address' => $data['email_address']));
          $customer_id = $customer_data['customers_id'];
        }
      }

      if ( $error === false ) {
        if ( isset($_POST['ab']) && is_array($_POST['ab']) && !empty($_POST['ab']) ) {
          foreach ( $_POST['ab'] as $address ) {
            if ( $address['changed'] == true ) {
              $data = array('id' => $address['id'],
                            'customer_id' => $customer_id,
                            'gender' => (isset($address['gender']) ? $address['gender'] : ''),
                            'firstname' => $address['firstname'],
                            'lastname' => $address['lastname'],
                            'company' => (isset($address['company']) ? $address['company'] : ''),
                            'street_address' => $address['street_address'],
                            'postcode' => $address['postcode'],
                            'suburb' => (isset($address['suburb']) ? $address['suburb'] : ''),
                            'city' => $address['city'],
                            'state' => (isset($address['state']) ? $address['state'] : ''),
                            'zone_id' => (isset($address['zone_id']) ? $address['zone_id'] : ''),
                            'country_id' => $address['country_id'],
                            'telephone' => (isset($address['telephone']) ? $address['telephone'] : ''),
                            'fax' => (isset($address['fax']) ? $address['fax'] : ''),
                            'default' => (isset($_POST['ab_default_id']) && ($_POST['ab_default_id'] == $address['id']) ? true : false));

              if ( Customers::saveAddress($data) === false ) {
                $error = true;
                break;
              }
            }
          }
        }
      }

      if ( $error === false ) {
        if ( isset($_POST['new_address']) && is_array($_POST['new_address']) && !empty($_POST['new_address']) ) {
          foreach ( $_POST['new_address'] as $address ) {
            $data = array('customer_id' => $customer_id,
                          'gender' => (isset($address['gender']) ? $address['gender'] : ''),
                          'firstname' => $address['firstname'],
                          'lastname' => $address['lastname'],
                          'company' => (isset($address['company']) ? $address['company'] : ''),
                          'street_address' => $address['street_address'],
                          'postcode' => $address['postcode'],
                          'suburb' => (isset($address['suburb']) ? $address['suburb'] : ''),
                          'city' => $address['city'],
                          'state' => (isset($address['state']) ? $address['state'] : ''),
                          'zone_id' => (isset($address['zone_id']) ? $address['zone_id'] : ''),
                          'country_id' => $address['country_id'],
                          'telephone' => (isset($address['telephone']) ? $address['telephone'] : ''),
                          'fax' => (isset($address['fax']) ? $address['fax'] : ''),
                          'default' => (isset($address['default']) && ($address['default'] == 'true') ? true : false));

            if ( Customers::saveAddress($data) === false ) {
              $error = true;
              break;
            }
          }
        }
      }

      if ( $error === false ) {
        if ( isset($_POST['deleteAB']) && is_array($_POST['deleteAB']) && !empty($_POST['deleteAB']) ) {
          foreach ( $_POST['deleteAB'] as $ab_id ) {
            if ( Customers::deleteAddress($ab_id, $customer_id) === false ) {
              $error = true;
              break;
            }
          }
        }
      }

      if ( $error === false ) {
        $OSCOM_MessageStack->add(null, OSCOM::getDef('ms_success_action_performed'), 'success');
      } else {
        $OSCOM_MessageStack->add(null, OSCOM::getDef('ms_error_action_not_performed'), 'error');
      }

      OSCOM::redirect(OSCOM::getLink());
    }
  }
?>
