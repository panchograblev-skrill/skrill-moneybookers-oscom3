<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Configuration\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class Get {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qgroup = $OSCOM_PDO->prepare('select cg.*, count(c.configuration_id) as total_entries from :table_configuration_group cg left join :table_configuration c on (cg.configuration_group_id = c.configuration_group_id) where cg.configuration_group_id = :configuration_group_id');
      $Qgroup->bindInt(':configuration_group_id', $data['id']);
      $Qgroup->execute();

      return $Qgroup->fetch();
    }
  }
?>
