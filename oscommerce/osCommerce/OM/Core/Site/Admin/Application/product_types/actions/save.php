<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2009 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Application_Product_types_Actions_save extends osC_Application_Product_types {
    public function __construct() {
      global $osC_MessageStack;

      parent::__construct();

      if ( isset($_GET['tID']) && is_numeric($_GET['tID']) ) {
        $this->_page_contents = 'edit.php';
      } else {
        $this->_page_contents = 'new.php';
      }

      if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
        $data = array('title' => $_POST['title']);

        if ( osC_ProductTypes_Admin::save((isset($_GET['tID']) && is_numeric($_GET['tID']) ? $_GET['tID'] : null), $data) ) {
          $osC_MessageStack->add($this->_module, OSCOM::getDef('ms_success_action_performed'), 'success');
        } else {
          $osC_MessageStack->add($this->_module, OSCOM::getDef('ms_error_action_not_performed'), 'error');
        }

        osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
      }
    }
  }
?>
