<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\TaxClasses\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class Find {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $result = array();

      $sql_query = 'select SQL_CALC_FOUND_ROWS tc.*, count(tr.tax_rates_id) as total_tax_rates from :table_tax_class tc left join :table_tax_rates tr on (tc.tax_class_id = tr.tax_class_id) where (tc.tax_class_title like :tax_class_title or tr.tax_description like :tax_description) group by tc.tax_class_id order by tc.tax_class_title';

      if ( $data['batch_pageset'] !== -1 ) {
        $sql_query .= ' limit :batch_pageset, :batch_max_results';
      }

      $sql_query .= '; select found_rows();';

      $Qclasses = $OSCOM_PDO->prepare($sql_query);
      $Qclasses->bindValue(':tax_class_title', '%' . $data['keywords'] . '%');
      $Qclasses->bindValue(':tax_description', '%' . $data['keywords'] . '%');

      if ( $data['batch_pageset'] !== -1 ) {
        $Qclasses->bindInt(':batch_pageset', $OSCOM_PDO->getBatchFrom($data['batch_pageset'], $data['batch_max_results']));
        $Qclasses->bindInt(':batch_max_results', $data['batch_max_results']);
      }

      $Qclasses->execute();

      $result['entries'] = $Qclasses->fetchAll();

      $Qclasses->nextRowset();

      $result['total'] = $Qclasses->fetchColumn();

      return $result;
    }
  }
?>
