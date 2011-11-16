<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Module\Service;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Session as SessionClass;

  class Session implements \osCommerce\OM\Core\Site\Shop\ServiceInterface {
    public static function start() {
      Registry::set('Session', SessionClass::load());

      $OSCOM_Session = Registry::get('Session');
      $OSCOM_Session->setLifeTime(SERVICE_SESSION_EXPIRATION_TIME * 60);

      if ( (SERVICE_SESSION_FORCE_COOKIE_USAGE == '1') || ((bool)ini_get('session.use_only_cookies') === true) ) {
        OSCOM::setCookie('cookie_test', 'please_accept_for_session', time()+60*60*24*90);

        if ( isset($_COOKIE['cookie_test']) ) {
          $OSCOM_Session->start();
        }
      } elseif ( SERVICE_SESSION_BLOCK_SPIDERS == '1' ) {
        $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $spider_flag = false;

        if ( !empty($user_agent) ) {
          $spiders = file(OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/assets/spiders.txt');

          foreach ( $spiders as $spider ) {
            if ( !empty($spider) ) {
              if ( strpos($user_agent, trim($spider)) !== false ) {
                $spider_flag = true;
                break;
              }
            }
          }
        }

        if ( $spider_flag === false ) {
          $OSCOM_Session->start();
        }
      } else {
        $OSCOM_Session->start();
      }

// verify the ssl_session_id
      if ( (OSCOM::getRequestType() == 'SSL') && (SERVICE_SESSION_CHECK_SSL_SESSION_ID == '1') && (OSCOM::getConfig('enable_ssl') == 'true') ) {
        if ( isset($_SERVER['SSL_SESSION_ID']) && ctype_xdigit($_SERVER['SSL_SESSION_ID']) ) {
          if ( !isset($_SESSION['SESSION_SSL_ID']) ) {
            $_SESSION['SESSION_SSL_ID'] = $_SERVER['SSL_SESSION_ID'];
          }

          if ( $_SESSION['SESSION_SSL_ID'] != $_SERVER['SSL_SESSION_ID'] ) {
            $OSCOM_Session->destroy();

            OSCOM::redirect(OSCOM::getLink(null, 'Info', 'SSLcheck', 'AUTO'));
          }
        }
      }

// verify the browser user agent
      if ( SERVICE_SESSION_CHECK_USER_AGENT == '1' ) {
        $http_user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

        if ( !isset($_SESSION['SESSION_USER_AGENT']) ) {
          $_SESSION['SESSION_USER_AGENT'] = $http_user_agent;
        }

        if ( $_SESSION['SESSION_USER_AGENT'] != $http_user_agent ) {
          $OSCOM_Session->destroy();

          OSCOM::redirect(OSCOM::getLink(null, 'Account', 'LogIn', 'SSL'));
        }
      }

// verify the IP address
      if ( SERVICE_SESSION_CHECK_IP_ADDRESS == '1' ) {
        if ( !isset($_SESSION['SESSION_IP_ADDRESS']) ) {
          $_SESSION['SESSION_IP_ADDRESS'] = OSCOM::getIPAddress();
        }

        if ( $_SESSION['SESSION_IP_ADDRESS'] != OSCOM::getIPAddress() ) {
          $OSCOM_Session->destroy();

          OSCOM::redirect(OSCOM::getLink(null, 'Account', 'LogIn', 'SSL'));
        }
      }

      Registry::get('MessageStack')->loadFromSession();

      return true;
    }

    public static function stop() {
      return true;
    }
  }
?>
