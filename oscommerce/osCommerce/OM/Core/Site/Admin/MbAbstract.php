<?php

/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core\Site\Admin;

use osCommerce\OM\Core\Registry;
use osCommerce\OM\Core\OSCOM;
use osCommerce\OM\Core\Cache;

abstract class MbAbstract extends \osCommerce\OM\Core\Site\Admin\PaymentModuleAbstract
{
    /**
     * Moneybookers Payment Code
     * @abstract 
     * @var string
     */
    protected $_mb_code = '';
    
    /**
     * The developers name
     *
     * @var string
     */
    protected $_author_name = 'Moneybookers';

    /**
     * The developers address
     *
     * @var string
     */
    protected $_author_www = 'http://www.moneybookers.com';
    
    /**
     * Initialize module
     *
     * @access protected
     */
    protected function initialize()
    {
        $this->_mb_code = str_replace('Mb', '', $this->_code);
        
        $this->_title = OSCOM::getDef('moneybookers_' . strtolower($this->_mb_code) . '_title');
        $this->_description = OSCOM::getDef('moneybookers_' . strtolower($this->_mb_code) . '_description');

        $this->_status = (defined('MODULE_PAYMENT_MONEYBOOKERS_' . $this->_mb_code) && 
                (constant('MODULE_PAYMENT_MONEYBOOKERS_' . $this->_mb_code) == '1') ? true : false);
    }
    
    /**
     * Check if the module is installed
     * 
     * @return bool
     */
    public function isInstalled()
    {
        $mb = new \osCommerce\OM\Core\Site\Admin\Module\Payment\Moneybookers();
        return $mb->isInstalled();
    }
}