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

  class Session extends \osCommerce\OM\Core\Site\Admin\ServiceAbstract {
    var $uninstallable = false;

    protected function initialize() {
      $this->title = OSCOM::getDef('services_session_title');
      $this->description = OSCOM::getDef('services_session_description');
    }

    public function install() {
      $data = array(array('title' => 'Session Expiration Time',
                          'key' => 'SERVICE_SESSION_EXPIRATION_TIME',
                          'value' => '30',
                          'description' => 'The time (in minutes) to keep sessions active for. A value of 0 means until the browser is closed.',
                          'group_id' => '6'),
                    array('title' => 'Force Cookie Usage',
                          'key' => 'SERVICE_SESSION_FORCE_COOKIE_USAGE',
                          'value' => '-1',
                          'description' => 'Only start a session when cookies are enabled.',
                          'group_id' => '6',
                          'use_function' => 'osc_cfg_use_get_boolean_value',
                          'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))'),
                    array('title' => 'Block Search Engine Spiders',
                          'key' => 'SERVICE_SESSION_BLOCK_SPIDERS',
                          'value' => '-1',
                          'description' => 'Block search engine spider robots from starting a session.',
                          'group_id' => '6',
                          'use_function' => 'osc_cfg_use_get_boolean_value',
                          'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))'),
                    array('title' => 'Check SSL Session ID',
                          'key' => 'SERVICE_SESSION_CHECK_SSL_SESSION_ID',
                          'value' => '-1',
                          'description' => 'Check the SSL_SESSION_ID on every secure HTTPS page request.',
                          'group_id' => '6',
                          'use_function' => 'osc_cfg_use_get_boolean_value',
                          'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))'),
                    array('title' => 'Check User Agent',
                          'key' => 'SERVICE_SESSION_CHECK_USER_AGENT',
                          'value' => '-1',
                          'description' => 'Check the browser user agent on every page request.',
                          'group_id' => '6',
                          'use_function' => 'osc_cfg_use_get_boolean_value',
                          'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))'),
                    array('title' => 'Check IP Address',
                          'key' => 'SERVICE_SESSION_CHECK_IP_ADDRESS',
                          'value' => '-1',
                          'description' => 'Check the IP address on every page request.',
                          'group_id' => '6',
                          'use_function' => 'osc_cfg_use_get_boolean_value',
                          'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))'),
                    array('title' => 'Regenerate Session ID',
                          'key' => 'SERVICE_SESSION_REGENERATE_ID',
                          'value' => '-1',
                          'description' => 'Regenerate the session ID when a customer logs on or creates an account.',
                          'group_id' => '6',
                          'use_function' => 'osc_cfg_use_get_boolean_value',
                          'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))')
                   );

      OSCOM::callDB('Admin\InsertConfigurationParameters', $data, 'Site');
    }

    public function remove() {
      OSCOM::callDB('Admin\DeleteConfigurationParameters', $this->keys(), 'Site');
    }

    public function keys() {
      return array('SERVICE_SESSION_EXPIRATION_TIME',
                   'SERVICE_SESSION_FORCE_COOKIE_USAGE',
                   'SERVICE_SESSION_BLOCK_SPIDERS',
                   'SERVICE_SESSION_CHECK_SSL_SESSION_ID',
                   'SERVICE_SESSION_CHECK_USER_AGENT',
                   'SERVICE_SESSION_CHECK_IP_ADDRESS',
                   'SERVICE_SESSION_REGENERATE_ID');
    }
  }
?>
