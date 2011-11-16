<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Languages\Model;

  use osCommerce\OM\Core\XML;
  use osCommerce\OM\Core\Site\Admin\Application\Currencies\Currencies;
  use osCommerce\OM\Core\Site\Admin\Application\Languages\Languages;
  use osCommerce\OM\Core\DirectoryListing;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Admin\Language;
  use osCommerce\OM\Core\Cache;

  class import {
    public static function execute($data) {
      $source = array('language' => XML::toArray(simplexml_load_file(OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Languages/' . $data['code'] . '.xml')));

      $language = array('name' => $source['language']['data']['title'],
                        'code' => $source['language']['data']['code'],
                        'locale' => $source['language']['data']['locale'],
                        'charset' => $source['language']['data']['character_set'],
                        'date_format_short' => $source['language']['data']['date_format_short'],
                        'date_format_long' => $source['language']['data']['date_format_long'],
                        'time_format' => $source['language']['data']['time_format'],
                        'text_direction' => $source['language']['data']['text_direction'],
                        'currency' => $source['language']['data']['default_currency'],
                        'numeric_separator_decimal' => $source['language']['data']['numerical_decimal_separator'],
                        'numeric_separator_thousands' => $source['language']['data']['numerical_thousands_separator'],
                        'parent_language_code' => (isset($source['language']['data']['parent_language_code']) ? $source['language']['data']['parent_language_code'] : ''),
                        'parent_id' => 0);

      if ( !Currencies::exists($language['currency']) ) {
        $language['currency'] = DEFAULT_CURRENCY;
      }

      $language['currencies_id'] = Currencies::get($language['currency'], 'currencies_id');

      if ( !empty($language['parent_language_code']) && Languages::exists($language['parent_language_code']) ) {
        $language['parent_id'] = Languages::get($language['parent_language_code'], 'languages_id');
      }

      $language['id'] = Languages::get($language['code'], 'languages_id');
      $language['default_language_id'] = Languages::get(DEFAULT_LANGUAGE, 'languages_id');
      $language['import_type'] = $data['type'];

      $definitions = array();

      if ( isset($source['language']['definitions']['definition']) ) {
        $definitions = $source['language']['definitions']['definition'];

        if ( isset($definitions['key']) && isset($definitions['value']) && isset($definitions['group']) ) {
          $definitions = array(array('key' => $definitions['key'],
                                     'value' => $definitions['value'],
                                     'group' => $definitions['group']));
        }
      }

      unset($source);

      $OSCOM_DirectoryListing = new DirectoryListing(OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Languages/' . $data['code']);
      $OSCOM_DirectoryListing->setRecursive(true);
      $OSCOM_DirectoryListing->setIncludeDirectories(false);
      $OSCOM_DirectoryListing->setAddDirectoryToFilename(true);
      $OSCOM_DirectoryListing->setCheckExtension('xml');

      foreach ( $OSCOM_DirectoryListing->getFiles() as $files ) {
        $definitions = array_merge($definitions, Language::extractDefinitions($data['code'] . '/' . $files['name']));
      }

      $language['definitions'] = $definitions;

      if ( OSCOM::callDB('Admin\Languages\Import', $language) ) {
        Cache::clear('languages');

        return true;
      }

      return false;
    }
  }
?>
