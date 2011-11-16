<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Configuration\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class GetAll {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $result = array();

      $Qgroups = $OSCOM_PDO->query('select cg.configuration_group_id, cg.configuration_group_title, count(c.configuration_id) as total_entries from :table_configuration_group cg, :table_configuration c where cg.visible = 1 and cg.configuration_group_id = c.configuration_group_id group by cg.configuration_group_id order by cg.sort_order, cg.configuration_group_title');
      $Qgroups->execute();

      $result['entries'] = $Qgroups->fetchAll();

      $result['total'] = count($result['entries']);

      return $result;
    }
  }
?>
