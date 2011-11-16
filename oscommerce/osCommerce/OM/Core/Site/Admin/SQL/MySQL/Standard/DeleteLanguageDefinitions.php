<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class DeleteLanguageDefinitions {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qdel = $OSCOM_PDO->prepare('delete from :table_languages_definitions where definition_key = :definition_key and content_group = :content_group');
      $Qdel->bindValue(':definition_key', $data['key']);
      $Qdel->bindValue(':content_group', $data['group']);
      $Qdel->execute();

      return !$Qdel->isError();
    }
  }
?>
