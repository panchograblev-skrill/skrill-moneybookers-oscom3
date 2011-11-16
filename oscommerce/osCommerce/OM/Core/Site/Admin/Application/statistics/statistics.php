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

  class osC_Application_Statistics extends osC_Template_Admin {

/* Protected variables */

    protected $_module = 'statistics',
              $_page_title,
              $_page_contents = 'main.php';

/* Class constructor */

    function __construct() {
      global $osC_Language;

      $this->_page_title = $osC_Language->get('heading_title');

      if ( !isset($_GET['module']) ) {
        $_GET['module'] = '';
      }

      if ( !isset($_GET['page']) || ( isset($_GET['page']) && !is_numeric($_GET['page']) ) ) {
        $_GET['page'] = 1;
      }

      if ( !empty($_GET['module']) && !file_exists('includes/modules/statistics/' . $_GET['module'] . '.php') ) {
        $_GET['module'] = '';
      }

      if ( empty($_GET['module']) ) {
        $this->_page_contents = 'listing.php';
      }
    }
  }
?>
