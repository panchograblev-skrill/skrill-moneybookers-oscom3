<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;
?>

<!doctype html>

<html dir="<?php echo $OSCOM_Language->getTextDirection(); ?>" lang="<?php echo $OSCOM_Language->getCode(); ?>">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $OSCOM_Language->getCharacterSet(); ?>" />

<title><?php echo STORE_NAME . ($OSCOM_Template->hasPageTitle() ? ': ' . $OSCOM_Template->getPageTitle() : ''); ?></title>

<link rel="icon" type="image/png" href="<?php echo OSCOM::getPublicSiteLink('images/store_icon.png'); ?>" />

<meta name="generator" value="osCommerce Online Merchant" />

<script type="text/javascript" src="public/external/jquery/jquery-1.6.1.min.js"></script>

<link rel="stylesheet" type="text/css" href="public/external/jquery/ui/themes/start/jquery-ui-1.8.13.custom.css" />
<script type="text/javascript" src="public/external/jquery/ui/jquery-ui-1.8.13.custom.min.js"></script>

<link rel="stylesheet" type="text/css" href="<?php echo OSCOM::getPublicSiteLink('templates/oscom/stylesheets/general.css'); ?>" />

<?php
  if ( $OSCOM_Template->hasPageTags() ) {
    echo $OSCOM_Template->getPageTags();
  }

  if ($OSCOM_Template->hasJavascript()) {
    $OSCOM_Template->getJavascript();
  }
?>

</head>

<body>

<div id="pageBlockLeft">
  <div id="pageContent">

<?php
  if ( $OSCOM_MessageStack->exists('header') ) {
    echo $OSCOM_MessageStack->get('header');
  }

  if ( $OSCOM_Template->hasPageContentModules() ) {
    foreach ( $OSCOM_Service->getCallBeforePageContent() as $service ) {
      Registry::get($service[0])->$service[1]();
    }

    foreach ( $OSCOM_Template->getContentModules('before') as $content_module ) {
      $OSCOM_ContentModule = new $content_module();
      $OSCOM_ContentModule->initialize();

      if ( $OSCOM_ContentModule->hasContent() ) { // HPDL move logic elsewhere
        if ( $OSCOM_Template->getCode() == DEFAULT_TEMPLATE ) {
          include(OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::getSite() . '/Module/Content/' . $OSCOM_ContentModule->getCode() . '/pages/main.php');
        } else { //HPDL old
          if (file_exists('templates/' . $osC_Template->getCode() . '/modules/content/' . $osC_Box->getCode() . '.php')) {
            include('templates/' . $osC_Template->getCode() . '/modules/content/' . $osC_Box->getCode() . '.php');
          } else {
            include('templates/' . DEFAULT_TEMPLATE . '/modules/content/' . $osC_Box->getCode() . '.php');
          }
        }
      }

      unset($OSCOM_ContentModule);
    }
  }

  if ( $OSCOM_Template->getCode() == DEFAULT_TEMPLATE ) {
    include(OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::getSite() . '/Application/' . OSCOM::getSiteApplication() . '/pages/' . $OSCOM_Template->getPageContentsFilename());
  } else { // HPDL old
    if (file_exists('templates/' . $osC_Template->getCode() . '/content/' . $osC_Template->getGroup() . '/' . $osC_Template->getPageContentsFilename())) {
      include('templates/' . $osC_Template->getCode() . '/content/' . $osC_Template->getGroup() . '/' . $osC_Template->getPageContentsFilename());
    } else {
      include('templates/' . DEFAULT_TEMPLATE . '/content/' . $osC_Template->getGroup() . '/' . $osC_Template->getPageContentsFilename());
    }
  }
?>

<div style="clear: both;"></div>

<?php
  if ( $OSCOM_Template->hasPageContentModules() ) {
    foreach ( $OSCOM_Service->getCallAfterPageContent() as $service ) {
      Registry::get($service[0])->$service[1]();
    }

    foreach ( $OSCOM_Template->getContentModules('after') as $content_module ) {
      $OSCOM_ContentModule = new $content_module();
      $OSCOM_ContentModule->initialize();

      if ( $OSCOM_ContentModule->hasContent() ) { // HPDL move logic elsewhere
        if ( $OSCOM_Template->getCode() == DEFAULT_TEMPLATE ) {
          include(OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::getSite() . '/Module/Content/' . $OSCOM_ContentModule->getCode() . '/pages/main.php');
        } else { //HPDL old
          if (file_exists('templates/' . $osC_Template->getCode() . '/modules/content/' . $osC_Box->getCode() . '.php')) {
            include('templates/' . $osC_Template->getCode() . '/modules/content/' . $osC_Box->getCode() . '.php');
          } else {
            include('templates/' . DEFAULT_TEMPLATE . '/modules/content/' . $osC_Box->getCode() . '.php');
          }
        }
      }

      unset($OSCOM_ContentModule);
    }
  }
?>

  </div>

<?php
  $content_left = '';

  if ( $OSCOM_Template->hasPageBoxModules() ) {
    ob_start();

    foreach ( $OSCOM_Template->getBoxModules('left') as $box ) {
      $OSCOM_Box = new $box();
      $OSCOM_Box->initialize();

      if ( $OSCOM_Box->hasContent() ) { // HPDL move logic elsewhere
        if ( $OSCOM_Template->getCode() == DEFAULT_TEMPLATE ) {
          include(OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::getSite() . '/Module/Box/' . $OSCOM_Box->getCode() . '/pages/main.php');
        } else { //HPDL old
          if (file_exists('templates/' . $osC_Template->getCode() . '/modules/boxes/' . $osC_Box->getCode() . '.php')) {
            include('templates/' . $osC_Template->getCode() . '/modules/boxes/' . $osC_Box->getCode() . '.php');
          } else {
            include('templates/' . DEFAULT_TEMPLATE . '/modules/boxes/' . $osC_Box->getCode() . '.php');
          }
        }
      }

      unset($OSCOM_Box);
    }

    $content_left = ob_get_contents();
    ob_end_clean();
  }

  if ( !empty($content_left) ) {
?>

  <div id="pageColumnLeft">
    <div class="boxGroup">

<?php
    echo $content_left;
?>

    </div>
  </div>

<?php
  } else {
?>

<style type="text/css">
#pageContent {
  width: 99%;
  padding-left: 5px;
}
</style>

<?php
  }
?>

</div>

<?php
  $content_right = '';

  if ( $OSCOM_Template->hasPageBoxModules() ) {
    ob_start();

    foreach ( $OSCOM_Template->getBoxModules('right') as $box ) {
      $OSCOM_Box = new $box();
      $OSCOM_Box->initialize();

      if ( $OSCOM_Box->hasContent() ) { // HPDL move logic elsewhere
        if ( $OSCOM_Template->getCode() == DEFAULT_TEMPLATE ) {
          include(OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::getSite() . '/Module/Box/' . $OSCOM_Box->getCode() . '/pages/main.php');
        } else { //HPDL old
          if (file_exists('templates/' . $osC_Template->getCode() . '/modules/boxes/' . $osC_Box->getCode() . '.php')) {
            include('templates/' . $osC_Template->getCode() . '/modules/boxes/' . $osC_Box->getCode() . '.php');
          } else {
            include('templates/' . DEFAULT_TEMPLATE . '/modules/boxes/' . $osC_Box->getCode() . '.php');
          }
        }
      }

      unset($OSCOM_Box);
    }

    $content_right = ob_get_contents();
    ob_end_clean();
  }

  if (!empty($content_right)) {
?>

<div id="pageColumnRight">
  <div class="boxGroup">

<?php
    echo $content_right;
?>

  </div>
</div>

<?php
  } elseif (empty($content_left)) {
?>

<style type="text/css"><!--
#pageBlockLeft {
  width: 99%;
}
//--></style>

<?php
  } else {
?>

<style type="text/css"><!--
#pageContent {
  width: 82%;
  padding-right: 5px;
}

#pageBlockLeft {
  width: 99%;
}

#pageColumnLeft {
  width: 16%;
}
//--></style>

<?php
  }

  unset($content_left);
  unset($content_right);

  if ( $OSCOM_Template->hasPageHeader() ) {
?>

<div id="pageHeader">

<?php
    echo HTML::link(OSCOM::getLink(OSCOM::getDefaultSite(), OSCOM::getDefaultSiteApplication()), HTML::image(OSCOM::getPublicSiteLink('images/store_logo.png'), STORE_NAME), 'id="siteLogo"');
?>

  <div id="navigationIcons">

<?php
    echo HTML::button(array('title' => OSCOM::getDef('cart_contents') . ($OSCOM_ShoppingCart->numberOfItems() > 0 ? ' (' . $OSCOM_ShoppingCart->numberOfItems() . ')' : ''), 'icon' => 'cart', 'href' => OSCOM::getLink(null, 'Cart'))) .
         HTML::button(array('title' => OSCOM::getDef('checkout'), 'icon' => 'triangle-1-e', 'href' => OSCOM::getLink(null, 'Checkout', null, 'SSL'))) .
         HTML::button(array('title' => OSCOM::getDef('my_account'), 'icon' => 'person', 'href' => OSCOM::getLink(null, 'Account', null, 'SSL')));

    if ( $OSCOM_Customer->isLoggedOn() ) {
      echo HTML::button(array('title' => OSCOM::getDef('sign_out'), 'href' => OSCOM::getLink(null, 'Account', 'LogOff', 'SSL')));
    }
?>

  </div>

  <script type="text/javascript">
    $('#navigationIcons').buttonset();
  </script>

  <div id="navigationBar">

<?php
    if ( $OSCOM_Service->isStarted('Breadcrumb') ) {
?>

    <div id="breadcrumbPath" class="ui-widget">
      <div class="ui-widget-header">
        <span style="padding-left: 5px;">
<?php
      echo $OSCOM_Breadcrumb->getPath();
?>

        </span>
      </div>
    </div>

<?php
    }
?>

  </div>
</div>

<?php
  } // ($osC_Template->hasPageHeader())

  if ( $OSCOM_Template->hasPageFooter() ) {
?>

<div id="pageFooter">

<?php
    echo sprintf(OSCOM::getDef('footer'), date('Y'), OSCOM::getLink(), STORE_NAME);
?>

</div>

<?php
    if ( $OSCOM_Service->isStarted('banner') && $OSCOM_Banner->exists('468x60') ) {
      echo '<p align="center">' . $OSCOM_Banner->display() . '</p>';
    }
  }
?>

</body>

</html>
