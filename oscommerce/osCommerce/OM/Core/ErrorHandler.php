<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core;

  use osCommerce\OM\Core\DateTime;
  use osCommerce\OM\Core\Language;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\PDO;

  class ErrorHandler {
    static protected $_dbh;

    public static function initialize() {
      ini_set('display_errors', false);
      ini_set('html_errors', false);

      if ( is_writable(OSCOM::BASE_DIRECTORY . 'Work/Logs') ) {
        ini_set('log_errors', true);
        ini_set('error_log', OSCOM::BASE_DIRECTORY . 'Work/Logs/errors.txt');
      }

      if ( in_array('sqlite', PDO::getAvailableDrivers()) && is_writable(OSCOM::BASE_DIRECTORY . 'Work/Database/') ) {
        set_error_handler(array('osCommerce\\OM\\Core\\ErrorHandler', 'execute'));

        if ( file_exists(OSCOM::BASE_DIRECTORY . 'Work/Logs/errors.txt') ) {
          self::import(OSCOM::BASE_DIRECTORY . 'Work/Logs/errors.txt');
        }
      }
    }

    public static function execute($errno, $errstr, $errfile, $errline) {
      if ( !is_resource(self::$_dbh) && !self::connect() ) {
        return false;
      }

      switch ($errno) {
        case E_NOTICE:
        case E_USER_NOTICE:
          $errors = "Notice";
          break;
        case E_WARNING:
        case E_USER_WARNING:
          $errors = "Warning";
          break;
        case E_ERROR:
        case E_USER_ERROR:
          $errors = "Fatal Error";
          break;
        default:
          $errors = "Unknown";
          break;
      }

      $errstr = Language::toUTF8($errstr);

      $error_msg = sprintf('PHP %s:  %s in %s on line %d', $errors, $errstr, $errfile, $errline);

      $Qinsert = self::$_dbh->prepare('insert into error_log (timestamp, message) values (:timestamp, :message)');
      $Qinsert->bindInt(':timestamp', time());
      $Qinsert->bindValue(':message', $error_msg);
      $Qinsert->execute();

// return true to stop further processing of internal php error handler
      return true;
    }

    public static function connect() {
      $result = false;

      try {
        self::$_dbh = PDO::initialize(OSCOM::BASE_DIRECTORY . 'Work/Database/errors.sqlite3', null, null, null, null, 'SQLite3');
        self::$_dbh->exec('create table if not exists error_log ( timestamp int, message text );');

        $result = true;
      } catch ( \Exception $e ) {
        trigger_error($e->getMessage());
      }

      return $result;
    }

    public static function getAll($limit = null, $pageset = null) {
      if ( !is_resource(self::$_dbh) && !self::connect() ) {
        return array();
      }

      $query = 'select timestamp, message from error_log order by rowid desc';

      if ( is_numeric($limit) ) {
        $query .= ' limit ' . (int)$limit;

        if ( is_numeric($pageset) ) {
          $offset = max(($pageset * $limit) - $limit, 0);

          $query .= ' offset ' . $offset;
        }
      }

      return self::$_dbh->query($query)->fetchAll();
    }

    public static function getTotalEntries() {
      if ( !is_resource(self::$_dbh) && !self::connect() ) {
        return 0;
      }

      $result = self::$_dbh->query('select count(*) as total from error_log')->fetch();

      return $result['total'];
    }

    public static function find($search, $limit = null, $pageset = null) {
      if ( !is_resource(self::$_dbh) && !self::connect() ) {
        return array();
      }

      $query = 'select timestamp, message from error_log where message like :message order by rowid desc';

      if ( is_numeric($limit) ) {
        $query .= ' limit ' . (int)$limit;

        if ( is_numeric($pageset) ) {
          $offset = max(($pageset * $limit) - $limit, 0);

          $query .= ' offset ' . $offset;
        }
      }

      $Qlogs = self::$_dbh->prepare($query);
      $Qlogs->bindValue(':message', '%' . $search . '%');
      $Qlogs->execute();

      return $Qlogs->fetchAll();
    }

    public static function getTotalFindEntries($search) {
      if ( !is_resource(self::$_dbh) && !self::connect() ) {
        return 0;
      }

      $Qlogs = self::$_dbh->prepare('select count(*) as total from error_log where message like :message');
      $Qlogs->bindValue(':message', '%' . $search . '%');
      $Qlogs->execute();

      $result = $Qlogs->fetch();

      return $result['total'];
    }

    public static function import($filename) {
      if ( !is_resource(self::$_dbh) && !self::connect() ) {
        return false;
      }

      $error_log = file($filename);
      unlink($filename);

      foreach ( $error_log as $error ) {
        $error = Language::toUTF8($error);

        if ( preg_match('/^\[([0-9]{2})-([A-Za-z]{3})-([0-9]{4}) ([0-9]{2}):([0-5][0-9]):([0-5][0-9])\] (.*)$/', $error) ) {
          $timestamp = DateTime::getTimestamp(substr($error, 1, 20), 'd-M-Y H:i:s');
          $message = substr($error, 23);

          $Qinsert = self::$_dbh->prepare('insert into error_log (timestamp, message) values (:timestamp, :message)');
          $Qinsert->bindInt(':timestamp', $timestamp);
          $Qinsert->bindValue(':message', $message);
          $Qinsert->execute();
        }
      }
    }

    public static function clear() {
      if ( !is_resource(self::$_dbh) && !self::connect() ) {
        return false;
      }

      self::$_dbh->exec('drop table if exists error_log');
    }
  }
?>
