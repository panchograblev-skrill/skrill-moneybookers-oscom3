<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Administrators\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Admin\Application\Administrators\Administrators;

  class SavePermissions { // HPDL Albert would proudly say "Hey, hey, hey, it's a faattt SQL module!"; abstraction needed
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $error = false;

      $OSCOM_PDO->beginTransaction();

      if ( ($data['mode'] == Administrators::ACCESS_MODE_ADD) || ($data['mode'] == Administrators::ACCESS_MODE_SET) ) {
        foreach ( $data['modules'] as $module ) {
          $execute = true;

          if ( $module != '*' ) {
            $Qcheck = $OSCOM_PDO->prepare('select administrators_id from :table_administrators_access where administrators_id = :administrators_id and module = :module limit 1');
            $Qcheck->bindInt(':administrators_id', $data['id']);
            $Qcheck->bindValue(':module', '*');
            $Qcheck->execute();

            if ( $Qcheck->fetch() !== false ) {
              $execute = false;
            }
          }

          if ( $execute === true ) {
            $Qcheck = $OSCOM_PDO->prepare('select administrators_id from :table_administrators_access where administrators_id = :administrators_id and module = :module limit 1');
            $Qcheck->bindInt(':administrators_id', $data['id']);
            $Qcheck->bindValue(':module', $module);
            $Qcheck->execute();

            if ( $Qcheck->fetch() === false ) {
              $Qinsert = $OSCOM_PDO->prepare('insert into :table_administrators_access (administrators_id, module) values (:administrators_id, :module)');
              $Qinsert->bindInt(':administrators_id', $data['id']);
              $Qinsert->bindValue(':module', $module);
              $Qinsert->execute();

              if ( $Qinsert->isError() ) {
                $error = true;
                break;
              }
            }
          }
        }
      }

      if ( $error === false ) {
        if ( ($data['mode'] == Administrators::ACCESS_MODE_REMOVE) || ($data['mode'] == Administrators::ACCESS_MODE_SET) || in_array('*', $data['modules']) ) {
          if ( !empty($data['modules']) ) {
            $sql_query = 'delete from :table_administrators_access where administrators_id = :administrators_id';

            if ( $data['mode'] == Administrators::ACCESS_MODE_REMOVE ) {
              if ( !in_array('*', $data['modules']) ) {
                $sql_query .= ' and module in ("' . implode('", "', $data['modules']) . '")'; // HPDL create bindRaw()?
              }
            } else {
              $sql_query .= ' and module not in ("' . implode('", "', $data['modules']) . '")'; // HPDL create bindRaw()?
            }

            $Qdel = $OSCOM_PDO->prepare($sql_query);
            $Qdel->bindInt(':administrators_id', $data['id']);
            $Qdel->execute();

            if ( $Qdel->isError() ) {
              $error = true;
              break;
            }
          }
        }
      }

      if ( $error === false ) {
        $OSCOM_PDO->commit();

        return true;
      }

      $OSCOM_PDO->rollBack();

      return false;
    }
  }
?>
