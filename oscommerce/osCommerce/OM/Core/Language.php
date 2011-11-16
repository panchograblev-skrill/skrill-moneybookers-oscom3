<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core;

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;

  class Language {
    protected $_code,
              $_languages = array(),
              $_definitions = array();

    public function __construct() {
      foreach ( OSCOM::callDB('GetLanguages', null, 'Core') as $lang ) {
        $this->_languages[$lang['code']] = array('id' => (int)$lang['languages_id'],
                                                 'code' => $lang['code'],
                                                 'name' => $lang['name'],
                                                 'locale' => $lang['locale'],
                                                 'charset' => $lang['charset'],
                                                 'date_format_short' => $lang['date_format_short'],
                                                 'date_format_long' => $lang['date_format_long'],
                                                 'time_format' => $lang['time_format'],
                                                 'text_direction' => $lang['text_direction'],
                                                 'currencies_id' => (int)$lang['currencies_id'],
                                                 'numeric_separator_decimal' => $lang['numeric_separator_decimal'],
                                                 'numeric_separator_thousands' => $lang['numeric_separator_thousands'],
                                                 'parent_id' => (int)$lang['parent_id']);
      }

      $this->set();
    }

    public function load($key, $language_code = null) {
      if ( !isset($language_code) ) {
        $language_code = $this->_code;
      }

      if ( $this->_languages[$language_code]['parent_id'] > 0 ) {
        $this->load($key, $this->getCodeFromID($this->_languages[$language_code]['parent_id']));
      }

      $def_data = array('language_id' => $this->getData('id', $language_code),
                        'group' => $key);

      foreach ( OSCOM::callDB('GetLanguageDefinitions', $def_data, 'Core') as $def ) {
        $this->_definitions[$def['definition_key']] = $def['definition_value'];
      }
    }

    public function get($key) {
      if ( isset($this->_definitions[$key]) ) {
        return $this->_definitions[$key];
      }

      return $key;
    }

    public function set($code = null) {
      $this->_code = $code;

      if ( empty($this->_code) ) {
        if ( isset($_GET['language']) ) {
          $this->_code = $_GET['language'];
        } elseif ( isset($_SESSION['language']) ) {
          $this->_code = $_SESSION['language'];
        } elseif ( isset($_COOKIE['language']) ) {
          $this->_code = $_COOKIE['language'];
        } else {
          $this->_code = $this->getBrowserSetting();
        }
      }

      if ( empty($this->_code) || ($this->exists($this->_code) === false) ) {
        $this->_code = DEFAULT_LANGUAGE;
      }

      if ( !isset($_COOKIE['language']) || ($_COOKIE['language'] != $this->_code) ) {
        OSCOM::setCookie('language', $this->_code, time()+60*60*24*90);
      }

      if ( !isset($_SESSION['language']) || ($_SESSION['language'] != $this->_code) ) {
        $_SESSION['language'] = $this->_code;
      }
    }

    public function getBrowserSetting() {
      if ( isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) ) {
        $browser_languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

        $languages = array('ar' => 'ar([-_][[:alpha:]]{2})?|arabic',
                           'bg' => 'bg|bulgarian',
                           'br' => 'pt[-_]br|brazilian portuguese',
                           'ca' => 'ca|catalan',
                           'cs' => 'cs|czech',
                           'da' => 'da|danish',
                           'de' => 'de([-_][[:alpha:]]{2})?|german',
                           'el' => 'el|greek',
                           'en' => 'en([-_][[:alpha:]]{2})?|english',
                           'es' => 'es([-_][[:alpha:]]{2})?|spanish',
                           'et' => 'et|estonian',
                           'fi' => 'fi|finnish',
                           'fr' => 'fr([-_][[:alpha:]]{2})?|french',
                           'gl' => 'gl|galician',
                           'he' => 'he|hebrew',
                           'hu' => 'hu|hungarian',
                           'id' => 'id|indonesian',
                           'it' => 'it|italian',
                           'ja' => 'ja|japanese',
                           'ko' => 'ko|korean',
                           'ka' => 'ka|georgian',
                           'lt' => 'lt|lithuanian',
                           'lv' => 'lv|latvian',
                           'nl' => 'nl([-_][[:alpha:]]{2})?|dutch',
                           'no' => 'no|norwegian',
                           'pl' => 'pl|polish',
                           'pt' => 'pt([-_][[:alpha:]]{2})?|portuguese',
                           'ro' => 'ro|romanian',
                           'ru' => 'ru|russian',
                           'sk' => 'sk|slovak',
                           'sr' => 'sr|serbian',
                           'sv' => 'sv|swedish',
                           'th' => 'th|thai',
                           'tr' => 'tr|turkish',
                           'uk' => 'uk|ukrainian',
                           'tw' => 'zh[-_]tw|chinese traditional',
                           'zh' => 'zh|chinese simplified');

        foreach ( $browser_languages as $browser_language ) {
          foreach ( $languages as $key => $value ) {
            if ( preg_match('/^(' . $value . ')(;q=[0-9]\\.[0-9])?$/i', $browser_language) && $this->exists($key) ) {
              return $key;
            }
          }
        }
      }

      return false;
    }

    public function exists($code) {
      return array_key_exists($code, $this->_languages);
    }

    public function getAll() {
      return $this->_languages;
    }

    public function getData($key, $language = null) {
      if ( !isset($language) ) {
        $language = $this->_code;
      }

      return $this->_languages[$language][$key];
    }

    public function getCodeFromID($id) {
      foreach ( $this->_languages as $code => $lang ) {
        if ( $lang['id'] == $id ) {
          return $code;
        }
      }
    }

    public function getID() {
      return $this->_languages[$this->_code]['id'];
    }

    public function getName() {
      return $this->_languages[$this->_code]['name'];
    }

    public function getCode() {
      return $this->_code;
    }

    public function getLocale() {
      return $this->_languages[$this->_code]['locale'];
    }

    public function getCharacterSet() {
      return $this->_languages[$this->_code]['charset'];
    }

    public function getDateFormatShort($with_time = false) {
      if ( $with_time === true ) {
        return $this->_languages[$this->_code]['date_format_short'] . ' ' . $this->getTimeFormat();
      }

      return $this->_languages[$this->_code]['date_format_short'];
    }

    public function getDateFormatLong() {
      return $this->_languages[$this->_code]['date_format_long'];
    }

    public function getTimeFormat() {
      return $this->_languages[$this->_code]['time_format'];
    }

    public function getTextDirection() {
      return $this->_languages[$this->_code]['text_direction'];
    }

    public function getCurrencyID() {
      return $this->_languages[$this->_code]['currencies_id'];
    }

    public function getNumericDecimalSeparator() {
      return $this->_languages[$this->_code]['numeric_separator_decimal'];
    }

    public function getNumericThousandsSeparator() {
      return $this->_languages[$this->_code]['numeric_separator_thousands'];
    }

    public function showImage($code = null, $width = 16, $height = 10, $parameters = null) {
      if ( !isset($code) ) {
        $code = $this->_code;
      }

      $image_code = strtolower(substr($code, 3));

      if ( !is_numeric($width) ) {
        $width = 16;
      }

      if ( !is_numeric($height) ) {
        $height = 10;
      }

      return HTML::image(OSCOM::getPublicSiteLink('images/worldflags/' . $image_code . '.png', null, 'Shop'), $this->_languages[$code]['name'], $width, $height, $parameters);
    }

/**
 * Converts string to UTF-8
 *
 * @param string $string The string to convert to UTF-8
 * @return string
 * @since v3.0.2
 */

    public static function toUTF8($string) {
      if ( !static::isUTF8($string) ) {
        $string = iconv('CP1252', 'UTF-8', $string);
      }

      return $string;
    }

/**
 * Detect if a string is UTF-8
 *
 * @param string $string The string to detect
 * @return boolean
 * @since v3.0.2
 */
    public static function isUTF8($string) {
      if ( strlen($string) > 5000 ) {
        for ( $i=0, $s=5000, $j = ceil(strlen($string)/5000); $i < $j; $i++, $s += 5000 ) {
          if ( static::isUTF8(substr($string, $s, 5000)) ) {
            return true;
          }
        }

        return false;
      } else {
        return preg_match('%^(?:[\x09\x0A\x0D\x20-\x7E]           # ASCII
                              | [\xC2-\xDF][\x80-\xBF]            # non-overlong 2-byte
                              |  \xE0[\xA0-\xBF][\x80-\xBF]       # excluding overlongs
                              | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
                              |  \xED[\x80-\x9F][\x80-\xBF]       # excluding surrogates
                              |  \xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
                              | [\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15
                              |  \xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
                             )*$%xs', $string);
      }
    }
  }
?>
