<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\PDO\MySQL;

  class V5 extends \osCommerce\OM\Core\PDO\MySQL\Standard {
    protected $_has_native_fk = true;
    protected $_driver_parent = 'MySQL\\Standard';

    public function connect() {
// STRICT_ALL_TABLES introduced in MySQL v5.0.2
// Only one init command can be issued (see http://bugs.php.net/bug.php?id=48859)
      $this->_driver_options[self::MYSQL_ATTR_INIT_COMMAND] = 'set session sql_mode="STRICT_ALL_TABLES", names utf8';

      parent::connect();
    }
  }
?>
