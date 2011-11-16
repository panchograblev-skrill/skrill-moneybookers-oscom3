<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Module\Dashboard;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Access;

  class Reviews extends \osCommerce\OM\Core\Site\Admin\IndexModulesAbstract {
    public function __construct() {
      Registry::get('Language')->loadIniFile('modules/Dashboard/Reviews.php');

      $this->_title = OSCOM::getDef('admin_indexmodules_reviews_title');
      $this->_title_link = OSCOM::getLink(null, 'Reviews');

      if ( Access::hasAccess(OSCOM::getSite(), 'Reviews') ) {
        $this->_data = '<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">' .
                       '  <thead>' .
                       '    <tr>' .
                       '      <th>' . OSCOM::getDef('admin_indexmodules_reviews_table_heading_products') . '</th>' .
                       '      <th>' . OSCOM::getDef('admin_indexmodules_reviews_table_heading_language') . '</th>' .
                       '      <th>' . OSCOM::getDef('admin_indexmodules_reviews_table_heading_rating') . '</th>' .
                       '      <th>' . OSCOM::getDef('admin_indexmodules_reviews_table_heading_date') . '</th>' .
                       '    </tr>' .
                       '  </thead>' .
                       '  <tbody>';

        $Qreviews = Registry::get('PDO')->query('select r.reviews_id, r.products_id, greatest(r.date_added, greatest(r.date_added, r.last_modified)) as date_last_modified, r.reviews_rating, pd.products_name, l.name as languages_name, l.code as languages_code from :table_reviews r left join :table_products_description pd on (r.products_id = pd.products_id and r.languages_id = pd.language_id), :table_languages l where r.languages_id = l.languages_id order by date_last_modified desc limit 6');
        $Qreviews->execute();

        $counter = 0;
 
        while ( $Qreviews->fetch() ) {
          $this->_data .= '    <tr onmouseover="$(this).addClass(\'mouseOver\');" onmouseout="$(this).removeClass(\'mouseOver\');"' . ($counter % 2 ? ' class="alt"' : '') . '>' .
                          '      <td>' . osc_link_object(OSCOM::getLink(null, 'Reviews', 'rID=' . $Qreviews->valueInt('reviews_id') . '&action=save'), osc_icon('reviews.png') . '&nbsp;' . $Qreviews->value('products_name')) . '</td>' .
                          '      <td align="center">' . Registry::get('Language')->showImage($Qreviews->value('languages_code')) . '</td>' .
                          '      <td align="center">' . osc_image('../images/stars_' . $Qreviews->valueInt('reviews_rating') . '.png', $Qreviews->valueInt('reviews_rating') . '/5') . '</td>' .
                          '      <td>' . $Qreviews->value('date_last_modified') . '</td>' .
                          '    </tr>';

          $counter++;
        }

        $this->_data .= '  </tbody>' .
                        '</table>';
      }
    }
  }
?>
