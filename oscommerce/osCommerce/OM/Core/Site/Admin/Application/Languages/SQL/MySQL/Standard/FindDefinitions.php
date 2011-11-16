<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Languages\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class FindDefinitions {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $result = array();

      $Qdefs = $OSCOM_PDO->prepare('select * from :table_languages_definitions where languages_id = :languages_id and content_group = :content_group and (definition_key like :definition_key or definition_value like :definition_value) order by definition_key');
      $Qdefs->bindInt(':languages_id', $data['id']);
      $Qdefs->bindValue(':content_group', $data['group']);
      $Qdefs->bindValue(':definition_key', '%' . $data['keywords'] . '%');
      $Qdefs->bindValue(':definition_value', '%' . $data['keywords'] . '%');
      $Qdefs->execute();

      $result['entries'] = $Qdefs->fetchAll();

      $result['total'] = count($result['entries']);

      return $result;
    }
  }
?>
