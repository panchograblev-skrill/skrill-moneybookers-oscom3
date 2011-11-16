<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop;

  class Service {
    protected $_services = array();
    protected $_started_services = array();
    protected $_call_before_page_content = array();
    protected $_call_after_page_content = array();

    public function __construct() {
      $this->_services = explode(';', MODULE_SERVICES_INSTALLED);
    }

    public function start() {
      $this->_started_services = array();

      foreach ( $this->_services as $service ) {
        $this->startService($service);
      }
    }

    public function stop() {
/*
  ugly workaround to force the output_compression/GZIP service module to be
  stopped last to make sure all content in the buffer is compressed and sent
  to the client
*/
      if ( $this->isStarted('output_compression') ) {
        $key = array_search('output_compression', $this->_started_services);
        unset($this->_started_services[$key]);

        $this->_started_services[] = 'output_compression';
      }

      foreach ( $this->_started_services as $service ) {
        $this->stopService($service);
      }
    }

    public function startService($service) {
      if ( class_exists('osCommerce\\OM\\Core\\Site\\Shop\\Module\\Service\\' . $service) ) {
        if ( call_user_func(array('osCommerce\\OM\\Core\\Site\\Shop\\Module\\Service\\' . $service, 'start')) ) {
          $this->_started_services[] = $service;
        }
      } else {
        trigger_error('\'osCommerce\\OM\\Core\\Site\\Shop\\Module\\Service\\' . $service . '\' does not exist', E_USER_ERROR);
      }
    }

    public function stopService($service) {
      if ( $this->isStarted($service) ) {
        call_user_func(array('osCommerce\\OM\\Core\\Site\\Shop\\Module\\Service\\' . $service, 'stop'));
      }
    }


    public function isStarted($service) {
      return in_array($service, $this->_started_services);
    }

    public function addCallBeforePageContent($object, $method) {
      $this->_call_before_page_content[] = array($object, $method);
    }

    public function addCallAfterPageContent($object, $method) {
      $this->_call_after_page_content[] = array($object, $method);
    }

    public function hasBeforePageContentCalls() {
      return !empty($this->_call_before_page_content);
    }

    public function hasAfterPageContentCalls() {
      return !empty($this->_call_after_page_content);
    }

    public function getCallBeforePageContent() {
      return $this->_call_before_page_content;
    }

    public function getCallAfterPageContent() {
      return $this->_call_after_page_content;
    }
  }
?>
