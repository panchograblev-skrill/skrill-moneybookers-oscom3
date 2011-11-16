<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop;

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  class Image {
    protected $_groups;

    public function __construct() {
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_Language = Registry::get('Language');

      $this->_groups = array();

      $Qgroups = $OSCOM_PDO->prepare('select * from :table_products_images_groups where language_id = :language_id');
      $Qgroups->bindInt(':language_id', $OSCOM_Language->getID());
      $Qgroups->setCache('images_groups-' . $OSCOM_Language->getID());
      $Qgroups->execute();

      foreach ( $Qgroups->fetchAll() as $group ) {
        $this->_groups[(int)$group['id']] = $group;
      }
    }

    public function getID($code) {
      foreach ( $this->_groups as $group ) {
        if ( $group['code'] == $code ) {
          return $group['id'];
        }
      }

      return 0;
    }

    public function getCode($id) {
      return $this->_groups[$id]['code'];
    }

    public function getWidth($code) {
      return $this->_groups[$this->getID($code)]['size_width'];
    }

    public function getHeight($code) {
      return $this->_groups[$this->getID($code)]['size_height'];
    }

    public function exists($code) {
      return isset($this->_groups[$this->getID($code)]);
    }

    public function show($image, $title, $parameters = null, $group = null) {
      if ( empty($group) || !$this->exists($group) ) {
        $group = $this->getCode(DEFAULT_IMAGE_GROUP_ID);
      }

      $group_id = $this->getID($group);

      $width = $height = '';

      if ( ($this->_groups[$group_id]['force_size'] == '1') || empty($image) ) {
        $width = $this->_groups[$group_id]['size_width'];
        $height = $this->_groups[$group_id]['size_height'];
      }

      if ( empty($image) ) {
        $image = 'pixel_trans.gif';
      } else {
        $image = $this->_groups[$group_id]['code'] . '/' . $image;
      }

      $url = (OSCOM::getRequestType() == 'NONSSL') ? OSCOM::getConfig('product_images_http_server') . OSCOM::getConfig('product_images_dir_ws_http_server') : OSCOM::getConfig('product_images_http_server') . OSCOM::getConfig('product_images_dir_ws_http_server');

      return HTML::image($url . $image, $title, $width, $height, $parameters);
    }

    public function getAddress($image, $group = 'default') {
      $group_id = $this->getID($group);

      $url = (OSCOM::getRequestType() == 'NONSSL') ? OSCOM::getConfig('product_images_http_server') . OSCOM::getConfig('product_images_dir_ws_http_server') : OSCOM::getConfig('product_images_http_server') . OSCOM::getConfig('product_images_dir_ws_http_server');

      return $url . $this->_groups[$group_id]['code'] . '/' . $image;
    }
  }
?>