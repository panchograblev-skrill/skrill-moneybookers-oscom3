<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\ZoneGroups\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class Find {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $result = array();

      $sql_query = 'select SQL_CALC_FOUND_ROWS gz.*, count(z2gz.association_id) as total_entries from :table_geo_zones gz left join :table_zones_to_geo_zones z2gz on (gz.geo_zone_id = z2gz.geo_zone_id), :table_countries c, :table_zones z where z2gz.zone_country_id = c.countries_id and z2gz.zone_id = z.zone_id and (gz.geo_zone_name like :geo_zone_name or gz.geo_zone_description like :geo_zone_description or c.countries_name like :countries_name or z.zone_name like :zone_name) group by gz.geo_zone_id order by gz.geo_zone_name';

      if ( $data['batch_pageset'] !== -1 ) {
        $sql_query .= ' limit :batch_pageset, :batch_max_results';
      }

      $sql_query .= '; select found_rows();';


      $Qgroups = $OSCOM_PDO->prepare($sql_query);
      $Qgroups->bindValue(':geo_zone_name', '%' . $data['keywords'] . '%');
      $Qgroups->bindValue(':geo_zone_description', '%' . $data['keywords'] . '%');
      $Qgroups->bindValue(':countries_name', '%' . $data['keywords'] . '%');
      $Qgroups->bindValue(':zone_name', '%' . $data['keywords'] . '%');

      if ( $data['batch_pageset'] !== -1 ) {
        $Qgroups->bindInt(':batch_pageset', $OSCOM_PDO->getBatchFrom($data['batch_pageset'], $data['batch_max_results']));
        $Qgroups->bindInt(':batch_max_results', $data['batch_max_results']);
      }

      $Qgroups->execute();

      $result['entries'] = $Qgroups->fetchAll();

      $Qgroups->nextRowset();

      $result['total'] = $Qgroups->fetchColumn();

      return $result;
    }
  }
?>
