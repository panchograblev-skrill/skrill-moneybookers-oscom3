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
  require('includes/classes/category_tree.php');
  require('includes/classes/image.php');
  require('../includes/classes/currencies.php');

  class osC_Products_Admin_rpc {
    public static function getAll() {
      global $_module, $osC_Currencies;

      if ( !isset($_GET['cID']) ) {
        $_GET['cID'] = '0';
      }

      if ( !isset($_GET['search']) ) {
        $_GET['search'] = '';
      }

      if ( !isset($_GET['page']) || !is_numeric($_GET['page']) ) {
        $_GET['page'] = 1;
      }

      $osC_Currencies = new osC_Currencies();

      if ( !empty($_GET['search']) ) {
        $result = osC_Products_Admin::find($_GET['search'], $_GET['cID'], $_GET['page']);
      } else {
        $result = osC_Products_Admin::getAll($_GET['cID'], $_GET['page']);
      }

      $result['rpcStatus'] = RPC_STATUS_SUCCESS;

      echo json_encode($result);
    }

    public static function getImages() {
      global $osC_Database, $_module;

      $osC_Image = new osC_Image_Admin();

      $result = array('entries' => array());

      $Qimages = $osC_Database->query('select id, image, default_flag from :table_products_images where products_id = :products_id order by sort_order');
      $Qimages->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
      $Qimages->bindInt(':products_id', $_GET[$_module]);
      $Qimages->execute();

      while ( $Qimages->next() ) {
        foreach ( $osC_Image->getGroups() as $group ) {
          $pass = true;

          if ( isset($_GET['filter']) && (($_GET['filter'] == 'originals') && ($group['id'] != '1')) ) {
            $pass = false;
          } elseif ( isset($_GET['filter']) && (($_GET['filter'] == 'others') && ($group['id'] == '1')) ) {
            $pass = false;
          }

          if ( $pass === true ) {
            $result['entries'][] = array($Qimages->valueInt('id'),
                                         $group['id'],
                                         $Qimages->value('image'),
                                         $group['code'],
                                         osc_href_link($osC_Image->getAddress($Qimages->value('image'), $group['code']), null, 'NONSSL', false, false, true),
                                         number_format(@filesize(DIR_FS_CATALOG . DIR_WS_IMAGES . 'products/' . $group['code'] . '/' . $Qimages->value('image'))),
                                         $Qimages->valueInt('default_flag'));
          }
        }
      }

      $result['rpcStatus'] = RPC_STATUS_SUCCESS;

      echo json_encode($result);
    }

    public static function getLocalImages() {
      $osC_DirectoryListing = new osC_DirectoryListing('../images/products/_upload', true);
      $osC_DirectoryListing->setCheckExtension('gif');
      $osC_DirectoryListing->setCheckExtension('jpg');
      $osC_DirectoryListing->setCheckExtension('png');
      $osC_DirectoryListing->setIncludeDirectories(false);

      $result = array('entries' => array());

      foreach ( $osC_DirectoryListing->getFiles() as $file ) {
        $result['entries'][] = $file['name'];
      }

      $result['rpcStatus'] = RPC_STATUS_SUCCESS;

      echo json_encode($result);
    }

    public static function assignLocalImages() {
      global $osC_Database, $_module;

      $osC_Image = new osC_Image_Admin();

      if ( is_numeric($_GET[$_module]) && isset($_GET['files']) ) {
        $default_flag = 1;

        $Qcheck = $osC_Database->query('select id from :table_products_images where products_id = :products_id and default_flag = :default_flag limit 1');
        $Qcheck->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
        $Qcheck->bindInt(':products_id', $_GET[$_module]);
        $Qcheck->bindInt(':default_flag', 1);
        $Qcheck->execute();

        if ( $Qcheck->numberOfRows() === 1 ) {
          $default_flag = 0;
        }

        foreach ( $_GET['files'] as $file ) {
          $file = basename($file);

          if ( file_exists('../images/products/_upload/' . $file) ) {
            copy('../images/products/_upload/' . $file, '../images/products/originals/' . $file);
            @unlink('../images/products/_upload/' . $file);

            if ( is_numeric($_GET[$_module]) ) {
              $Qimage = $osC_Database->query('insert into :table_products_images (products_id, image, default_flag, sort_order, date_added) values (:products_id, :image, :default_flag, :sort_order, :date_added)');
              $Qimage->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
              $Qimage->bindInt(':products_id', $_GET[$_module]);
              $Qimage->bindValue(':image', $file);
              $Qimage->bindInt(':default_flag', $default_flag);
              $Qimage->bindInt(':sort_order', 0);
              $Qimage->bindRaw(':date_added', 'now()');
              $Qimage->setLogging($_SESSION['module'], $_GET[$_module]);
              $Qimage->execute();

              foreach ( $osC_Image->getGroups() as $group ) {
                if ( $group['id'] != '1' ) {
                  $osC_Image->resize($file, $group['id']);
                }
              }
            }
          }
        }
      }

      $result = array('result' => 1,
                      'rpcStatus' => RPC_STATUS_SUCCESS);

      echo json_encode($result);
    }

    public static function setDefaultImage() {
      $osC_Image = new osC_Image_Admin();

      if ( isset($_GET['image']) ) {
        $osC_Image->setAsDefault($_GET['image']);
      }

      $result = array('result' => 1,
                      'rpcStatus' => RPC_STATUS_SUCCESS);

      echo json_encode($result);
    }

    public static function deleteProductImage() {
      $osC_Image = new osC_Image_Admin();

      if ( isset($_GET['image']) ) {
        $osC_Image->delete($_GET['image']);
      }

      $result = array('result' => 1,
                      'rpcStatus' => RPC_STATUS_SUCCESS);

      echo json_encode($result);
    }

    public static function reorderImages() {
      $osC_Image = new osC_Image_Admin();

      if ( isset($_GET['image']) ) {
        $osC_Image->reorderImages($_GET['image']);
      }

      $result = array('result' => 1,
                      'rpcStatus' => RPC_STATUS_SUCCESS);

      echo json_encode($result);
    }

    public static function fileUpload() {
      global $osC_Database, $_module;

      $osC_Image = new osC_Image_Admin();

      if ( is_numeric($_GET[$_module]) ) {
        $products_image = new upload('products_image');
        $products_image->set_extensions(array('gif', 'jpg', 'jpeg', 'png'));

        if ( $products_image->exists() ) {
          $products_image->set_destination(realpath('../images/products/originals'));

          if ( $products_image->parse() && $products_image->save() ) {
            $default_flag = 1;

            $Qcheck = $osC_Database->query('select id from :table_products_images where products_id = :products_id and default_flag = :default_flag limit 1');
            $Qcheck->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
            $Qcheck->bindInt(':products_id', $_GET[$_module]);
            $Qcheck->bindInt(':default_flag', 1);
            $Qcheck->execute();

            if ( $Qcheck->numberOfRows() === 1 ) {
              $default_flag = 0;
            }

            $Qimage = $osC_Database->query('insert into :table_products_images (products_id, image, default_flag, sort_order, date_added) values (:products_id, :image, :default_flag, :sort_order, :date_added)');
            $Qimage->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
            $Qimage->bindInt(':products_id', $_GET[$_module]);
            $Qimage->bindValue(':image', $products_image->filename);
            $Qimage->bindInt(':default_flag', $default_flag);
            $Qimage->bindInt(':sort_order', 0);
            $Qimage->bindRaw(':date_added', 'now()');
            $Qimage->setLogging($_SESSION['module'], $_GET[$_module]);
            $Qimage->execute();

            foreach ( $osC_Image->getGroups() as $group ) {
              if ( $group['id'] != '1' ) {
                $osC_Image->resize($products_image->filename, $group['id']);
              }
            }
          }
        }
      }

      $result = array('result' => 1,
                      'rpcStatus' => RPC_STATUS_SUCCESS);

      echo json_encode($result);
    }
  }
?>
