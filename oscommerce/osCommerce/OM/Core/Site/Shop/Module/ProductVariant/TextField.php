<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Module\ProductVariant;

  use osCommerce\OM\Core\HTML;

  class TextField implements \osCommerce\OM\Core\Site\Shop\ProductVariantInterface {
    const ALLOW_MULTIPLE_VALUES = true;
    const HAS_CUSTOM_VALUE = true;

    public static function parse($data) {
      $string = '<table border="0" cellspacing="0" cellpadding="2">';

      $i = 0;

      foreach ( $data['data'] as $field ) {
        $i++;

        $string .= '  <tr>' .
                   '    <td width="100">' . $field['text'] . ':</td>' .
                   '    <td>' . HTML::inputField('variants[' . $data['group_id'] . '][' . $field['id'] . ']', null, 'id="variants_' . $data['group_id'] . '_' . $i . '"') . '</td>' .
                   '  </tr>';
      }

      $string .= '</table>';

      return $string;
    }

    public static function getGroupTitle($data) {
      return $data['value_title'];
    }

    public static function getValueTitle($data) {
      return $_POST['variants'][$data['group_id']][$data['value_id']];
    }

    public static function allowsMultipleValues() {
      return self::ALLOW_MULTIPLE_VALUES;
    }

    public static function hasCustomValue() {
      return self::HAS_CUSTOM_VALUE;
    }
  }
?>
