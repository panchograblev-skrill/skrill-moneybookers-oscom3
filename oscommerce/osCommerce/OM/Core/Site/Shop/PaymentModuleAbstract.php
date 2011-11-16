<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop;

  abstract class PaymentModuleAbstract {
    protected $_code;
    protected $_title;
    protected $_method_title;
    protected $_status;
    protected $_sort_order = 0;
    protected $_order_status = 0;
    protected $_gateway_url;

    abstract protected function initialize();
    abstract public function process();

    public function __construct() {
      $module_class = explode('\\', get_called_class());
      $this->_code = end($module_class);

      $this->initialize();
    }

    public function isEnabled() {
      return $this->_status;
    }

    public function getCode() {
      return $this->_code;
    }

    public function getTitle() {
      return $this->_title;
    }

    public function getMethodTitle() {
      return $this->_method_title;
    }

    public function getSortOrder() {
      return $this->_sort_order;
    }

    public function getJavascriptBlock() {
      return false;
    }

    public function selection() {
      return array('id' => $this->_code,
                   'module' => $this->_method_title);
    }

    public function preConfirmationCheck() {
      return false;
    }

    public function confirmation() {
      return false;
    }

    public function hasGateway() {
      return isset($this->_gateway_url);
    }

    public function getGatewayURL() {
      return $this->_gateway_url;
    }

    public function getProcessButton() {
      return false;
    }
  }
?>
