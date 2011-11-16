<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\CoreUpdate\Model;

  use osCommerce\OM\Core\DateTime;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Admin\Application\CoreUpdate\CoreUpdate;

/**
 * @since v3.0.2
 */

  class findLog {
    public static function execute($log, $search) {
      $data = CoreUpdate::getLog($log);

      $result = array('entries' => array());

      foreach ( $data['entries'] as $l ) {
        if ( stripos($l['message'], $search) !== false ) {
          $result['entries'][] = $l;
        }
      }

      $result['total'] = count($result['entries']);

      return $result;
    }
  }
?>
