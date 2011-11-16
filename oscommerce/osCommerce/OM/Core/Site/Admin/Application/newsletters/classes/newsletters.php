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

  class osC_Newsletters_Admin {
    public static function getData($id) {
      global $osC_Database;

      $Qnewsletter = $osC_Database->query('select * from :table_newsletters where newsletters_id = :newsletters_id');
      $Qnewsletter->bindTable(':table_newsletters', TABLE_NEWSLETTERS);
      $Qnewsletter->bindInt(':newsletters_id', $id);
      $Qnewsletter->execute();

      $data = $Qnewsletter->toArray();

      $Qnewsletter->freeResult();

      return $data;
    }

    public static function save($id = null, $data) {
      global $osC_Database;

      if ( is_numeric($id) ) {
        $Qemail = $osC_Database->query('update :table_newsletters set title = :title, content = :content, module = :module where newsletters_id = :newsletters_id');
        $Qemail->bindInt(':newsletters_id', $id);
      } else {
        $Qemail = $osC_Database->query('insert into :table_newsletters (title, content, module, date_added, status) values (:title, :content, :module, now(), 0)');
      }

      $Qemail->bindTable(':table_newsletters', TABLE_NEWSLETTERS);
      $Qemail->bindValue(':title', $data['title']);
      $Qemail->bindValue(':content', $data['content']);
      $Qemail->bindValue(':module', $data['module']);
      $Qemail->setLogging($_SESSION['module'], $id);
      $Qemail->execute();

      if ( !$osC_Database->isError() ) {
        return true;
      }

      return false;
    }

    public static function delete($id) {
      global $osC_Database;

      $Qdelete = $osC_Database->query('delete from :table_newsletters where newsletters_id = :newsletters_id');
      $Qdelete->bindTable(':table_newsletters', TABLE_NEWSLETTERS);
      $Qdelete->bindInt(':newsletters_id', $id);
      $Qdelete->setLogging($_SESSION['module'], $id);
      $Qdelete->execute();

      return !$osC_Database->isError();
    }
  }
?>
