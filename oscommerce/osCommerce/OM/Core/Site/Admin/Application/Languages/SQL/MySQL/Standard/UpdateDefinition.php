<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Languages\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class UpdateDefinition {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qupdate = $OSCOM_PDO->prepare('update :table_languages_definitions set definition_value = :definition_value where definition_key = :definition_key and languages_id = :languages_id and content_group = :content_group');
      $Qupdate->bindValue(':definition_value', $data['value']);
      $Qupdate->bindValue(':definition_key', $data['key']);
      $Qupdate->bindInt(':languages_id', $data['language_id']);
      $Qupdate->bindValue(':content_group', $data['group']);
      $Qupdate->execute();

      return ( ($Qupdate->rowCount() === 1) || !$Qupdate->isError() );
    }
  }
?>
