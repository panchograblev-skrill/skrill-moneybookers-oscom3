<?php

namespace osCommerce\OM\Core\Site\Admin\Module\Payment;

use osCommerce\OM\Core\OSCOM;
use osCommerce\OM\Core\Registry;

use osCommerce\OM\Core\Site\Admin\Application\PaymentModules\PaymentModules;
use osCommerce\OM\Core\DirectoryListing;

/**
 * MoneyBookers
 * 
 * @author Pancho Grablev <pancho.grablev@skrill.com>
 */
class Moneybookers extends \osCommerce\OM\Core\Site\Admin\PaymentModuleAbstract {

    /**
     * Title of the payment module
     *
     * @var string
     */
    protected $_title;

    /**
     * Description of the payment module
     *
     * @var string
     */
    protected $_description;

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
     * The status of the module
     *
     * @var boolean
     */
    protected $_status = false;

    /**
     * Initialize module
     *
     * @access protected
     */
    protected function initialize() {
        $this->_title = OSCOM::getDef('moneybookers_title');
        $this->_description = OSCOM::getDef('moneybookers_description');
        $this->_status = (defined('MODULE_PAYMENT_MONEYBOOKERS_STATUS') && (MODULE_PAYMENT_MONEYBOOKERS_STATUS == '1') ? true : false);
        $this->_sort_order = (defined('MODULE_PAYMENT_MONEYBOOKERS_SORT_ORDER') ? MODULE_PAYMENT_MONEYBOOKERS_SORT_ORDER : 0);
    }

    /**
     * Checks to see if the module has been installed
     *
     * @access public
     * @return boolean
     */
    public function isInstalled() {
        return defined('MODULE_PAYMENT_MONEYBOOKERS_STATUS');
    }

    /**
     * Installs the module
     *
     * @access public
     * @see \osCommerce\OM\Core\Site\Admin\PaymentModuleAbstract::install()
     */
    public function install()
    {
        parent::install();

        $data = array(array('title' => 'Enable Moneybookers',
                'key' => 'MODULE_PAYMENT_MONEYBOOKERS_STATUS',
                'value' => '-1',
                'description' => 'Do you want to accept Moneybookers E-wallet payments?<br />',
                'group_id' => '6',
                'use_function' => 'osc_cfg_use_get_boolean_value',
                'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))'),
            array('title' => 'Pay to email',
                'key' => 'MODULE_PAYMENT_MONEYBOOKERS_PAY_TO_EMAIL',
                'value' => '',
                'description' => 'The email address of the seller account if no API credentials has been setup.',
                'group_id' => '6'),
            array('title' => 'Secret word',
                'key' => 'MODULE_PAYMENT_MONEYBOOKERS_SECRET_WORD',
                'value' => '',
                'description' => 'Secret word submitted in the "Merchant Tools" section of the Merchant\'s online Moneybookers account.
                    <br />If the "Merchant Tools" section is not shown in your account, please contact merchantservices@moneybookers.com.',
                'group_id' => '6'),
            array('title' => 'Sort order of display.',
                'key' => 'MODULE_PAYMENT_MONEYBOOKERS_SORT_ORDER',
                'value' => '0',
                'description' => 'Sort order of display. Lowest is displayed first.',
                'group_id' => '6'),
            array('title' => 'Set Order Status',
                'key' => 'MODULE_PAYMENT_MONEYBOOKERS_ORDER_STATUS_ID',
                'value' => '0',
                'description' => 'Set the status of orders made with this payment module to this value',
                'group_id' => '6',
                'use_function' => 'osc_cfg_use_get_order_status_title',
                'set_function' => 'osc_cfg_set_order_statuses_pull_down_menu'),
            array('title' => 'All Credit cards',
                'key' => 'MODULE_PAYMENT_MONEYBOOKERS_ACC',
                'value' => '-1',
                'description' => 'Do you want to enable All Credit card payments?<br />',
                'group_id' => '6',
                'use_function' => 'osc_cfg_use_get_boolean_value',
                'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))'),
            array('title' => 'Sort order of display.',
                'key' => 'MODULE_PAYMENT_MONEYBOOKERS_ACC_SORT_ORDER',
                'value' => '0',
                'description' => 'All Credit Cards - Sort order of display. Lowest is displayed first.',
                'group_id' => '6'),
            array('title' => 'Online Bank Transfer',
                'key' => 'MODULE_PAYMENT_MONEYBOOKERS_OBT',
                'value' => '-1',
                'description' => 'Do you want to enable Online Bank Transfer payments?<br />',
                'group_id' => '6',
                'use_function' => 'osc_cfg_use_get_boolean_value',
                'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))'),
            array('title' => 'Sort order of display.',
                'key' => 'MODULE_PAYMENT_MONEYBOOKERS_OBT_SORT_ORDER',
                'value' => '0',
                'description' => 'Online Bank Transfer - Sort order of display. Lowest is displayed first.',
                'group_id' => '6'),
            array('title' => 'Giropay',
                'key' => 'MODULE_PAYMENT_MONEYBOOKERS_GIR',
                'value' => '-1',
                'description' => 'Do you want to enable Giropay payments?<br />',
                'group_id' => '6',
                'use_function' => 'osc_cfg_use_get_boolean_value',
                'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))'),
            array('title' => 'Sort order of display.',
                'key' => 'MODULE_PAYMENT_MONEYBOOKERS_GIR_SORT_ORDER',
                'value' => '0',
                'description' => 'Giropay - Sort order of display. Lowest is displayed first.',
                'group_id' => '6'),
            array('title' => 'Direct Debit / ELV',
                'key' => 'MODULE_PAYMENT_MONEYBOOKERS_DID',
                'value' => '-1',
                'description' => 'Do you want to enable Direct Debit / ELV payments?<br />',
                'group_id' => '6',
                'use_function' => 'osc_cfg_use_get_boolean_value',
                'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))'),
            array('title' => 'Sort order of display.',
                'key' => 'MODULE_PAYMENT_MONEYBOOKERS_DID_SORT_ORDER',
                'value' => '0',
                'description' => 'Direct Debit / ELV - Sort order of display. Lowest is displayed first.',
                'group_id' => '6'),
            array('title' => 'Sofortueberweisung',
                'key' => 'MODULE_PAYMENT_MONEYBOOKERS_SFT',
                'value' => '-1',
                'description' => 'Do you want to enable Sofortueberweisung payments?<br />',
                'group_id' => '6',
                'use_function' => 'osc_cfg_use_get_boolean_value',
                'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))'),
            array('title' => 'Sort order of display.',
                'key' => 'MODULE_PAYMENT_MONEYBOOKERS_SFT_SORT_ORDER',
                'value' => '0',
                'description' => 'Sofortueberweisung - Sort order of display. Lowest is displayed first.',
                'group_id' => '6'),
            array('title' => 'eNETS',
                'key' => 'MODULE_PAYMENT_MONEYBOOKERS_ENT',
                'value' => '-1',
                'description' => 'Do you want to enable eNETS payments?<br />',
                'group_id' => '6',
                'use_function' => 'osc_cfg_use_get_boolean_value',
                'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))'),
            array('title' => 'Sort order of display.',
                'key' => 'MODULE_PAYMENT_MONEYBOOKERS_ENT_SORT_ORDER',
                'value' => '0',
                'description' => 'eNETS - Sort order of display. Lowest is displayed first.',
                'group_id' => '6'),
            array('title' => 'Nordea Solo (Sweden)',
                'key' => 'MODULE_PAYMENT_MONEYBOOKERS_EBT',
                'value' => '-1',
                'description' => 'Do you want to enable Nordea Solo (Sweden) payments?<br />',
                'group_id' => '6',
                'use_function' => 'osc_cfg_use_get_boolean_value',
                'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))'),
            array('title' => 'Sort order of display.',
                'key' => 'MODULE_PAYMENT_MONEYBOOKERS_EBT_SORT_ORDER',
                'value' => '0',
                'description' => 'Nordea Solo (Sweden) - Sort order of display. Lowest is displayed first.',
                'group_id' => '6'),
            array('title' => 'Nordea Solo (Finland)',
                'key' => 'MODULE_PAYMENT_MONEYBOOKERS_SO2',
                'value' => '-1',
                'description' => 'Do you want to enable Nordea Solo (Finland) payments?<br />',
                'group_id' => '6',
                'use_function' => 'osc_cfg_use_get_boolean_value',
                'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))'),
            array('title' => 'Sort order of display.',
                'key' => 'MODULE_PAYMENT_MONEYBOOKERS_SO2_SORT_ORDER',
                'value' => '0',
                'description' => 'Nordea Solo (Finland) - Sort order of display. Lowest is displayed first.',
                'group_id' => '6'),
            array('title' => 'iDEAL',
                'key' => 'MODULE_PAYMENT_MONEYBOOKERS_IDL',
                'value' => '-1',
                'description' => 'Do you want to enable iDEAL payments?<br />',
                'group_id' => '6',
                'use_function' => 'osc_cfg_use_get_boolean_value',
                'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))'),
            array('title' => 'Sort order of display.',
                'key' => 'MODULE_PAYMENT_MONEYBOOKERS_IDL_SORT_ORDER',
                'value' => '0',
                'description' => 'iDEAL - Sort order of display. Lowest is displayed first.',
                'group_id' => '6'),
            array('title' => 'EPS (Netpay)',
                'key' => 'MODULE_PAYMENT_MONEYBOOKERS_NPY',
                'value' => '-1',
                'description' => 'Do you want to enable EPS (Netpay) payments?<br />',
                'group_id' => '6',
                'use_function' => 'osc_cfg_use_get_boolean_value',
                'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))'),
            array('title' => 'Sort order of display.',
                'key' => 'MODULE_PAYMENT_MONEYBOOKERS_NPY_SORT_ORDER',
                'value' => '0',
                'description' => 'EPS (Netpay) - Sort order of display. Lowest is displayed first.',
                'group_id' => '6'),
            array('title' => 'POLi',
                'key' => 'MODULE_PAYMENT_MONEYBOOKERS_PLI',
                'value' => '-1',
                'description' => 'Do you want to enable POLi payments?<br />',
                'group_id' => '6',
                'use_function' => 'osc_cfg_use_get_boolean_value',
                'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))'),
            array('title' => 'Sort order of display.',
                'key' => 'MODULE_PAYMENT_MONEYBOOKERS_PLI_SORT_ORDER',
                'value' => '0',
                'description' => 'POLi - Sort order of display. Lowest is displayed first.',
                'group_id' => '6'),
            array('title' => 'All Polish Banks',
                'key' => 'MODULE_PAYMENT_MONEYBOOKERS_PWI',
                'value' => '-1',
                'description' => 'Do you want to enable All Polish Banks payments?<br />',
                'group_id' => '6',
                'use_function' => 'osc_cfg_use_get_boolean_value',
                'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))'),
            array('title' => 'Sort order of display.',
                'key' => 'MODULE_PAYMENT_MONEYBOOKERS_PWI_SORT_ORDER',
                'value' => '0',
                'description' => 'All Polish Banks - Sort order of display. Lowest is displayed first.',
                'group_id' => '6'),
            array('title' => 'ePay Bulgaria',
                'key' => 'MODULE_PAYMENT_MONEYBOOKERS_EPY',
                'value' => '-1',
                'description' => 'Do you want to enable ePay payments?<br />',
                'group_id' => '6',
                'use_function' => 'osc_cfg_use_get_boolean_value',
                'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))'),
            array('title' => 'Sort order of display.',
                'key' => 'MODULE_PAYMENT_MONEYBOOKERS_EPY_SORT_ORDER',
                'value' => '0',
                'description' => 'ePay Bulgaria - Sort order of display. Lowest is displayed first.',
                'group_id' => '6'),
        );

        OSCOM::callDB('Admin\InsertConfigurationParameters', $data, 'Site');
        
        $this->_installSubPaymentOptions();
    }
    
    /**
     * Uninstall module
     */
    public function remove()
    {
        parent::remove();
        
        $this->_removeSubPaymentOptions();
    }

    /**
     * Return the configuration parameter keys in an array
     *
     * @access public
     * @return array
     */
    public function getKeys() {
        return array(
            'MODULE_PAYMENT_MONEYBOOKERS_STATUS',
            'MODULE_PAYMENT_MONEYBOOKERS_PAY_TO_EMAIL',
            'MODULE_PAYMENT_MONEYBOOKERS_SECRET_WORD',
            'MODULE_PAYMENT_MONEYBOOKERS_SORT_ORDER',
            'MODULE_PAYMENT_MONEYBOOKERS_ORDER_STATUS_ID',
            'MODULE_PAYMENT_MONEYBOOKERS_ACC',
            'MODULE_PAYMENT_MONEYBOOKERS_ACC_SORT_ORDER',
            'MODULE_PAYMENT_MONEYBOOKERS_OBT',
            'MODULE_PAYMENT_MONEYBOOKERS_OBT_SORT_ORDER',
            'MODULE_PAYMENT_MONEYBOOKERS_GIR',
            'MODULE_PAYMENT_MONEYBOOKERS_GIR_SORT_ORDER',
            'MODULE_PAYMENT_MONEYBOOKERS_DID',
            'MODULE_PAYMENT_MONEYBOOKERS_DID_SORT_ORDER',
            'MODULE_PAYMENT_MONEYBOOKERS_SFT',
            'MODULE_PAYMENT_MONEYBOOKERS_SFT_SORT_ORDER',
            'MODULE_PAYMENT_MONEYBOOKERS_ENT',
            'MODULE_PAYMENT_MONEYBOOKERS_ENT_SORT_ORDER',
            'MODULE_PAYMENT_MONEYBOOKERS_EBT',
            'MODULE_PAYMENT_MONEYBOOKERS_EBT_SORT_ORDER',
            'MODULE_PAYMENT_MONEYBOOKERS_SO2',
            'MODULE_PAYMENT_MONEYBOOKERS_SO2_SORT_ORDER',
            'MODULE_PAYMENT_MONEYBOOKERS_IDL',
            'MODULE_PAYMENT_MONEYBOOKERS_IDL_SORT_ORDER',
            'MODULE_PAYMENT_MONEYBOOKERS_NPY',
            'MODULE_PAYMENT_MONEYBOOKERS_NPY_SORT_ORDER',
            'MODULE_PAYMENT_MONEYBOOKERS_PLI',
            'MODULE_PAYMENT_MONEYBOOKERS_PLI_SORT_ORDER',
            'MODULE_PAYMENT_MONEYBOOKERS_PWI',
            'MODULE_PAYMENT_MONEYBOOKERS_PWI_SORT_ORDER',
            'MODULE_PAYMENT_MONEYBOOKERS_EPY',
            'MODULE_PAYMENT_MONEYBOOKERS_EPY_SORT_ORDER',
        );
    }
    
    /**
     * Install sub payment options for moneybookers
     * 
     * @access protected
     * @return void
     */
    protected function _installSubPaymentOptions()
    {
        $installed_modules = PaymentModules::getInstalled();
        $installed = array();

        foreach ( $installed_modules['entries'] as $module ) {
            $installed[] = $module['code'];
        }
        
        // install sub modules
        $DLpm = new DirectoryListing(OSCOM::BASE_DIRECTORY . 'Core/Site/Admin/Module/Payment');
        $DLpm->setIncludeDirectories(false);

        foreach ($DLpm->getFiles() as $file) {
            $module = substr($file['name'], 0, strrpos($file['name'], '.'));
            
            if (stripos($module, 'Mb') !== 0 ) {
                continue;
            }

            if (!in_array($module, $installed)) {
                $class = 'osCommerce\\OM\\Core\\Site\\Admin\\Module\\Payment\\' . $module;
                $OSCOM_PM = new $class();
                $OSCOM_PM->install();
            }
        }
    }
    
    /**
     * Remove sub payment options for moneybookers
     * 
     * @access protected
     * @return void
     */
    protected function _removeSubPaymentOptions()
    {
        // remove sub modules
        $DLpm = new DirectoryListing(OSCOM::BASE_DIRECTORY . 'Core/Site/Admin/Module/Payment');
        $DLpm->setIncludeDirectories(false);

        foreach ($DLpm->getFiles() as $file) {
            $module = substr($file['name'], 0, strrpos($file['name'], '.'));

            if (stripos($module, 'Mb') !== 0 ) {
                continue;
            }

            $class = 'osCommerce\\OM\\Core\\Site\\Admin\\Module\\Payment\\' . $module;
            $OSCOM_PM = new $class();
            $OSCOM_PM->remove();
        }
    }
    
    

}

?>
