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

  class Tax {
    protected $tax_rates = array();

    public function getTaxRate($class_id, $country_id = -1, $zone_id = -1) {
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');
      $OSCOM_PDO = Registry::get('PDO');

      if ( ($country_id == -1) && ($zone_id == -1) ) {
        $country_id = $OSCOM_ShoppingCart->getTaxingAddress('country_id');
        $zone_id = $OSCOM_ShoppingCart->getTaxingAddress('zone_id');
      }

      if ( !isset($this->tax_rates[$class_id][$country_id][$zone_id]['rate']) ) {
        $Qtax = $OSCOM_PDO->prepare('select sum(tax_rate) as tax_rate from :table_tax_rates tr left join :table_zones_to_geo_zones za on (tr.tax_zone_id = za.geo_zone_id) left join :table_geo_zones tz on (tz.geo_zone_id = tr.tax_zone_id) where (za.zone_country_id is null or za.zone_country_id = 0 or za.zone_country_id = :zone_country_id) and (za.zone_id is null or za.zone_id = 0 or za.zone_id = :zone_id) and tr.tax_class_id = :tax_class_id group by tr.tax_priority');
        $Qtax->bindInt(':zone_country_id', $country_id);
        $Qtax->bindInt(':zone_id', $zone_id);
        $Qtax->bindInt(':tax_class_id', $class_id);
        $Qtax->execute();

        $tax_rates = $Qtax->fetchAll();

        if ( count($tax_rates) > 0 ) {
          $tax_multiplier = 1.0;

          foreach ( $tax_rates as $tr ) {
            $tax_multiplier *= 1.0 + ($tr['tax_rate'] / 100);
          }

          $tax_rate = ($tax_multiplier - 1.0) * 100;
        } else {
          $tax_rate = 0;
        }

        $this->tax_rates[$class_id][$country_id][$zone_id]['rate'] = $tax_rate;
      }

      return $this->tax_rates[$class_id][$country_id][$zone_id]['rate'];
    }

    public function getTaxRateDescription($class_id, $country_id, $zone_id) {
      $OSCOM_PDO = Registry::get('PDO');

      if ( !isset($this->tax_rates[$class_id][$country_id][$zone_id]['description']) ) {
        $Qtax = $OSCOM_PDO->prepare('select tax_description from :table_tax_rates tr left join :table_zones_to_geo_zones za on (tr.tax_zone_id = za.geo_zone_id) left join :table_geo_zones tz on (tz.geo_zone_id = tr.tax_zone_id) where (za.zone_country_id is null or za.zone_country_id = 0 or za.zone_country_id = :zone_country_id) and (za.zone_id is null or za.zone_id = 0 or za.zone_id = :zone_id) and tr.tax_class_id = :tax_class_id group by tr.tax_priority');
        $Qtax->bindInt(':zone_country_id', $country_id);
        $Qtax->bindInt(':zone_id', $zone_id);
        $Qtax->bindInt(':tax_class_id', $class_id);
        $Qtax->execute();

        $tax_rates = $Qtax->fetchAll();

        if ( count($tax_rates) > 0 ) {
          $tax_description = '';

          foreach ( $tax_rates as $tr ) {
            $tax_description .= $tr['tax_description'] . ' + ';
          }

          $this->tax_rates[$class_id][$country_id][$zone_id]['description'] = substr($tax_description, 0, -3);
        } else {
          $this->tax_rates[$class_id][$country_id][$zone_id]['description'] = OSCOM::getDef('tax_rate_unknown');
        }
      }

      return $this->tax_rates[$class_id][$country_id][$zone_id]['description'];
    }

    public function calculate($price, $tax_rate) {
      $OSCOM_Currencies = Registry::get('Currencies');

      return round($price * $tax_rate / 100, $OSCOM_Currencies->currencies[DEFAULT_CURRENCY]['decimal_places']);
    }

    public function displayTaxRateValue($value, $padding = null) {
      if ( !is_numeric($padding) ) {
        $padding = TAX_DECIMAL_PLACES;
      }

      if ( strpos($value, '.') !== false ) {
        while ( true ) {
          if ( substr($value, -1) == '0' ) {
            $value = substr($value, 0, -1);
          } else {
            if ( substr($value, -1) == '.' ) {
              $value = substr($value, 0, -1);
            }

            break;
          }
        }
      }

      if ( $padding > 0 ) {
        if ( ($decimal_pos = strpos($value, '.')) !== false ) {
          $decimals = strlen(substr($value, ($decimal_pos+1)));

          for ( $i=$decimals; $i<$padding; $i++ ) {
            $value .= '0';
          }
        } else {
          $value .= '.';

          for ( $i=0; $i<$padding; $i++ ) {
            $value .= '0';
          }
        }
      }

      return $value . '%';
    }
  }
?>
