<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<?php
// optional Product List Filter
  if ( PRODUCT_LIST_FILTER > 0 ) {
    if ( isset($_GET['Manufacturers']) && !empty($_GET['Manufacturers']) ) {
      $filterlist_sql = "select distinct c.categories_id as id, cd.categories_name as name from :table_products p, :table_products_to_categories p2c, :table_categories c, :table_categories_description cd, :table_templates_boxes tb, :table_product_attributes pa where p.products_status = '1' and p.products_id = p2c.products_id and p2c.categories_id = c.categories_id and p2c.categories_id = cd.categories_id and cd.language_id = '" . (int)$OSCOM_Language->getID() . "' and tb.code = 'Manufacturers' and tb.id = pa.id and pa.products_id = p.products_id and pa.value = '" . (int)$_GET['Manufacturers'] . "' order by cd.categories_name";
    } else {
      $filterlist_sql = "select distinct m.manufacturers_id as id, m.manufacturers_name as name from :table_products p, :table_products_to_categories p2c, :table_manufacturers m where p.products_status = '1' and p.manufacturers_id = m.manufacturers_id and p.products_id = p2c.products_id and p2c.categories_id = '" . $OSCOM_Category->getID() . "' order by m.manufacturers_name";
    }

    $Qfilterlist = $OSCOM_PDO->query($filterlist_sql);
    $Qfilterlist->execute();

    $filter_result = $Qfilterlist->fetchAll();

    if ( count($filter_result) > 1 ) {
      echo '<p><form name="filter" action="' . OSCOM::getLink() . '" method="get">' . $OSCOM_Language->get('filter_show') . '&nbsp;';

      if ( isset($_GET['Manufacturers']) && !empty($_GET['Manufacturers']) ) {
        echo HTML::hiddenField('Manufacturers', $_GET['Manufacturers']);

        $options = array(array('id' => '', 'text' => OSCOM::getDef('filter_all_categories')));
      } else {
        echo HTML::hiddenField('cPath', $OSCOM_Category->getPath());

        $options = array(array('id' => '', 'text' => OSCOM::getDef('filter_all_manufacturers')));
      }

      if ( isset($_GET['sort']) ) {
        echo HTML::hiddenField('sort', $_GET['sort']);
      }

      foreach ( $filter_result as $f ) {
        $options[] = array('id' => $f['id'], 'text' => $f['name']);
      }

      echo HTML::selectMenu('filter', $options, (isset($_GET['filter']) ? $_GET['filter'] : null), 'onchange="this.form.submit()"') .
           HTML::hiddenSessionIDField() . '</form></p>' . "\n";
    }
  }

  if ( isset($_GET['Manufacturers']) && !empty($_GET['Manufacturers']) ) {
    $OSCOM_Products->setManufacturer($_GET['Manufacturers']);
  }

  $products_listing = $OSCOM_Products->execute();

  require(OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Application/Products/pages/product_listing.php');
?>
