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

  class getAll {
    public static function execute($pageset = 1) {
      if ( !is_numeric($pageset) || (floor($pageset) != $pageset) ) {
        $pageset = 1;
      }

      $result = array('entries' => array(),
                      'total' => ErrorHandler::getTotalEntries());

      foreach ( ErrorHandler::getAll(MAX_DISPLAY_SEARCH_RESULTS, $pageset) as $row ) {
        $result['entries'][] = array('date' => DateTime::getShort(DateTime::fromUnixTimestamp($row['timestamp']), true),
                                     'message' => $row['message']);
      }

      return $result;
    }
  }
?>
