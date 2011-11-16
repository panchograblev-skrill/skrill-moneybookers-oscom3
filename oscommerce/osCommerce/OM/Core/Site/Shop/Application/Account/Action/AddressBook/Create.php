<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Application\Account\Action\AddressBook;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Shop\AddressBook;

  class Create {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_Breadcrumb = Registry::get('Breadcrumb');
      $OSCOM_Template = Registry::get('Template');
      $OSCOM_MessageStack = Registry::get('MessageStack');

      if ( $OSCOM_Service->isStarted('Breadcrumb') ) {
        $OSCOM_Breadcrumb->add(OSCOM::getDef('breadcrumb_address_book_add_entry'), OSCOM::getLink(null, null, 'AddressBook&Create', 'SSL'));
      }

      $application->setPageTitle(OSCOM::getDef('address_book_add_entry_heading'));
      $application->setPageContent('address_book_process.php');

      $OSCOM_Template->addJavascriptPhpFilename(OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/assets/form_check.js.php');

      if ( AddressBook::numberOfEntries() >= MAX_ADDRESS_BOOK_ENTRIES ) {
        $OSCOM_MessageStack->add('AddressBook', OSCOM::getDef('error_address_book_full'));

        $application->setPageTitle(OSCOM::getDef('address_book_heading'));
        $application->setPageContent('address_book.php');

        return true;
      }
    }
  }
?>
