<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Languages\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class FindGroups {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $result = array();

      $Qgroups = $OSCOM_PDO->prepare('select distinct content_group, count(*) as total_entries from :table_languages_definitions where languages_id = :languages_id and (definition_key like :definition_key or definition_value like :definition_value) group by content_group order by content_group');
      $Qgroups->bindInt(':languages_id', $data['id']);
      $Qgroups->bindValue(':definition_key', '%' . $data['keywords'] . '%');
      $Qgroups->bindValue(':definition_value', '%' . $data['keywords'] . '%');
      $Qgroups->execute();

      $result['entries'] = $Qgroups->fetchAll();

      $result['total'] = count($result['entries']);;

      return $result;
    }
  }
?>
