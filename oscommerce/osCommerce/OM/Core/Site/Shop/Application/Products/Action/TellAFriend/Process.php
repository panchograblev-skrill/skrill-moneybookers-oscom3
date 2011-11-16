<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Application\Products\Action\TellAFriend;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\Mail;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Shop\Product;

  class Process {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_NavigationHistory = Registry::get('NavigationHistory');
      $OSCOM_MessageStack = Registry::get('MessageStack');
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_Breadcrumb = Registry::get('Breadcrumb');

      if ( (ALLOW_GUEST_TO_TELL_A_FRIEND == '-1') && ($OSCOM_Customer->isLoggedOn() === false) ) {
        $OSCOM_NavigationHistory->setSnapshot();

        OSCOM::redirect(OSCOM::getLink(null, 'Account', 'LogIn', 'SSL'));
      }

      $requested_product = null;
      $product_check = false;

      if ( count($_GET) > 3 ) {
        $requested_product = basename(key(array_slice($_GET, 3, 1, true)));

        if ( $requested_product == 'Write' ) {
          unset($requested_product);

          if ( count($_GET) > 4 ) {
            $requested_product = basename(key(array_slice($_GET, 4, 1, true)));
          }
        }
      }

      if ( isset($requested_product) ) {
        if ( Product::checkEntry($requested_product) ) {
          $product_check = true;
        }
      }

      if ( $product_check === false ) {
        $application->setPageContent('not_found.php');

        return false;
      }

      Registry::set('Product', new Product($requested_product));
      $OSCOM_Product = Registry::get('Product');

      if ( empty($_POST['from_name']) ) {
        $OSCOM_MessageStack->add('TellAFriend', OSCOM::getDef('error_tell_a_friend_customers_name_empty'));
      }

      if ( !filter_var($_POST['from_email_address']. FILTER_VALIDATE_EMAIL) ) {
        $OSCOM_MessageStack->add('TellAFriend', OSCOM::getDef('error_tell_a_friend_invalid_customers_email_address'));
      }

      if ( empty($_POST['to_name']) ) {
        $OSCOM_MessageStack->add('TellAFriend', OSCOM::getDef('error_tell_a_friend_friends_name_empty'));
      }

      if ( !filter_var($_POST['to_email_address'], FILTER_VALIDATE_EMAIL) ) {
        $OSCOM_MessageStack->add('TellAFriend', OSCOM::getDef('error_tell_a_friend_invalid_friends_email_address'));
      }

      if ( $OSCOM_MessageStack->size('TellAFriend') < 1 ) {
        $email_subject = sprintf(OSCOM::getDef('email_tell_a_friend_subject'), HTML::sanitize($_POST['from_name']), STORE_NAME);
        $email_body = sprintf(OSCOM::getDef('email_tell_a_friend_intro'), HTML::sanitize($_POST['to_name']), HTML::sanitize($_POST['from_name']), $OSCOM_Product->getTitle(), STORE_NAME) . "\n\n";

        if ( !empty($_POST['message']) ) {
          $email_body .= HTML::sanitize($_POST['message']) . "\n\n";
        }

        $email_body .= sprintf(OSCOM::getDef('email_tell_a_friend_link'), OSCOM::getLink(null, null, $OSCOM_Product->getKeyword(), 'NONSSL', false)) . "\n\n" .
                       sprintf(OSCOM::getDef('email_tell_a_friend_signature'), STORE_NAME . "\n" . HTTP_SERVER . DIR_WS_CATALOG . "\n");

        $pEmail = new Mail(HTML::sanitize($_POST['to_name']), HTML::sanitize($_POST['to_email_address']), HTML::sanitize($_POST['from_name']), HTML::sanitize($_POST['from_email_address']), $email_subject);
        $pEmail->setBodyPlain($email_body);
        $pEmail->send();

        $OSCOM_MessageStack->add('header', sprintf(OSCOM::getDef('success_tell_a_friend_email_sent'), $OSCOM_Product->getTitle(), HTML::outputProtected($_POST['to_name'])), 'success');

        OSCOM::redirect(OSCOM::getLink(null, null, $OSCOM_Product->getKeyword()));
      }

      $application->setPageTitle($OSCOM_Product->getTitle());
      $application->setPageContent('tell_a_friend.php');
    }
  }
?>
