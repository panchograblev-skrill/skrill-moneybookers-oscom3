<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2009 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  require('includes/applications/product_types/classes/product_types.php');

  class osC_Application_Product_types extends osC_Template_Admin {
    protected $_module = 'product_types';
    protected $_page_title;
    protected $_page_contents = 'main.php';

    public function __construct() {
      $this->_page_title = OSCOM::getDef('heading_title');

      if ( !empty($_GET[$this->_module]) && is_numeric($_GET[$this->_module]) ) {
        $this->_page_contents = 'entries.php';
        $this->_page_title .= ': ' . osC_ProductTypes_Admin::get($_GET[$this->_module], 'title');
      }
    }
  }
?>
