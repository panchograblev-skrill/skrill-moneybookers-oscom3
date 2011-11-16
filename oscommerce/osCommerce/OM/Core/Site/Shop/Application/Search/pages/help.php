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

<style type="text/css">
#pageContent {
  width: 100%;
  margin: 0;
  padding: 0;
}

div#pageBlockLeft {
  width: 100%;
  margin: 0;
}
</style>

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('search_help_heading'); ?></h6>

  <div class="content">
    <p><?php echo OSCOM::getDef('search_help'); ?></p>

    <p align="right"><?php echo HTML::link('javascript:window.close();', OSCOM::getDef('close_window')); ?></p>
  </div>
</div>
