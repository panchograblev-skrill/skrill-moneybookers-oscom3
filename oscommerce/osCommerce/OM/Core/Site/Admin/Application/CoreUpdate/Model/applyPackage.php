<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\CoreUpdate\Model;

  use \Phar;
  use \RecursiveIteratorIterator;
  use osCommerce\OM\Core\DateTime;
  use osCommerce\OM\Core\DirectoryListing;
  use osCommerce\OM\Core\OSCOM;

  class applyPackage {
    protected static $_to_version;

    public static function execute() {
      $phar_can_open = true;

      $meta = array();
      $pro_hart = array();

      try {
        $phar = new Phar(OSCOM::BASE_DIRECTORY . 'Work/CoreUpdate/update.phar');

        $meta = $phar->getMetadata();

        self::$_to_version = $meta['version_to'];

// reset the log
        if ( file_exists(OSCOM::BASE_DIRECTORY . 'Work/Logs/update-' . self::$_to_version . '.txt') && is_writable(OSCOM::BASE_DIRECTORY . 'Work/Logs/update-' . self::$_to_version . '.txt') ) {
          unlink(OSCOM::BASE_DIRECTORY . 'Work/Logs/update-' . self::$_to_version . '.txt');
        }

        self::log('##### UPDATE TO ' . self::$_to_version . ' STARTED');

// first delete files before extracting new files
        if ( isset($meta['delete']) ) {
          foreach ( $meta['delete'] as $file ) {
            $directory = (substr($file, 0, 14) == 'osCommerce/OM/' ? realpath(OSCOM::BASE_DIRECTORY . '../../') : realpath(OSCOM::getConfig('dir_fs_public', 'OSCOM') . '../')) . '/';

            if ( file_exists($directory . $file) ) {
              if ( is_dir($directory . $file) ) {
                if ( rename($directory . $file, $directory . dirname($file) . '/.CU_' . basename($file)) ) {
                  $pro_hart[] = array('type' => 'directory',
                                      'where' => $directory,
                                      'path' => dirname($file) . '/.CU_' . basename($file),
                                      'log' => true);
                }
              } else {
                if ( rename($directory . $file, $directory . dirname($file) . '/.CU_' . basename($file)) ) {
                  $pro_hart[] = array('type' => 'file',
                                      'where' => $directory,
                                      'path' => dirname($file) . '/.CU_' . basename($file),
                                      'log' => true);
                }
              }
            }
          }
        }

// loop through each file individually as extractTo() does not work with
// directories (see http://bugs.php.net/bug.php?id=54289)
        foreach ( new RecursiveIteratorIterator($phar) as $iteration ) {
          if ( ($pos = strpos($iteration->getPathName(), 'update.phar')) !== false ) {
            $file = substr($iteration->getPathName(), $pos+12);

            $directory = (substr($file, 0, 14) == 'osCommerce/OM/' ? realpath(OSCOM::BASE_DIRECTORY . '../../') : realpath(OSCOM::getConfig('dir_fs_public', 'OSCOM') . '../')) . '/';

            if ( file_exists($directory . $file) ) {
              if ( rename($directory . $file, $directory . dirname($file) . '/.CU_' . basename($file)) ) {
                $pro_hart[] = array('type' => 'file',
                                    'where' => $directory,
                                    'path' => dirname($file) . '/.CU_' . basename($file),
                                    'log' => false);
              }
            }

            if ( $phar->extractTo($directory, $file, true) ) {
              self::log('Extracted: ' . $file);
            } else {
              self::log('*** Could Not Extract: ' . $file);
            }
          }
        }

        self::log('##### CLEANUP');

        foreach ( array_reverse($pro_hart, true) as $mess ) {
          if ( $mess['type'] == 'directory' ) {
            if ( self::rmdir_r($mess['where'] . $mess['path']) ) {
              if ( $mess['log'] === true ) {
                self::log('Deleted: ' . str_replace('/.CU_', '/', $mess['path']));
              }
            } else {
              if ( $mess['log'] === true ) {
                self::log('*** Could Not Delete: ' . str_replace('/.CU_', '/', $mess['path']));
              }
            }
          } else {
            if ( unlink($mess['where'] . $mess['path']) ) {
              if ( $mess['log'] === true ) {
                self::log('Deleted: ' . str_replace('/.CU_', '/', $mess['path']));
              }
            } else {
              if ( $mess['log'] === true ) {
                self::log('*** Could Not Delete: ' . str_replace('/.CU_', '/', $mess['path']));
              }
            }
          }
        }
      } catch ( \Exception $e ) {
        $phar_can_open = false;

        self::log('##### ERROR: ' . $e->getMessage());

        self::log('##### REVERTING STARTED');

        foreach ( array_reverse($pro_hart, true) as $mess ) {
          if ( $mess['type'] == 'directory' ) {
            if ( file_exists($mess['where'] . str_replace('/.CU_', '/', $mess['path'])) ) {
              self::rmdir_r($mess['where'] . str_replace('/.CU_', '/', $mess['path']));
            }
          } else {
            if ( file_exists($mess['where'] . str_replace('/.CU_', '/', $mess['path'])) ) {
              unlink($mess['where'] . str_replace('/.CU_', '/', $mess['path']));
            }
          }

          if ( file_exists($mess['where'] . $mess['path']) ) {
            rename($mess['where'] . $mess['path'], $mess['where'] . str_replace('/.CU_', '/', $mess['path']));
          }

          self::log('Reverted: ' . str_replace('/.CU_', '/', $mess['path']));
        }

        self::log('##### REVERTING COMPLETE');
        self::log('##### UPDATE TO ' . self::$_to_version . ' FAILED');

        trigger_error($e->getMessage());
        trigger_error('Please review the update log at: ' . OSCOM::BASE_DIRECTORY . 'Work/Logs/update-' . self::$_to_version . '.txt');
      }

      if ( $phar_can_open === true ) {
        if ( isset($meta['run']) && method_exists('osCommerce\\OM\\Work\\CoreUpdate\\' . $meta['run'] . '\\Controller', 'runAfter') ) {
          $results = call_user_func(array('osCommerce\\OM\\Work\\CoreUpdate\\' . $meta['run'] . '\\Controller', 'runAfter'));

          if ( !empty($results) ) {
            self::log('##### RAN AFTER');

            foreach ( $results as $r ) {
              self::log($r);
            }
          }

          self::log('##### CLEANUP');

          if ( self::rmdir_r(OSCOM::BASE_DIRECTORY . 'Work/CoreUpdate/' . $meta['run']) ) {
            self::log('Deleted: osCommerce/OM/Work/CoreUpdate/' . $meta['run']);
          } else {
            self::log('*** Could Not Delete: osCommerce/OM/Work/CoreUpdate/' . $meta['run']);
          }
        }

        self::log('##### UPDATE TO ' . self::$_to_version . ' COMPLETE');
      }

      return $phar_can_open;
    }

    protected static function log($message) {
      if ( is_writable(OSCOM::BASE_DIRECTORY . 'Work/Logs') ) {
        file_put_contents(OSCOM::BASE_DIRECTORY . 'Work/Logs/update-' . self::$_to_version . '.txt', '[' . DateTime::getNow('d-M-Y H:i:s') . '] ' . $message . "\n", FILE_APPEND);
      }
    }

    protected static function rmdir_r($dir) {
      foreach ( scandir($dir) as $file ) {
        if ( !in_array($file, array('.', '..')) ) {
          if ( is_dir($dir . '/' . $file) ) {
            self::rmdir_r($dir . '/' . $file);
          } else {
            unlink($dir . '/' . $file);
          }
        }
      }

      return rmdir($dir);
    }
  }
?>
