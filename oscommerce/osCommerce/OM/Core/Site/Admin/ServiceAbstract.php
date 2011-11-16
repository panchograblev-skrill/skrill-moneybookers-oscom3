<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin;

  use osCommerce\OM\Core\Registry;

/**
 * @since v3.0.2
 */

  abstract class ServiceAbstract {
    protected $code;
    protected $title;
    protected $description;
    protected $uninstallable = true;
    protected $depends;
    protected $precedes;

    abstract protected function initialize();

    public function __construct() {
      $OSCOM_Language = Registry::get('Language');

      $module_class = explode('\\', get_called_class());
      $this->code = end($module_class);

      $OSCOM_Language->loadIniFile('modules/Service/' . $this->code . '.php');

      $this->initialize();
    }

    public function getCode() {
      return $this->code;
    }

    public function getTitle() {
      return $this->title;
    }

    public function getDescription() {
      return $this->description;
    }

    public function isUninstallable() {
      return $this->uninstallable;
    }

    public function hasKeys() {
      $keys = $this->keys();

      return ( is_array($keys) && !empty($keys) );
    }

    public function install() {
      return false;
    }

    public function remove() {
      return false;
    }

    public function keys() {
      return false;
    }
  }
?>
