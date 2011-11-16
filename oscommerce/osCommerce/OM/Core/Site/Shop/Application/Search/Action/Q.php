<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Application\Search\Action;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;

  class Q {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_Breadcrumb = Registry::get('Breadcrumb');
      $OSCOM_Search = Registry::get('Search');
      $OSCOM_MessageStack = Registry::get('MessageStack');

      $application->setPageTitle(OSCOM::getDef('search_results_heading'));
      $application->setPageContent('results.php');

      if ( $OSCOM_Service->isStarted('Breadcrumb') ) {
        $OSCOM_Breadcrumb->add(OSCOM::getDef('breadcrumb_search_results'), OSCOM::getLink(null, null, OSCOM::getAllGET()));
      }

      $error = false;

      if ( isset($_GET['datefrom_days']) && is_numeric($_GET['datefrom_days']) && isset($_GET['datefrom_months']) && is_numeric($_GET['datefrom_months']) && isset($_GET['datefrom_years']) && is_numeric($_GET['datefrom_years']) ) {
        if ( checkdate($_GET['datefrom_months'], $_GET['datefrom_days'], $_GET['datefrom_years']) ) {
          $OSCOM_Search->setDateFrom(mktime(0, 0, 0, $_GET['datefrom_months'], $_GET['datefrom_days'], $_GET['datefrom_years']));
        } else {
          $error = true;

          $OSCOM_MessageStack->add('Search', OSCOM::getDef('error_search_invalid_from_date'));
        }
      }

      if ( isset($_GET['dateto_days']) && is_numeric($_GET['dateto_days']) && isset($_GET['dateto_months']) && is_numeric($_GET['dateto_months']) && isset($_GET['dateto_years']) && is_numeric($_GET['dateto_years']) ) {
        if ( checkdate($_GET['dateto_months'], $_GET['dateto_days'], $_GET['dateto_years']) ) {
          $OSCOM_Search->setDateTo(mktime(23, 59, 59, $_GET['dateto_months'], $_GET['dateto_days'], $_GET['dateto_years']));
        } else {
          $error = true;

          $OSCOM_MessageStack->add('Search', OSCOM::getDef('error_search_invalid_to_date'));
        }
      }

      if ( $OSCOM_Search->hasDateSet() ) {
        if ( $OSCOM_Search->getDateFrom() > $OSCOM_Search->getDateTo() ) {
          $error = true;

          $OSCOM_MessageStack->add('Search', OSCOM::getDef('error_search_to_date_less_than_from_date'));
        }
      }

      if ( isset($_GET['pfrom']) && !empty($_GET['pfrom']) ) {
        if ( settype($_GET['pfrom'], 'double') ) {
          $OSCOM_Search->setPriceFrom($_GET['pfrom']);
        } else {
          $error = true;

          $OSCOM_MessageStack->add('Search', OSCOM::getDef('error_search_price_from_not_numeric'));
        }
      }

      if ( isset($_GET['pto']) && !empty($_GET['pto']) ) {
        if ( settype($_GET['pto'], 'double') ) {
          $OSCOM_Search->setPriceTo($_GET['pto']);
        } else {
          $error = true;

          $OSCOM_MessageStack->add('Search', OSCOM::getDef('error_search_price_to_not_numeric'));
        }
      }

      if ( $OSCOM_Search->hasPriceSet('from') && $OSCOM_Search->hasPriceSet('to') && ($OSCOM_Search->getPriceFrom() >= $OSCOM_Search->getPriceTo()) ) {
        $error = true;

        $OSCOM_MessageStack->add('Search', OSCOM::getDef('error_search_price_to_less_than_price_from'));
      }

      if ( isset($_GET['Q']) && is_string($_GET['Q']) && !empty($_GET['Q']) ) {
        $OSCOM_Search->setKeywords(urldecode($_GET['Q']));

        if ( $OSCOM_Search->hasKeywords() === false ) {
          $error = true;

          $OSCOM_MessageStack->add('Search', OSCOM::getDef('error_search_invalid_keywords'));
        }
      }

      if ( !$OSCOM_Search->hasKeywords() && !$OSCOM_Search->hasPriceSet('from') && !$OSCOM_Search->hasPriceSet('to') && !$OSCOM_Search->hasDateSet('from') && !$OSCOM_Search->hasDateSet('to') ) {
        $error = true;

        $OSCOM_MessageStack->add('Search', OSCOM::getDef('error_search_at_least_one_input'));
      }

      if ( isset($_GET['category']) && is_numeric($_GET['category']) && ($_GET['category'] > 0) ) {
        $OSCOM_Search->setCategory($_GET['category'], (isset($_GET['recursive']) && ($_GET['recursive'] == '1') ? true : false));
      }

      if ( isset($_GET['manufacturer']) && is_numeric($_GET['manufacturer']) && ($_GET['manufacturer'] > 0) ) {
        $OSCOM_Search->setManufacturer($_GET['manufacturer']);
      }

      if ( isset($_GET['sort']) && !empty($_GET['sort']) ) {
        if ( strpos($_GET['sort'], '|d') !== false ) {
          $OSCOM_Search->setSortBy(substr($_GET['sort'], 0, -2), '-');
        } else {
          $OSCOM_Search->setSortBy($_GET['sort']);
        }
      }

      if ( $error === false ) {
        $OSCOM_Search->execute();
      } else {
        $application->setPageContent('main.php');
      }
    }
  }
?>
