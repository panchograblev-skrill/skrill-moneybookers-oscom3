<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin;

  abstract class IndexModulesAbstract {
    protected $_title;
    protected $_title_link;
    protected $_data;

    public function getTitle() {
      return $this->_title;
    }

    public function getTitleLink() {
      return $this->_title_link;
    }

    public function hasTitleLink() {
      return isset($this->_title_link) && !empty($this->_title_link);
    }

    public function getData() {
      return $this->_data;
    }

    public function hasData() {
      return isset($this->_data) && !empty($this->_data);
    }
  }
?>
