<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Module\Service;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Language as LanguageClass;

  class Language implements \osCommerce\OM\Core\Site\Shop\ServiceInterface {
    public static function start() {
      Registry::set('Language', new LanguageClass());

      $OSCOM_Language = Registry::get('Language');

      if ( isset($_GET['language']) && !empty($_GET['language']) ) {
        $OSCOM_Language->set($_GET['language']);
      }

      $OSCOM_Language->load('general');
      $OSCOM_Language->load('modules-boxes');
      $OSCOM_Language->load('modules-content');

      $OSCOM_Language->load(OSCOM::getSiteApplication());

      header('Content-Type: text/html; charset=' . $OSCOM_Language->getCharacterSet());

      setlocale(LC_TIME, explode(',', $OSCOM_Language->getLocale()));

      return true;
    }

    public static function stop() {
      return true;
    }
  }
?>
