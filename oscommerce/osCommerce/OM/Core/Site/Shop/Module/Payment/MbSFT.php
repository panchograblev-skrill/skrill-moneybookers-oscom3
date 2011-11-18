<?php

namespace osCommerce\OM\Core\Site\Shop\Module\Payment;

class MbSFT extends \osCommerce\OM\Core\Site\Shop\MbAbstract
{
    protected $_allowedCountries = array(
        'DEU', 'GBR', 'BEL', 
        'CHE', 'AUT', 'NLD',
    );
    
    protected $_image_name = 'sofort-by-mb.gif';
}