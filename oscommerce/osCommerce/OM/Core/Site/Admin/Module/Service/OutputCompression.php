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

  class OutputCompression extends \osCommerce\OM\Core\Site\Admin\ServiceAbstract {
    var $precedes = 'Session';

    protected function initialize() {
      $this->title = OSCOM::getDef('services_output_compression_title');
      $this->description = OSCOM::getDef('services_output_compression_description');
    }

    public function install() {
      $data = array('title' => 'GZIP Compression Level',
                    'key' => 'SERVICE_OUTPUT_COMPRESSION_GZIP_LEVEL',
                    'value' => '5',
                    'description' => 'Set the GZIP compression level to this value (0=min, 9=max).',
                    'group_id' => '6',
                    'set_function' => 'osc_cfg_set_boolean_value(array(\'0\', \'1\', \'2\', \'3\', \'4\', \'5\', \'6\', \'7\', \'8\', \'9\'))');

      OSCOM::callDB('Admin\InsertConfigurationParameters', $data, 'Site');
    }

    public function remove() {
      OSCOM::callDB('Admin\DeleteConfigurationParameters', $this->keys(), 'Site');
    }

    public function keys() {
      return array('SERVICE_OUTPUT_COMPRESSION_GZIP_LEVEL');
    }
  }
?>
