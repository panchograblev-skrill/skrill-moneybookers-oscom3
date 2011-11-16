<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\Autoloader;
  use osCommerce\OM\Core\OSCOM;

  define('OSCOM_TIMESTAMP_START', microtime());

  error_reporting(E_ALL | E_STRICT);

  define('OSCOM_PUBLIC_BASE_DIRECTORY', __DIR__ . '/');

  require('osCommerce/OM/Core/Autoloader.php');
  $OSCOM_Autoloader = new Autoloader('osCommerce\OM');
  $OSCOM_Autoloader->register();

  OSCOM::initialize();

  require($OSCOM_Template->getTemplateFile());
?>
