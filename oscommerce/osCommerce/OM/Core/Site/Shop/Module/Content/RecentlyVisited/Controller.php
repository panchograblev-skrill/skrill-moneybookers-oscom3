<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Module\Content\RecentlyVisited;

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  class Controller extends \osCommerce\OM\Core\Modules {
    var $_title,
        $_code = 'RecentlyVisited',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'Content';

    public function __construct() {
      $this->_title = OSCOM::getDef('recently_visited_title');
    }

    function initialize() {
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_RecentlyVisited = Registry::get('RecentlyVisited');
      $OSCOM_Image = Registry::get('Image');

      if ( $OSCOM_Service->isStarted('RecentlyVisited') && $OSCOM_RecentlyVisited->hasHistory() ) {
        $this->_content = '<table border="0" width="100%" cellspacing="0" cellpadding="2">' .
                          '  <tr>';

        if ( $OSCOM_RecentlyVisited->hasProducts() ) {
          $this->_content .= '    <td valign="top">' .
                             '      <h6>' . OSCOM::getDef('recently_visited_products_title') . '</h6>' .
                             '      <ol style="list-style: none; margin: 0; padding: 0;">';

          foreach ( $OSCOM_RecentlyVisited->getProducts() as $product ) {
            $this->_content .= '<li style="padding-bottom: 15px;">';

            if ( SERVICE_RECENTLY_VISITED_SHOW_PRODUCT_IMAGES == '1' ) {
              $this->_content .= '<span style="float: left; width: ' . ($OSCOM_Image->getWidth('mini') + 10) . 'px; text-align: center;">' . HTML::link(OSCOM::getLink(null, 'Products', $product['keyword']), $OSCOM_Image->show($product['image'], $product['name'], null, 'mini')) . '</span>';
            }

            $this->_content .= '<div style="float: left;">' . HTML::link(OSCOM::getLink(null, 'Products', $product['keyword']), $product['name']) . '<br />';

            if ( SERVICE_RECENTLY_VISITED_SHOW_PRODUCT_PRICES == '1' ) {
              $this->_content .= $product['price'] . '&nbsp;';
            }

            $this->_content .= '<i>(' . sprintf(OSCOM::getDef('recently_visited_item_in_category'), HTML::link(OSCOM::getLink(null, 'Index', 'cPath=' . $product['category_path']), $product['category_name'])) . ')</i></div>' .
                               '<div style="clear: both;"></div>' .
                               '</li>';
          }

          $this->_content .= '      </ol>' .
                             '    </td>';
        }

        if ( $OSCOM_RecentlyVisited->hasCategories() ) {
          $this->_content .= '      <td valign="top">' .
                             '        <h6>' . OSCOM::getDef('recently_visited_categories_title') . '</h6>' .
                             '        <ol style="list-style: none; margin: 0; padding: 0;">';

          foreach ( $OSCOM_RecentlyVisited->getCategories() as $category ) {
            $this->_content .= '<li>' . HTML::link(OSCOM::getLink(null, 'Index', 'cPath=' . $category['path']), $category['name']);

            if ( !empty($category['parent_id']) ) {
              $this->_content .= '&nbsp;<i>(' . sprintf(OSCOM::getDef('recently_visited_item_in_category'), HTML::link(OSCOM::getLink(null, 'Index', 'cPath=' . $category['parent_id']), $category['parent_name'])) . ')</i>';
            }

            $this->_content .= '</li>';
          }

          $this->_content .= '      </ol>' .
                             '    </td>';
        }

        if ( $OSCOM_RecentlyVisited->hasSearches() ) {
          $this->_content .= '      <td valign="top">' .
                             '        <h6>' . OSCOM::getDef('recently_visited_searches_title') . '</h6>' .
                             '        <ol style="list-style: none; margin: 0; padding: 0;">';

          foreach ( $OSCOM_RecentlyVisited->getSearches() as $searchphrase ) {
            $this->_content .= '<li>' . HTML::link(OSCOM::getLink(null, 'Search', 'Q=' . $searchphrase['keywords']), HTML::outputProtected($searchphrase['keywords'])) . ' <i>(' . number_format($searchphrase['results']) . ' results)</i></li>';
          }

          $this->_content .= '      </ol>' .
                             '    </td>';
        }

        $this->_content .= '  </tr>' .
                           '</table>';
      }
    }
  }
?>
