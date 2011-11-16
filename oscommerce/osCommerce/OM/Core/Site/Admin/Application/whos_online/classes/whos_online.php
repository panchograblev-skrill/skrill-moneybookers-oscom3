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

  class osC_WhosOnline_Admin {
    public static function getData($id) {
      global $osC_Database;

      $Qwho = $osC_Database->query('select * from :table_whos_online where session_id = :session_id');
      $Qwho->bindTable(':table_whos_online', TABLE_WHOS_ONLINE);
      $Qwho->bindValue(':session_id', $id);
      $Qwho->execute();

      $data = $Qwho->toArray();

      $Qwho->freeResult();

      return $data;
    }

    public static function delete($id) {
      global $osC_Database;

      OSCOM_Registry::get('Session')->delete($id);

      $Qwho = $osC_Database->query('delete from :table_whos_online where session_id = :session_id');
      $Qwho->bindTable(':table_whos_online', TABLE_WHOS_ONLINE);
      $Qwho->bindValue(':session_id', $id);
      $Qwho->execute();

      if ( !$osC_Database->isError() ) {
        return true;
      }

      return false;
    }
  }
?>
