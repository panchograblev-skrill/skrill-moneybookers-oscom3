<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Module\Service;

  use osCommerce\OM\Core\OSCOM;

/**
 * @since v3.0.2
 */

  class Currencies extends \osCommerce\OM\Core\Site\Admin\ServiceAbstract {
    var $uninstallable = false;
    var $depends = 'Language';

    protected function initialize() {
      $this->title = OSCOM::getDef('services_currencies_title');
      $this->description = OSCOM::getDef('services_currencies_description');
    }

    public function install() {
      $data = array('title' => 'Use Default Language Currency',
                    'key' => 'USE_DEFAULT_LANGUAGE_CURRENCY',
                    'value' => '-1',
                    'description' => 'Automatically use the currency set with the language (eg, German->Euro).',
                    'group_id' => '6',
                    'use_function' => 'osc_cfg_use_get_boolean_value',
                    'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))');

      OSCOM::callDB('Admin\InsertConfigurationParameters', $data, 'Site');
    }

    public function remove() {
      OSCOM::callDB('Admin\DeleteConfigurationParameters', $this->keys(), 'Site');
    }

    public function keys() {
      return array('USE_DEFAULT_LANGUAGE_CURRENCY');
    }
  }
?>
