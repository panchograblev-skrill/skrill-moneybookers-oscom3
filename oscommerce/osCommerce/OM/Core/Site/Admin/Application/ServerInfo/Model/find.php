<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\ServerInfo\Model;

  use osCommerce\OM\Core\Site\Admin\Application\ServerInfo\ServerInfo;

  class find {
    public static function execute($search) {
      $modules = ServerInfo::getAll();

      $result = array('entries' => array());

      foreach ( $modules['entries'] as $module ) {
        if ( (stripos($module['key'], $search) !== false) || (stripos($module['title'], $search) !== false) || (stripos($module['value'], $search) !== false) ) {
          $result['entries'][] = $module;
        }
      }

      $result['total'] = count($result['entries']);

      return $result;
    }
  }
?>
