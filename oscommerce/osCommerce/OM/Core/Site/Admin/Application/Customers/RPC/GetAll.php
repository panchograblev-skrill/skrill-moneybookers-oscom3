<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Customers\RPC;

  use osCommerce\OM\Core\DateTime;
  use osCommerce\OM\Core\Site\Admin\Application\Customers\Customers;
  use osCommerce\OM\Core\Site\RPC\Controller as RPC;

/**
 * @since v3.0.2
 */

  class GetAll {
    public static function execute() {
      if ( !isset($_GET['search']) ) {
        $_GET['search'] = '';
      }

      if ( !isset($_GET['page']) || !is_numeric($_GET['page']) ) {
        $_GET['page'] = 1;
      }

      if ( !empty($_GET['search']) ) {
        $result = Customers::find($_GET['search'], $_GET['page']);
      } else {
        $result = Customers::getAll($_GET['page']);
      }

      foreach ( $result['entries'] as &$c ) {
        $c['date_account_created'] = DateTime::getShort($c['date_account_created'], true);
      }

      $result['rpcStatus'] = RPC::STATUS_SUCCESS;

      echo json_encode($result);
    }
  }
?>
