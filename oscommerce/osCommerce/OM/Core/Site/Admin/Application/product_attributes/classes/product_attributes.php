<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  abstract class osC_ProductAttributes_Admin {
    protected $_title;

    abstract public function setFunction($value);

    public function __construct() {
      global $osC_Language;

      $osC_Language->loadIniFile('modules/product_attributes/' . $this->getCode() . '.php');

      $this->_title = $osC_Language->get('product_attributes_' . $this->getCode() . '_title');
    }

    public function getID() {
      global $osC_Database;

      $Qmodule = $osC_Database->query('select id from :table_templates_boxes where code = :code and modules_group = :modules_group');
      $Qmodule->bindTable(':table_templates_boxes', TABLE_TEMPLATES_BOXES);
      $Qmodule->bindValue(':code', $this->getCode());
      $Qmodule->bindValue(':modules_group', 'product_attributes');
      $Qmodule->execute();

      return ( $Qmodule->numberOfRows() === 1 ) ? $Qmodule->valueInt('id') : 0;
    }

    public function getCode() {
      return substr(get_class($this), 22);
    }

    public function getTitle() {
      return $this->_title;
    }

    public function isInstalled() {
      return ($this->getID() > 0);
    }

    public function install() {
      global $osC_Database;

      $Qinstall = $osC_Database->query('insert into :table_templates_boxes (title, code, author_name, author_www, modules_group) values (:title, :code, :author_name, :author_www, :modules_group)');
      $Qinstall->bindTable(':table_templates_boxes', TABLE_TEMPLATES_BOXES);
      $Qinstall->bindValue(':title', $this->getTitle());
      $Qinstall->bindValue(':code', $this->getCode());
      $Qinstall->bindValue(':author_name', '');
      $Qinstall->bindValue(':author_www', '');
      $Qinstall->bindValue(':modules_group', 'product_attributes');
      $Qinstall->execute();

      return ( $osC_Database->isError() === false );
    }

    public function uninstall() {
      global $osC_Database;

      $error = false;

      $osC_Database->startTransaction();

      $Qdelete = $osC_Database->query('delete from :table_product_attributes where id = :id');
      $Qdelete->bindTable(':table_product_attributes', TABLE_PRODUCT_ATTRIBUTES);
      $Qdelete->bindInt(':id', $this->getID());
      $Qdelete->execute();

      if ( $osC_Database->isError() ) {
        $error = true;
      }

      if ( $error === false ) {
        $Quninstall = $osC_Database->query('delete from :table_templates_boxes where code = :code and modules_group = :modules_group');
        $Quninstall->bindTable(':table_templates_boxes', TABLE_TEMPLATES_BOXES);
        $Quninstall->bindValue(':code', $this->getCode());
        $Quninstall->bindValue(':modules_group', 'product_attributes');
        $Quninstall->execute();

        if ( $osC_Database->isError() ) {
          $error = true;
        }
      }

      if ( $error === false ) {
        $osC_Database->commitTransaction();
      } else {
        $osC_Database->rollbackTransaction();
      }

      return ( $error === false );
    }
  }
?>
