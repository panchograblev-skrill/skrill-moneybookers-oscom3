<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core;

  use osCommerce\OM\Core\Registry;

  class CreditCard {
    protected $_owner,
              $_number,
              $_expiry_month,
              $_expiry_year,
              $_cvc,
              $_type,
              $_data;

    public function __construct($number = null, $exp_month = null, $exp_year = null) {
      $OSCOM_PDO = Registry::get('PDO');

      if ( !empty($number) ) {
        $this->_number = preg_replace('/[^0-9]/', '', $number);
        $this->_expiry_month = (int)$exp_month;
        $this->_expiry_year = (int)$exp_year;
      }

      $this->_data = array();

      $Qcc = $OSCOM_PDO->query('select id, credit_card_name as title, pattern from :table_credit_cards where credit_card_status = 1 order by sort_order, credit_card_name');
      $Qcc->setCache('credit_cards');
      $Qcc->execute();

      while ( $Qcc->fetch() ) {
        $this->_data[$Qcc->valueInt('id')] = $Qcc->toArray();
      }
    }

    public function isValid($valid_cc_types = null) {
      if ( CFG_CREDIT_CARDS_VERIFY_WITH_REGEXP == '1' ) {
        if ( $this->hasValidNumber() === false ) {
          return -1;
        }

        if ( $this->isAccepted($valid_cc_types) === false ) {
          return -5;
        }
      }

      if ( $this->hasValidExpiryDate() === false ) {
        return -2;
      }

      if ( $this->hasExpired() === true ) {
        return -3;
      }

      if ( $this->hasOwner() && ($this->hasValidOwner() === false) ) {
        return -4;
      }

      return true;
    }

    public function hasValidNumber() {
      if ( !empty($this->_number) && (strlen($this->_number) >= CC_NUMBER_MIN_LENGTH) ) {
        $cardNumber = strrev($this->_number);
        $numSum = 0;

        for ( $i=0, $n=strlen($cardNumber); $i<$n; $i++ ) {
          $currentNum = substr($cardNumber, $i, 1);

// Double every second digit
          if ( $i % 2 == 1 ) {
            $currentNum *= 2;
          }

// Add digits of 2-digit numbers together
          if ( $currentNum > 9 ) {
            $firstNum = $currentNum % 10;
            $secondNum = ($currentNum - $firstNum) / 10;
            $currentNum = $firstNum + $secondNum;
          }

          $numSum += $currentNum;
        }

// If the total has no remainder it's OK
        return ($numSum % 10 == 0);
      }

      return false;
    }

    public function isAccepted($valid_cc_types) {
      if ( !empty($valid_cc_types) && !empty($this->_number) && (strlen($this->_number) >= CC_NUMBER_MIN_LENGTH) ) {
        if ( !is_array($valid_cc_types) ) {
          $valid_cc_types = explode(',', $valid_cc_types);
        }

        foreach ( $this->_data as $data ) {
          if ( in_array($data['id'], $valid_cc_types) ) {
            if ( preg_match($data['pattern'], $this->_number) === 1 ) {
              $this->_type = $data['title'];

              return true;
            }
          }
        }
      }

      return false;
    }

    public function hasValidExpiryDate() {
      $year = date('Y');

      return ( ($this->_expiry_month > 0) && ($this->_expiry_month < 13) && ($this->_expiry_year >= $year) && ($this->_expiry_year <= ($year+10)) );
    }

    public function hasExpired() {
      return ( ($this->_expiry_year <= date('Y')) && ($this->_expiry_month < date('n')) );
    }

    public function hasOwner() {
      return isset($this->_owner);
    }

    public function hasValidOwner() {
      return ( !empty($this->_owner) && (strlen($this->_owner) >= CC_OWNER_MIN_LENGTH) );
    }

    public function typeExists($id) {
      return isset($this->_data[$id]);
    }

    public function getNumber() {
      return $this->_number;
    }

    public function getSafeNumber() {
      return str_repeat('X', strlen($this->_number)-4) . substr($this->_number, -4);
    }

    public function getExpiryMonth() {
      return str_pad($this->_expiry_month, 2, '0', STR_PAD_LEFT);
    }

    public function getExpiryYear() {
      return $this->_expiry_year;
    }

    public function getCVC() {
      return $this->_cvc;
    }

    public function getOwner() {
      return $this->_owner;
    }

    public function getTypePattern($id) {
      return $this->_data[$id]['pattern'];
    }

    public function setOwner($name) {
      $this->_owner = trim($name);
    }

    public function setCVC($cvc) {
      $this->_cvc = trim($cvc);
    }
  }
?>
