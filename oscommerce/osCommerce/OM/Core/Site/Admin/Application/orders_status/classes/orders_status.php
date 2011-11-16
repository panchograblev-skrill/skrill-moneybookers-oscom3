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

  class osC_OrdersStatus_Admin {
    public static function getData($id) {
      global $osC_Database, $osC_Language;

      $Qstatus = $osC_Database->query('select * from :table_orders_status where orders_status_id = :orders_status_id and language_id = :language_id');
      $Qstatus->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
      $Qstatus->bindInt(':orders_status_id', $id);
      $Qstatus->bindInt(':language_id', $osC_Language->getID());
      $Qstatus->execute();

      $data = $Qstatus->toArray();

      $Qstatus->freeResult();

      return $data;
    }

    public static function save($id = null, $data, $default = false) {
      global $osC_Database, $osC_Language;

      $error = false;

      $osC_Database->startTransaction();

      if ( is_numeric($id) ) {
        $orders_status_id = $id;
      } else {
        $Qstatus = $osC_Database->query('select max(orders_status_id) as orders_status_id from :table_orders_status');
        $Qstatus->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
        $Qstatus->execute();

        $orders_status_id = $Qstatus->valueInt('orders_status_id') + 1;
      }

      foreach ( $osC_Language->getAll() as $l ) {
        if ( is_numeric($id) ) {
          $Qstatus = $osC_Database->query('update :table_orders_status set orders_status_name = :orders_status_name where orders_status_id = :orders_status_id and language_id = :language_id');
        } else {
          $Qstatus = $osC_Database->query('insert into :table_orders_status (orders_status_id, language_id, orders_status_name) values (:orders_status_id, :language_id, :orders_status_name)');
        }

        $Qstatus->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
        $Qstatus->bindInt(':orders_status_id', $orders_status_id);
        $Qstatus->bindValue(':orders_status_name', $data['name'][$l['id']]);
        $Qstatus->bindInt(':language_id', $l['id']);
        $Qstatus->setLogging($_SESSION['module'], $orders_status_id);
        $Qstatus->execute();

        if ( $osC_Database->isError() ) {
          $error = true;
          break;
        }
      }

      if ( $error === false ) {
        if ( $default === true ) {
          $Qupdate = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
          $Qupdate->bindTable(':table_configuration', TABLE_CONFIGURATION);
          $Qupdate->bindInt(':configuration_value', $orders_status_id);
          $Qupdate->bindValue(':configuration_key', 'DEFAULT_ORDERS_STATUS_ID');
          $Qupdate->setLogging($_SESSION['module'], $orders_status_id);
          $Qupdate->execute();

          if ( $osC_Database->isError() ) {
            $error = true;
          }
        }
      }

      if ( $error === false ) {
        $osC_Database->commitTransaction();

        if ( $default === true ) {
          osC_Cache::clear('configuration');
        }

        return true;
      }

      $osC_Database->rollbackTransaction();

      return false;
    }

    public static function delete($id) {
      global $osC_Database;

      $Qstatus = $osC_Database->query('delete from :table_orders_status where orders_status_id = :orders_status_id');
      $Qstatus->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
      $Qstatus->bindInt(':orders_status_id', $id);
      $Qstatus->setLogging($_SESSION['module'], $id);
      $Qstatus->execute();

      if ( !$osC_Database->isError() ) {
        return true;
      }

      return false;
    }
  }
?>
