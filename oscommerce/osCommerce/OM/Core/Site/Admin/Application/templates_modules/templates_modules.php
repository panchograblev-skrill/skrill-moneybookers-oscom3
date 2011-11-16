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

  class osC_Application_Templates_modules extends osC_Template_Admin {

/* Protected variables */

    protected $_module = 'templates_modules',
              $_page_title,
              $_page_contents = 'main.php';

/* Class constructor */

    function __construct() {
      global $osC_Language, $osC_MessageStack;

      if ( !isset($_GET['set']) ) {
        $_GET['set'] = '';
      }

      if ( !isset($_GET['action']) ) {
        $_GET['action'] = '';
      }

      switch ( $_GET['set'] ) {
        case 'content':
          $this->_page_title = $osC_Language->get('heading_title_content');

          break;

        case 'boxes':
        default:
          $_GET['set'] = 'boxes';
          $this->_page_title = $osC_Language->get('heading_title_boxes');

          break;
      }

      include('../includes/classes/modules.php');

      $osC_Language->load('modules-' . $_GET['set']);

      if ( !empty($_GET['action']) ) {
        switch ( $_GET['action'] ) {
          case 'info':
            $this->_page_contents = 'info.php';

            break;

          case 'save':
            $this->_page_contents = 'edit.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              $data = array('configuration' => $_POST['configuration']);

              if ( $this->_save($data) ) {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
              } else {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&set=' . $_GET['set']));
            }

            break;

          case 'install':
            if ( $this->_install($_GET['module'], $_GET['set']) ) {
              $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
            } else {
              $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
            }

            osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&set=' . $_GET['set']));

            break;

          case 'uninstall':
            $this->_page_contents = 'uninstall.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              if ( $this->_uninstall($_GET['module'], $_GET['set']) ) {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
              } else {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&set=' . $_GET['set']));
            }

            break;
        }
      }
    }

/* Private methods */

    function _save($data) {
      global $osC_Database;

      $error = false;

      $osC_Database->startTransaction();

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

      if ( $error === false ) {
        $osC_Database->commitTransaction();

        osC_Cache::clear('configuration');

        return true;
      }

      $osC_Database->rollbackTransaction();

      return false;
    }

    function _install($module_name, $set) {
      global $osC_Language;

      if ( file_exists('../includes/modules/' . $set . '/' . $module_name . '.php') ) {
        include('../includes/modules/' . $set . '/' . $module_name . '.php');

        $osC_Language->injectDefinitions('modules/' . $set . '/' . $module_name . '.xml');

        $class = 'osC_' . ucfirst($set) . '_' . $module_name;

        $module = new $class();
        $module->install();

        osC_Cache::clear('configuration');
        osC_Cache::clear('modules_' . $set);
        osC_Cache::clear('templates_' . $set . '_layout');

        return true;
      }

      return false;
    }

    function _uninstall($module_name, $set) {
      global $osC_Language;

      if ( file_exists('../includes/modules/' . $set . '/' . $module_name . '.php') ) {
        include('../includes/modules/' . $set . '/' . $module_name . '.php');

        $osC_Language->injectDefinitions('modules/' . $set . '/' . $module_name . '.xml');

        $class = 'osC_' . ucfirst($set) . '_' . $module_name;

        $module = new $class();
        $module->remove();

        osC_Cache::clear('configuration');
        osC_Cache::clear('modules_' . $set);
        osC_Cache::clear('templates_' . $set . '_layout');

        return true;
      }

      return false;
    }
  }
?>
