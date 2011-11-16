<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core;

  use osCommerce\OM\Core\OSCOM;

/**
 * @since v3.0.2
 */

  class Upload {
    protected $_file,
              $_destination,
              $_permissions,
              $_extensions = array(),
              $_replace = false,
              $_upload = array();

    public function __construct($file, $destination, $permissions = null, $extensions = null, $replace = false) {
// Remove trailing directory separator
      if ( substr($destination, -1) == '/' ) {
        $destination = substr($destination, 0, -1);
      }

      if ( !isset($permissions) ) {
        $permissions = '777';
      }

      $this->_file = $file;
      $this->_destination = $destination;

      $this->setPermissions($permissions);

      if ( isset($extensions) ) {
        $this->addExtensions($extensions);
      }

      $this->_replace = $replace;
    }

    public function check() {
      if ( isset($_GET[$this->_file]) ) {
        $temp_filename = 'temp_' . mt_rand();

        while ( file_exists(OSCOM::BASE_DIRECTORY . 'Work/Temp/' . $temp_filename) ) {
          $temp_filename = 'temp_' . mt_rand();
        }

        $input = fopen('php://input', 'r');

        $size = file_put_contents(OSCOM::BASE_DIRECTORY . 'Work/Temp/' . $temp_filename, $input);

        fclose($input);

        if ( isset($_SERVER['CONTENT_LENGTH']) && ($size == $_SERVER['CONTENT_LENGTH']) ) {
          $this->_upload = array('type' => 'PUT',
                                 'name' => $_GET[$this->_file],
                                 'size' => $size,
                                 'temp_filename' => $temp_filename);
        } else {
          trigger_error('File Upload [PUT]: $_SERVER[\'CONTENT_LENGTH\'] (' . (int)$_SERVER['CONTENT_LENGTH'] . ') not set or not equal to stream size (' . (int)$size . ')');
        }
      } elseif ( isset($_FILES[$this->_file]) ) {
        if ( isset($_FILES[$this->_file]['tmp_name']) && !empty($_FILES[$this->_file]['tmp_name']) && is_uploaded_file($_FILES[$this->_file]['tmp_name']) && ($_FILES[$this->_file]['size'] > 0) ) {
          $this->_upload = array('type' => 'POST',
                                 'name' => $_FILES[$this->_file]['name'],
                                 'size' => $_FILES[$this->_file]['size'],
                                 'tmp_name' => $_FILES[$this->_file]['tmp_name']);
        } else {
          trigger_error('File Upload [POST]: Cannot process $_FILES[' . $this->_file . '][\'tmp_name\']');
        }
      }

      if ( !empty($this->_upload) ) {
        if ( !empty($this->_extensions) ) {
          if ( !in_array(strtolower(substr($this->_upload['name'], strrpos($this->_upload['name'], '.')+1)), $this->_extensions) ) {
            trigger_error('File Upload [' . $this->_upload['type'] . ']: ' . $this->_upload['name'] . ' not allowed as ' . implode(', ', $this->_extensions));

            return false;
          }
        }

        if ( !is_dir($this->_destination) ) {
          trigger_error('File Upload [' . $this->_upload['type'] . ']: Destination directory does not exist: ' . $this->_destination);

          return false;
        }

        if ( !is_writable($this->_destination) ) {
          trigger_error('File Upload [' . $this->_upload['type'] . ']: Destination directory is not writeable: ' . $this->_destination);

          return false;
        }

        return true;
      }

      return false;
    }

    public function save() {
      if ( $this->_replace === false ) {
        while ( file_exists($this->_destination . '/' . $this->_upload['name']) ) {
          $this->_upload['name'] = rand(10, 99) . $this->_upload['name'];
        }
      }

      if ( $this->_upload['type'] == 'PUT' ) {
        if ( rename(OSCOM::BASE_DIRECTORY . 'Work/Temp/' . $this->_upload['temp_filename'], $this->_destination . '/' . $this->_upload['name']) ) {
          chmod($this->_destination . '/' . $this->_upload['name'], $this->_permissions);

          return true;
        }
      } elseif ( $this->_upload['type'] == 'POST' ) {
        if ( move_uploaded_file($this->_upload['tmp_name'], $this->_destination . '/' . $this->_upload['name']) ) {
          chmod($this->_destination . '/' . $this->_upload['name'], $this->_permissions);

          return true;
        }
      }

      trigger_error('File Upload [' . $this->_upload['type'] . ']: Cannot save uploaded file to destination');

      return false;
    }

    public function setPermissions($permissions) {
      $this->_permissions = octdec($permissions);
    }

    public function addExtensions($extensions) {
      if ( !is_array($extensions) ) {
        $extensions = array($extensions);
      }

      $extensions = array_map('strtolower', $extensions);

      $this->_extensions = array_merge($this->_extensions, $extensions);
    }

    public function setReplace($bool) {
      $this->_replace = ($bool === true);
    }

    public function getDestination() {
      return $this->_destination;
    }

    public function getFilename() {
      return $this->_upload['name'];
    }

    public function getPermissions() {
      return $this->_permissions;
    }

    public function __destruct() {
      if ( isset($this->_upload['temp_filename']) && file_exists(OSCOM::BASE_DIRECTORY . 'Work/Temp/' . $this->_upload['temp_filename']) ) {
        unlink(OSCOM::BASE_DIRECTORY . 'Work/Temp/' . $this->_upload['temp_filename']);
      }
    }
  }
?>
