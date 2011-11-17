<?php

namespace osCommerce\OM\Core\Site\Shop\Application\Checkout\Action;

use osCommerce\OM\Core\ApplicationAbstract;
use osCommerce\OM\Core\Registry;

/**
 * @author Pancho Grablev <pancho.grablev@skrill.com>
 */
class MBPayment {

    public static function execute(ApplicationAbstract $application) {

        // delete the shopping cart
        $OSCOM_ShoppingCart = Registry::get('ShoppingCart');
        $OSCOM_ShoppingCart->reset(true);

        $application->setPageContent('mb_payment.php');
    }
}