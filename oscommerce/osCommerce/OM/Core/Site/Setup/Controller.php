<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Setup;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  class Controller implements \osCommerce\OM\Core\SiteInterface {
    protected static $_default_application = 'Index';

    public static function initialize() {
      Registry::set('Language', new Language());
      Registry::set('osC_Language', Registry::get('Language')); // HPDL to remove

      if ( !self::hasAccess(OSCOM::getSiteApplication()) ) {
        OSCOM::redirect(OSCOM::getLink(null, 'Offline'));
      }

      $application = 'osCommerce\\OM\\Core\\Site\\Setup\\Application\\' . OSCOM::getSiteApplication() . '\\Controller';
      Registry::set('Application', new $application());

      Registry::set('Template', new Template());
      Registry::get('Template')->setApplication(Registry::get('Application'));
    }

    public static function getDefaultApplication() {
      return self::$_default_application;
    }

    public static function hasAccess($application) {
      if ( OSCOM::configExists('offline') && (OSCOM::getConfig('offline') == 'true') && ($application != 'Offline') ) {
        return false;
      }

      return true;
    }
  }
?>
