<?php

namespace osCommerce\OM\Core\Site\Shop\Module\Payment\Moneybookers;

class MbOBT extends MbAbstract
{
    protected $_allowedCountries = array(
        'DEU', 'GBR', 'DNK', 
        'FIN', 'SWE', 'POL',
        'EST', 'LVA', 'LTU'
    );
}