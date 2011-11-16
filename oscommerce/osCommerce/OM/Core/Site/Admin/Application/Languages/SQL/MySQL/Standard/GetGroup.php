<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Languages\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class GetGroup {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $result = array();

      $Qgroup = $OSCOM_PDO->prepare('select languages_id, count(*) as total_entries from :table_languages_definitions where content_group = :content_group group by languages_id');
      $Qgroup->bindValue(':content_group', $data['group']);
      $Qgroup->execute();

      $result['entries'] = $Qgroup->fetchAll();

      $result['total'] = count($result['entries']);

      return $result;
    }
  }
?>
