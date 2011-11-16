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
 * The Session class manages the session data and custom storage handlers
 * 
 * @since v3.0.0
 */

  abstract class SessionAbstract {

/**
 * Holds the session cookie parameters (lifetime, path, domain, secure, httponly)
 *
 * @var array
 * @since v3.0.0
 */

    protected $_cookie_parameters = array();

/**
 * Defines if the session has been started or not
 *
 * @var boolean
 * @since v3.0.0
 */

    protected $_is_started = false;

/**
 * Holds the name of the session
 *
 * @var string
 * @since v3.0.0
 */

    protected $_name = 'sid';

/**
 * Holds the session id
 *
 * @var string
 * @since v3.0.0
 */

    protected $_id = null;

/**
 * Holds the life time in seconds of the session
 *
 * @var string
 * @since v3.0.0
 */

    protected $_life_time;

/**
 * Verify an existing session ID and create or resume the session if the existing session ID is valid
 *
 * @return boolean
 * @since v3.0.0
 */

    public function start() {
      if ( $this->_life_time > 0 ) {
        ini_set('session.gc_maxlifetime', $this->_life_time);
      } else {
        $this->_life_time = ini_get('session.gc_maxlifetime');
      }

      session_set_cookie_params(0, ((OSCOM::getRequestType() == 'NONSSL') ? OSCOM::getConfig('http_cookie_path') : OSCOM::getConfig('https_cookie_path')), ((OSCOM::getRequestType() == 'NONSSL') ? OSCOM::getConfig('http_cookie_domain') : OSCOM::getConfig('https_cookie_domain')));

      if ( isset($_GET[$this->_name]) && (empty($_GET[$this->_name]) || !ctype_alnum($_GET[$this->_name]) || !$this->exists($_GET[$this->_name])) ) {
        unset($_GET[$this->_name]);
      }

      if ( isset($_POST[$this->_name]) && (empty($_POST[$this->_name]) || !ctype_alnum($_POST[$this->_name]) || !$this->exists($_POST[$this->_name])) ) {
        unset($_POST[$this->_name]);
      }

      if ( isset($_COOKIE[$this->_name]) && (empty($_COOKIE[$this->_name]) || !ctype_alnum($_COOKIE[$this->_name]) || !$this->exists($_COOKIE[$this->_name])) ) {
        setcookie($this->_name, '', time()-42000, $this->getCookieParameters('path'), $this->getCookieParameters('domain'));
      }

      if ( session_start() ) {
        register_shutdown_function(array($this, 'close'));

        $this->_is_started = true;
        $this->_id = session_id();

        return true;
      }

      return false;
    }

/**
 * Checks if the session has been started or not
 *
 * @return boolean
 * @since v3.0.0
 */

    public function hasStarted() {
      return $this->_is_started;
    }

/**
 * Closes the session and writes the session data to the storage handler
 *
 * @since v3.0.0
 */

    public function close() {
      if ( $this->_is_started === true ) {
        $this->_is_started = false;

        return session_write_close();
      }
    }

/**
 * Deletes an existing session
 *
 * @since v3.0.0
 */

    public function destroy() {
      if ( $this->_is_started === true ) {
        if ( isset($_COOKIE[$this->_name]) ) {
          setcookie($this->_name, '', time()-42000, $this->getCookieParameters('path'), $this->getCookieParameters('domain'));
        }

        return session_destroy();
      }
    }

/**
 * Delete an existing session and move the session data to a new session with a new session ID
 *
 * @since v3.0.0
 */

    public function recreate() {
      if ( $this->_is_started === true ) {
        return session_regenerate_id(true);
      }
    }

/**
 * Return the session ID
 *
 * @return string
 * @since v3.0.0
 */

    public function getID() {
      return $this->_id;
    }

/**
 * Return the name of the session
 *
 * @return string
 * @since v3.0.0
 */

    public function getName() {
      return $this->_name;
    }

/**
 * Sets the name of the session
 *
 * @param string $name The name of the session
 * @since v3.0.0
 */

    public function setName($name) {
      if ( empty($name) ) {
        $name = $this->_name;
      }

      session_name($name);

      $this->_name = session_name();
    }

/**
 * Sets the life time of the session (in seconds)
 *
 * @param int $time The life time of the session (in seconds)
 * @since v3.0.0
 */

    public function setLifeTime($time) {
      $this->_life_time = $time;
    }

/**
 * Returns the cookie parameters for the session (lifetime, path, domain, secure, httponly)
 *
 * @param string $key If specified, return only the value of this cookie parameter setting
 * @since v3.0.0
 */

    public function getCookieParameters($key = null) {
      if ( empty($this->_cookie_parameters) ) {
        $this->_cookie_parameters = session_get_cookie_params();
      }

      if ( !empty($key) ) {
        return $this->_cookie_parameters[$key];
      }

      return $this->_cookie_parameters;
    }
  }
?>
