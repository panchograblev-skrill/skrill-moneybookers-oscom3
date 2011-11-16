<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Languages\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class GetDefinitions {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $result = array();

      $sql_query = 'select * from :table_languages_definitions where languages_id = :languages_id and';

      if ( is_array($data['group']) ) {
        $sql_query .= ' content_group in ("' . implode('", "', $data['group']) . '")';
      } else {
        $sql_query .= ' content_group = :content_group';
      }

      $sql_query .= ' order by content_group, definition_key';

      $Qdefs = $OSCOM_PDO->prepare($sql_query);

      if ( !is_array($data['group']) ) {
        $Qdefs->bindValue(':content_group', $data['group']);
      }

      $Qdefs->bindInt(':languages_id', $data['id']);
      $Qdefs->execute();

      $result['entries'] = $Qdefs->fetchAll();

      $result['total'] = count($result['entries']);

      return $result;
    }
  }
?>
