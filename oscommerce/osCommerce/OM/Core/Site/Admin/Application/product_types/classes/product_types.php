<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2009 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_ProductTypes_Admin {
    public static function get($id, $key = null) {
      global $osC_Database;

      $Qtype = $osC_Database->query('select * from :table_product_types where id = :id');
      $Qtype->bindTable(':table_product_types', TABLE_PRODUCT_TYPES);
      $Qtype->bindInt(':id', $id);
      $Qtype->execute();

      $Qassignments = $osC_Database->query('select count(distinct action) as total_assignments from :table_product_types_assignments where types_id = :types_id');
      $Qassignments->bindTable(':table_product_types_assignments', TABLE_PRODUCT_TYPES_ASSIGNMENTS);
      $Qassignments->bindInt(':types_id', $Qtype->valueInt('id'));
      $Qassignments->execute();

      $Qproducts = $osC_Database->query('select count(*) as total_products from :table_products where products_types_id = :products_types_id');
      $Qproducts->bindTable(':table_products', TABLE_PRODUCTS);
      $Qproducts->bindInt(':products_types_id', $Qtype->valueInt('id'));
      $Qproducts->execute();

      $data = array_merge($Qtype->toArray(), $Qassignments->toArray(), $Qproducts->toArray());

      if ( empty($key) ) {
        return $data;
      } else {
        return $data[$key];
      }
    }

    public static function getAll($pageset = 1) {
      global $osC_Database;

      if ( !is_numeric($pageset) || (floor($pageset) != $pageset) ) {
        $pageset = 1;
      }

      $result = array('entries' => array());

      $Qtypes = $osC_Database->query('select SQL_CALC_FOUND_ROWS * from :table_product_types order by title');
      $Qtypes->bindTable(':table_product_types', TABLE_PRODUCT_TYPES);

      if ( $pageset !== -1 ) {
        $Qtypes->setBatchLimit($pageset, MAX_DISPLAY_SEARCH_RESULTS);
      }

      $Qtypes->execute();

      while ( $Qtypes->next() ) {
        $Qassignments = $osC_Database->query('select count(distinct action) as total_assignments from :table_product_types_assignments where types_id = :types_id');
        $Qassignments->bindTable(':table_product_types_assignments', TABLE_PRODUCT_TYPES_ASSIGNMENTS);
        $Qassignments->bindInt(':types_id', $Qtypes->valueInt('id'));
        $Qassignments->execute();

        $result['entries'][] = array_merge($Qtypes->toArray(), $Qassignments->toArray());
      }

      $result['total'] = $Qtypes->getBatchSize();

      if ( $Qtypes->numberOfRows() > 0 ) {
        $Qassignments->freeResult();
      }

      $Qtypes->freeResult();

      return $result;
    }

    public static function save($id = null, $data) {
      global $osC_Database;

      if ( is_numeric($id) ) {
        $Qtype = $osC_Database->query('update :table_product_types set title = :title where id = :id');
        $Qtype->bindInt(':id', $id);
      } else {
        $Qtype = $osC_Database->query('insert into :table_product_types (title) values (:title)');
      }

      $Qtype->bindTable(':table_product_types', TABLE_PRODUCT_TYPES);
      $Qtype->bindValue(':title', $data['title']);
      $Qtype->setLogging($_SESSION['module'], $id);
      $Qtype->execute();

      if ( !$osC_Database->isError() ) {
        return true;
      }

      return false;
    }

    public static function delete($id) {
      global $osC_Database;

      $Qdelete = $osC_Database->query('delete from :table_product_types where id = :id');
      $Qdelete->bindTable(':table_product_types', TABLE_PRODUCT_TYPES);
      $Qdelete->bindInt(':id', $id);
      $Qdelete->setLogging($_SESSION['module'], $id);
      $Qdelete->execute();

      return !$osC_Database->isError();
    }

    public static function getAssignments($type_id, $action) {
      global $osC_Database;

      if ( !class_exists('osC_ProductTypes_Actions_' . $action) ) {
        include('../includes/modules/product_types/actions/' . $action . '.php');
      }

      $action_title = call_user_func(array('osC_ProductTypes_Actions_' . $action, 'getTitle'));

      $action_modules = array();

      $Qmodules = $osC_Database->query('select module from :table_product_types_assignments where types_id = :types_id and action = :action order by sort_order, module');
      $Qmodules->bindTable(':table_product_types_assignments', TABLE_PRODUCT_TYPES_ASSIGNMENTS);
      $Qmodules->bindInt(':types_id', $type_id);
      $Qmodules->bindValue(':action', $action);
      $Qmodules->execute();

      while ( $Qmodules->next() ) {
        if ( !class_exists('osC_ProductTypes_Modules_' . $Qmodules->value('module')) ) {
          include('../includes/modules/product_types/modules/' . $Qmodules->value('module') . '.php');
        }

        $module_title = call_user_func(array('osC_ProductTypes_Modules_' . $Qmodules->value('module'), 'getTitle'));

        $action_modules[] = array('module' => $Qmodules->value('module'),
                                  'module_title' => $module_title);
      }

      $result = array('types_id' => $type_id,
                      'action' => $action,
                      'action_title' => $action_title,
                      'modules' => $action_modules);

      return $result;
    }

    public static function getAllAssignments($type_id) {
      global $osC_Database;

      $result = array('entries' => array());

      $Qactions = $osC_Database->query('select distinct action from :table_product_types_assignments where types_id = :types_id order by action');
      $Qactions->bindTable(':table_product_types_assignments', TABLE_PRODUCT_TYPES_ASSIGNMENTS);
      $Qactions->bindInt(':types_id', $type_id);
      $Qactions->execute();

      while ( $Qactions->next() ) {
        if ( !class_exists('osC_ProductTypes_Actions_' . $Qactions->value('action')) ) {
          include('../includes/modules/product_types/actions/' . $Qactions->value('action') . '.php');
        }

        $action_title = call_user_func(array('osC_ProductTypes_Actions_' . $Qactions->value('action'), 'getTitle'));

        $action_modules = array();

        $Qmodules = $osC_Database->query('select module from :table_product_types_assignments where types_id = :types_id and action = :action order by sort_order, module');
        $Qmodules->bindTable(':table_product_types_assignments', TABLE_PRODUCT_TYPES_ASSIGNMENTS);
        $Qmodules->bindInt(':types_id', $type_id);
        $Qmodules->bindValue(':action', $Qactions->value('action'));
        $Qmodules->execute();

        while ( $Qmodules->next() ) {
          if ( !class_exists('osC_ProductTypes_Modules_' . $Qmodules->value('module')) ) {
            include('../includes/modules/product_types/modules/' . $Qmodules->value('module') . '.php');
          }

          $module_title = call_user_func(array('osC_ProductTypes_Modules_' . $Qmodules->value('module'), 'getTitle'));

          $action_modules[] = array('module' => $Qmodules->value('module'),
                                    'module_title' => $module_title);
        }

        $result['entries'][] = array('types_id' => $type_id,
                                     'action' => $Qactions->value('action'),
                                     'action_title' => $action_title,
                                     'modules' => $action_modules);
      }

      $result['total'] = $Qactions->numberOfRows();

      $Qactions->freeResult();

      return $result;
    }

    public static function saveAssignments($type_id, $action, $data) {
      global $osC_Database;

      $error = false;

      $osC_Database->startTransaction();

      $Qdel = $osC_Database->query('delete from :table_product_types_assignments where types_id = :types_id and action = :action');
      $Qdel->bindTable(':table_product_types_assignments', TABLE_PRODUCT_TYPES_ASSIGNMENTS);
      $Qdel->bindInt(':types_id', $type_id);
      $Qdel->bindValue(':action', $action);
      $Qdel->setLogging($_SESSION['module'], $type_id);
      $Qdel->execute();

      $counter = 1;

      foreach ( $data['modules'] as $module ) {
        $Qinsert = $osC_Database->query('insert into :table_product_types_assignments (types_id, action, module, sort_order) values (:types_id, :action, :module, :sort_order)');
        $Qinsert->bindTable(':table_product_types_assignments', TABLE_PRODUCT_TYPES_ASSIGNMENTS);
        $Qinsert->bindInt(':types_id', $type_id);
        $Qinsert->bindValue(':action', $action);
        $Qinsert->bindValue(':module', $module);
        $Qinsert->bindInt(':sort_order', $counter);
        $Qinsert->setLogging($_SESSION['module'], $type_id);
        $Qinsert->execute();

        if ( $osC_Database->isError() ) {
          $error = true;
          break;
        }

        $counter++;
      }

      if ( $error === false ) {
        $osC_Database->commitTransaction();

        return true;
      }

      $osC_Database->rollbackTransaction();

      return false;
    }

    public static function deleteAssignments($type_id, $action) {
      global $osC_Database;

      $Qdelete = $osC_Database->query('delete from :table_product_types_assignments where types_id = :types_id and action = :action');
      $Qdelete->bindTable(':table_product_types_assignments', TABLE_PRODUCT_TYPES_ASSIGNMENTS);
      $Qdelete->bindInt(':types_id', $type_id);
      $Qdelete->bindValue(':action', $action);
      $Qdelete->setLogging($_SESSION['module'], $type_id);
      $Qdelete->execute();

      if ( !$osC_Database->isError() ) {
        return true;
      }

      return false;
    }

    public static function getActions($type_id = null) {
      global $osC_Database;

      $filter = array();

      if ( !empty($type_id) ) {
        $Qactions = $osC_Database->query('select distinct action from :table_product_types_assignments where types_id = :types_id order by action');
        $Qactions->bindTable(':table_product_types_assignments', TABLE_PRODUCT_TYPES_ASSIGNMENTS);
        $Qactions->bindInt(':types_id', $type_id);
        $Qactions->execute();

        while ( $Qactions->next() ) {
          $filter[] = $Qactions->value('action');
        }
      }

      $osC_DirectoryListing = new osC_DirectoryListing('../includes/modules/product_types/actions');
      $osC_DirectoryListing->setIncludeDirectories(false);
      $files = $osC_DirectoryListing->getFiles();

      $actions_array = array();

      foreach ( $osC_DirectoryListing->getFiles() as $file ) {
        $class = substr($file['name'], 0, strrpos($file['name'], '.'));

        if ( !in_array($class, $filter) ) {
          if ( !class_exists('osC_ProductTypes_Actions_' . ucfirst($class)) ) {
            include('../includes/modules/product_types/actions/' . $file['name']);
          }

          $module_title = call_user_func(array('osC_ProductTypes_Actions_' . ucfirst($class), 'getTitle'));

          $actions_array[] = array('id' => $class,
                                   'title' => $module_title);
        }
      }

      return $actions_array;
    }

    public static function getModules() {
      $osC_DirectoryListing = new osC_DirectoryListing('../includes/modules/product_types/modules');
      $osC_DirectoryListing->setIncludeDirectories(false);
      $files = $osC_DirectoryListing->getFiles();

      $modules_array = array();

      foreach ( $osC_DirectoryListing->getFiles() as $file ) {
        $class = substr($file['name'], 0, strrpos($file['name'], '.'));

        if ( !class_exists('osC_ProductTypes_Modules_' . ucfirst($class)) ) {
          include('../includes/modules/product_types/modules/' . $file['name']);
        }

        $module_title = call_user_func(array('osC_ProductTypes_Modules_' . ucfirst($class), 'getTitle'));

        $modules_array[] = array('id' => $class,
                                 'title' => $module_title);
      }

      return $modules_array;
    }
  }
?>
