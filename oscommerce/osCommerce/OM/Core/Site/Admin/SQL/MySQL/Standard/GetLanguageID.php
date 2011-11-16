<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class GetLanguageID {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qlanguage = $OSCOM_PDO->prepare('select languages_id from :table_languages where code = :code');
      $Qlanguage->bindValue(':code', $data['code']);
      $Qlanguage->execute();

      return $Qlanguage->fetch();
    }
  }
?>
