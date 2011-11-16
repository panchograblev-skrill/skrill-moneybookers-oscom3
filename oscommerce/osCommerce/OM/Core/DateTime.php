<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core;

  class DateTime {

/**
 * @since v3.0.0
 */

    const DEFAULT_FORMAT = 'Y-m-d H:i:s';

/**
 * @since v3.0.0
 */

    public static function getNow($format = null) {
      if ( !isset($format) ) {
        $format = self::DEFAULT_FORMAT;
      }

      return date($format);
    }

/**
 * @since v3.0.0
 */

    public static function getShort($date = null, $with_time = false) {
      $OSCOM_Language = Registry::get('Language');

      if ( !isset($date) ) {
        $date = self::getNow();
      }

      $year = substr($date, 0, 4);
      $month = (int)substr($date, 5, 2);
      $day = (int)substr($date, 8, 2);
      $hour = (int)substr($date, 11, 2);
      $minute = (int)substr($date, 14, 2);
      $second = (int)substr($date, 17, 2);

      if ( @date('Y', mktime($hour, $minute, $second, $month, $day, $year)) == $year ) {
        return strftime($OSCOM_Language->getDateFormatShort($with_time), mktime($hour, $minute, $second, $month, $day, $year));
      } else {
        return preg_replace('/2037/', $year, strftime($OSCOM_Language->getDateFormatShort($with_time), mktime($hour, $minute, $second, $month, $day, 2037)));
      }
    }

/**
 * @since v3.0.0
 */

    public static function getLong($date = null) {
      $OSCOM_Language = Registry::get('Language');

      if ( !isset($date) ) {
        $date = self::getNow();
      }

      $year = substr($date, 0, 4);
      $month = (int)substr($date, 5, 2);
      $day = (int)substr($date, 8, 2);
      $hour = (int)substr($date, 11, 2);
      $minute = (int)substr($date, 14, 2);
      $second = (int)substr($date, 17, 2);

      if ( @date('Y', mktime($hour, $minute, $second, $month, $day, $year)) == $year ) {
        return strftime($OSCOM_Language->getDateFormatLong(), mktime($hour, $minute, $second, $month, $day, $year));
      } else {
        return preg_replace('/2037/', $year, strftime($OSCOM_Language->getDateFormatLong(), mktime($hour, $minute, $second, $month, $day, 2037)));
      }
    }

/**
 * @since v3.0.0
 */

    public static function getTimestamp($date = null, $format = null) {
      if ( !isset($date) ) {
        $date = self::getNow($format);
      }

      if ( !isset($format) ) {
        $format = self::DEFAULT_FORMAT;
      }

      $dt = \DateTime::createFromFormat($format, $date);
      $timestamp = $dt->getTimestamp();

      return $timestamp;
    }

/**
 * @since v3.0.0
 */

    public static function fromUnixTimestamp($timestamp, $format = null) {
      if ( !isset($format) ) {
        $format = self::DEFAULT_FORMAT;
      }

      return date($format, $timestamp);
    }

/**
 * @since v3.0.0
 */

    public static function isLeapYear($year = null) {
      if ( !isset($year) ) {
        $year = self::getNow('Y');
      }

      if ( $year % 100 == 0 ) {
        if ( $year % 400 == 0 ) {
          return true;
        }
      } else {
        if ( ($year % 4) == 0 ) {
          return true;
        }
      }

      return false;
    }

/**
 * @since v3.0.0
 */

    public static function validate($date_to_check, $format_string, &$date_array) {
      $separator_idx = -1;

      $separators = array('-', ' ', '/', '.');
      $month_abbr = array('jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec');
      $no_of_days = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

      $format_string = strtolower($format_string);

      if (strlen($date_to_check) != strlen($format_string)) {
        return false;
      }

      $size = sizeof($separators);
      for ($i=0; $i<$size; $i++) {
        $pos_separator = strpos($date_to_check, $separators[$i]);
        if ($pos_separator != false) {
          $date_separator_idx = $i;
          break;
        }
      }

      for ($i=0; $i<$size; $i++) {
        $pos_separator = strpos($format_string, $separators[$i]);
        if ($pos_separator != false) {
          $format_separator_idx = $i;
          break;
        }
      }

      if ($date_separator_idx != $format_separator_idx) {
        return false;
      }

      if ($date_separator_idx != -1) {
        $format_string_array = explode( $separators[$date_separator_idx], $format_string );
        if (sizeof($format_string_array) != 3) {
          return false;
        }

        $date_to_check_array = explode( $separators[$date_separator_idx], $date_to_check );
        if (sizeof($date_to_check_array) != 3) {
          return false;
        }

        $size = sizeof($format_string_array);
        for ($i=0; $i<$size; $i++) {
          if ($format_string_array[$i] == 'mm' || $format_string_array[$i] == 'mmm') $month = $date_to_check_array[$i];
          if ($format_string_array[$i] == 'dd') $day = $date_to_check_array[$i];
          if ( ($format_string_array[$i] == 'yyyy') || ($format_string_array[$i] == 'aaaa') ) $year = $date_to_check_array[$i];
        }
      } else {
        if (strlen($format_string) == 8 || strlen($format_string) == 9) {
          $pos_month = strpos($format_string, 'mmm');
          if ($pos_month != false) {
            $month = substr( $date_to_check, $pos_month, 3 );
            $size = sizeof($month_abbr);
            for ($i=0; $i<$size; $i++) {
              if ($month == $month_abbr[$i]) {
                $month = $i;
                break;
              }
            }
          } else {
            $month = substr($date_to_check, strpos($format_string, 'mm'), 2);
          }
        } else {
          return false;
        }

        $day = substr($date_to_check, strpos($format_string, 'dd'), 2);
        $year = substr($date_to_check, strpos($format_string, 'yyyy'), 4);
      }

      if (strlen($year) != 4) {
        return false;
      }

      if (!settype($year, 'integer') || !settype($month, 'integer') || !settype($day, 'integer')) {
        return false;
      }

      if ($month > 12 || $month < 1) {
        return false;
      }

      if ($day < 1) {
        return false;
      }

      if ( self::isLeapYear($year) ) {
        $no_of_days[1] = 29;
      }

      if ($day > $no_of_days[$month - 1]) {
        return false;
      }

      $date_array = array($year, $month, $day);

      return true;
    }

/**
 * Set the time zone to use for dates.
 * 
 * @param string $time_zone An optional time zone to set to
 * @param string $site The Site to retrieve the time zone from
 * @return boolean
 * @since v3.0.1
 */

    public static function setTimeZone($time_zone = null, $site = 'OSCOM') {
      if ( !isset($time_zone) ) {
        if ( OSCOM::configExists('time_zone', $site) ) {
          $time_zone = OSCOM::getConfig('time_zone', $site);
        } else {
          $time_zone = date_default_timezone_get();
        }
      }

      return date_default_timezone_set($time_zone);
    }

/**
 * Return an array of available time zones.
 * 
 * @return array
 * @since v3.0.1
 */

    public static function getTimeZones() {
      $result = array();

      foreach ( \DateTimeZone::listIdentifiers() as $id ) {
        $tz_string = str_replace('_', ' ', $id);

        $id_array = explode('/', $tz_string, 2);

        $result[$id_array[0]][$id] = isset($id_array[1]) ? $id_array[1] : $id_array[0];
      }

      return $result;
    }
  }
?>
