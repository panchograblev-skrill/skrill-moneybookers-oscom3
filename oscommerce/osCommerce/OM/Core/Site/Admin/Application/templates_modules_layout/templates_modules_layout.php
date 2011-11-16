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

  class osC_Application_Templates_modules_layout extends osC_Template_Admin {

/* Protected variables */

    protected $_module = 'templates_modules_layout',
              $_page_title,
              $_page_contents = 'main.php';

/* Class constructor */

    function __construct() {
      global $osC_Language, $osC_MessageStack;

      if ( !isset($_GET['set']) ) {
        $_GET['set'] = '';
      }

      if ( !isset($_GET['filter']) ) {
        $_GET['filter'] = DEFAULT_TEMPLATE;
      }

      if ( !isset($_GET['action']) ) {
        $_GET['action'] = '';
      }

      switch ( $_GET['set'] ) {
        case 'content':
          $this->_page_title = $osC_Language->get('heading_title_content');

          break;

        case 'boxes':
        default:
          $_GET['set'] = 'boxes';
          $this->_page_title = $osC_Language->get('heading_title_boxes');

          break;
      }

      if ( !empty($_GET['action']) ) {
        switch ( $_GET['action'] ) {
          case 'save':
            if ( isset($_GET['lID']) && is_numeric($_GET['lID']) ) {
              $this->_page_contents = 'edit.php';
            } else {
              $this->_page_contents = 'new.php';
            }

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              $data = array('box' => $_POST['box'],
                            'content_page' => $_POST['content_page'],
                            'page_specific' => (isset($_POST['page_specific']) && ($_POST['page_specific'] == 'on') ? true : false),
                            'group' => (isset($_POST['group']) && !empty($_POST['group']) ? $_POST['group'] : $_POST['group_new']),
                            'sort_order' => $_POST['sort_order']);

              if ( $this->_save((isset($_GET['lID']) && is_numeric($_GET['lID']) ? $_GET['lID'] : null), $data, $_GET['set']) ) {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
              } else {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&set=' . $_GET['set'] . '&filter=' . $_GET['filter']));
            }

            break;

          case 'delete':
            $this->_page_contents = 'delete.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              if ( $this->_delete($_GET['lID'], $_GET['set']) ) {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
              } else {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&set=' . $_GET['set'] . '&filter=' . $_GET['filter']));
            }

            break;

          case 'batchDelete':
            if ( isset($_POST['batch']) && is_array($_POST['batch']) && !empty($_POST['batch']) ) {
              $this->_page_contents = 'batch_delete.php';

              if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
                $error = false;

                foreach ($_POST['batch'] as $id) {
                  if ( !$this->_delete($id, $_GET['set']) ) {
                    $error = true;
                    break;
                  }
                }

                if ( $error === false ) {
                  $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
                } else {
                  $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
                }

                osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&set=' . $_GET['set'] . '&filter=' . $_GET['filter']));
              }
            }

            break;
        }
      }
    }

/* Private methods */

    function _save($id = null, $data, $set) {
      global $osC_Database;

      $link = explode('/', $data['content_page'], 2);

      if ( is_numeric($id) ) {
        $Qlayout = $osC_Database->query('update :table_templates_boxes_to_pages set content_page = :content_page, boxes_group = :boxes_group, sort_order = :sort_order, page_specific = :page_specific where id = :id');
        $Qlayout->bindInt(':id', $id);
      } else {
        $Qlayout = $osC_Database->query('insert into :table_templates_boxes_to_pages (templates_boxes_id, templates_id, content_page, boxes_group, sort_order, page_specific) values (:templates_boxes_id, :templates_id, :content_page, :boxes_group, :sort_order, :page_specific)');
        $Qlayout->bindInt(':templates_boxes_id', $data['box']);
        $Qlayout->bindInt(':templates_id', $link[0]);
      }

      $Qlayout->bindTable(':table_templates_boxes_to_pages', TABLE_TEMPLATES_BOXES_TO_PAGES);
      $Qlayout->bindValue(':content_page', $link[1]);
      $Qlayout->bindValue(':boxes_group', $data['group']);
      $Qlayout->bindInt(':sort_order', $data['sort_order']);
      $Qlayout->bindInt(':page_specific', ($data['page_specific'] === true) ? '1' : '0');
      $Qlayout->setLogging($_SESSION['module'], $id);
      $Qlayout->execute();

      if ( !$osC_Database->isError() ) {
        osC_Cache::clear('templates_' . $set . '_layout');

        return true;
      }

      return false;
    }

    function _delete($id, $set) {
      global $osC_Database;

      $Qdel = $osC_Database->query('delete from :table_templates_boxes_to_pages where id = :id');
      $Qdel->bindTable(':table_templates_boxes_to_pages', TABLE_TEMPLATES_BOXES_TO_PAGES);
      $Qdel->bindInt(':id', $id);
      $Qdel->setLogging($_SESSION['module'], $id);
      $Qdel->execute();

      if ( !$osC_Database->isError() ) {
        osC_Cache::clear('templates_' . $set . '_layout');

        return true;
      }

      return false;
    }
  }
?>
