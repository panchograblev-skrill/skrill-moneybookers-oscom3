<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core;

/**
 * The osC_Access manages the permission levels of administrators who have access to the Administration Tool
 */

  class Access {

/**
 * Holds the group code for the current access module
 *
 * @var string
 * @access protected
 */

    protected $_group = 'misc';

/**
 * Holds the icon for the current access module
 *
 * @var string
 * @access protected
 */

    protected $_icon = 'default.png';

/**
 * Holds the title of the current access module
 *
 * @var string
 * @access protected
 */

    protected $_title;

/**
 * Holds the sort ordering number for the current access module
 *
 * @var int
 * @access protected
 */

    protected $_sort_order = 0;

/**
 * Return the Administration Tool Application modules the administrator has access to
 *
 * @param int $id The ID of the administrator
 * @access public
 * @return array
 */

    public static function getUserLevels($id, $site = null) {
      if ( empty($site) ) {
        $site = OSCOM::getSite();
      }

      $data = array('id' => $id);

      $applications = array();

      foreach ( OSCOM::callDB('GetAccessUserLevels', $data, 'Core') as $am ) {
        $applications[] = $am['module'];
      }

      if ( in_array('*', $applications) ) {
        $applications = array();

        $DLapps = new DirectoryListing(OSCOM::BASE_DIRECTORY . 'Core/Site/' . $site . '/Application');
        $DLapps->setIncludeFiles(false);

        foreach ( $DLapps->getFiles() as $file ) {
          if ( preg_match('/[A-Z]/', substr($file['name'], 0, 1)) && !in_array($file['name'], call_user_func(array('osCommerce\\OM\\Core\\Site\\' . $site . '\\Controller', 'getGuestApplications'))) && file_exists($DLapps->getDirectory() . '/' . $file['name'] . '/Controller.php') ) { // HPDL remove preg_match
            $applications[] = $file['name'];
          }
        }

        $DLcapps = new DirectoryListing(OSCOM::BASE_DIRECTORY . 'Custom/Site/' . $site . '/Application');
        $DLcapps->setIncludeFiles(false);

        foreach ( $DLcapps->getFiles() as $file ) {
          if ( !in_array($file['name'], $applications) && !in_array($file['name'], call_user_func(array('osCommerce\\OM\\Core\\Site\\' . $site . '\\Controller', 'getGuestApplications'))) && file_exists($DLcapps->getDirectory() . '/' . $file['name'] . '/Controller.php') ) {
            $applications[] = $file['name'];
          }
        }
      }

      $shortcuts = array();

      foreach ( OSCOM::callDB('GetAccessUserShortcuts', $data, 'Core') as $as ) {
        $shortcuts[] = $as['module'];
      }

      $levels = array();

      foreach ( $applications as $app ) {
        $application_class = 'osCommerce\\OM\\Core\\Site\\' . $site . '\\Application\\' . $app . '\\Controller';

        if ( class_exists($application_class) ) {
          if ( Registry::exists('Application') && ($app == OSCOM::getSiteApplication()) ) {
            $OSCOM_Application = Registry::get('Application');
          } else {
            Registry::get('Language')->loadIniFile($app . '.php');
            $OSCOM_Application = new $application_class(false);
          }

          $levels[$app] = array('module' => $app,
                                'icon' => $OSCOM_Application->getIcon(),
                                'title' => $OSCOM_Application->getTitle(),
                                'group' => $OSCOM_Application->getGroup(),
                                'linkable' => $OSCOM_Application->canLinkTo(),
                                'shortcut' => in_array($app, $shortcuts),
                                'sort_order' => $OSCOM_Application->getSortOrder());
        }
      }

      return $levels;
    }

    public static function getShortcuts($site = null) {
      if ( empty($site) ) {
        $site = OSCOM::getSite();
      }

      $shortcuts = array();

      if ( isset($_SESSION[$site]['id']) ) {
        foreach ( $_SESSION[$site]['access'] as $module => $data ) {
          if ( $data['shortcut'] === true ) {
            $shortcuts[$module] = $data;
          }
        }

        ksort($shortcuts);
      }

      return $shortcuts;
    }

    public static function hasShortcut($site = null) {
      if ( empty($site) ) {
        $site = OSCOM::getSite();
      }

      if ( isset($_SESSION[$site]['id']) ) {
        foreach ( $_SESSION[$site]['access'] as $module => $data ) {
          if ( $data['shortcut'] === true ) {
            return true;
          }
        }
      }

      return false;
    }

    public static function isShortcut($application, $site = null) {
      if ( empty($site) ) {
        $site = OSCOM::getSite();
      }

      if ( isset($_SESSION[$site]['id']) ) {
        return $_SESSION[$site]['access'][$application]['shortcut'];
      }

      return false;
    }

    public static function getLevels($group = null) {
      $access = array();

      if ( isset($_SESSION['Admin']['id']) && isset($_SESSION['Admin']['access']) ) {
        foreach ( $_SESSION['Admin']['access'] as $module => $data ) {
          if ( ($data['linkable'] === true) && (empty($group) || ($group == $data['group'])) ) {
            if ( !isset($access[$data['group']][$data['sort_order']]) ) {
              $access[$data['group']][$data['sort_order']] = $data;
            } else {
              $access[$data['group']][] = $data;
            }
          }
        }

        ksort($access);

        foreach ( $access as $group => $modules ) {
          ksort($access[$group]);
        }
      }

      return $access;
    }

    function getModule() {
      return $this->_module;
    }

    public static function getGroup($module = null) {
      if ( empty($module) && isset($this) ) { // HPDL to remove
        return $this->_group;
      }

      foreach ( self::getLevels() as $group => $links ) {
        foreach ( $links as $link ) {
          if ( $link['module'] == $module ) {
            return $group;
          }
        }
      }

      return false;
    }

    public static function getGroupTitle($group) {
      $OSCOM_Language = Registry::get('Language');

      if ( !$OSCOM_Language->isDefined('access_group_' . $group . '_title') ) {
        $OSCOM_Language->loadIniFile( 'modules/access/groups/' . $group . '.php' );
      }

      return $OSCOM_Language->get('access_group_' . $group . '_title');
    }

    function getIcon() {
      return $this->_icon;
    }

    function getTitle() {
      return $this->_title;
    }

    function getSortOrder() {
      return $this->_sort_order;
    }

    public static function hasAccess($site, $application) {
      return in_array($application, call_user_func(array('osCommerce\\OM\\Core\\Site\\' . $site . '\\Controller', 'getGuestApplications'))) || isset($_SESSION[$site]['access'][$application]);
    }
  }
?>
