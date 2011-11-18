<?php

/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core\Site\Shop\Module\Payment;

class MbDID extends \osCommerce\OM\Core\Site\Shop\MbAbstract
{
    protected $_allowedCountries = array('DEU');
    
    protected $_image_name = 'ec-by-mb.gif';
}