<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;

  class Payment {
    protected $_modules = array();

    public function __construct() {
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_Language = Registry::get('Language');

      $Qmodules = $OSCOM_PDO->prepare('select code from :table_modules where modules_group = :modules_group');
      $Qmodules->bindValue(':modules_group', 'Payment');
      $Qmodules->setCache('modules-payment');
      $Qmodules->execute();

      while ( $Qmodules->fetch() ) {
        $this->_modules[] = $Qmodules->value('code');
      }

      $OSCOM_Language->load('modules-payment');
    }

    public function loadAll() {
      foreach ( $this->_modules as $module ) {
        $module_class = 'osCommerce\\OM\\Core\\Site\\Shop\\Module\\Payment\\' . $module;

        Registry::set('Payment_' . $module, new $module_class(), true);
      }

      usort($this->_modules, function ($a, $b) {
        if ( Registry::get('Payment_' . $a)->getSortOrder() == Registry::get('Payment_' . $b)->getSortOrder() ) {
          return strnatcasecmp(Registry::get('Payment_' . $a)->getTitle(), Registry::get('Payment_' . $b)->getTitle());
        }

        return (Registry::get('Payment_' . $a)->getSortOrder() < Registry::get('Payment_' . $b)->getSortOrder()) ? -1 : 1;
      });
    }

    public function load($module) {
      if ( in_array($module, $this->_modules) ) {
        $module_class = 'osCommerce\\OM\\Core\\Site\\Shop\\Module\\Payment\\' . $module;

        Registry::set('PaymentModule', new $module_class(), true);
      }
    }

    function getJavascriptBlocks() {
      $js = '';

      if ( is_array($this->_modules) ) {
        $js = '<script type="text/javascript"><!-- ' . "\n" .
              'function check_form() {' . "\n" .
              '  var error = 0;' . "\n" .
              '  var error_message = "' . OSCOM::getDef('js_error') . '";' . "\n" .
              '  var payment_value = null;' . "\n" .
              '  if (document.checkout_payment.payment_method.length) {' . "\n" .
              '    for (var i=0; i<document.checkout_payment.payment_method.length; i++) {' . "\n" .
              '      if (document.checkout_payment.payment_method[i].checked) {' . "\n" .
              '        payment_value = document.checkout_payment.payment_method[i].value;' . "\n" .
              '      }' . "\n" .
              '    }' . "\n" .
              '  } else if (document.checkout_payment.payment_method.checked) {' . "\n" .
              '    payment_value = document.checkout_payment.payment_method.value;' . "\n" .
              '  } else if (document.checkout_payment.payment_method.value) {' . "\n" .
              '    payment_value = document.checkout_payment.payment_method.value;' . "\n" .
              '  }' . "\n\n";

        foreach ( $this->_modules as $module ) {
          if ( Registry::get('Payment_' . $module)->isEnabled() ) {
            $js .= Registry::get('Payment_' . $module)->getJavascriptBlock();
          }
        }

        $js .= "\n" . '  if (payment_value == null) {' . "\n" .
               '    error_message = error_message + "' . OSCOM::getDef('js_no_payment_module_selected') . '\n";' . "\n" .
               '    error = 1;' . "\n" .
               '  }' . "\n\n" .
               '  if (error == 1) {' . "\n" .
               '    alert(error_message);' . "\n" .
               '    return false;' . "\n" .
               '  } else {' . "\n" .
               '    return true;' . "\n" .
               '  }' . "\n" .
               '}' . "\n" .
               '//--></script>' . "\n";
      }

      return $js;
    }

    function selection() {
      $selection_array = array();

      foreach ($this->_modules as $module) {
        if (Registry::get('Payment_' . $module)->isEnabled()) {
          $selection = Registry::get('Payment_' . $module)->selection();
          if (is_array($selection)) $selection_array[] = $selection;
        }
      }

      return $selection_array;
    }

    public function hasActive() {
      $has_active = false;

      foreach ( $this->_modules as $module ) {
        if ( Registry::get('Payment_' . $module)->isEnabled() ) {
          $has_active = true;
          break;
        }
      }

      return $has_active;
    }

    public function numberOfActive() {
      $active = 0;

      foreach ( $this->_modules as $module ) {
        if ( Registry::get('Payment_' . $module)->isEnabled() ) {
          $active++;
        }
      }

      return $active;
    }

    public function getActive() {
      $active = array();

      foreach ( $this->_modules as $module ) {
        if ( Registry::get('Payment_' . $module)->isEnabled() ) {
          $active[] = $module;
        }
      }

      return $active;
    }
  }
?>
