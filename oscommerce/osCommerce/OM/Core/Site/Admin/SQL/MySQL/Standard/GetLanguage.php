<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class GetLanguage {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qlanguage = $OSCOM_PDO->prepare('select * from :table_languages where languages_id = :languages_id');
      $Qlanguage->bindInt(':languages_id', $data['id']);
      $Qlanguage->execute();

      return $Qlanguage->fetch();
    }
  }
?>
