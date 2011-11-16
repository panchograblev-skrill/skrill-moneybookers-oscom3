<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Categories\RPC;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Upload;

/**
 * @since v3.0.2
 */

  class SaveUploadedImage {
    public static function execute() {
      $error = true;

      $image = new Upload('qqfile', OSCOM::getConfig('dir_fs_public', 'OSCOM') . 'upload', null, array('gif', 'jpg', 'png'));

      if ( $image->check() && $image->save() ) {
        $error = false;
      }

      if ( $error === false ) {
        $result = array('success' => true, 'filename' => $image->getFilename());
      } else {
        $result = array('error' => 'Error');
      }

      echo json_encode($result);
    }
  }
?>
