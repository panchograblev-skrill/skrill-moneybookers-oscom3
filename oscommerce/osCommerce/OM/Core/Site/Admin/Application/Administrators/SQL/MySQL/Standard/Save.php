<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Administrators\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class Save {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      if ( !isset($data['id']) ) {
        $data['id'] = null;
      }

      $error = false;

      $sql_query = 'select id from :table_administrators where user_name = :user_name';

      if ( is_numeric($data['id']) ) {
        $sql_query .= ' and id != :id';
      }

      $sql_query .= ' limit 1';

      $Qcheck = $OSCOM_PDO->prepare($sql_query);
      $Qcheck->bindValue(':user_name', $data['username']);

      if ( is_numeric($data['id']) ) {
        $Qcheck->bindInt(':id', $data['id']);
      }

      $Qcheck->execute();

      if ( $Qcheck->fetch() === false ) {
        $OSCOM_PDO->beginTransaction();

        if ( is_numeric($data['id']) ) {
          $sql_query = 'update :table_administrators set user_name = :user_name';

          if ( !empty($data['password']) ) {
            $sql_query .= ', user_password = :user_password';
          }

          $sql_query .= ' where id = :id';
        } else {
          $sql_query = 'insert into :table_administrators (user_name, user_password) values (:user_name, :user_password)';
        }

        $Qadmin = $OSCOM_PDO->prepare($sql_query);

        if ( is_numeric($data['id']) ) {
          if ( !empty($data['password']) ) {
            $Qadmin->bindValue(':user_password', $data['password']);
          }

          $Qadmin->bindInt(':id', $data['id']);
        } else {
          $Qadmin->bindValue(':user_password', $data['password']);
        }

        $Qadmin->bindValue(':user_name', $data['username']);
        $Qadmin->execute();

        if ( !$Qadmin->isError() ) {
          if ( !is_numeric($data['id']) ) {
            $data['id'] = $OSCOM_PDO->lastInsertId();
          }
        } else {
          $error = true;
        }

        if ( $error === false ) {
          if ( !empty($data['modules']) ) {
            if ( in_array('0', $data['modules']) ) {
              $data['modules'] = array('*');
            }

            foreach ( $data['modules'] as $module ) {
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
          $sql_query = 'delete from :table_administrators_access where administrators_id = :administrators_id';

          if ( !empty($data['modules']) ) {
            $sql_query .= ' and module not in ("' . implode('", "', $data['modules']) . '")'; // HPDL create bindRaw()?
          }

          $Qdel = $OSCOM_PDO->prepare($sql_query);
          $Qdel->bindInt(':administrators_id', $data['id']);
          $Qdel->execute();

          if ( $Qdel->isError() ) {
            $error = true;
          }
        }

        if ( $error === false ) {
          $OSCOM_PDO->commit();

          return 1;
        } else {
          $OSCOM_PDO->rollBack();

          return -1;
        }
      } else {
        return -2;
      }
    }
  }
?>
