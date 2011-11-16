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

  class osC_ProductVariants_Admin {
    public static function getData($id, $language_id = null, $key = null) {
      global $osC_Database, $osC_Language;

      if ( empty($language_id) ) {
        $language_id = $osC_Language->getID();
      }

      $Qgroup = $osC_Database->query('select * from :table_products_variants_groups where id = :id and languages_id = :languages_id');
      $Qgroup->bindTable(':table_products_variants_groups', TABLE_PRODUCTS_VARIANTS_GROUPS);
      $Qgroup->bindInt(':id', $id);
      $Qgroup->bindInt(':languages_id', $language_id);
      $Qgroup->execute();

      $data = $Qgroup->toArray();

      $Qentries = $osC_Database->query('select count(*) as total_entries from :table_products_variants_values where products_variants_groups_id = :products_variants_groups_id');
      $Qentries->bindTable(':table_products_variants_values', TABLE_PRODUCTS_VARIANTS_VALUES);
      $Qentries->bindInt(':products_variants_groups_id', $id);
      $Qentries->execute();

      $data['total_entries'] = $Qentries->valueInt('total_entries');

      $Qproducts = $osC_Database->query('select count(*) as total_products from :table_products_variants pv, :table_products_variants_values pvv where pvv.products_variants_groups_id = :products_variants_groups_id and pvv.id = pv.products_variants_values_id');
      $Qproducts->bindTable(':table_products_variants', TABLE_PRODUCTS_VARIANTS);
      $Qproducts->bindTable(':table_products_variants_values', TABLE_PRODUCTS_VARIANTS_VALUES);
      $Qproducts->bindInt(':products_variants_groups_id', $id);
      $Qproducts->execute();

      $data['total_products'] = $Qproducts->valueInt('total_products');

      if ( empty($key) ) {
        return $data;
      } else {
        return $data[$key];
      }
    }

    public static function save($id = null, $data) {
      global $osC_Database, $osC_Language;

      $error = false;

      if ( is_numeric($id) ) {
        $group_id = $id;
      } else {
        $Qcheck = $osC_Database->query('select max(id) as id from :table_products_variants_groups');
        $Qcheck->bindTable(':table_products_variants_groups', TABLE_PRODUCTS_VARIANTS_GROUPS);
        $Qcheck->execute();

        $group_id = $Qcheck->valueInt('id') + 1;
      }

      $osC_Database->startTransaction();

      foreach ( $osC_Language->getAll() as $l ) {
        if ( is_numeric($id) ) {
          $Qgroup = $osC_Database->query('update :table_products_variants_groups set title = :title, sort_order = :sort_order, module = :module where id = :id and languages_id = :languages_id');
        } else {
          $Qgroup = $osC_Database->query('insert into :table_products_variants_groups (id, languages_id, title, sort_order, module) values (:id, :languages_id, :title, :sort_order, :module)');
        }

        $Qgroup->bindTable(':table_products_variants_groups', TABLE_PRODUCTS_VARIANTS_GROUPS);
        $Qgroup->bindInt(':id', $group_id);
        $Qgroup->bindInt(':languages_id', $l['id']);
        $Qgroup->bindValue(':title', $data['name'][$l['id']]);
        $Qgroup->bindInt(':sort_order', $data['sort_order']);
        $Qgroup->bindValue(':module', $data['module']);
        $Qgroup->setLogging($_SESSION['module'], $group_id);
        $Qgroup->execute();

        if ( $osC_Database->isError() ) {
          $error = true;
          break;
        }
      }

      if ( $error === false ) {
        $osC_Database->commitTransaction();

        return true;
      }

      $osC_Database->rollbackTransaction();

      return false;
    }

    public static function delete($id) {
      global $osC_Database;

      $Qdelete = $osC_Database->query('delete from :table_products_variants_groups where id = :id');
      $Qdelete->bindTable(':table_products_variants_groups', TABLE_PRODUCTS_VARIANTS_GROUPS);
      $Qdelete->bindInt(':id', $id);
      $Qdelete->setLogging($_SESSION['module'], $id);
      $Qdelete->execute();

      return !$osC_Database->isError();
    }

    public static function getEntry($id, $language_id = null) {
      global $osC_Database, $osC_Language;

      if ( empty($language_id) ) {
        $language_id = $osC_Language->getID();
      }

      $Qentry = $osC_Database->query('select * from :table_products_variants_values where id = :id and languages_id = :languages_id');
      $Qentry->bindTable(':table_products_variants_values', TABLE_PRODUCTS_VARIANTS_VALUES);
      $Qentry->bindInt(':id', $id);
      $Qentry->bindInt(':languages_id', $language_id);
      $Qentry->execute();

      $data = $Qentry->toArray();

      $Qproducts = $osC_Database->query('select count(*) as total_products from :table_products_variants where products_variants_values_id = :products_variants_values_id');
      $Qproducts->bindTable(':table_products_variants', TABLE_PRODUCTS_VARIANTS);
      $Qproducts->bindInt(':products_variants_values_id', $Qentry->valueInt('id'));
      $Qproducts->execute();

      $data['total_products'] = $Qproducts->valueInt('total_products');

      $Qproducts->freeResult();
      $Qentry->freeResult();

      return $data;
    }

    public static function saveEntry($id = null, $data) {
      global $osC_Database, $osC_Language;

      $error = false;

      if ( is_numeric($id) ) {
        $entry_id = $id;
      } else {
        $Qcheck = $osC_Database->query('select max(id) as id from :table_products_variants_values');
        $Qcheck->bindTable(':table_products_variants_values', TABLE_PRODUCTS_VARIANTS_VALUES);
        $Qcheck->execute();

        $entry_id = $Qcheck->valueInt('id') + 1;
      }

      $osC_Database->startTransaction();

      foreach ( $osC_Language->getAll() as $l ) {
        if ( is_numeric($id) ) {
          $Qentry = $osC_Database->query('update :table_products_variants_values set title = :title, sort_order = :sort_order where id = :id and languages_id = :languages_id');
        } else {
          $Qentry = $osC_Database->query('insert into :table_products_variants_values (id, languages_id, products_variants_groups_id, title, sort_order) values (:id, :languages_id, :products_variants_groups_id, :title, :sort_order)');
          $Qentry->bindInt(':products_variants_groups_id', $data['group_id']);
        }

        $Qentry->bindTable(':table_products_variants_values', TABLE_PRODUCTS_VARIANTS_VALUES);
        $Qentry->bindInt(':id', $entry_id);
        $Qentry->bindInt(':languages_id', $l['id']);
        $Qentry->bindValue(':title', $data['name'][$l['id']]);
        $Qentry->bindInt(':sort_order', $data['sort_order']);
        $Qentry->setLogging($_SESSION['module'], $entry_id);
        $Qentry->execute();

        if ( $osC_Database->isError() ) {
          $error = true;
          break;
        }
      }

      if ( $error === false ) {
        $osC_Database->commitTransaction();

        return true;
      }

      $osC_Database->rollbackTransaction();

      return false;
    }

    public static function deleteEntry($id, $group_id) {
      global $osC_Database;

      $Qentry = $osC_Database->query('delete from :table_products_variants_values where id = :id and products_variants_groups_id = :products_variants_groups_id');
      $Qentry->bindTable(':table_products_variants_values', TABLE_PRODUCTS_VARIANTS_VALUES);
      $Qentry->bindInt(':id', $id);
      $Qentry->bindInt(':products_variants_groups_id', $group_id);
      $Qentry->setLogging($_SESSION['module'], $id);
      $Qentry->execute();

      if ( !$osC_Database->isError() ) {
        return true;
      }

      return false;
    }
  }
?>
