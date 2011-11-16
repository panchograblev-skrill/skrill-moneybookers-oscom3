<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core;

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;

  class PDO extends \PDO {
    protected $_connected = false;
    protected $_server;
    protected $_username;
    protected $_password;
    protected $_database;
    protected $_port;
    protected $_driver;
    protected $_driver_options = array();
    protected $_driver_parent;

    public static function initialize($server = null, $username = null, $password = null, $database = null, $port = null, $driver = null, $driver_options = array()) {
      if ( !isset($server) ) {
        $server = OSCOM::getConfig('db_server');
      }

      if ( !isset($username) && OSCOM::configExists('db_server_username')) {
        $username = OSCOM::getConfig('db_server_username');
      }

      if ( !isset($password) && OSCOM::configExists('db_server_password')) {
        $password = OSCOM::getConfig('db_server_password');
      }

      if ( !isset($database) && OSCOM::configExists('db_database')) {
        $database = OSCOM::getConfig('db_database');
      }

      if ( !isset($port) && OSCOM::configExists('db_server_port')) {
        $port = OSCOM::getConfig('db_server_port');
      }

      if ( !isset($driver) && OSCOM::configExists('db_driver')) {
        $driver = OSCOM::getConfig('db_driver');
      }

      if ( !isset($driver_options[PDO::ATTR_ERRMODE]) ) {
        $driver_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_WARNING;
      }

      if ( !isset($driver_options[PDO::ATTR_DEFAULT_FETCH_MODE]) ) {
        $driver_options[PDO::ATTR_DEFAULT_FETCH_MODE] = PDO::FETCH_ASSOC;
      }

      if ( !isset($driver_options[PDO::ATTR_STATEMENT_CLASS]) ) {
        $driver_options[PDO::ATTR_STATEMENT_CLASS] = array('osCommerce\\OM\\Core\\PDOStatement');
      }

      $class = 'osCommerce\\OM\\Core\\PDO\\' . $driver;
      $object = new $class($server, $username, $password, $database, $port, $driver_options);

      $object->_driver = $driver;

      return $object;
    }

    public function exec($statement) {
      $statement = $this->_autoPrefixTables($statement);

      return parent::exec($statement);
    }

    public function prepare($statement, $driver_options = array()) {
      $statement = $this->_autoPrefixTables($statement);

      return parent::prepare($statement, $driver_options);
    }

    public function query($statement) {
      $statement = $this->_autoPrefixTables($statement);

      $args = func_get_args();

      if ( count($args) > 1 ) {
        return call_user_func_array(array($this, 'parent::query'), $args);
      } else {
        return parent::query($statement);
      }
    }

    public function getBatchFrom($pageset, $max_results) {
      return max(($pageset * $max_results) - $max_results, 0);
    }

    public function getDriver() {
      return $this->_driver;
    }

    public function getDriverParent() {
      return $this->_driver_parent;
    }

    public function hasDriverParent() {
      return isset($this->_driver_parent);
    }

    public function importSQL($sql_file, $table_prefix = null) {
      if ( file_exists($sql_file) ) {
        $import_queries = file_get_contents($sql_file);
      } else {
        trigger_error(sprintf(ERROR_SQL_FILE_NONEXISTENT, $sql_file));

        return false;
      }

      set_time_limit(0);

      $sql_queries = array();
      $sql_length = strlen($import_queries);
      $pos = strpos($import_queries, ';');

      for ( $i=$pos; $i<$sql_length; $i++ ) {
// remove comments
        if ( $import_queries[0] == '#' ) {
          $import_queries = ltrim(substr($import_queries, strpos($import_queries, "\n")));
          $sql_length = strlen($import_queries);
          $i = strpos($import_queries, ';')-1;
          continue;
        }

        if ( $import_queries[($i+1)] == "\n" ) {
          $next = '';

          for ( $j=($i+2); $j<$sql_length; $j++ ) {
            if ( !empty($import_queries[$j]) ) {
              $next = substr($import_queries, $j, 6);

              if ( $next[0] == '#' ) {
// find out where the break position is so we can remove this line (#comment line)
                for ( $k=$j; $k<$sql_length; $k++ ) {
                  if ( $import_queries[$k] == "\n" ) {
                    break;
                  }
                }

                $query = substr($import_queries, 0, $i+1);

                $import_queries = substr($import_queries, $k);

// join the query before the comment appeared, with the rest of the dump
                $import_queries = $query . $import_queries;
                $sql_length = strlen($import_queries);
                $i = strpos($import_queries, ';')-1;
                continue 2;
              }

              break;
            }
          }

          if ( empty($next) ) { // get the last insert query
            $next = 'insert';
          }

          if ( (strtoupper($next) == 'DROP T') || (strtoupper($next) == 'CREATE') || (strtoupper($next) == 'INSERT') || (strtoupper($next) == 'ALTER ') || (strtoupper($next) == 'SET FO') ) {
            $next = '';

            $sql_query = substr($import_queries, 0, $i);

            if ( isset($table_prefix) ) {
              if ( strtoupper(substr($sql_query, 0, 25)) == 'DROP TABLE IF EXISTS OSC_' ) {
                $sql_query = 'DROP TABLE IF EXISTS ' . $table_prefix . substr($sql_query, 25);
              } elseif ( strtoupper(substr($sql_query, 0, 17)) == 'CREATE TABLE OSC_' ) {
                $sql_query = 'CREATE TABLE ' . $table_prefix . substr($sql_query, 17);
              } elseif ( strtoupper(substr($sql_query, 0, 16)) == 'INSERT INTO OSC_' ) {
                $sql_query = 'INSERT INTO ' . $table_prefix . substr($sql_query, 16);
              }
            }

            $sql_queries[] = trim($sql_query);

            $import_queries = ltrim(substr($import_queries, $i+1));
            $sql_length = strlen($import_queries);
            $i = strpos($import_queries, ';')-1;
          }
        }
      }

      $error = false;

      foreach ( $sql_queries as $q ) {
        if ( $this->exec($q) === false ) {
          $error = true;

          break;
        }
      }

      return !$error;
    }

    public static function getBatchTotalPages($text, $pageset_number = 1, $total) {
      $pageset_number = (is_numeric($pageset_number) ? $pageset_number : 1);

      if ( $total < 1 ) {
        $from = 0;
      } else {
        $from = max(($pageset_number * MAX_DISPLAY_SEARCH_RESULTS) - MAX_DISPLAY_SEARCH_RESULTS, 1);
      }

      $to = min($pageset_number * MAX_DISPLAY_SEARCH_RESULTS, $total);

      return sprintf($text, $from, $to, $total);
    }

    public static function getBatchPageLinks($batch_keyword = 'page', $total, $parameters = '', $with_pull_down_menu = true) {
      $batch_number = (isset($_GET[$batch_keyword]) && is_numeric($_GET[$batch_keyword]) ? $_GET[$batch_keyword] : 1);
      $number_of_pages = ceil($total / MAX_DISPLAY_SEARCH_RESULTS);

      if ( $number_of_pages > 1 ) {
        $string = static::getBatchPreviousPageLink($batch_keyword, $parameters);

        if ( $with_pull_down_menu === true ) {
          $string .= static::getBatchPagesPullDownMenu($batch_keyword, $total, $parameters);
        }

        $string .= static::getBatchNextPageLink($batch_keyword, $total, $parameters);
      } else {
        $string = sprintf(OSCOM::getDef('result_set_current_page'), 1, 1);
      }

      return $string;
    }

    public static function getBatchPagesPullDownMenu($batch_keyword = 'page', $total, $parameters = null) {
      $batch_number = (isset($_GET[$batch_keyword]) && is_numeric($_GET[$batch_keyword]) ? $_GET[$batch_keyword] : 1);
      $number_of_pages = ceil($total / MAX_DISPLAY_SEARCH_RESULTS);

      $pages_array = array();

      for ( $i = 1; $i <= $number_of_pages; $i++ ) {
        $pages_array[] = array('id' => $i,
                               'text' => $i);
      }

      $hidden_parameter = '';

      if ( !empty($parameters) ) {
        $parameters = explode('&', $parameters);

        foreach ( $parameters as $parameter ) {
          $keys = explode('=', $parameter, 2);

          if ( $keys[0] != $batch_keyword ) {
            $hidden_parameter .= HTML::hiddenField($keys[0], (isset($keys[1]) ? $keys[1] : ''));
          }
        }
      }

      $string = '<form action="' . OSCOM::getLink(null, null) . '" action="get">' . $hidden_parameter .
                sprintf(OSCOM::getDef('result_set_current_page'), HTML::selectMenu($batch_keyword, $pages_array, $batch_number, 'onchange="this.form.submit();"'), $number_of_pages) .
                HTML::hiddenSessionIDField() . '</form>';

      return $string;
    }

    public static function getBatchPreviousPageLink($batch_keyword = 'page', $parameters = null) {
      $batch_number = (isset($_GET[$batch_keyword]) && is_numeric($_GET[$batch_keyword]) ? $_GET[$batch_keyword] : 1);

      if ( !empty($parameters) ) {
        $parameters .= '&';
      }

      $back_string = HTML::icon('nav_back.png', OSCOM::getDef('result_set_previous_page'));
      $back_grey_string = HTML::icon('nav_back_grey.png', OSCOM::getDef('result_set_previous_page'));

      if ( $batch_number > 1 ) {
        $string = HTML::link(OSCOM::getLink(null, null, $parameters . $batch_keyword . '=' . ($batch_number - 1)), $back_string);
      } else {
        $string = $back_grey_string;
      }

      $string .= '&nbsp;';

      return $string;
    }

    public static function getBatchNextPageLink($batch_keyword = 'page', $total, $parameters = null) {
      $batch_number = (isset($_GET[$batch_keyword]) && is_numeric($_GET[$batch_keyword]) ? $_GET[$batch_keyword] : 1);
      $number_of_pages = ceil($total / MAX_DISPLAY_SEARCH_RESULTS);

      if ( !empty($parameters) ) {
        $parameters .= '&';
      }

      $forward_string = HTML::icon('nav_forward.png', OSCOM::getDef('result_set_next_page'));
      $forward_grey_string = HTML::icon('nav_forward_grey.png', OSCOM::getDef('result_set_next_page'));

      $string = '&nbsp;';

      if ( ( $batch_number < $number_of_pages ) && ( $number_of_pages != 1 ) ) {
        $string .= HTML::link(OSCOM::getLink(null, null, $parameters . $batch_keyword . '=' . ($batch_number + 1)), $forward_string);
      } else {
        $string .= $forward_grey_string;
      }

      return $string;
    }

    protected function _autoPrefixTables($statement) {
      if ( OSCOM::configExists('db_table_prefix') ) {
        $statement = str_replace(':table_', OSCOM::getConfig('db_table_prefix'), $statement);
      }

      return $statement;
    }
  }
?>
