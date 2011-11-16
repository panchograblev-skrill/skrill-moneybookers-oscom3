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

  class Reviews extends \osCommerce\OM\Core\Site\Admin\ServiceAbstract {
    protected function initialize() {
      $this->title = OSCOM::getDef('services_reviews_title');
      $this->description = OSCOM::getDef('services_reviews_description');
    }

    public function install() {
      $data = array(array('title' => 'New Reviews',
                          'key' => 'MAX_DISPLAY_NEW_REVIEWS',
                          'value' => '6',
                          'description' => 'Maximum number of new reviews to display',
                          'group_id' => '6'),
                    array('title' => 'Review Level',
                          'key' => 'SERVICE_REVIEW_ENABLE_REVIEWS',
                          'value' => '1',
                          'description' => 'Customer level required to write a review.',
                          'group_id' => '6',
                          'set_function' => 'osc_cfg_set_boolean_value(array(\'0\', \'1\', \'2\'))'),
                    array('title' => 'Moderate Reviews',
                          'key' => 'SERVICE_REVIEW_ENABLE_MODERATION',
                          'value' => '-1',
                          'description' => 'Should reviews be approved by store admin.',
                          'group_id' => '6',
                          'set_function' => 'osc_cfg_set_boolean_value(array(\'-1\', \'0\', \'1\'))')
                   );

      OSCOM::callDB('Admin\InsertConfigurationParameters', $data, 'Site');
    }

    public function remove() {
      OSCOM::callDB('Admin\DeleteConfigurationParameters', $this->keys(), 'Site');
    }

    public function keys() {
      return array('MAX_DISPLAY_NEW_REVIEWS',
                   'SERVICE_REVIEW_ENABLE_REVIEWS',
                   'SERVICE_REVIEW_ENABLE_MODERATION');
    }
  }
?>
