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

  class osC_BannerManager_Admin {
    public static function getData($id) {
      global $osC_Database;

      $Qbanner = $osC_Database->query('select * from :table_banners where banners_id = :banners_id');
      $Qbanner->bindTable(':table_banners', TABLE_BANNERS);
      $Qbanner->bindInt(':banners_id', $id);
      $Qbanner->execute();

      $data = $Qbanner->toArray();

      $Qbanner->freeResult();

      return $data;
    }

    public static function save($id = null, $data) {
      global $osC_Database;

      $error = false;

      if ( empty($data['html_text']) && empty($data['image_local']) && !empty($data['image']) ) {
        $image = new upload($data['image'], realpath('../images/' . $data['image_target']));

        if ( !$image->exists() || !$image->parse() || !$image->save() ) {
          $error = true;
        }
      }

      if ( $error === false ) {
        $image_location = (!empty($data['image_local']) ? $data['image_local'] : (isset($image) ? $data['image_target'] . $image->filename : null));

        if ( is_numeric($id) ) {
          $Qbanner = $osC_Database->query('update :table_banners set banners_title = :banners_title, banners_url = :banners_url, banners_image = :banners_image, banners_group = :banners_group, banners_html_text = :banners_html_text, expires_date = :expires_date, expires_impressions = :expires_impressions, date_scheduled = :date_scheduled, status = :status where banners_id = :banners_id');
          $Qbanner->bindInt(':banners_id', $id);
        } else {
          $Qbanner = $osC_Database->query('insert into :table_banners (banners_title, banners_url, banners_image, banners_group, banners_html_text, expires_date, expires_impressions, date_scheduled, status, date_added) values (:banners_title, :banners_url, :banners_image, :banners_group, :banners_html_text, :expires_date, :expires_impressions, :date_scheduled, :status, now())');
        }

        $Qbanner->bindTable(':table_banners', TABLE_BANNERS);
        $Qbanner->bindValue(':banners_title', $data['title']);
        $Qbanner->bindValue(':banners_url', $data['url']);
        $Qbanner->bindValue(':banners_image', $image_location);
        $Qbanner->bindValue(':banners_group', (!empty($data['group_new']) ? $data['group_new'] : $data['group']));
        $Qbanner->bindValue(':banners_html_text', $data['html_text']);

        if ( empty($data['date_expires']) ) {
          $Qbanner->bindRaw(':expires_date', 'null');
          $Qbanner->bindInt(':expires_impressions', $data['expires_impressions']);
        } else {
          $Qbanner->bindValue(':expires_date', $data['date_expires']);
          $Qbanner->bindInt(':expires_impressions', 0);
        }

        if ( empty($data['date_scheduled']) ) {
          $Qbanner->bindRaw(':date_scheduled', 'null');
          $Qbanner->bindInt(':status', (($data['status'] === true) ? 1 : 0));
        } else {
          $Qbanner->bindValue(':date_scheduled', $data['date_scheduled']);
          $Qbanner->bindInt(':status', ($data['date_scheduled'] > date('Y-m-d') ? 0 : (($data['status'] === true) ? 1 : 0)));
        }

        $Qbanner->setLogging($_SESSION['module'], $id);
        $Qbanner->execute();

        if ( !$osC_Database->isError() ) {
          return true;
        }
      }

      return false;
    }

    public static function delete($id, $delete_image = false) {
      global $osC_Database;

      $error = false;

      if ( $delete_image === true ) {
        $Qimage = $osC_Database->query('select banners_image from :table_banners where banners_id = :banners_id');
        $Qimage->bindTable(':table_banners', TABLE_BANNERS);
        $Qimage->bindInt(':banners_id', $id);
        $Qimage->execute();
      }

      $Qdelete = $osC_Database->query('delete from :table_banners where banners_id = :banners_id');
      $Qdelete->bindTable(':table_banners', TABLE_BANNERS);
      $Qdelete->bindInt(':banners_id', $id);
      $Qdelete->setLogging($_SESSION['module'], $id);
      $Qdelete->execute();

      if ( $osC_Database->isError() ) {
        $error = true;
      }

      if ( $error === false) {
        if ( $delete_image === true ) {
          if ( !osc_empty($Qimage->value('banners_image')) ) {
            if ( is_file('../images/' . $Qimage->value('banners_image')) && is_writeable('../images/' . $Qimage->value('banners_image')) ) {
              @unlink('../images/' . $Qimage->value('banners_image'));
            }
          }
        }

        $image_extension = osc_dynamic_image_extension();

        if ( !empty($image_extension) ) {
          if ( is_file('images/graphs/banner_yearly-' . $id . '.' . $image_extension) && is_writeable('images/graphs/banner_yearly-' . $id . '.' . $image_extension) ) {
            @unlink('images/graphs/banner_yearly-' . $id . '.' . $image_extension);
          }

          if ( is_file('images/graphs/banner_monthly-' . $id . '.' . $image_extension) && is_writeable('images/graphs/banner_monthly-' . $id . '.' . $image_extension) ) {
            @unlink('images/graphs/banner_monthly-' . $id . '.' . $image_extension);
          }

          if ( is_file('images/graphs/banner_daily-' . $id . '.' . $image_extension) && is_writeable('images/graphs/banner_daily-' . $id . '.' . $image_extension) ) {
            unlink('images/graphs/banner_daily-' . $id . '.' . $image_extension);
          }
        }

        return true;
      }

      return false;
    }
  }
?>
