<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Module\Box\TellAFriend;

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  class Controller extends \osCommerce\OM\Core\Modules {
    var $_title,
        $_code = 'TellAFriend',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'Box';

    public function __construct() {
      $this->_title = OSCOM::getDef('box_tell_a_friend_heading');
    }

    public function initialize() {
      $OSCOM_Product = ( Registry::exists('Product') ) ? Registry::get('Product') : null;

      if ( isset($OSCOM_Product) && ($OSCOM_Product instanceof \osCommerce\OM\Site\Shop\Product) && $OSCOM_Product->isValid() ) { // HPDL && ($osC_Template->getModule() != 'tell_a_friend')) {
        $this->_content = '<form name="tell_a_friend" action="' . OSCOM::getLink(null, null, 'TellAFriend&' . $OSCOM_Product->getKeyword()) . '" method="post">' . "\n" .
                          HTML::inputField('to_email_address', null, 'style="width: 80%;"') . '&nbsp;' . HTML::submitImage('button_tell_a_friend.gif', OSCOM::getDef('box_tell_a_friend_text')) . '<br />' . OSCOM::getDef('box_tell_a_friend_text') . "\n" .
                          '</form>' . "\n";
      }
    }
  }
?>
