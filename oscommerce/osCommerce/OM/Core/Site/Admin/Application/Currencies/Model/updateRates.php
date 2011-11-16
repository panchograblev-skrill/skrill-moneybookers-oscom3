<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Currencies\Model;

  use osCommerce\OM\Core\Site\Admin\Application\Currencies\Currencies;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Cache;

  class updateRates {
    public static function execute($service) {
      $updated = array('0' => array(),
                       '1' => array());

      $currencies = Currencies::getAll(-1);
      $currencies = $currencies['entries'];

      foreach ( $currencies as $currency ) {
        $data = array('id' => $currency['currencies_id'],
                      'rate' => call_user_func('quote_' . $service . '_currency', $currency['code']));

        if ( !empty($data['rate']) && OSCOM::callDB('Admin\Currencies\UpdateRate', $data) ) {
          $updated[1][] = array('title' => $currency['title'],
                                'code' => $currency['code']);
        } else {
          $updated[0][] = array('title' => $currency['title'],
                                'code' => $currency['code']);
        }
      }

      if ( !empty($updated[1]) ) {
        Cache::clear('currencies');
      }

      return $updated;
    }
  }
?>
