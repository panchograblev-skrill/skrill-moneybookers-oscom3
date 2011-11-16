<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Currencies\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class Find {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $result = array();

      $sql_query = 'select SQL_CALC_FOUND_ROWS * from :table_currencies where (title like :title or code like :code or symbol_left like :symbol_left or symbol_right like :symbol_right) order by title';

      if ( $data['batch_pageset'] !== -1 ) {
        $sql_query .= ' limit :batch_pageset, :batch_max_results';
      }

      $sql_query .= '; select found_rows();';

      $Qcurrencies = $OSCOM_PDO->prepare($sql_query);
      $Qcurrencies->bindValue(':title', '%' . $data['keywords'] . '%');
      $Qcurrencies->bindValue(':code', '%' . $data['keywords'] . '%');
      $Qcurrencies->bindValue(':symbol_left', '%' . $data['keywords'] . '%');
      $Qcurrencies->bindValue(':symbol_right', '%' . $data['keywords'] . '%');

      if ( $data['batch_pageset'] !== -1 ) {
        $Qcurrencies->bindInt(':batch_pageset', $OSCOM_PDO->getBatchFrom($data['batch_pageset'], $data['batch_max_results']));
        $Qcurrencies->bindInt(':batch_max_results', $data['batch_max_results']);
      }

      $Qcurrencies->execute();

      $result['entries'] = $Qcurrencies->fetchAll();

      $Qcurrencies->nextRowset();

      $result['total'] = $Qcurrencies->fetchColumn();

      return $result;
    }
  }
?>
