<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop;

  use osCommerce\OM\Core\Registry;

  class Customer {
    protected $_is_logged_on = false;
    protected $_data = array();

    public function __construct() {
      if ( !isset($_SESSION['osC_Customer_data']) ) {
        $_SESSION['osC_Customer_data'] = $this->_data;
      }

      $this->_data =& $_SESSION['osC_Customer_data'];

      if ( isset($this->_data['id']) && is_numeric($this->_data['id']) && ($this->_data['id'] > 0) ) {
        $this->setIsLoggedOn(true);
      }
    }

    public function getID() {
      if ( isset($this->_data['id']) && is_numeric($this->_data['id']) ) {
        return (int)$this->_data['id'];
      }

      return 0;
    }

    public function getFirstName() {
      if ( isset($this->_data['first_name']) ) {
        return $this->_data['first_name'];
      }

      return false;
    }

    public function getLastName() {
      if ( isset($this->_data['last_name']) ) {
        return $this->_data['last_name'];
      }

      return false;
    }

    public function getName() {
      $name = '';

      if ( isset($this->_data['first_name']) ) {
        $name .= $this->_data['first_name'];
      }

      if ( isset($this->_data['last_name']) ) {
        if ( !empty($name) ) {
          $name .= ' ';
        }

        $name .= $this->_data['last_name'];
      }

      return $name;
    }

    public function getGender() {
      if ( isset($this->_data['gender']) ) {
        return $this->_data['gender'];
      }

      return false;
    }

    function hasEmailAddress() {
      return isset($this->_data['email_address']);
    }

    function getEmailAddress() {
      if ( isset($this->_data['email_address']) ) {
        return $this->_data['email_address'];
      }

      return false;
    }

    function getCountryID() {
      static $country_id = null;

      if ( is_null($country_id) ) {
        if ( isset($this->_data['country_id']) ) {
          $country_id = $this->_data['country_id'];
        }
      }

      return $country_id;
    }

    function getZoneID() {
      static $zone_id = null;

      if ( is_null($zone_id) ) {
        if ( isset($this->_data['zone_id']) ) {
          $zone_id = $this->_data['zone_id'];
        }
      }

      return $zone_id;
    }

    function getDefaultAddressID() {
      static $id = null;

      if ( is_null($id) ) {
        if ( isset($this->_data['default_address_id']) ) {
          $id = $this->_data['default_address_id'];
        }
      }

      return $id;
    }

    function setCustomerData($customer_id = -1) {
      $OSCOM_PDO = Registry::get('PDO');

      $this->_data = array();

      if ( is_numeric($customer_id) && ($customer_id > 0) ) {
        $Qcustomer = $OSCOM_PDO->prepare('select customers_gender, customers_firstname, customers_lastname, customers_email_address, customers_default_address_id from :table_customers where customers_id = :customers_id');
        $Qcustomer->bindInt(':customers_id', $customer_id);
        $Qcustomer->execute();

        if ( $Qcustomer->fetch() !== false ) {
          $this->setIsLoggedOn(true);
          $this->setID($customer_id);
          $this->setGender($Qcustomer->value('customers_gender'));
          $this->setFirstName($Qcustomer->value('customers_firstname'));
          $this->setLastName($Qcustomer->value('customers_lastname'));
          $this->setEmailAddress($Qcustomer->value('customers_email_address'));

          if ( is_numeric($Qcustomer->value('customers_default_address_id')) && ($Qcustomer->value('customers_default_address_id') > 0) ) {
            $Qab = $OSCOM_PDO->prepare('select entry_country_id, entry_zone_id from :table_address_book where address_book_id = :address_book_id and customers_id = :customers_id');
            $Qab->bindInt(':address_book_id', $Qcustomer->value('customers_default_address_id'));
            $Qab->bindInt(':customers_id', $customer_id);
            $Qab->execute();

            if ( $Qab->fetch() !== false ) {
              $this->setCountryID($Qab->value('entry_country_id'));
              $this->setZoneID($Qab->value('entry_zone_id'));
              $this->setDefaultAddressID($Qcustomer->value('customers_default_address_id'));
            }
          }
        }
      }

      if ( sizeof($this->_data) > 0 ) {
        $_SESSION['osC_Customer_data'] = $this->_data;
      } elseif ( isset($_SESSION['osC_Customer_data']) ) {
        $this->reset();
      }
    }

    function setIsLoggedOn($state) {
      if ( $state === true ) {
        $this->_is_logged_on = true;
      } else {
        $this->_is_logged_on = false;
      }
    }

    function isLoggedOn() {
      if ( $this->_is_logged_on === true ) {
        return true;
      }

      return false;
    }

    function setID($id) {
      if ( is_numeric($id) && ($id > 0) ) {
        $this->_data['id'] = $id;
      } else {
        $this->_data['id'] = false;
      }
    }

    function setDefaultAddressID($id) {
      if ( is_numeric($id) && ($id > 0) ) {
        $this->_data['default_address_id'] = $id;
      } else {
        $this->_data['default_address_id'] = false;
      }
    }

    function hasDefaultAddress() {
      if ( isset($this->_data['default_address_id']) && is_numeric($this->_data['default_address_id']) ) {
        return true;
      }

      return false;
    }

    function setGender($gender) {
      if ( (strtolower($gender) == 'm') || (strtolower($gender) == 'f') ) {
        $this->_data['gender'] = strtolower($gender);
      } else {
        $this->_data['gender'] = false;
      }
    }

    function setFirstName($first_name) {
      $this->_data['first_name'] = $first_name;
    }

    function setLastName($last_name) {
      $this->_data['last_name'] = $last_name;
    }

    function setEmailAddress($email_address) {
      $this->_data['email_address'] = $email_address;
    }

    function setCountryID($id) {
      $this->_data['country_id'] = $id;
    }

    function setZoneID($id) {
      $this->_data['zone_id'] = $id;
    }

    function reset() {
      $this->_is_logged_on = false;
      $this->_data = array();

      if ( isset($_SESSION['osC_Customer_data']) ) {
        unset($_SESSION['osC_Customer_data']);
      }
    }

// HPDL integrate into class better
    public function hasProductNotifications() {
      $OSCOM_PDO = Registry::get('PDO');

      $Qcheck = $OSCOM_PDO->prepare('select products_id from :table_products_notifications where customers_id = :customers_id limit 1');
      $Qcheck->bindInt(':customers_id', $this->_data['id']);
      $Qcheck->execute();

      return ( $Qcheck->fetch() !== false );
    }

// HPDL integrate into class better
    function getProductNotifications() {
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_Language = Registry::get('Language');

      $Qproducts = $OSCOM_PDO->prepare('select pd.products_id, pd.products_name from :table_products_description pd, :table_products_notifications pn where pn.customers_id = :customers_id and pn.products_id = pd.products_id and pd.language_id = :language_id order by pd.products_name');
      $Qproducts->bindInt(':customers_id', $this->_data['id']);
      $Qproducts->bindInt(':language_id', $OSCOM_Language->getID());
      $Qproducts->execute();

      return $Qproducts;
    }
  }
?>
