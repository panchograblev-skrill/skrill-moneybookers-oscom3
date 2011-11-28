<?php

/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core\Site\Shop\Application\Checkout\Action;

use osCommerce\OM\Core\ApplicationAbstract;
use osCommerce\OM\Core\Registry;

class WPF {

    public static function execute(ApplicationAbstract $application) {

        // delete the shopping cart
        $OSCOM_ShoppingCart = Registry::get('ShoppingCart');
        $OSCOM_ShoppingCart->reset(true);

        $application->setPageContent('mb_wpf.php');
    }
}