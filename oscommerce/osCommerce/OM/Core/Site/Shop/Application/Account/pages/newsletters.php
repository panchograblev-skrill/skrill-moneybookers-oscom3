<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;

// HPDL Should be moved to the customers class!
  $Qnewsletter = $OSCOM_PDO->prepare('select customers_newsletter from :table_customers where customers_id = :customers_id');
  $Qnewsletter->bindInt(':customers_id', $OSCOM_Customer->getID());
  $Qnewsletter->execute();
?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<form name="account_newsletter" action="<?php echo OSCOM::getLink(null, null, 'Newsletters&Process', 'SSL'); ?>" method="post">

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('newsletter_subscriptions_heading'); ?></h6>

  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="30"><?php echo HTML::checkboxField('newsletter_general', '1', $Qnewsletter->value('customers_newsletter')); ?></td>
        <td><b><?php echo HTML::label(OSCOM::getDef('newsletter_general'), 'newsletter_general'); ?></b></td>
      </tr>
      <tr>
        <td width="30">&nbsp;</td>
        <td><?php echo OSCOM::getDef('newsletter_general_description'); ?></td>
      </tr>
    </table>
  </div>
</div>

<div class="submitFormButtons" style="text-align: right;">
  <?php echo HTML::button(array('icon' => 'triangle-1-e', 'title' => OSCOM::getDef('button_continue'))); ?>
</div>

</form>
