<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Module\Service;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Shop\Currencies as CurrenciesClass;

  class Currencies implements \osCommerce\OM\Core\Site\Shop\ServiceInterface {
    public static function start() {
      Registry::set('Currencies', new CurrenciesClass());
      $OSCOM_Currencies = Registry::get('Currencies');

      $OSCOM_Language = Registry::get('Language');

      if ( !isset($_SESSION['currency']) || isset($_GET['currency']) || ( (USE_DEFAULT_LANGUAGE_CURRENCY == '1') && ($OSCOM_Currencies->getCode($OSCOM_Language->getCurrencyID()) != $_SESSION['currency']) ) ) {
        if ( isset($_GET['currency']) && $OSCOM_Currencies->exists($_GET['currency']) ) {
          $_SESSION['currency'] = $_GET['currency'];
        } else {
          $_SESSION['currency'] = (USE_DEFAULT_LANGUAGE_CURRENCY == '1') ? $OSCOM_Currencies->getCode($OSCOM_Language->getCurrencyID()) : DEFAULT_CURRENCY;
        }

        if ( isset($_SESSION['cartID']) ) {
          unset($_SESSION['cartID']);
        }
      }

      return true;
    }

    public static function stop() {
      return true;
    }
  }
?>
