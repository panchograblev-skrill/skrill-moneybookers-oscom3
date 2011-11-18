<?php

namespace osCommerce\OM\Core\Site\Shop\Module\Payment;

class MbOBT extends \osCommerce\OM\Core\Site\Shop\MbAbstract
{
    protected $_allowedCountries = array(
        'DEU', 'GBR', 'DNK', 
        'FIN', 'SWE', 'POL',
        'EST', 'LVA', 'LTU'
    );
    
    protected $_image_name = 'obt-en-by-mb.gif';
}