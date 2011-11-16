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

  class osC_Application_Cache extends osC_Template_Admin {

/* Protected variables */

    protected $_module = 'cache',
              $_page_title,
              $_page_contents = 'main.php';

/* Class constructor */

    function __construct() {
      global $osC_Language, $osC_MessageStack;

      $this->_page_title = $osC_Language->get('heading_title');

      if ( !isset($_GET['action']) ) {
        $_GET['action'] = '';
      }

// check if the cache directory exists
      if ( is_dir(DIR_FS_WORK) ) {
        if ( !is_writeable(DIR_FS_WORK) ) {
          $osC_MessageStack->add('header', sprintf($osC_Language->get('ms_error_cache_directory_not_writable'), DIR_FS_WORK), 'error');
        }
      } else {
        $osC_MessageStack->add('header', sprintf($osC_Language->get('ms_error_cache_directory_non_existant'), DIR_FS_WORK), 'error');
      }

      if ( !empty($_GET['action']) ) {
        switch ( $_GET['action'] ) {
          case 'delete':
/*HPDL
            if ( osC_Cache::clear($_GET['block']) ) {
              $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
            } else {
              $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
            }
*/

            osC_Cache::clear($_GET['block']);

            osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));

            break;

          case 'batchDelete':
            if ( isset($_POST['batch']) && is_array($_POST['batch']) && !empty($_POST['batch']) ) {
              foreach ($_POST['batch'] as $id) {
                osC_Cache::clear($id);
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
            }

            break;
        }
      }
    }
  }
?>
