<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Cache;

  class Shipping extends \osCommerce\OM\Core\Site\Shop\Shipping {
    var $_group = 'shipping';

    public function hasKeys() {
      return (count($this->getKeys()) > 0);
    }

    public function install() {
      $OSCOM_Language = Registry::get('Language');

      $data = array('title' => $this->_title,
                    'code' => $this->_code,
                    'author_name' => $this->_author_name,
                    'author_www' => $this->_author_www,
                    'group' => 'Shipping');

      OSCOM::callDB('Admin\InsertModule', $data, 'Site');

      foreach ( $OSCOM_Language->getAll() as $key => $value ) {
        if ( file_exists(OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Languages/' . $key . '/modules/shipping/' . $this->_code . '.xml') ) {
          foreach ( $OSCOM_Language->extractDefinitions($key . '/modules/shipping/' . $this->_code . '.xml') as $def ) {
            $def['id'] = $value['id'];

            OSCOM::callDB('Admin\InsertLanguageDefinition', $def, 'Site');
          }
        }
      }

      Cache::clear('languages');
    }

    public function remove() {
      $OSCOM_Language = Registry::get('Language');

      $data = array('code' => $this->_code,
                    'group' => 'Shipping');

      OSCOM::callDB('Admin\DeleteModule', $data, 'Site');

      if ( $this->hasKeys() ) {
        OSCOM::callDB('Admin\DeleteConfigurationParameters', $this->getKeys(), 'Site');

        Cache::clear('configuration');
      }

      if ( file_exists(OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Languages/' . $OSCOM_Language->getCode() . '/modules/shipping/' . $this->_code . '.xml') ) {
        foreach ( $OSCOM_Language->extractDefinitions($OSCOM_Language->getCode() . '/modules/shipping/' . $this->_code . '.xml') as $def ) {
          OSCOM::callDB('Admin\DeleteLanguageDefinitions', $def, 'Site');
        }

        Cache::clear('languages');
      }
    }
  }
?>
