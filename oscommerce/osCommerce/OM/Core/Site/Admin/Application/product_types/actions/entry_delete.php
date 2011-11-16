<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2009 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Application_Product_types_Actions_entry_delete extends osC_Application_Product_types {
    public function __construct() {
      global $osC_MessageStack;

      parent::__construct();

      $this->_page_contents = 'entries_delete.php';

      if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
        if ( osC_ProductTypes_Admin::deleteAssignments($_GET[$this->_module], $_GET['aID']) ) {
          $osC_MessageStack->add($this->_module, OSCOM::getDef('ms_success_action_performed'), 'success');
        } else {
          $osC_MessageStack->add($this->_module, OSCOM::getDef('ms_error_action_not_performed'), 'error');
        }

        osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '=' . $_GET[$this->_module]));
      }
    }
  }
?>
