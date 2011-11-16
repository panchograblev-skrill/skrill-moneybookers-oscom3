<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin;

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  abstract class ApplicationAbstract extends \osCommerce\OM\Core\ApplicationAbstract {
    protected $_link_to = true;
    protected $_group;
    protected $_icon = 'default.png';
    protected $_title;
    protected $_sort_order;

    public function __construct($process = true) {
      $OSCOM_Session = Registry::get('Session');

      $this->initialize();

      if ( $process === true ) {
        $this->process();

        $action = null;
        $action_index = 1;

        if ( count($_GET) > 1 ) {
          $requested_action = HTML::sanitize(basename(key(array_slice($_GET, 1, 1, true))));

          if ( $requested_action == OSCOM::getSiteApplication() ) {
            $requested_action = null;

            if ( count($_GET) > 2 ) {
              $requested_action = HTML::sanitize(basename(key(array_slice($_GET, 2, 1, true))));

              $action_index = 2;
            }
          }

          if ( !empty($requested_action) && self::siteApplicationActionExists($requested_action) ) {
            $action = $requested_action;
          }
        }

        if ( isset($action) ) {
          call_user_func(array('osCommerce\\OM\\Core\\Site\\Admin\\Application\\' . OSCOM::getSiteApplication() . '\\Action\\' . $action, 'execute'), $this);

          $action_index++;

          if ( $action_index < count($_GET) ) {
            $action = array($action);

            for ( $i = $action_index, $n = count($_GET); $i < $n; $i++ ) {
              $subaction = HTML::sanitize(basename(key(array_slice($_GET, $i, 1, true))));

              if ( $subaction != $OSCOM_Session->getName() && self::siteApplicationActionExists(implode('\\', $action) . '\\' . $subaction) ) {
                call_user_func(array('osCommerce\\OM\\Core\\Site\\Admin\\Application\\' . OSCOM::getSiteApplication() . '\\Action\\' . implode('\\', $action) . '\\' . $subaction, 'execute'), $this);

                $action[] = $subaction;
              } else {
                break;
              }
            }
          }
        }
      }
    }

    public function siteApplicationActionExists($action) {
      return class_exists('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Application\\' . OSCOM::getSiteApplication() . '\\Action\\' . $action);
    }

    public function canLinkTo() {
      return $this->_link_to;
    }

    public function getGroup() {
      return $this->_group;
    }

    public function getIcon() {
      return $this->_icon;
    }

    public function getTitle() {
      return $this->_title;
    }

    public function getSortOrder() {
      return $this->_sort_order;
    }
  }
?>
