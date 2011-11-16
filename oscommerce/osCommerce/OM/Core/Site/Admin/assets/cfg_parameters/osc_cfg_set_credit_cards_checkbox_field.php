<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\Registry;

  function osc_cfg_set_credit_cards_checkbox_field($default, $key = null) {
    $OSCOM_PDO = Registry::get('PDO');

    $name = (empty($key)) ? 'configuration_value' : 'configuration[' . $key . '][]';

    $cc_array = array();

    $Qcc = $OSCOM_PDO->prepare('select id, credit_card_name from :table_credit_cards where credit_card_status = :credit_card_status order by sort_order, credit_card_name');
    $Qcc->bindInt(':credit_card_status', 1);
    $Qcc->execute();

    while ( $Qcc->fetch() ) {
      $cc_array[] = array('id' => $Qcc->valueInt('id'),
                          'text' => $Qcc->value('credit_card_name'));
    }

    return HTML::checkboxField($name, $cc_array, explode(',', $default), null, '<br />');
  }
?>
