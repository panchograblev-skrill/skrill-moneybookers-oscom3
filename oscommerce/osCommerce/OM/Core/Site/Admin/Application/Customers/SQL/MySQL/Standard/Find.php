<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Customers\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

/**
 * @since v3.0.2
 */

  class Find {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $result = array();

      $sql_query = 'select SQL_CALC_FOUND_ROWS c.customers_id, c.customers_gender, c.customers_firstname, c.customers_lastname, c.customers_status, c.date_account_created, a.entry_country_id from :table_customers c left join :table_address_book a on (c.customers_id = a.customers_id and c.customers_default_address_id = a.address_book_id) where (c.customers_firstname like :customers_firstname or c.customers_lastname like :customers_lastname) order by c.customers_lastname, c.customers_firstname';

      if ( $data['batch_pageset'] !== -1 ) {
        $sql_query .= ' limit :batch_pageset, :batch_max_results';
      }

      $sql_query .= '; select found_rows();';

      $Qcustomers = $OSCOM_PDO->prepare($sql_query);
      $Qcustomers->bindValue(':customers_firstname', '%' . $data['keywords'] . '%');
      $Qcustomers->bindValue(':customers_lastname', '%' . $data['keywords'] . '%');

      if ( $data['batch_pageset'] !== -1 ) {
        $Qcustomers->bindInt(':batch_pageset', $OSCOM_PDO->getBatchFrom($data['batch_pageset'], $data['batch_max_results']));
        $Qcustomers->bindInt(':batch_max_results', $data['batch_max_results']);
      }

      $Qcustomers->execute();

      $result['entries'] = $Qcustomers->fetchAll();

      $Qcustomers->nextRowset();

      $result['total'] = $Qcustomers->fetchColumn();

      return $result;
    }
  }
?>
