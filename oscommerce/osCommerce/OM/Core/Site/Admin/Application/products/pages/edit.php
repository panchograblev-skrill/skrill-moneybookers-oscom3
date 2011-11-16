<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<script type="text/javascript" src="../ext/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
tinyMCE.init({
  mode : "none",
  theme : "advanced",
  language : "<?php echo substr($osC_Language->getCode(), 0, 2); ?>",
  height : "400",
  theme_advanced_toolbar_align : "left",
  theme_advanced_toolbar_location : "top",
  theme_advanced_statusbar_location : "bottom",
  cleanup : false,
  plugins : "style,layer,table,advimage,advlink,preview,contextmenu,paste,fullscreen,visualchars",
  theme_advanced_buttons1 : "bold,italic,underline,strikethrough,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,formatselect,fontselect,fontsizeselect,bullist,numlist,separator,outdent,indent",
  theme_advanced_buttons2 : "undo,redo,separator,link,unlink,anchor,image,code,separator,preview,separator,forecolor,backcolor,tablecontrols,separator,hr,removeformat,visualaid",
  theme_advanced_buttons3 : "sub,sup,separator,charmap,fullscreen,separator,insertlayer,moveforward,movebackward,absolute,|,styleprops,|,visualchars"
});

function toggleEditor(id) {
  if ( !tinyMCE.get(id) ) {
    tinyMCE.execCommand('mceAddControl', false, id);
  } else {
    tinyMCE.execCommand('mceRemoveControl', false, id);
  }
}
</script>

<?php
  if ( is_numeric($_GET[$osC_Template->getModule()]) ) {
    $osC_ObjectInfo = new osC_ObjectInfo(osC_Products_Admin::get($_GET[$osC_Template->getModule()]));
    $attributes = $osC_ObjectInfo->get('attributes');

    $Qpd = $osC_Database->query('select products_name, products_description, products_keyword, products_tags, products_url, language_id from :table_products_description where products_id = :products_id');
    $Qpd->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
    $Qpd->bindInt(':products_id', $osC_ObjectInfo->getInt('products_id'));
    $Qpd->execute();

    $products_name = array();
    $products_description = array();
    $products_keyword = array();
    $products_tags = array();
    $products_url = array();

    while ($Qpd->next()) {
      $products_name[$Qpd->valueInt('language_id')] = $Qpd->value('products_name');
      $products_description[$Qpd->valueInt('language_id')] = $Qpd->value('products_description');
      $products_keyword[$Qpd->valueInt('language_id')] = $Qpd->value('products_keyword');
      $products_tags[$Qpd->valueInt('language_id')] = $Qpd->value('products_tags');
      $products_url[$Qpd->valueInt('language_id')] = $Qpd->value('products_url');
    }
  }

  $Qtc = $osC_Database->query('select tax_class_id, tax_class_title from :table_tax_class order by tax_class_title');
  $Qtc->bindTable(':table_tax_class', TABLE_TAX_CLASS);
  $Qtc->execute();

  $tax_class_array = array(array('id' => '0',
                                 'text' => $osC_Language->get('none')));

  while ($Qtc->next()) {
    $tax_class_array[] = array('id' => $Qtc->valueInt('tax_class_id'),
                               'text' => $Qtc->value('tax_class_title'));
  }

  $Qwc = $osC_Database->query('select weight_class_id, weight_class_title from :table_weight_class where language_id = :language_id order by weight_class_title');
  $Qwc->bindTable(':table_weight_class', TABLE_WEIGHT_CLASS);
  $Qwc->bindInt(':language_id', $osC_Language->getID());
  $Qwc->execute();

  $weight_class_array = array();

  while ($Qwc->next()) {
    $weight_class_array[] = array('id' => $Qwc->valueInt('weight_class_id'),
                                  'text' => $Qwc->value('weight_class_title'));
  }
?>

<style type="text/css"><!--
.attributeAdd {
  background-color: #F0F1F1;
  margin: 2px;
}

.variantActive {
  background-color: #E8FFC6;
  margin: 2px;
}
//--></style>

<script type="text/javascript"><!--
  var tax_rates = new Array();

<?php
  foreach ($tax_class_array as $tc_entry) {
    if ( $tc_entry['id'] > 0 ) {
      echo '  tax_rates["' . $tc_entry['id'] . '"] = ' . $osC_Tax->getTaxRate($tc_entry['id']) . ';' . "\n";
    }
  }
?>

  function doRound(x, places) {
    return Math.round(x * Math.pow(10, places)) / Math.pow(10, places);
  }

  function getTaxRate(fieldcounter) {
    var selected_value = document.getElementById('tax_class' + fieldcounter).selectedIndex;
    var parameterVal = document.getElementById('tax_class' + fieldcounter).options[selected_value].value;

    if ( (parameterVal > 0) && (tax_rates[parameterVal] > 0) ) {
      return tax_rates[parameterVal];
    } else {
      return 0;
    }
  }

  function updateGross(field) {
    var fieldcounter = field.substring(14);

    var taxRate = getTaxRate(fieldcounter);
    var grossValue = document.getElementById(field).value;

    if (taxRate > 0) {
      grossValue = grossValue * ((taxRate / 100) + 1);
    }

    document.getElementById(field + '_gross').value = doRound(grossValue, 4);
  }

  function updateNet(field) {
    var fieldcounter = field.substring(14);

    var taxRate = getTaxRate(fieldcounter);
    var netValue = document.getElementById(field + "_gross").value;

    if (taxRate > 0) {
      netValue = netValue / ((taxRate / 100) + 1);
    }

    document.getElementById(field).value = doRound(netValue, 4);
  }

  var variants_groups = new Array();
  var variants_values = new Array();

<?php
  $Qvgroups = $osC_Database->query('select id, title, module from :table_products_variants_groups where languages_id = :languages_id order by sort_order, title');
  $Qvgroups->bindTable(':table_products_variants_groups', TABLE_PRODUCTS_VARIANTS_GROUPS);
  $Qvgroups->bindInt(':languages_id', $osC_Language->getID());
  $Qvgroups->execute();

  while ( $Qvgroups->next() ) {
    echo 'variants_groups[' . $Qvgroups->valueInt('id') . '] = new Array();' .
         'variants_groups[' . $Qvgroups->valueInt('id') . '][\'title\'] = \'' . $Qvgroups->valueProtected('title') . '\';' .
         'variants_groups[' . $Qvgroups->valueInt('id') . '][\'multiple\'] = ' . (osC_Variants::allowsMultipleValues($Qvgroups->value('module')) ? 'true' : 'false') . ';';
  }

  $Qvvalues = $osC_Database->query('select id, title from :table_products_variants_values where languages_id = :languages_id order by sort_order, title');
  $Qvvalues->bindTable(':table_products_variants_values', TABLE_PRODUCTS_VARIANTS_VALUES);
  $Qvvalues->bindInt(':languages_id', $osC_Language->getID());
  $Qvvalues->execute();

  while ($Qvvalues->next()) {
    echo 'variants_values[' . $Qvvalues->valueInt('id') . '] = \'' . $Qvvalues->valueProtected('title') . '\';';
  }
?>

  var variants = new Array();
  var variants_default_combo = null;
  var variant_selected = null;
  var variants_counter = 1;

  function moreFields() {
    if (variant_selected == null) {
      addVariant();
    }

    if (variants[variant_selected][document.product.variantGroups.options[document.product.variantGroups.options.selectedIndex].parentNode.id] == undefined) {
      variants[variant_selected][document.product.variantGroups.options[document.product.variantGroups.options.selectedIndex].parentNode.id] = new Array();
    }

    if (variants_groups[document.product.variantGroups.options[document.product.variantGroups.options.selectedIndex].parentNode.id]['multiple'] == false) {
      variants[variant_selected][document.product.variantGroups.options[document.product.variantGroups.options.selectedIndex].parentNode.id] = new Array();
    }

    variants[variant_selected][document.product.variantGroups.options[document.product.variantGroups.options.selectedIndex].parentNode.id][document.product.variantGroups.options[document.product.variantGroups.options.selectedIndex].value] = document.product.variantGroups.options[document.product.variantGroups.options.selectedIndex].value;

    var spanFields = document.getElementById('variant' + variant_selected).getElementsByTagName('span');

    var variant_string = '';
    var variant_combo_string = '';

    for (i=0; i<variants[variant_selected].length; i++) {
      if (variants[variant_selected][i] != undefined) {
        for (y=0; y<variants[variant_selected][i].length; y++) {
          if (variants[variant_selected][i][y] != undefined) {
            variant_string += variants_groups[i]['title'] + ': ' + variants_values[variants[variant_selected][i][y]] + ', ';
            variant_combo_string += i + '_' + variants[variant_selected][i][y] + ';';
          }
        }
      }
    }

    if (variant_string != '') {
      variant_string = variant_string.substring(0, variant_string.length-2);
      variant_combo_string = variant_combo_string.substring(0, variant_combo_string.length-1);
    }

    spanFields[0].innerHTML = '<?php echo osc_icon('attach.png') . '&nbsp;'; ?>' + variant_string;

    document.getElementById('variants_combo_' + variant_selected).value = variant_combo_string;
  }

  function addVariant() {
    if ( variants_values.length < 1 ) {
      return false;
    }

    var newFields = document.getElementById('readroot').cloneNode(true);
    newFields.id = 'variant' + variants_counter;

    var vp_holder = 'variants_price' + variants_counter;

    var aFields = newFields.getElementsByTagName('a');
    var inputFields = newFields.getElementsByTagName('input');
    var selectFields = newFields.getElementsByTagName('select');
    var images = newFields.getElementsByTagName('img');

    for (y=0; y<aFields.length; y++) {
      if (aFields[y].name == 'trash') {
        aFields[y].href = "javascript:removeVariant('variant" + variants_counter + "');";
      } else if (aFields[y].name == 'default') {
        aFields[y].href = "javascript:setDefaultVariant('" + variants_counter + "');";
      }
    }

    for (y=0; y<inputFields.length; y++) {
      if (inputFields[y].name == 'new_variants_price') {
        inputFields[y].id = inputFields[y].name.substr(4) + variants_counter;
        inputFields[y].onkeyup = function() { updateGross(vp_holder) };
      } else if (inputFields[y].name == 'new_variants_price_gross') {
        inputFields[y].id = 'variants_price' + variants_counter + '_gross';
        inputFields[y].onkeyup = function() { updateNet(vp_holder) };
      } else {
        inputFields[y].id = inputFields[y].name.substr(4) + '_' + variants_counter;
      }

      inputFields[y].name = inputFields[y].name.substr(4) + '[' + variants_counter + ']';
      inputFields[y].disabled = false;
    }

    for (y=0; y<selectFields.length; y++) {
      if (selectFields[y].name == 'new_variants_tax_class_id') {
        selectFields[y].id = 'tax_class' + variants_counter;
        selectFields[y].onchange = function() { updateGross(vp_holder) };
      } else {
        selectFields[y].id = selectFields[y].name.substr(4) + '_' + variants_counter;
      }

      selectFields[y].name = selectFields[y].name.substr(4) + '[' + variants_counter + ']';
      selectFields[y].disabled = false;
    }

    for (y=0; y<images.length; y++) {
      if (images[y].name == 'vdcnew') {
        images[y].id = 'vdc' + variants_counter;
      }
    }

    document.getElementById('writeroot').insertBefore(newFields, document.getElementById('writeroot').firstChild);

    newFields.className = 'variantActive';

    if (variant_selected != null) {
      document.getElementById('variant' + variant_selected).className = 'attributeAdd';
    }

    if (variants_default_combo == null) {
      setDefaultVariant(variants_counter);
    }

    newFields.style.display = 'block';

    variant_selected = variants_counter;

    variants[variant_selected] = new Array();

    variants_counter++;
  }

  var being_removed = false;

  function removeVariant(id) {
    being_removed = true;

    var to_remove = id.substr(7);

    document.getElementById('writeroot').removeChild(document.getElementById(id));

    if (to_remove == variant_selected) {
      variant_selected = null;
    }
  }

  function activateVariant(element) {
    if (being_removed == true) {
      being_removed = false;
      return true;
    }

    var to_activate = element.id.substr(7);

    if (to_activate != variant_selected) {
      if (variant_selected != null) {
        document.getElementById('variant' + variant_selected).className = 'attributeAdd';
      }

      element.className = 'variantActive';
      variant_selected = to_activate;
    }
  }

  function setDefaultVariant(id) {
    if ( id != variants_default_combo ) {
      document.getElementById('variants_default_combo').value = id;

      document.getElementById('vdc' + id).src = "<?php echo osc_icon_raw('default.png'); ?>";

      if (variants_default_combo != null) {
        document.getElementById('vdc' + variants_default_combo).src = "<?php echo osc_icon_raw('default_grey.png'); ?>";
      }

      variants_default_combo = id;
    }
  }

<?php
  if ( isset($osC_ObjectInfo) ) {
?>

  function removeImage(id) {
    $('#deleteImageDialog').dialog('option', 'buttons', {
      "Cancel": function() {
        $(this).dialog("close");
      },
      "Ok": function() {
        var image = id.split('_');

        $.getJSON('<?php echo osc_href_link_admin('rpc.php', $osC_Template->getModule() . '=' . $osC_ObjectInfo->getInt('products_id') . '&action=deleteProductImage'); ?>' + '&image=' + image[1],
          function (data) {
            getImages();
          }
        );

        $(this).dialog("close");
      }
    } );

    $('#deleteImageDialog').dialog('open');
  }

  function setDefaultImage(id) {
    var image = id.split('_');

    $.getJSON('<?php echo osc_href_link_admin('rpc.php', $osC_Template->getModule() . '=' . $osC_ObjectInfo->getInt('products_id') . '&action=setDefaultImage'); ?>' + '&image=' + image[1],
      function (data) {
        getImagesOriginals();
      }
    );
  }

  function showImages(data) {
    for ( i=0; i<data.entries.length; i++ ) {
      var entry = data.entries[i];

      var style = 'width: <?php echo $osC_Image->getWidth('mini') + 20; ?>px; padding: 10px; float: left; text-align: center;';

      if ( entry[1] == '1' ) { // original (products_images_groups_id)
        var onmouseover = 'this.style.backgroundColor=\'#EFEBDE\'; this.style.backgroundImage=\'url(<?php echo osc_href_link_admin('templates/' . $osC_Template->getCode() . '/images/icons/16x16/drag.png'); ?>)\'; this.style.backgroundRepeat=\'no-repeat\'; this.style.backgroundPosition=\'0 0\';';

        if ( entry[6] == '1' ) { // default_flag
          style += ' background-color: #E5EFE5;';

          var onmouseout = 'this.style.backgroundColor=\'#E5EFE5\'; this.style.backgroundImage=\'none\';';
        } else {
          var onmouseout = 'this.style.backgroundColor=\'#FFFFFF\'; this.style.backgroundImage=\'none\';';
        }
      } else {
        var onmouseover = 'this.style.backgroundColor=\'#EFEBDE\';';
        var onmouseout = 'this.style.backgroundColor=\'#FFFFFF\';';
      }

      var newdiv = '<span id="image_' + entry[0] + '" style="' + style + '" onmouseover="' + onmouseover + '" onmouseout="' + onmouseout + '">';
      newdiv += '<a href="' + entry[4] + '" target="_blank"><img src="<?php echo DIR_WS_HTTP_CATALOG . 'images/products/mini/'; ?>' + entry[2] + '" border="0" height="<?php echo $osC_Image->getHeight('mini'); ?>" alt="' + entry[2] + '" title="' + entry[2] + '" style="max-width: <?php echo $osC_Image->getWidth('mini') + 20; ?>px;" /></a><br />' + entry[3] + '<br />' + entry[5] + ' bytes<br />';

      if ( entry[1] == '1' ) {
        if ( entry[6] == '1' ) {
          newdiv += '<?php echo osc_icon('default.png'); ?>&nbsp;';
        } else {
          newdiv += '<a href="#" onclick="setDefaultImage(\'image_' + entry[0] + '\');"><?php echo osc_icon('default_grey.png'); ?></a>&nbsp;';
        }

        newdiv += '<a href="#" onclick="removeImage(\'image_' + entry[0] + '\');"><?php echo osc_icon('trash.png'); ?></a>';
      }

      newdiv += '</span>';

      if ( entry[1] == '1' ) {
        $('#imagesOriginal').append(newdiv);
      } else {
        $('#imagesOther').append(newdiv);
      }
    }

    $('#imagesOriginal').sortable( {
      update: function(event, ui) {
        $.getJSON('<?php echo osc_href_link_admin('rpc.php', $osC_Template->getModule() . '=' . $osC_ObjectInfo->getInt('products_id') . '&action=reorderImages'); ?>' + '&' + $(this).sortable('serialize'),
          function (data) {
            getImagesOthers();
          }
        );
      }
    } );

    if ( $('#showProgressOriginal').css('display') != 'none') {
      $('#showProgressOriginal').css('display', 'none');
    }

    if ( $('#showProgressOther').css('display') != 'none') {
      $('#showProgressOther').css('display', 'none');
    }
  }

  function getImages() {
    getImagesOriginals(false);
    getImagesOthers(false);

    $.getJSON('<?php echo osc_href_link_admin('rpc.php', $osC_Template->getModule() . '=' . $osC_ObjectInfo->getInt('products_id') . '&action=getImages'); ?>',
      function (data) {
        showImages(data);
      }
    );
  }

  function getImagesOriginals(makeCall) {
    $('#imagesOriginal').html('<div id="showProgressOriginal" style="float: left; padding-left: 10px;"><?php echo osc_icon('progress_ani.gif') . '&nbsp;' . $osC_Language->get('images_loading_from_server'); ?></div>');

    if ( makeCall != false ) {
      $.getJSON('<?php echo osc_href_link_admin('rpc.php', $osC_Template->getModule() . '=' . $osC_ObjectInfo->getInt('products_id') . '&action=getImages&filter=originals'); ?>',
        function (data) {
          showImages(data);
        }
      );
    }
  }

  function getImagesOthers(makeCall) {
    $('#imagesOther').html('<div id="showProgressOther" style="float: left; padding-left: 10px;"><?php echo osc_icon('progress_ani.gif') . '&nbsp;' . $osC_Language->get('images_loading_from_server'); ?></div>');

    if ( makeCall != false ) {
      $.getJSON('<?php echo osc_href_link_admin('rpc.php', $osC_Template->getModule() . '=' . $osC_ObjectInfo->getInt('products_id') . '&action=getImages&filter=others'); ?>',
        function (data) {
          showImages(data);
        }
      );
    }
  }

  function assignLocalImages() {
    $('#showProgressAssigningLocalImages').css('display', 'inline');

    var selectedFiles = '';

    $('#localImagesSelection :selected').each(function(i, selected) {
      selectedFiles += 'files[]=' + $(selected).text() + '&';
    });

    $.getJSON('<?php echo osc_href_link_admin('rpc.php', $osC_Template->getModule() . '=' . $osC_ObjectInfo->getInt('products_id') . '&action=assignLocalImages'); ?>' + '&' + selectedFiles,
      function (data) {
        $('#showProgressAssigningLocalImages').css('display', 'none');
        getLocalImages();
        getImages();
      }
    );
  }

<?php
  }
?>

  function getLocalImages() {
    $('#showProgressGetLocalImages').css('display', 'inline');

    $.getJSON('<?php echo osc_href_link_admin('rpc.php', $osC_Template->getModule() . '&action=getLocalImages'); ?>',
      function (data) {
        var i = 0;
        var selectList = document.getElementById('localImagesSelection');

        for ( i=selectList.options.length; i>=0; i-- ) {
          selectList.options[i] = null;
        }

        for ( i=0; i<data.entries.length; i++ ) {
          selectList.options[i] = new Option(data.entries[i]);
          selectList.options[i].selected = false;
        }

        $('#showProgressGetLocalImages').css('display', 'none');
      }
    );
  }

  function switchImageFilesView(layer) {
    if (layer == 'local') {
      var layer1 = document.getElementById('remoteFiles');
      var layer1link = document.getElementById('remoteFilesLink');
      var layer2 = document.getElementById('localFiles');
      var layer2link = document.getElementById('localFilesLink');
    } else {
      var layer1 = document.getElementById('localFiles');
      var layer1link = document.getElementById('localFilesLink');
      var layer2 = document.getElementById('remoteFiles');
      var layer2link = document.getElementById('remoteFilesLink');
    }

    if ( (layer != 'local') || ((layer == 'local') && (layer1.style.display != 'none')) ) {
      layer1.style.display='none';
      layer2.style.display='inline';
      layer1link.style.backgroundColor='';
      layer2link.style.backgroundColor='#E5EFE5';
    } else {
      getLocalImages();
    }
  }
//--></script>

<div id="deleteImageDialog" title="<?php echo $osC_Language->get('action_heading_delete_image'); ?>"><p><?php echo $osC_Language->get('introduction_delete_image'); ?></p></div>

<script type="text/javascript">
  $(document).ready(function() {
    $('#deleteImageDialog').dialog( {
      autoOpen: false,
      width: 600,
      modal: true
    } );
  });
</script>

<h1><?php echo (isset($osC_ObjectInfo) && isset($products_name[$osC_Language->getID()])) ? $products_name[$osC_Language->getID()] : $osC_Language->get('heading_title_new_product'); ?></h1>

<?php
  if ( $osC_MessageStack->exists($osC_Template->getModule()) ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<script type="text/javascript">
$(document).ready(function(){
  $("#mainTabs").tabs( { selected: 0 } );
  $("#languageTabs").tabs( { selected: 0 } );
});
</script>

<form name="product" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . (isset($osC_ObjectInfo) ? $osC_ObjectInfo->getInt('products_id') : '') . '&cID=' . $_GET['cID'] . '&action=save'); ?>" method="post" enctype="multipart/form-data">

<div id="mainTabs">
  <ul>
    <li><?php echo osc_link_object('#section_general_content', $osC_Language->get('section_general')); ?></li>
    <li><?php echo osc_link_object('#section_data_content', $osC_Language->get('section_data')); ?></li>
    <li><?php echo osc_link_object('#section_images_content', $osC_Language->get('section_images')); ?></li>
    <li><?php echo osc_link_object('#section_variants_content', $osC_Language->get('section_variants')); ?></li>
    <li><?php echo osc_link_object('#section_categories_content', $osC_Language->get('section_categories')); ?></li>
  </ul>

  <div id="section_general_content">
    <div id="languageTabs">
      <ul>

<?php
  foreach ( $osC_Language->getAll() as $l ) {
    echo '<li>' . osc_link_object('#languageTabs_' . $l['code'], $osC_Language->showImage($l['code']) . '&nbsp;' . $l['name']) . '</li>';
  }
?>

      </ul>

<?php
  foreach ( $osC_Language->getAll() as $l ) {
?>

      <div id="languageTabs_<?php echo $l['code']; ?>">
        <fieldset>
          <div><label for="<?php echo 'products_name[' . $l['id'] . ']'; ?>"><?php echo $osC_Language->get('field_name'); ?></label><?php echo osc_draw_input_field('products_name[' . $l['id'] . ']', (isset($osC_ObjectInfo) && isset($products_name[$l['id']]) ? $products_name[$l['id']] : null)); ?></div>
          <div><label for="<?php echo 'products_description[' . $l['id'] . ']'; ?>"><?php echo $osC_Language->get('field_description'); ?></label><?php echo osc_draw_textarea_field('products_description[' . $l['id'] . ']', (isset($osC_ObjectInfo) && isset($products_description[$l['id']]) ? $products_description[$l['id']] : null)); ?><div style="width: 58.5%; text-align: right;"><?php echo '<a href="javascript:toggleEditor(\'products_description[' . $l['id'] . ']\');">' . $osC_Language->get('toggle_html_editor') . '</a>'; ?></div></div>
          <div><label for="<?php echo 'products_keyword[' . $l['id'] . ']'; ?>"><?php echo $osC_Language->get('field_keyword'); ?></label><?php echo osc_draw_input_field('products_keyword[' . $l['id'] . ']', (isset($osC_ObjectInfo) && isset($products_keyword[$l['id']]) ? $products_keyword[$l['id']] : null)); ?></div>
          <div><label for="<?php echo 'products_tags[' . $l['id'] . ']'; ?>"><?php echo $osC_Language->get('field_tags'); ?></label><?php echo osc_draw_input_field('products_tags[' . $l['id'] . ']', (isset($osC_ObjectInfo) && isset($products_tags[$l['id']]) ? $products_tags[$l['id']] : null)); ?></div>
          <div><label for="<?php echo 'products_url[' . $l['id'] . ']'; ?>"><?php echo $osC_Language->get('field_url'); ?></label><?php echo osc_draw_input_field('products_url[' . $l['id'] . ']', (isset($osC_ObjectInfo) && isset($products_url[$l['id']]) ? $products_url[$l['id']] : null)); ?></div>
        </fieldset>
      </div>

<?php
  }
?>

    </div>
  </div>

  <div id="section_data_content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>

<?php
  $data_width = ( isset($osC_ObjectInfo) && ((int)$osC_ObjectInfo->get('has_children') === 1) ) ? '100%' : '50%';

  if ( !isset($osC_ObjectInfo) || (isset($osC_ObjectInfo) && ($osC_ObjectInfo->getInt('has_children') !== 1)) ) {
?>

        <td width="<?php echo $data_width;?>" height="100%" valign="top">
          <fieldset style="height: 100%;">
            <legend><?php echo $osC_Language->get('subsection_price'); ?></legend>

            <div><label for="tax_class0"><?php echo $osC_Language->get('field_tax_class'); ?></label><?php echo osc_draw_pull_down_menu('products_tax_class_id', $tax_class_array, (isset($osC_ObjectInfo) ? $osC_ObjectInfo->getInt('products_tax_class_id') : null), 'id="tax_class0" onchange="updateGross(\'products_price0\');"'); ?></div>
            <div><label for="products_price0"><?php echo $osC_Language->get('field_price_net'); ?></label><?php echo osc_draw_input_field('products_price', (isset($osC_ObjectInfo) ? $osC_ObjectInfo->get('products_price') : null), 'id="products_price0" onkeyup="updateGross(\'products_price0\')"'); ?></div>
            <div><label for="products_price0_gross"><?php echo $osC_Language->get('field_price_gross'); ?></label><?php echo osc_draw_input_field('products_price_gross', (isset($osC_ObjectInfo) ? $osC_ObjectInfo->get('products_price') : null), 'id="products_price0_gross" onkeyup="updateNet(\'products_price0\')"'); ?></div>
          </fieldset>

<script type="text/javascript"><!--
  updateGross('products_price0');
//--></script>
        </td>

<?php
  }
?>

        <td width="<?php echo $data_width;?>" height="100%" valign="top">
          <fieldset style="height: 100%;">
            <legend><?php echo $osC_Language->get('subsection_data'); ?></legend>

            <div><label for="products_status"><?php echo $osC_Language->get('field_status'); ?></label><?php echo osc_draw_radio_field('products_status', array(array('id' => '1', 'text' => $osC_Language->get('status_enabled')), array('id' => '0', 'text' => $osC_Language->get('status_disabled'))), (isset($osC_ObjectInfo) ? $osC_ObjectInfo->get('products_status') : '0')); ?></div>

<?php
  if ( isset($osC_ObjectInfo) && ($osC_ObjectInfo->getInt('has_children') !== 1) ) {
?>

            <div><label for="products_model"><?php echo $osC_Language->get('field_model'); ?></label><?php echo osc_draw_input_field('products_model', (isset($osC_ObjectInfo) ? $osC_ObjectInfo->get('products_model') : null)); ?></div>
            <div><label for="products_quantity"><?php echo $osC_Language->get('field_quantity'); ?></label><?php echo osc_draw_input_field('products_quantity', (isset($osC_ObjectInfo) ? $osC_ObjectInfo->get('products_quantity') : null)); ?></div>
            <div><label for="products_weight"><?php echo $osC_Language->get('field_weight'); ?></label><?php echo osc_draw_input_field('products_weight', (isset($osC_ObjectInfo) ? $osC_ObjectInfo->get('products_weight') : null)) . osc_draw_pull_down_menu('products_weight_class', $weight_class_array, (isset($osC_ObjectInfo) ? $osC_ObjectInfo->get('products_weight_class') : SHIPPING_WEIGHT_UNIT)); ?></div>

<?php
  }
?>

          </fieldset>
        </td>
      </tr>
    </table>

<?php
  if ( isset($osC_ObjectInfo) && ($osC_ObjectInfo->getInt('has_children') === 1) ) {
    echo osc_draw_hidden_field('products_tax_class_id', 0) . osc_draw_hidden_field('products_price', 0) . osc_draw_hidden_field('products_model') . osc_draw_hidden_field('products_quantity', 0), osc_draw_hidden_field('products_weight', 0), osc_draw_hidden_field('products_weight_class', 0);
  }
?>

    <fieldset>
      <legend>Attributes</legend>

      <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
  $Qattributes = $osC_Database->query('select id, code from :table_templates_boxes where modules_group = :modules_group order by code');
  $Qattributes->bindTable(':table_templates_boxes', TABLE_TEMPLATES_BOXES);
  $Qattributes->bindValue(':modules_group', 'product_attributes');
  $Qattributes->execute();

  while ( $Qattributes->next() ) {
    $module = basename($Qattributes->value('code'));

    if ( !class_exists('osC_ProductAttributes_' . $module) ) {
      if ( file_exists(DIR_FS_CATALOG . 'admin/includes/modules/product_attributes/' . $module . '.php') ) {
        include(DIR_FS_CATALOG . 'admin/includes/modules/product_attributes/' . $module . '.php');
      }
    }

    if ( class_exists('osC_ProductAttributes_' . $module) ) {
      $module = 'osC_ProductAttributes_' . $module;
      $module = new $module();
?>

        <tr>
          <td width="100px"><?php echo $module->getTitle() . ':'; ?></td>
          <td><?php echo $module->setFunction((isset($attributes[$Qattributes->valueInt('id')]) ? $attributes[$Qattributes->valueInt('id')] : null)); ?></td>
        </tr>

<?php
    }
  }
?>
      </table>
    </fieldset>
  </div>

  <div id="section_images_content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%" height="100%" valign="top">
          <fieldset style="height: 100%;">
            <legend><?php echo $osC_Language->get('subsection_new_image'); ?></legend>

            <div style="float: right;">
              <a href="#" id="remoteFilesLink" onclick="switchImageFilesView('remote');" style="background-color: #E5EFE5;"><?php echo $osC_Language->get('image_remote_upload'); ?></a> | <a href="#" id="localFilesLink" onclick="switchImageFilesView('local');"><?php echo $osC_Language->get('image_local_files'); ?></a>
            </div>

            <div id="remoteFiles">
              <span id="fileUploadField"></span>

<?php
    if ( isset($osC_ObjectInfo) ) {
      echo '<input type="button" id="uploadFile" value="' . $osC_Language->get('button_send_to_server') . '" class="operationButton" /><div id="showProgress" style="display: none; padding-left: 10px;">' . osc_icon('progress_ani.gif') . '&nbsp;' . $osC_Language->get('image_upload_progress') . '</div>';
    } else {
      echo osc_draw_file_field('products_image');
    }
?>
            </div>

<?php
    if ( isset($osC_ObjectInfo) ) {
?>

<script type="text/javascript"><!--
  $('#uploadFile').upload( {
    name: 'products_image',
    method: 'post',
    enctype: 'multipart/form-data',
    action: '<?php echo osc_href_link_admin('rpc.php', $osC_Template->getModule() . '=' . $osC_ObjectInfo->getInt('products_id') . '&action=fileUpload'); ?>',
    onSubmit: function() {
      $('#showProgress').css('display', 'inline');
    },
    onComplete: function(data) {
      $('#showProgress').css('display', 'none');
      getImages();
    }
  } );
//--></script>

<?php
    }
?>

            <div id="localFiles" style="display: none;">
              <p><?php echo $osC_Language->get('introduction_select_local_images'); ?></p>

              <select id="localImagesSelection" name="localimages[]" size="5" multiple="multiple" style="width: 100%;"></select>

              <div id="showProgressGetLocalImages" style="display: none; float: right; padding-right: 10px;"><?php echo osc_icon('progress_ani.gif') . '&nbsp;' . $osC_Language->get('image_retrieving_local_files'); ?></div>

              <p><?php echo realpath('../images/products/_upload'); ?></p>

<?php
    if ( isset($osC_ObjectInfo) ) {
      echo '<input type="button" value="Assign To Product" class="operationButton" onclick="assignLocalImages();" /><div id="showProgressAssigningLocalImages" style="display: none; padding-left: 10px;">' . osc_icon('progress_ani.gif') . '&nbsp;' . $osC_Language->get('image_multiple_upload_progress') . '</div>';
    }
?>

            </div>
          </fieldset>

<script type="text/javascript"><!--
  getLocalImages();
//--></script>

<?php
    if ( isset($osC_ObjectInfo) ) {
?>

          <fieldset style="height: 100%;">
            <legend><?php echo $osC_Language->get('subsection_original_images'); ?></legend>

            <div id="imagesOriginal" style="overflow: auto;"></div>
          </fieldset>

          <fieldset style="height: 100%;">
            <legend><?php echo $osC_Language->get('subsection_images'); ?></legend>

            <div id="imagesOther" style="overflow: auto;"></div>
          </fieldset>

<script type="text/javascript"><!--
  getImages();
//--></script>

<?php
    }
?>

        </td>
      </tr>
    </table>
  </div>

  <div id="section_variants_content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td colspan="3" align="right"><input type="button" value="Add Variant" class="infoBoxButton" onclick="addVariant();" /></td>
      </tr>
      <tr>
        <td width="30%" valign="top">
          <select name="variantGroups" ondblclick="moreFields();" size="20" style="width: 100%;">

<?php
  $Qvgroups = $osC_Database->query('select id, title, module from :table_products_variants_groups where languages_id = :languages_id order by sort_order, title');
  $Qvgroups->bindTable(':table_products_variants_groups', TABLE_PRODUCTS_VARIANTS_GROUPS);
  $Qvgroups->bindInt(':languages_id', $osC_Language->getID());
  $Qvgroups->execute();

  $has_multiple_value_groups = false;

  while ($Qvgroups->next()) {
    $vgroup_title = $Qvgroups->value('title');

    if ( osC_Variants::allowsMultipleValues($Qvgroups->value('module')) ) {
      if ( $has_multiple_value_groups === false ) {
        $has_multiple_value_groups = true;
      }

      $vgroup_title .= ' (*)';
    }

    echo '          <optgroup label="' . $vgroup_title . '" id="' . $Qvgroups->valueInt('id') . '">' . "\n";

    $Qvvalues = $osC_Database->query('select id, title from :table_products_variants_values where products_variants_groups_id = :products_variants_groups_id and languages_id = :languages_id order by sort_order, title');
    $Qvvalues->bindTable(':table_products_variants_values', TABLE_PRODUCTS_VARIANTS_VALUES);
    $Qvvalues->bindInt(':products_variants_groups_id', $Qvgroups->valueInt('id'));
    $Qvvalues->bindInt(':languages_id', $osC_Language->getID());
    $Qvvalues->execute();

    while ($Qvvalues->next()) {
      echo '            <option value="' . $Qvvalues->valueInt('id') . '">' . $Qvvalues->value('title') . '</option>' . "\n";
    }

    echo '          </optgroup>' . "\n";
  }
?>

          </select>

<?php
  if ( $has_multiple_value_groups === true ) {
    echo '<div style="text-align: center; font-style: italic;">(*) Multiple values can be assiged to the same product variant</div>';
  }
?>

        </td>
        <td align="center" width="5%">
          <input type="button" value=">>" onclick="moreFields();" class="infoBoxButton">
        </td>
        <td width="65%" valign="top">
          <fieldset>
            <legend><?php echo $osC_Language->get('subsection_assigned_variants'); ?></legend>

            <span id="writeroot">

<?php
  $variants_default_combo = null;

  if ( isset($osC_ObjectInfo) ) {
    $Qvariants = $osC_Database->query('select * from :table_products where parent_id = :parent_id');
    $Qvariants->bindTable(':table_products', TABLE_PRODUCTS);
    $Qvariants->bindInt(':parent_id', $osC_ObjectInfo->getInt('products_id'));
    $Qvariants->execute();

    $counter = 1;

    while ( $Qvariants->next() ) {
      $Qcombos = $osC_Database->query('select pv.default_combo, pvg.id as group_id, pvg.title as group_title, pvv.id as value_id, pvv.title as value_title from :table_products_variants pv, :table_products_variants_groups pvg, :table_products_variants_values pvv where pv.products_id = :products_id and pv.products_variants_values_id = pvv.id and pvv.languages_id = :languages_id and pvv.products_variants_groups_id = pvg.id and pvg.languages_id = :languages_id order by pvg.sort_order, pvg.title');
      $Qcombos->bindTable(':table_products_variants', TABLE_PRODUCTS_VARIANTS);
      $Qcombos->bindTable(':table_products_variants_groups', TABLE_PRODUCTS_VARIANTS_GROUPS);
      $Qcombos->bindTable(':table_products_variants_values', TABLE_PRODUCTS_VARIANTS_VALUES);
      $Qcombos->bindInt(':products_id', $Qvariants->valueInt('products_id'));
      $Qcombos->bindInt(':languages_id', $osC_Language->getID());
      $Qcombos->bindInt(':languages_id', $osC_Language->getID());
      $Qcombos->execute();

      $variants_string = '';
      $variants_combo_string = '';

?>

<script type="text/javascript">
  variants[<?php echo $counter; ?>] = new Array();
</script>

<?php
      while ( $Qcombos->next() ) {
        if ( ($variants_default_combo === null) && ($Qcombos->valueInt('default_combo') === 1) ) {
          $variants_default_combo = $counter;
        }

        $variants_string .= $Qcombos->value('group_title') . ': ' . $Qcombos->value('value_title') . ', ';

        $variants_combo_string .= $Qcombos->valueInt('group_id') . '_' . $Qcombos->valueInt('value_id') . ';';
?>

<script type="text/javascript">
  if (variants[<?php echo $counter; ?>][<?php echo $Qcombos->valueInt('group_id'); ?>] == undefined) {
    variants[<?php echo $counter; ?>][<?php echo $Qcombos->valueInt('group_id'); ?>] = new Array();
  }

  variants[<?php echo $counter; ?>][<?php echo $Qcombos->valueInt('group_id'); ?>][<?php echo $Qcombos->valueInt('value_id'); ?>] = <?php echo $Qcombos->valueInt('value_id'); ?>;
</script>

<?php
      }

      $variants_string = substr($variants_string, 0, -2);
      $variants_combo_string = substr($variants_combo_string, 0, -1);
?>


            <div id="variant<?php echo $counter; ?>" class="attributeAdd" onclick="activateVariant(this);">
              <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                  <td colspan="2"><div style="float: right;"><?php echo '<a href="javascript:setDefaultVariant(\'' . $counter . '\');">' . osc_icon((($variants_default_combo === $counter) ? 'default.png' : 'default_grey.png'), null, null, 'id="vdc' . $counter . '"') . '</a>'; ?>&nbsp;<a href="javascript:removeVariant('variant<?php echo $counter; ?>');"><?php echo osc_icon('trash.png'); ?></a></div><span style="font-weight: bold;"><?php echo osc_icon('attach.png') . '&nbsp;' . $variants_string; ?></span></td>
                </tr>
                <tr>
                  <td width="50%" height="100%" valign="top">
                    <fieldset style="height: 100%;">
                      <legend><?php echo $osC_Language->get('subsection_price'); ?></legend>

                      <table border="0" width="100%" cellspacing="0" cellpadding="2">
                        <tr>
                          <td><?php echo $osC_Language->get('field_tax_class'); ?></td>
                          <td><?php echo osc_draw_pull_down_menu('variants_tax_class_id[' . $counter . ']', $tax_class_array, $Qvariants->valueInt('products_tax_class_id'), 'id="tax_class' . $counter . '" onchange="updateGross(\'variants_price' . $counter . '\');"'); ?></td>
                        </tr>
                        <tr>
                          <td><?php echo $osC_Language->get('field_price_net'); ?></td>
                          <td><?php echo osc_draw_input_field('variants_price[' . $counter . ']', $Qvariants->value('products_price'), 'id="variants_price' . $counter . '" onkeyup="updateGross(\'variants_price' . $counter . '\')"'); ?></td>
                        </tr>
                        <tr>
                          <td><?php echo $osC_Language->get('field_price_gross'); ?></td>
                          <td><?php echo osc_draw_input_field('variants_price_gross[' . $counter . ']', $Qvariants->value('products_price'), 'id="variants_price' . $counter . '_gross" onkeyup="updateNet(\'variants_price' . $counter . '\')"'); ?></td>
                        </tr>
                      </table>

                      <script type="text/javascript"><!--
                        updateGross('variants_price<?php echo $counter; ?>');
                      //--></script>
                    </fieldset>
                  </td>
                  <td width="50%" height="100%" valign="top">
                    <fieldset style="height: 100%;">
                      <legend><?php echo $osC_Language->get('subsection_data'); ?></legend>

                      <table border="0" width="100%" cellspacing="0" cellpadding="2">
                        <tr>
                          <td><?php echo $osC_Language->get('field_model'); ?></td>
                          <td><?php echo osc_draw_input_field('variants_model[' . $counter . ']', $Qvariants->value('products_model')); ?></td>
                        </tr>
                        <tr>
                          <td><?php echo $osC_Language->get('field_quantity'); ?></td>
                          <td><?php echo osc_draw_input_field('variants_quantity[' . $counter . ']', $Qvariants->value('products_quantity')) . osc_draw_hidden_field('variants_combo[' . $counter . ']', $variants_combo_string, 'id="variants_combo_' . $counter . '"') . osc_draw_hidden_field('variants_combo_db[' . $counter . ']', $Qvariants->valueInt('products_id')); ?></td>
                        </tr>
                        <tr>
                          <td><?php echo $osC_Language->get('field_weight'); ?></td>
                          <td><?php echo osc_draw_input_field('variants_weight[' . $counter . ']', $Qvariants->value('products_weight'), 'size="6"'). '&nbsp;' . osc_draw_pull_down_menu('variants_weight_class[' . $counter . ']', $weight_class_array, $Qvariants->value('products_weight_class')); ?></td>
                        </tr>
                        <tr>
                          <td><?php echo $osC_Language->get('field_status'); ?></td>
                          <td><?php echo osc_draw_radio_field('variants_status[' . $counter . ']', array(array('id' => '1', 'text' => $osC_Language->get('status_enabled')), array('id' => '0', 'text' => $osC_Language->get('status_disabled'))), $Qvariants->value('products_status')); ?></td>
                        </tr>
                      </table>
                    </fieldset>
                  </td>
                </tr>
              </table>
            </div>

<?php
      $counter++;
    }

    if ( $counter > 0 ) {
?>

<script type="text/javascript">
  variants_counter = <?php echo $counter; ?>;
</script>

<?php
    }
  }
?>

            </span>

<?php
  echo osc_draw_hidden_field('variants_default_combo', $variants_default_combo, 'id="variants_default_combo"');

  if ( is_numeric($variants_default_combo) ) {
?>

<script type="text/javascript">
  variants_default_combo = <?php echo $variants_default_combo; ?>;
</script>

<?php
  }
?>

            <div id="readroot" style="display: none" class="attributeAdd" onclick="activateVariant(this);">
              <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                  <td colspan="2"><div style="float: right;"><a href="#" name="default"><?php echo osc_icon('default_grey.png', null, null, 'name="vdcnew"'); ?></a>&nbsp;<a href="#" name="trash"><?php echo osc_icon('trash.png'); ?></a></div><span style="font-weight: bold;"><?php echo osc_icon('attach.png') . '&nbsp;'; ?></span></td>
                </tr>
                <tr>
                  <td width="50%" height="100%" valign="top">
                    <fieldset style="height: 100%;">
                      <legend><?php echo $osC_Language->get('subsection_price'); ?></legend>

                      <table border="0" width="100%" cellspacing="0" cellpadding="2">
                        <tr>
                          <td><?php echo $osC_Language->get('field_tax_class'); ?></td>
                          <td><?php echo osc_draw_pull_down_menu('new_variants_tax_class_id', $tax_class_array, null, 'disabled="disabled"'); ?></td>
                        </tr>
                        <tr>
                          <td><?php echo $osC_Language->get('field_price_net'); ?></td>
                          <td><?php echo osc_draw_input_field('new_variants_price', null, 'disabled="disabled"'); ?></td>
                        </tr>
                        <tr>
                          <td><?php echo $osC_Language->get('field_price_gross'); ?></td>
                          <td><?php echo osc_draw_input_field('new_variants_price_gross', null, 'disabled="disabled"'); ?></td>
                        </tr>
                      </table>
                    </fieldset>
                  </td>
                  <td width="50%" height="100%" valign="top">
                    <fieldset style="height: 100%;">
                      <legend><?php echo $osC_Language->get('subsection_data'); ?></legend>

                      <table border="0" width="100%" cellspacing="0" cellpadding="2">
                        <tr>
                          <td><?php echo $osC_Language->get('field_model'); ?></td>
                          <td><?php echo osc_draw_input_field('new_variants_model', null, 'disabled="disabled"'); ?></td>
                        </tr>
                        <tr>
                          <td><?php echo $osC_Language->get('field_quantity'); ?></td>
                          <td><?php echo osc_draw_input_field('new_variants_quantity', null, 'disabled="disabled"') . osc_draw_hidden_field('new_variants_combo', null, 'disabled="disabled"'); ?></td>
                        </tr>
                        <tr>
                          <td><?php echo $osC_Language->get('field_weight'); ?></td>
                          <td><?php echo osc_draw_input_field('new_variants_weight', null, 'size="6" disabled="disabled"'). '&nbsp;' . osc_draw_pull_down_menu('new_variants_weight_class', $weight_class_array, SHIPPING_WEIGHT_UNIT, 'disabled="disabled"'); ?></td>
                        </tr>
                        <tr>
                          <td><?php echo $osC_Language->get('field_status'); ?></td>
                          <td><?php echo osc_draw_radio_field('new_variants_status', array(array('id' => '1', 'text' => $osC_Language->get('status_enabled')), array('id' => '0', 'text' => $osC_Language->get('status_disabled'))), '0', 'disabled="disabled"'); ?></td>
                        </tr>
                      </table>
                    </fieldset>
                  </td>
                </tr>
              </table>
            </div>
          </fieldset>
        </td>
      </tr>
    </table>
  </div>

  <div id="section_categories_content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
          <thead>
            <tr>
              <th width="20">&nbsp;</th>
              <th><?php echo $osC_Language->get('table_heading_categories'); ?></th>
            </tr>
          </thead>
          <tbody>
<?php
  $product_categories_array = array();

  if ( isset($osC_ObjectInfo) ) {
    $Qcategories = $osC_Database->query('select categories_id from :table_products_to_categories where products_id = :products_id');
    $Qcategories->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
    $Qcategories->bindInt(':products_id', $osC_ObjectInfo->getInt('products_id'));
    $Qcategories->execute();

    while ($Qcategories->next()) {
      $product_categories_array[] = $Qcategories->valueInt('categories_id');
    }
  }

  $assignedCategoryTree = new osC_CategoryTree();
  $assignedCategoryTree->setBreadcrumbUsage(false);
  $assignedCategoryTree->setSpacerString('&nbsp;', 5);

  foreach ($assignedCategoryTree->getArray() as $value) {
    echo '          <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">' . "\n" .
         '            <td>' . osc_draw_checkbox_field('categories[]', $value['id'], in_array($value['id'], $product_categories_array), 'id="categories_' . $value['id'] . '"') . '</td>' . "\n" .
         '            <td><a href="#" onclick="document.product.categories_' . $value['id'] . '.checked=!document.product.categories_' . $value['id'] . '.checked;">' . $value['title'] . '</a></td>' . "\n" .
         '          </tr>' . "\n";
  }
?>
        </table></td>
      </tr>
    </table>
  </div>
</div>

<p align="right"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&cID=' . $_GET['cID']) . '\';" class="operationButton" />'; ?></p>

</form>
