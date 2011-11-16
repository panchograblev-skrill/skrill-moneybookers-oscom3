<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Languages\Model;

  use osCommerce\OM\Core\Site\Admin\Application\Languages\Languages;
  use osCommerce\OM\Core\Site\Admin\Application\Currencies\Currencies;
  use osCommerce\OM\Core\XML;

  class export {
    public static function execute($data) {
      $language = Languages::get($data['id']);

      $export_array = array();

      if ( $data['include_data'] === true ) {
        $export_array['language']['data'] = array('title-CDATA' => $language['name'],
                                                  'code-CDATA' => $language['code'],
                                                  'locale-CDATA' => $language['locale'],
                                                  'character_set-CDATA' => $language['charset'],
                                                  'text_direction-CDATA' => $language['text_direction'],
                                                  'date_format_short-CDATA' => $language['date_format_short'],
                                                  'date_format_long-CDATA' => $language['date_format_long'],
                                                  'time_format-CDATA' => $language['time_format'],
                                                  'default_currency-CDATA' => Currencies::get($language['currencies_id'], 'code'),
                                                  'numerical_decimal_separator-CDATA' => $language['numeric_separator_decimal'],
                                                  'numerical_thousands_separator-CDATA' => $language['numeric_separator_thousands']);

        if ( $language['parent_id'] > 0 ) {
          $export_array['language']['data']['parent_language_code'] = Languages::get($language['parent_id'], 'code');
        }
      }

      $definitions = Languages::getDefinitions($data['id'], $data['groups']);
      $definitions = $definitions['entries'];

      foreach ( $definitions as $def ) {
        $export_array['language']['definitions']['definition'][] = array('key' => $def['definition_key'],
                                                                         'value-CDATA' => $def['definition_value'],
                                                                         'group' => $def['content_group']);
      }

      $xml = XML::fromArray($export_array, $language['charset']);

      header('Content-disposition: attachment; filename=' . $language['code'] . '.xml');
      header('Content-Type: application/force-download');
      header('Content-Transfer-Encoding: binary');
      header('Content-Length: ' . strlen($xml));
      header('Pragma: no-cache');
      header('Expires: 0');

      echo $xml;

      exit;
    }
  }
?>
