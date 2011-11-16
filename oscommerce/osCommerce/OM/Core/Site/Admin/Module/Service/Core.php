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

  class Core extends \osCommerce\OM\Core\Site\Admin\ServiceAbstract {
    var $uninstallable = false;
    var $depends = 'Currencies';

    protected function initialize() {
      $this->title = OSCOM::getDef('services_core_title');
      $this->description = OSCOM::getDef('services_core_description');
    }
  }
?>
