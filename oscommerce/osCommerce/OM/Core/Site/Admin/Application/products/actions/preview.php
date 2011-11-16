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

  class osC_Application_Products_Actions_preview extends osC_Application_Products {
    public function __construct() {
      parent::__construct();

      if ( isset($_GET[$this->_module]) && is_numeric($_GET[$this->_module]) ) {
        $this->_page_contents = 'preview.php';
      }
    }
  }
?>
