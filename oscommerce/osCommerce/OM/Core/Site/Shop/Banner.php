<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop;

  use osCommerce\OM\Core\DateTime;
  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\Registry;

/**
 * The Banner class manages the banners shown throughout the online store
 */

  class Banner {

/**
 * Controls whether banners should be shown multiple times on the page
 *
 * @var boolean
 * @access protected
 */

    protected $_show_duplicates_in_group = false;

/**
 * A placeholder that keeps the banner ID in memory when checking if a banner
 * exists before showing it
 *
 * @var id
 * @access protected
 */

    protected $_exists_id;

/**
 * An array containing the banner IDs already shown on the page
 *
 * @var array
 * @access protected
 */

    protected $_shown_ids = array();

/**
 * Constructor
 *
 * @access public
 */

    public function __construct() {
      if ( SERVICE_BANNER_SHOW_DUPLICATE == 'True' ) {
        $this->_show_duplicates_in_group = true;
      }
    }

/**
 * Activate a banner that has been on schedule
 *
 * @param int $id The ID of the banner to activate
 * @access public
 * @return boolean
 */

    public function activate($id) {
      return $this->_setStatus($id, true);
    }

/**
 * Activate all banners on schedule
 *
 * @access public
 */

    public function activateAll() {
      $OSCOM_PDO = Registry::get('PDO');

      $Qbanner = $OSCOM_PDO->query('select banners_id, date_scheduled from :table_banners where date_scheduled != ""');
      $Qbanner->execute();

      while ( $Qbanner->fetch() ) {
        if ( DateTime::getNow() >= $Qbanner->value('date_scheduled') ) {
          $this->activate($Qbanner->valueInt('banners_id'));
        }
      }
    }

/**
 * Deactivate a banner
 *
 * @param int $id The ID of the banner to deactivate
 * @access public
 * @return boolean
 */

    public function expire($id) {
      return $this->_setStatus($id, false);
    }

/**
 * Deactivate all banners that have passed their schedule
 *
 * @access public
 */

    public function expireAll() {
      $OSCOM_PDO = Registry::get('PDO');

      $Qbanner = $OSCOM_PDO->query('select b.banners_id, b.expires_date, b.expires_impressions, sum(bh.banners_shown) as banners_shown from :table_banners b, :table_banners_history bh where b.status = 1 and b.banners_id = bh.banners_id group by b.banners_id');
      $Qbanner->execute();

      while ( $Qbanner->fetch() ) {
        if ( strlen($Qbanner->value('expires_date')) > 0 ) {
          if ( DateTime::getNow() >= $Qbanner->value('expires_date') ) {
            $this->expire($Qbanner->valueInt('banners_id'));
          }
        } elseif ( strlen($Qbanner->valueInt('expires_impressions')) > 0 ) {
          if ( ($Qbanner->valueInt('expires_impressions') > 0) && ($Qbanner->valueInt('banners_shown') >= $Qbanner->valueInt('expires_impressions')) ) {
            $this->expire($Qbanner->valueInt('banners_id'));
          }
        }
      }
    }

/**
 * Check if an existing banner is active
 *
 * @param int $id The ID of the banner to check
 * @access public
 * @return boolean
 */

    public function isActive($id) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qbanner = $OSCOM_PDO->prepare('select status from :table_banners where banners_id = :banners_id');
      $Qbanner->bindInt(':banners_id', $id);
      $Qbanner->execute();

      return ( $Qbanner->valueInt('status') === 1 );
    }

/**
 * Check if banners exist in a group. If banners exists, select a random entry
 * and assign its ID to $_exists_id.
 *
 * @param string $group The group to check in
 * @access public
 * @return boolean
 */

    public function exists($group) {
      $OSCOM_PDO = Registry::get('PDO');

      $sql_query = 'select banners_id from :table_banners where status = 1 and banners_group = :banners_group';

      if ( ($this->_show_duplicates_in_group === false) && (count($this->_shown_ids) > 0) ) {
        $sql_query .= ' and banners_id not in (' . implode(',', $this->_shown_ids) . ')';
      }

      $sql_query .= ' order by rand() limit 1';

      $Qbanner = $OSCOM_PDO->prepare($sql_query);
      $Qbanner->bindValue(':banners_group', $group);
      $Qbanner->execute();

      $result = $Qbanner->fetch();

      if ( $result !== false ) {
        $this->_exists_id = $result['banners_id'];

        return true;
      }

      return false;
    }

/**
 * Display a banner. If no ID is passed, the value defined in $_exists_id is
 * used.
 *
 * @param int $id The ID of the banner to show
 * @access public
 * @return string
 */

    public function display($id = null) {
      $OSCOM_PDO = Registry::get('PDO');

      $banner_string = '';

      if ( empty($id) && isset($this->_exists_id) && is_numeric($this->_exists_id) ) {
        $id = $this->_exists_id;

        unset($this->_exists_id);
      }

      $Qbanner = $OSCOM_PDO->prepare('select * from :table_banners where banners_id = :banners_id and status = 1');
      $Qbanner->bindInt(':banners_id', $id);
      $Qbanner->execute();

      $result = $Qbanner->fetch();

      if ( $result !== false ) {
        if ( !empty($result['banners_html_text']) ) {
          $banner_string = $result['banners_html_text'];
        } else {
// HPDL create Redirect action; fix banner image location
          $banner_string = HTML::link(OSCOM::getLink('Shop', 'Index', 'Redirect&action=banner&goto=' . (int)$result['banners_id']), HTML::image('public/' . $Qbanner->value('banners_image'), $Qbanner->value('banners_title')), 'target="_blank"');
        }

        $this->_updateDisplayCount($result['banners_id']);

        if ( $this->_show_duplicates_in_group === false ) {
          $this->_shown_ids[] = $result['banners_id'];
        }
      }

      return $banner_string;
    }

/**
 * Return the URL assigned to the banner
 *
 * @param int $id The ID of the banner
 * @param boolean $increment_click_flag A flag to state if the banner click count should be incremented
 * @access public
 * @return string
 */

    public function getURL($id, $increment_click_flag = false) {
      $OSCOM_PDO = Registry::get('PDO');

      $url = '';

      $Qbanner = $OSCOM_PDO->prepare('select banners_url from :table_banners where banners_id = :banners_id and status = 1');
      $Qbanner->bindInt(':banners_id', $id);
      $Qbanner->execute();

      $result = $Qbanner->fetch();

      if ( $result !== false ) {
        $url = $result['banners_url'];

        if ( $increment_click_flag === true ) {
          $this->_updateClickCount($id);
        }
      }

      return $url;
    }

/**
 * Sets the status of a banner
 *
 * @param int $id The ID of the banner to set the status to
 * @param boolean $active_flag A flag that enables or disables the banner
 * @access private
 * @return boolean
 */

    private function _setStatus($id, $active_flag) {
      $OSCOM_PDO = Registry::get('PDO');

      if ( $active_flag === true ) {
        $Qbanner = $OSCOM_PDO->prepare('update :table_banners set status = 1, date_status_change = now(), date_scheduled = NULL where banners_id = :banners_id');
      } else {
        $Qbanner = $OSCOM_PDO->prepare('update :table_banners set status = 0, date_status_change = now() where banners_id = :banners_id');
      }

      $Qbanner->bindInt(':banners_id', $id);
      $Qbanner->execute();

      return ( $Qbanner->rowCount() === 1 );
    }

/**
 * Increment the display count of the banner
 *
 * @param int $id The ID of the banner
 * @access private
 */

    private function _updateDisplayCount($id) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qcheck = $OSCOM_PDO->prepare('select count(*) as count from :table_banners_history where banners_id = :banners_id and date_format(banners_history_date, "%Y%m%d") = date_format(now(), "%Y%m%d")');
      $Qcheck->bindInt(':banners_id', $id);
      $Qcheck->execute();

      $result = $Qcheck->fetch();

      if ( ($result !== false) && ($result['count'] > 0) ) {
        $Qbanner = $OSCOM_PDO->prepare('update :table_banners_history set banners_shown = banners_shown + 1 where banners_id = :banners_id and date_format(banners_history_date, "%Y%m%d") = date_format(now(), "%Y%m%d")');
      } else {
        $Qbanner = $OSCOM_PDO->prepare('insert into :table_banners_history (banners_id, banners_shown, banners_history_date) values (:banners_id, 1, now())');
      }

      $Qbanner->bindInt(':banners_id', $id);
      $Qbanner->execute();
    }

/**
 * Increment the click count of the banner
 *
 * @param int $id The ID of the banner
 * @access private
 */

    private function _updateClickCount($id) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qcheck = $OSCOM_PDO->prepare('select count(*) as count from :table_banners_history where banners_id = :banners_id and date_format(banners_history_date, "%Y%m%d") = date_format(now(), "%Y%m%d")');
      $Qcheck->bindInt(':banners_id', $id);
      $Qcheck->execute();

      $result = $Qcheck->fetch();

      if ( ($result !== false) && ($result['count'] > 0) ) {
        $Qbanner = $OSCOM_PDO->prepare('update :table_banners_history set banners_clicked = banners_clicked + 1 where banners_id = :banners_id and date_format(banners_history_date, "%Y%m%d") = date_format(now(), "%Y%m%d")');
      } else {
        $Qbanner = $OSCOM_PDO->prepare('insert into :table_banners_history (banners_id, banners_clicked, banners_history_date) values (:banners_id, 1, now())');
      }

      $Qbanner->bindInt(':banners_id', $id);
      $Qbanner->execute();
    }
  }
?>
