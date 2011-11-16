<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\ErrorLog\Model;

  use osCommerce\OM\Core\ErrorHandler;
  use osCommerce\OM\Core\DateTime;

  class getShortcutNotification {
    public static function execute($datetime) {
      $errors = ErrorHandler::getAll(100);

      $from_timestamp = DateTime::getTimestamp($datetime, 'Y-m-d H:i:s');

      $result = 0;

      foreach ( $errors as $error ) {
        if ( $error['timestamp'] > $from_timestamp ) {
          $result++;
        }
      }

      return $result;
    }
  }
?>
