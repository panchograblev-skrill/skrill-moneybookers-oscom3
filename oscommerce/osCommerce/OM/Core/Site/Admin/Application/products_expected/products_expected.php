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

  require('includes/applications/products/classes/products.php');

  class osC_Application_Products_expected extends osC_Template_Admin {

/* Protected variables */

    protected $_module = 'products_expected',
              $_page_title,
              $_page_contents = 'main.php';

/* Class constructor */

    function __construct() {
      global $osC_Database, $osC_Language, $osC_MessageStack;

      $this->_page_title = $osC_Language->get('heading_title');

      if ( !isset($_GET['action']) ) {
        $_GET['action'] = '';
      }

      if ( !isset($_GET['page']) || ( isset($_GET['page']) && !is_numeric($_GET['page']) ) ) {
        $_GET['page'] = 1;
      }

      $Qcheck = $osC_Database->query('select pa.* from :table_product_attributes pa, :table_templates_boxes tb where tb.code = :code and tb.modules_group = :modules_group and tb.id = pa.id and unix_timestamp(now()) > unix_timestamp(str_to_date(pa.value, "%Y-%m-%d"))');
      $Qcheck->bindTable(':table_product_attributes', TABLE_PRODUCT_ATTRIBUTES);
      $Qcheck->bindTable(':table_templates_boxes', TABLE_TEMPLATES_BOXES);
      $Qcheck->bindValue(':code', 'date_available');
      $Qcheck->bindValue(':modules_group', 'product_attributes');
      $Qcheck->execute();

      if ($Qcheck->numberOfRows()) {
        $Qdelete = $osC_Database->query('delete from :table_product_attributes where id = :id and products_id = :products_id');
        $Qdelete->bindTable(':table_product_attributes', TABLE_PRODUCT_ATTRIBUTES);
        $Qdelete->bindInt(':id', $Qcheck->valueInt('id'));
        $Qdelete->bindInt(':products_id', $Qcheck->valueInt('products_id'));
        $Qdelete->execute();
      }

      if ( !empty($_GET['action']) ) {
        switch ( $_GET['action'] ) {
          case 'save':
            $this->_page_contents = 'edit.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              $data = array('date_available' => $_POST['products_date_available']);

              if ( osC_Products_Admin::setDateAvailable($_GET['pID'], $data) ) {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
              } else {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
            }

            break;
        }
      }
    }
  }
?>
