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

  require('includes/applications/product_attributes/classes/product_attributes.php');

  class osC_Application_Product_attributes extends osC_Template_Admin {

/* Protected variables */

    protected $_module = 'product_attributes',
              $_page_title,
              $_page_contents = 'main.php';

/* Class constructor */

    function __construct() {
      global $osC_Language, $osC_MessageStack;

      $this->_page_title = $osC_Language->get('heading_title');

      if (!isset($_GET['action'])) {
        $_GET['action'] = '';
      }

      if ( !empty($_GET['action']) ) {
        switch ( $_GET['action'] ) {
          case 'install':
            $module = basename($_GET['module']);

            if ( file_exists('includes/modules/product_attributes/' . $module . '.php') ) {
              include('includes/modules/product_attributes/' . $module . '.php');

              if ( class_exists('osC_ProductAttributes_' . $module) ) {
                $module = 'osC_ProductAttributes_' . $module;
                $module = new $module();

                if ( $module->install() ) {
                  $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
                } else {
                  $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
                }

                osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
              }
            }

            break;

          case 'uninstall':
            $this->_page_contents = 'uninstall.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              $module = basename($_GET['module']);

              if ( file_exists('includes/modules/product_attributes/' . $module . '.php') ) {
                include('includes/modules/product_attributes/' . $module . '.php');

                if ( class_exists('osC_ProductAttributes_' . $module) ) {
                  $module = 'osC_ProductAttributes_' . $module;
                  $module = new $module();

                  if ( $module->uninstall() ) {
                    $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
                  } else {
                    $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
                  }

                  osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
                }
              }
            }

            break;
        }
      }
    }
  }
?>
