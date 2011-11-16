<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class GetTemplates {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qtemplates = $OSCOM_PDO->query('select id, code, title from :table_templates');
      $Qtemplates->setCache('templates');
      $Qtemplates->execute();

      return $Qtemplates->fetchAll();
    }
  }
?>
