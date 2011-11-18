<?php

namespace osCommerce\OM\Core\Site\Shop\Module\Payment;

class MbEBT extends \osCommerce\OM\Core\Site\Shop\MbAbstract
{
    protected $_allowedCountries = array('SWE');
    
    protected $_image_name = 'nordea-by-mb.gif';
}