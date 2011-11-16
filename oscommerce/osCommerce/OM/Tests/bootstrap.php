<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  define('OSCOM_TIMESTAMP_START', microtime());

  require('Autoloader.php');
  $OSCOM_Autoloader = new Autoloader('osCommerce\OM');
  $OSCOM_Autoloader->register();

  osCommerce\OM\Core\OSCOM::initialize();
?>
