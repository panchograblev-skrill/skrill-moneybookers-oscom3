<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\DateTime;
  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Shop\Reviews;

  $Qreviews = Reviews::getEntry($_GET['View']);
?>

<h1 style="float: right;"><?php echo $OSCOM_Product->getPriceFormated(true); ?></h1>

<h1><?php echo $OSCOM_Template->getPageTitle() . ($OSCOM_Product->hasModel() ? '<br /><span class="smallText">' . $OSCOM_Product->getModel() . '</span>' : ''); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists('Reviews') ) {
    echo $OSCOM_MessageStack->get('Reviews');
  }

  if ( $OSCOM_Product->hasImage() ) {
?>

<div style="float: right; text-align: center;">
  <?php echo HTML::link(OSCOM::getLink(null, null, 'Images&' . $OSCOM_Product->getKeyword()), $OSCOM_Image->show($OSCOM_Product->getImage(), $OSCOM_Product->getTitle(), 'hspace="5" vspace="5"', 'thumbnail'), 'target="_blank" onclick="window.open(\'' . OSCOM::getLink(null, null, 'Images&' . $OSCOM_Product->getKeyword()) . '\', \'popUp\', \'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=' . (($OSCOM_Product->numberOfImages() > 1) ? $OSCOM_Image->getWidth('large') + ($OSCOM_Image->getWidth('thumbnails') * 2) + 70 : $OSCOM_Image->getWidth('large') + 20) . ',height=' . ($OSCOM_Image->getHeight('large') + 20) . '\'); return false;"'); ?>
  <?php echo '<p>' . HTML::button(array('href' => OSCOM::getLink(null, 'Cart', 'Add&' . $OSCOM_Product->getKeyword()), 'icon' => 'cart', 'title' => OSCOM::getDef('button_add_to_cart'))) . '</p>'; ?>
</div>

<?php
  }
?>

<p><?php echo HTML::image(OSCOM::getPublicSiteLink('images/stars_' . $Qreviews->valueInt('reviews_rating') . '.png'), sprintf(OSCOM::getDef('rating_of_5_stars'), $Qreviews->valueInt('reviews_rating'))) . '&nbsp;' . sprintf(OSCOM::getDef('reviewed_by'), $Qreviews->valueProtected('customers_name')) . '; ' . DateTime::getLong($Qreviews->value('date_added')); ?></p>

<p><?php echo nl2br(wordwrap($Qreviews->valueProtected('reviews_text'), 60, '&shy;')); ?></p>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo HTML::button(array('href' => OSCOM::getLink(null, null, 'Reviews&Write&' . $OSCOM_Product->getKeyword()), 'icon' => 'pencil', 'title' => OSCOM::getDef('button_write_review'))); ?></span>

  <?php echo HTML::button(array('href' => OSCOM::getLink(null, null, 'Reviews&' . $OSCOM_Product->getKeyword()), 'icon' => 'triangle-1-w', 'title' => OSCOM::getDef('button_back'))); ?>
</div>
