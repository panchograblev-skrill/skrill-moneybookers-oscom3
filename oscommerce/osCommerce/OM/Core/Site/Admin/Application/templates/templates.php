<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Application_Templates extends osC_Template_Admin {

/* Protected variables */

    protected $_module = 'templates',
              $_page_title,
              $_page_contents = 'main.php';

/* Class constructor */

    function __construct() {
      global $osC_Language, $osC_MessageStack;

      $this->_page_title = $osC_Language->get('heading_title');

      if ( !isset($_GET['action']) ) {
        $_GET['action'] = '';
      }

      if ( !empty($_GET['action']) ) {
        switch ($_GET['action']) {
          case 'info':
            $this->_page_contents = 'info.php';

            break;

          case 'save':
            $this->_page_contents = 'edit.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              $data = array('configuration' => $_POST['configuration']);

              if ( $this->_save($_GET['template'], $data, ( isset($_POST['default']) && ( $_POST['default'] == 'on' ) ? true : false )) ) {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
              } else {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
            }

            break;

          case 'install':
            if ( $this->_install($_GET['template']) ) {
              $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
            } else {
              $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
            }

            osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));

            break;

          case 'uninstall':
            $this->_page_contents = 'uninstall.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              if ( $this->_uninstall($_GET['template']) ) {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
              } else {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
            }

            break;
        }
      }
    }

/* Private methods */

    function _save($module_name, $data, $default = false) {
      global $osC_Database;

      $error = false;

      $osC_Database->startTransaction();

      if ( !empty($data['configuration']) ) {
        if ( $default === true ) {
          $data['configuration']['DEFAULT_TEMPLATE'] = $module_name;
        }

        foreach ( $data['configuration'] as $key => $value ) {
          $Qupdate = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
          $Qupdate->bindTable(':table_configuration', TABLE_CONFIGURATION);
          $Qupdate->bindValue(':configuration_value', $value);
          $Qupdate->bindValue(':configuration_key', $key);
          $Qupdate->setLogging($_SESSION['module']);
          $Qupdate->execute();

          if ( $osC_Database->isError() ) {
            $error = true;
            break;
          }
        }
      } elseif ( $default === true ) {
        $Qupdate = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
        $Qupdate->bindTable(':table_configuration', TABLE_CONFIGURATION);
        $Qupdate->bindValue(':configuration_value', $module_name);
        $Qupdate->bindValue(':configuration_key', 'DEFAULT_TEMPLATE');
        $Qupdate->setLogging($_SESSION['module']);
        $Qupdate->execute();

        if ( $osC_Database->isError() ) {
          $error = true;
        }
      }

      if ( $error === false ) {
        $osC_Database->commitTransaction();

        osC_Cache::clear('configuration');

        return true;
      }

      $osC_Database->rollbackTransaction();

      return false;
    }

    function _install($module_name) {
      if ( file_exists('includes/templates/' . $module_name . '.php') ) {
        include('includes/templates/' . $module_name . '.php');

        $class = 'osC_Template_' . $module_name;

        $module = new $class();
        $module->install();

        osC_Cache::clear('configuration');
        osC_Cache::clear('templates');

        return true;
      }

      return false;
    }

    function _uninstall($module_name) {
      if ( file_exists('includes/templates/' . $module_name . '.php') ) {
        include('includes/templates/' . $module_name . '.php');

        $class = 'osC_Template_' . $module_name;

        $module = new $class();
        $module->remove();

        osC_Cache::clear('configuration');
        osC_Cache::clear('templates');

        return true;
      }

      return false;
    }
  }
?>
