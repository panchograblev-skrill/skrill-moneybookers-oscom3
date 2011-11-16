<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Orders_Admin {
    public static function delete($id, $restock = false) {
      global $osC_Database;

      $error = false;

      $osC_Database->startTransaction();

      if ($restock === true) {
        $Qproducts = $osC_Database->query('select products_id, products_quantity from :table_orders_products where orders_id = :orders_id');
        $Qproducts->bindTable(':table_orders_products', TABLE_ORDERS_PRODUCTS);
        $Qproducts->bindInt(':orders_id', $id);
        $Qproducts->execute();

        while ($Qproducts->next()) {
          $Qupdate = $osC_Database->query('update :table_products set products_quantity = products_quantity + :products_quantity, products_ordered = products_ordered - :products_ordered where products_id = :products_id');
          $Qupdate->bindTable(':table_products', TABLE_PRODUCTS);
          $Qupdate->bindInt(':products_quantity', $Qproducts->valueInt('products_quantity'));
          $Qupdate->bindInt(':products_ordered', $Qproducts->valueInt('products_quantity'));
          $Qupdate->bindInt(':products_id', $Qproducts->valueInt('products_id'));
          $Qupdate->setLogging($_SESSION['module'], $id);
          $Qupdate->execute();

          if ($osC_Database->isError() === true) {
            $error = true;
            break;
          }

          $Qcheck = $osC_Database->query('select products_quantity from :table_products where products_id = :products_id and products_Status = 0');
          $Qcheck->bindTable(':table_products', TABLE_PRODUCTS);
          $Qcheck->bindInt(':products_id', $Qproducts->valueInt('products_id'));
          $Qcheck->execute();

          if (($Qcheck->numberOfRows() === 1) && ($Qcheck->valueInt('products_quantity') > 0)) {
            $Qstatus = $osC_Database->query('update :table_products set products_status = 1 where products_id = :products_id');
            $Qstatus->bindTable(':table_products', TABLE_PRODUCTS);
            $Qstatus->bindInt(':products_id', $Qproducts->valueInt('products_id'));
            $Qstatus->setLogging($_SESSION['module'], $id);
            $Qstatus->execute();

            if ($osC_Database->isError() === true) {
              $error = true;
              break;
            }
          }
        }
      }

      if ($error === false) {
        $Qo = $osC_Database->query('delete from :table_orders where orders_id = :orders_id');
        $Qo->bindTable(':table_orders', TABLE_ORDERS);
        $Qo->bindInt(':orders_id', $id);
        $Qo->setLogging($_SESSION['module'], $id);
        $Qo->execute();

        if ($osC_Database->isError() === true) {
          $error = true;
        }
      }

      if ($error === false) {
        $osC_Database->commitTransaction();

        return true;
      } else {
        $osC_Database->rollbackTransaction();

        return false;
      }
    }
  }
?>
