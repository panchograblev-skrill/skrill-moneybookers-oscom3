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
  use osCommerce\OM\Core\PDO;
  use osCommerce\OM\Core\Site\Shop\Reviews;
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

  if ( $OSCOM_Product->getData('reviews_average_rating') > 0 ) {
?>

<p><?php echo OSCOM::getDef('average_rating') . ' ' . HTML::image(OSCOM::getPublicSiteLink('images/stars_' . $OSCOM_Product->getData('reviews_average_rating') . '.png'), sprintf(OSCOM::getDef('rating_of_5_stars'), $OSCOM_Product->getData('reviews_average_rating'))); ?></p>

<?php
  }

  $counter = 0;

  $reviews_listing = Reviews::getListing($OSCOM_Product->getID());

  foreach ( $reviews_listing['entries'] as $r ) {
    $counter++;

    if ( $counter > 1 ) {
?>

<hr style="height: 1px; width: 150px; text-align: left; margin-left: 0px" />

<?php
    }
?>

<p><?php echo HTML::image(OSCOM::getPublicSiteLink('images/stars_' . (int)$r['reviews_rating'] . '.png'), sprintf(OSCOM::getDef('rating_of_5_stars'), (int)$r['reviews_rating'])) . '&nbsp;' . sprintf(OSCOM::getDef('reviewed_by'), HTML::outputProtected($r['customers_name'])) . '; ' . DateTime::getLong($r['date_added']); ?></p>

<p><?php echo nl2br(wordwrap(HTML::outputProtected($r['reviews_text']), 60, '&shy;')); ?></p>

<?php
  }
?>

<div class="listingPageLinks">
  <span style="float: right;"><?php echo PDO::getBatchPageLinks('page', $reviews_listing['total'], OSCOM::getAllGET('page')); ?></span>

  <?php echo PDO::getBatchTotalPages(OSCOM::getDef('result_set_number_of_reviews'), (isset($_GET['page']) ? $_GET['page'] : 1), $reviews_listing['total']); ?>
</div>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo HTML::button(array('href' => OSCOM::getLink(null, null, 'Reviews&Write&' . $OSCOM_Product->getKeyword()), 'icon' => 'pencil', 'title' => OSCOM::getDef('button_write_review'))); ?></span>

  <?php echo HTML::button(array('href' => OSCOM::getLink(null, null, $OSCOM_Product->getKeyword()), 'icon' => 'triangle-1-w', 'title' => OSCOM::getDef('button_back'))); ?>
</div>
