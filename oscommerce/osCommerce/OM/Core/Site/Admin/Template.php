<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin;

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;

  class Template extends \osCommerce\OM\Core\Template {
    public function __construct() {
      $this->set('oscom');
    }

    public function getIcon($size = 16, $icon = null, $title = null) {
      if ( !isset($icon) ) {
        $icon = $this->_application->getIcon();
      }

      return HTML::image(OSCOM::getPublicSiteLink('images/applications/' . $size . '/' . $icon), $title, $size, $size);
    }
  }
?>
