<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\PDO\Microsoft;

  class SqlServer extends \osCommerce\OM\Core\PDO {
    public function __construct($server, $username, $password, $database, $port, $driver_options) {
      $this->_server = $server;
      $this->_username = $username;
      $this->_password = $password;
      $this->_database = $database;
      $this->_port = $port;
      $this->_driver_options = $driver_options;

      if ( $this->_connected === false ) {
        $this->connect();
      }
    }

    public function connect() {
      $dsn = 'sqlsrv:Server=' . $this->_server;

      if ( !empty($this->_port) ) {
        $dsn .= ', ' . $this->_port;
      }

      $dsn .= '; Database=' . $this->_database;

      parent::__construct($dsn, $this->_username, $this->_password, $this->_driver_options);

      $this->_connected = true;
    }
  }
?>
