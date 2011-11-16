<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin;

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;

  class MessageStack extends \osCommerce\OM\Core\MessageStack {
    public function get($group = null) {
      if ( empty($group) ) {
        $group = OSCOM::getSiteApplication();
      }

      $result = false;

      if ( $this->exists($group) ) {
        $data = array();

        foreach ( $this->_data[$group] as $message ) {
          $data['messageStack' . ucfirst($message['type'])][] = $message['text'];
        }

        $result = '';

        foreach ( $data as $type => $messages ) {
          $result .= '<div class="' . HTML::outputProtected($type) . '" onmouseover="$(this).find(\'span:first\').show();" onmouseout="$(this).find(\'span:first\').hide();"><span style="float: right; display: none;"><a href="#" onclick="$(this).parent().parent().slideFadeToggle();">' . HTML::icon('minimize.png', 'Hide') . '</a></span>';

          foreach ( $messages as $message ) {
            $result .= '<p>' . HTML::outputProtected($message) . '</p>';
          }

          $result .= '</div>';
        }

        unset($this->_data[$group]);
      }

      return $result;
    }
  }
?>
