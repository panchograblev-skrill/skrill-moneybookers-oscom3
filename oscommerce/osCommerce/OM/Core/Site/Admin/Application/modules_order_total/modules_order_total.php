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

  class osC_Application_Modules_order_total extends osC_Template_Admin {

/* Protected variables */

    protected $_module = 'modules_order_total',
              $_page_title,
              $_page_contents = 'main.php';

/* Class constructor */

    function __construct() {
      global $osC_Language, $osC_MessageStack;

      $this->_page_title = $osC_Language->get('heading_title');

      if ( !isset($_GET['action']) ) {
        $_GET['action'] = '';
      }

      include('includes/classes/order_total.php');

      if ( !empty($_GET['action']) ) {
        switch ( $_GET['action'] ) {
          case 'save':
            $this->_page_contents = 'edit.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              $data = array('configuration' => $_POST['configuration']);

              if ( $this->_save($data) ) {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
              } else {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
            }

            break;

          case 'install':
            if ( $this->_install($_GET['module']) ) {
              $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
            } else {
              $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
            }

            osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));

            break;

          case 'uninstall':
            $this->_page_contents = 'uninstall.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              if ( $this->_uninstall($_GET['module']) ) {
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

    function _save($data) {
      global $osC_Database;

      $error = false;

      $osC_Database->startTransaction();

      foreach ( $data['configuration'] as $key => $value ) {
        $Qupdate = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
        $Qupdate->bindTable(':table_configuration', TABLE_CONFIGURATION);
        $Qupdate->bindValue(':configuration_value', is_array($data['configuration'][$key]) ? implode(',', $data['configuration'][$key]) : $value);
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

    function _install($key) {
      global $osC_Database, $osC_Language;

      if ( file_exists('includes/modules/order_total/' . $key . '.php') ) {
        $osC_Language->injectDefinitions('modules/order_total/' . $key . '.xml');

        include('includes/modules/order_total/' . $key . '.php');

        $module = 'osC_OrderTotal_' . $key;
        $module = new $module();

        $module->install();

        osC_Cache::clear('modules-order_total');
        osC_Cache::clear('configuration');

        return true;
      }

      return false;
    }

    function _uninstall($key) {
      global $osC_Database, $osC_Language;

      if ( file_exists('includes/modules/order_total/' . $key . '.php') ) {
        $osC_Language->injectDefinitions('modules/order_total/' . $key . '.xml');

        include('includes/modules/order_total/' . $key . '.php');

        $module = 'osC_OrderTotal_' . $key;
        $module = new $module();

        $module->remove();

        osC_Cache::clear('modules-order_total');
        osC_Cache::clear('configuration');

        return true;
      }

      return false;
    }
  }
?>
