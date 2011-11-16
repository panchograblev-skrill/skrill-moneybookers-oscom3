<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\Access;
  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;
?>

<!doctype html>

<html dir="<?php echo $OSCOM_Language->getTextDirection(); ?>" lang="<?php echo $OSCOM_Language->getCode(); ?>">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $OSCOM_Language->getCharacterSet(); ?>" />

<title><?php echo STORE_NAME . ': ' . OSCOM::getDef('administration_title') . ($OSCOM_Template->hasPageTitle() ? ': ' . $OSCOM_Template->getPageTitle() : ''); ?></title>

<link rel="icon" type="image/png" href="<?php echo OSCOM::getPublicSiteLink('images/oscommerce_icon.png'); ?>" />

<meta name="generator" value="osCommerce Online Merchant" />
<meta name="robots" content="noindex,nofollow" />

<script type="text/javascript" src="public/external/jquery/jquery-1.6.1.min.js"></script>
<script type="text/javascript" src="public/external/jquery/jquery.cookie.js"></script>
<script type="text/javascript" src="public/external/jquery/jquery.json-2.2.min.js"></script>
<script type="text/javascript" src="public/external/jquery/jquery.tinysort.min.js"></script>
<script type="text/javascript" src="public/external/jquery/jquery.ocupload-1.1.2.packed.js"></script>
<script type="text/javascript" src="public/external/jquery/jquery.hoverIntent.minified.js"></script>
<script type="text/javascript" src="public/external/jquery/jquery.placeholder.min.js"></script>
<script type="text/javascript" src="public/external/jquery/jquery.droppy.js"></script>
<script type="text/javascript" src="public/external/jquery/jquery.blockUI.js"></script>
<script type="text/javascript" src="public/external/jquery/jquery.md5.js"></script>

<script type="text/javascript" src="public/external/jquery/tipsy/jquery.tipsy.js"></script>
<link rel="stylesheet" type="text/css" href="public/external/jquery/tipsy/tipsy.css" />

<script src="public/external/jquery/jquery.netchanger.min.js"></script>
<script src="public/external/jquery/jquery.safetynet.js"></script>

<script src="public/sites/Admin/javascript/jquery/jquery.buttonsetTabs.js"></script>
<script src="public/sites/Admin/javascript/jquery/jquery.equalResize.js"></script>
<script src="public/sites/Admin/javascript/jquery/jquery.imageSelector.js"></script>

<link rel="stylesheet" type="text/css" href="public/external/fileuploader/fileuploader.css" />
<script src="public/external/fileuploader/fileuploader.min.js"></script>

<link rel="stylesheet" type="text/css" href="public/external/jquery/ui/themes/smoothness/jquery-ui-1.8.13.custom.css" />
<script type="text/javascript" src="public/external/jquery/ui/jquery-ui-1.8.13.custom.min.js"></script>

<script type="text/javascript" src="public/external/alexei/sprintf.js"></script>

<script type="text/javascript" src="<?php echo OSCOM::getPublicSiteLink('javascript/general.js'); ?>"></script>
<script type="text/javascript" src="<?php echo OSCOM::getPublicSiteLink('javascript/datatable.js'); ?>"></script>

<link rel="stylesheet" type="text/css" href="<?php echo OSCOM::getPublicSiteLink('templates/oscom/stylesheets/general.css'); ?>" />

<script type="text/javascript">
  var pageURL = '<?php echo OSCOM::getLink(); ?>';
  var pageModule = '<?php echo OSCOM::getSiteApplication(); ?>';

  var batchSize = parseInt('<?php echo MAX_DISPLAY_SEARCH_RESULTS; ?>');
  var batchTotalPagesText = '<?php echo addslashes(OSCOM::getDef('batch_results_number_of_entries')); ?>';
  var batchCurrentPageset = '<?php echo addslashes(OSCOM::getDef('result_set_current_page')); ?>';
  var batchIconNavigationBack = '<?php echo HTML::icon('nav_back.png'); ?>';
  var batchIconNavigationBackGrey = '<?php echo HTML::icon('nav_back_grey.png'); ?>';
  var batchIconNavigationForward = '<?php echo HTML::icon('nav_forward.png'); ?>';
  var batchIconNavigationForwardGrey = '<?php echo HTML::icon('nav_forward_grey.png'); ?>';
  var batchIconNavigationReload = '<?php echo HTML::icon('reload.png'); ?>';
  var batchIconProgress = '<?php echo HTML::icon('progress_ani.gif'); ?>';

  var taxDecimalPlaces = parseInt('<?php echo TAX_DECIMAL_PLACES; ?>');
</script>

<meta name="application-name" content="osCommerce Dashboard" />
<meta name="msapplication-tooltip" content="osCommerce Administration Dashboard" />
<meta name="msapplication-window" content="width=1024;height=768" />
<meta name="msapplication-navbutton-color" content="#ff7900" />
<meta name="msapplication-starturl" content="<?php echo OSCOM::getLink(null, OSCOM::getDefaultSiteApplication(), null, 'SSL', false); ?>" />

</head>

<body>

<?php
  if ( $OSCOM_Template->hasPageHeader() ) {
    include($OSCOM_Template->getTemplateFile('header.php'));
  }
?>

<div id="appContent">

<?php
  if ( Registry::get('MessageStack')->exists('header') ) {
    echo Registry::get('MessageStack')->get('header');
  }

  require($OSCOM_Template->getPageContentsFile());
?>

</div>

<?php
  if ( $OSCOM_Template->hasPageFooter() ) {
?>

<div id="footer">
  <?php include($OSCOM_Template->getTemplateFile('footer.php')); ?>
</div>

<?php
  }
?>

</body>

</html>
