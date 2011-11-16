<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Session;

  use osCommerce\OM\Core\OSCOM;

/**
 * The Session\File class stores the session data in files
 * 
 * @since v3.0.0
 */

  class File extends \osCommerce\OM\Core\SessionAbstract {

/**
 * Holds the file system path where sessions are saved.
 *
 * @var string
 * @since v3.0.0
 */

    protected $_save_path;

/**
 * Initialize file based session storage handler
 *
 * @param string $name The name of the session
 * @since v3.0.0
 */

    public function __construct($name) {
      $this->setName($name);
      $this->setSavePath(OSCOM::BASE_DIRECTORY . 'Work/Session');
    }

/**
 * Checks if a session exists
 *
 * @param string $id The ID of the session
 * @since v3.0.2
 */

    public function exists($id) {
      return file_exists($this->_save_path . '/' . $id);
    }

/**
 * Deletes an existing session
 *
 * @since v3.0.0
 */

    public function destroy() {
      $this->delete();

      parent::destroy();
    }

/**
 * Deletes an existing session from the storage handler
 *
 * @param string $id The ID of the session
 * @since v3.0.0
 */

    public function delete($id = null) {
      if ( empty($id) ) {
        $id = $this->_id;
      }

      if ( $this->exists($id) ) {
        unlink($this->_save_path . '/' . $id);
      }
    }

/**
 * Return the session file based storage location
 *
 * @return string
 * @since v3.0.0
 */

    public function getSavePath() {
      return $this->_save_path;
    }

/**
 * Sets the storage location for the file based storage handler
 *
 * @param string $path The file path to store the session data in
 * @since v3.0.0
 */

    public function setSavePath($path) {
      if ( substr($path, -1) == '/' ) {
        $path = substr($path, 0, -1);
      }

      session_save_path($path);

      $this->_save_path = session_save_path();
    }
  }
?>
