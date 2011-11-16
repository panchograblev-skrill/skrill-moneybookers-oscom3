<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Countries\SQL\MySQL\V5;

  use osCommerce\OM\Core\Registry;

  class GetAll {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $result = array();

      $Qcountries = $OSCOM_PDO->prepare('CALL CountriesGetAll(:batch_pageset, :batch_max_results)');

      if ( $data['batch_pageset'] !== -1 ) {
        $Qcountries->bindInt(':batch_pageset', $OSCOM_PDO->getBatchFrom($data['batch_pageset'], $data['batch_max_results']));
        $Qcountries->bindInt(':batch_max_results', $data['batch_max_results']);
      } else {
        $Qcountries->bindNull(':batch_pageset');
        $Qcountries->bindNull(':batch_max_results');
      }

      $Qcountries->execute();

      $result['entries'] = $Qcountries->fetchAll();

      $Qcountries->nextRowset();

      $result['total'] = $Qcountries->fetchColumn();

      return $result;
    }
  }
?>
