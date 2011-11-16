<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Languages\Model;

  use osCommerce\OM\Core\DirectoryListing;
  use osCommerce\OM\Core\OSCOM;

  class getDirectoryListing {
    public static function execute() {
      $result = array();

      $OSCOM_DirectoryListing = new DirectoryListing(OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Languages');
      $OSCOM_DirectoryListing->setIncludeDirectories(false);
      $OSCOM_DirectoryListing->setCheckExtension('xml');

      foreach ( $OSCOM_DirectoryListing->getFiles() as $file ) {
        $result[] = substr($file['name'], 0, strrpos($file['name'], '.'));
      }

      return $result;
    }
  }
?>
