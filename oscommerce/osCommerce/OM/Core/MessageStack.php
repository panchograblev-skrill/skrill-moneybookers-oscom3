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
 * The MessageStack class manages information messages to be displayed.
 * Messages shown are automatically removed from the stack.
 * Core message types: info, success, warning, error
 * 
 * @since v3.0.0
 */

  class MessageStack {

/**
 * The storage handler for the messages
 *
 * @var array
 * @since v3.0.0
 */

    protected $_data = array();

/**
 * Constructor, registers a shutdown function to store the remaining messages
 * in the session
 *
 * @since v3.0.0
 */

    public function __construct() {
      register_shutdown_function(array($this, 'saveInSession'));
    }

/**
 * Loads messages stored in the session into the stack
 *
 * @since v3.0.0
 */

    public function loadFromSession() {
      if ( isset($_SESSION['osC_MessageStack_Data']) && !empty($_SESSION['osC_MessageStack_Data']) ) {
        foreach ( $_SESSION['osC_MessageStack_Data'] as $group => $messages ) {
          foreach ( $messages as $message ) {
            $this->_data[$group][] = $message;
          }
        }

        unset($_SESSION['osC_MessageStack_Data']);
      }
    }

/**
 * Stores remaining messages in the session
 *
 * @since v3.0.0
 */

    public function saveInSession() {
      if ( !empty($this->_data) ) {
        $_SESSION['osC_MessageStack_Data'] = $this->_data;
      }
    }

/**
 * Add a message to the stack
 *
 * @param string $group The group the message belongs to
 * @param string $message The message information text
 * @param string $type The type of message: info, error, warning, success
 * @since v3.0.0
 */

    public function add($group = null, $message, $type = 'error') {
      if ( !isset($group) ) {
        $group = OSCOM::getSiteApplication();
      }

      $stack = array('text' => $message,
                     'type' => $type);

      if ( !$this->exists($group) || !in_array($stack, $this->_data[$group]) ) {
        $this->_data[$group][] = $stack;
      }
    }

/**
 * Reset the message stack
 *
 * @since v3.0.0
 */

    public function reset() {
      $this->_data = array();
    }

/**
 * Checks to see if a group in the stack contains messages
 *
 * @param string $group The name of the group to check
 * @since v3.0.0
 */

    public function exists($group = null) {
      if ( !isset($group) ) {
        $group = OSCOM::getSiteApplication();
      }

      return array_key_exists($group, $this->_data);
    }

/**
 * Checks to see if the message stack contains messages
 *
 * @since v3.0.0
 */

    public function hasContent() {
      return !empty($this->_data);
    }

/**
 * Get the messages belonging to a group. The messages are placed into an
 * unsorted list wrapped in a DIV element with the "messageStack" style sheet
 * class.
 *
 * @param string $group The name of the group to get the messages from
 * @since v3.0.0
 */

    public function get($group = null) {
      if ( !isset($group) ) {
        $group = OSCOM::getSiteApplication();
      }

      $result = false;

      if ( $this->exists($group) ) {
        $result = '<div class="messageStack"><ul>';

        foreach ( $this->_data[$group] as $message ) {
          switch ( $message['type'] ) {
            case 'error':
              $bullet_image = 'error.gif';
              break;

            case 'warning':
              $bullet_image = 'warning.gif';
              break;

            case 'success':
            default:
              $bullet_image = 'success.gif';
              break;
          }

          $result .= '<li style="list-style-image: url(\'' . HTML::iconRaw($bullet_image) . '\')">' . HTML::output($message['text']) . '</li>';
        }

        $result .= '</ul></div>';

        unset($this->_data[$group]);
      }

      return $result;
    }

/**
 * Get the messages belonging to a group. The messages are separated by a new
 * line character.
 *
 * @param string $group The name of the group to get the messages from
 * @since v3.0.0
 */

    public function getRaw($group = null) {
      if ( !isset($group) ) {
        $group = OSCOM::getSiteApplication();
      }

      $result = false;

      if ( $this->exists($group) ) {
        $result = '';

        foreach ( $this->_data[$group] as $message ) {
          $result .= HTML::output($message['text']) . "\n";
        }

        unset($this->_data[$group]);
      }

      return $result;
    }

/**
 * Get the message stack array data set
 *
 * @since v3.0.0
 */

    public function getAll() {
      return $this->_data;
    }

/**
 * Get the number of messages belonging to a group
 *
 * @param string $group The name of the group to check
 * @since v3.0.0
 */

    public function size($group = null) {
      if ( !isset($group) ) {
        $group = OSCOM::getSiteApplication();
      }

      $size = 0;

      if ( $this->exists($group) ) {
        $size = count($this->_data[$group]);
      }

      return $size;
    }
  }
?>
