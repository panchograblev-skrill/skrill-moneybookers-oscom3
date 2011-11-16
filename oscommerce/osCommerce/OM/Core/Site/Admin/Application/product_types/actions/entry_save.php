<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2009 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Application_Product_types_Actions_entry_save extends osC_Application_Product_types {
    public function __construct() {
      global $osC_MessageStack;

      parent::__construct();

      if ( isset($_GET['aID']) && !empty($_GET['aID']) ) {
        $this->_page_contents = 'entries_edit.php';
      } else {
        $this->_page_contents = 'entries_new.php';

        if ( sizeof(osC_ProductTypes_Admin::getActions($_GET[$this->_module])) < 1 ) {
          $osC_MessageStack->add($this->_module, OSCOM::getDef('ms_warning_no_available_actions'), 'warning');

          $this->_page_contents = 'entries.php';
        }
      }

      if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
        $data = array('modules' => explode(',', $_POST['modules']));

        if ( osC_ProductTypes_Admin::saveAssignments($_GET[$this->_module], (isset($_GET['aID']) ? $_GET['aID'] : $_POST['action']), $data) ) {
          $osC_MessageStack->add($this->_module, OSCOM::getDef('ms_success_action_performed'), 'success');
        } else {
          $osC_MessageStack->add($this->_module, OSCOM::getDef('ms_error_action_not_performed'), 'error');
        }

        osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '=' . $_GET[$this->_module]));
      }
    }
  }
?>
