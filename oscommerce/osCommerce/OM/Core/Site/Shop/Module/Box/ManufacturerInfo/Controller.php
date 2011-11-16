<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Module\Box\ManufacturerInfo;

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  class Controller extends \osCommerce\OM\Core\Modules {
    var $_title,
        $_code = 'ManufacturerInfo',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'Box';

    public function __construct() {
      $this->_title = OSCOM::getDef('box_manufacturer_info_heading');
    }

    public function initialize() {
      $OSCOM_Product = ( Registry::exists('Product') ) ? Registry::get('Product') : null;
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_Language = Registry::get('Language');

      if ( isset($OSCOM_Product) && ($OSCOM_Product instanceof \osCommerce\OM\Site\Shop\Product) && $OSCOM_Product->isValid() ) {
        $Qmanufacturer = $OSCOM_PDO->query('select m.manufacturers_id, m.manufacturers_name, m.manufacturers_image, mi.manufacturers_url from :table_manufacturers m left join :table_manufacturers_info mi on (m.manufacturers_id = mi.manufacturers_id and mi.languages_id = :languages_id), :table_products p  where p.products_id = :products_id and p.manufacturers_id = m.manufacturers_id');
        $Qmanufacturer->bindInt(':languages_id', $OSCOM_Language->getID());
        $Qmanufacturer->bindInt(':products_id', $OSCOM_Product->getID());
        $Qmanufacturer->execute();

        $result = $Qmanufacturer->fetch();

        if ( !empty($result) ) {
          $this->_content = '';

          if ( strlen($result['manufacturers_image']) > 0 ) {
            $this->_content .= '<div style="text-align: center;">' .
                               HTML::link(OSCOM::getLink(null, 'Index', 'Manufacturers=' . $result['manufacturers_id']), HTML::image('public/manufacturers/' . $result['manufacturers_image'], $result['manufacturers_name'])) .
                               '</div>';
          }

          $this->_content .= '<ol style="list-style: none; margin: 0; padding: 0;">';

          if ( strlen($result['manufacturers_url']) > 0 ) {
            $this->_content .= '<li>' . HTML::link(OSCOM::getLink(null, 'Redirct', 'Manufacturer=' . $result['manufacturers_id']), sprintf(OSCOM::getDef('box_manufacturer_info_website'), $result['manufacturers_name']), 'target="_blank"') . '</li>';
          }

          $this->_content .= '<li>' . HTML::link(OSCOM::getLink(null, 'Index', 'Manufacturers=' . $result['manufacturers_id']), OSCOM::getDef('box_manufacturer_info_products')) . '</li>';

          $this->_content .= '</ol>';
        }
      }
    }
  }
?>
