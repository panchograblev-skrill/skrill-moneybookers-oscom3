<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core;

  use osCommerce\OM\Core\HTML;

/**
 * The osC_ObjectInfo class wraps an object instance around an array data set
 */

  class ObjectInfo {

/**
 * Holds the array data set values
 *
 * @var array
 * @access protected
 */

    protected $_data = array();

/**
 * Constructor, loads the array data set into the object instance
 *
 * @param array $data The array data set to insert into the object instance
 * @access public
 */

    public function __construct($data) {
      $this->_data = $data;
    }

/**
 * Get the value of a key element in the array data set
 *
 * @param string $key The name of the array key
 * @access public
 */

    public function get($key) {
      return $this->_data[$key];
    }

/**
 * Get the value of a key element in the array data set and protect the output value
 *
 * @param string $key The name of the array key
 * @access public
 */

    public function getProtected($key) {
      return HTML::outputProtected($this->_data[$key]);
    }

/**
 * Get the integer value of a key element in the array data set
 *
 * @param string $key The name of the array key
 * @access public
 */

    public function getInt($key) {
      return (int)$this->_data[$key];
    }

/**
 * Get the whole array data set
 *
 * @access public
 */

    public function getAll() {
      return $this->_data;
    }

/**
 * Set a value in the array data set
 *
 * @param string $key The name of the array key
 * @param string $value The value of the array key
 * @access public
 */

    public function set($key, $value) {
      $this->_data[$key] = $value;
    }

/**
 * Checks the existance of a key in the array data set
 *
 * @param string $key The name of the array key
 * @access public
 */

    public function exists($key) {
      return isset($this->_data[$key]);
    }

    public static function to($array) {
      return new static($array);
    }
  }
?>
