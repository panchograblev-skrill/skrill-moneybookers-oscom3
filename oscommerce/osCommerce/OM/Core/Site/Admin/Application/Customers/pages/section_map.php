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

<div id="sectionMenu_map">
  <div class="infoBox">

<?php
  if ( $new_customer ) {
    echo '<h3>' . HTML::icon('new.png') . ' ' . OSCOM::getDef('action_heading_new_customer') . '</h3>';
  } else {
    echo '<h3>' . HTML::icon('edit.png') . ' ' . $OSCOM_ObjectInfo->getProtected('customers_name') . '</h3>';
  }
?>

    <div id="map_canvas" style="height: 400px; width: 75%; margin: 10px;"></div>
  </div>
</div>

<script>
var map, geocoder, latlngbounds, addresses;
var markers = [];

function loadMapsScript() {
  var script = document.createElement('script');
  script.src = 'https://maps-api-ssl.google.com/maps/api/js?sensor=false&callback=initializeMap';
  document.body.appendChild(script);
}

function initializeMap() {
  var myOptions = {
    zoom: 6,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };

  map = new google.maps.Map(document.getElementById('map_canvas'), myOptions);
  geocoder = new google.maps.Geocoder();

  geocoder.geocode( { 'address': $('#ab_country option[value="<?php echo STORE_COUNTRY; ?>"]').text() }, function(results, status) {
    if (status == google.maps.GeocoderStatus.OK) {
      map.setCenter(results[0].geometry.location);
    }
  });

  showAddressesOnMap();
}

function showAddressesOnMap() {
  if ( markers ) {
    for ( i in markers ) {
      markers[i].setMap(null);
    }

    markers.length = 0;
  }

  latlngbounds = new google.maps.LatLngBounds();

  addresses = new Object;

  $('input[name^="ab["]').each(function() {
    var key = $(this).attr('name').substring(3, $(this).attr('name').indexOf(']'));

    if ( $('#abDelete' + key).length < 1 ) {
      if ( key in addresses === false ) {
        addresses[key] = {
          'street_address': $('input[name="ab[' + key + '][street_address]"]').val(),
          'postcode': $('input[name="ab[' + key + '][postcode]"]').val(),
          'country_id': $('input[name="ab[' + key + '][country_id]"]').val(),
          'label': $('#abEntry' + key + ' .abLabel').html()
        };
      }
    }
  });

  $('input[name^="new_address["]').each(function() {
    var id = $(this).attr('name').substring(12, $(this).attr('name').indexOf(']'));
    var key = 'new' + id;

    if ( key in addresses === false ) {
      addresses[key] = {
        'street_address': $('input[name="new_address[' + id + '][street_address]"]').val(),
        'postcode': $('input[name="new_address[' + id + '][postcode]"]').val(),
        'country_id': $('input[name="new_address[' + id + '][country_id]"]').val(),
        'label': $('#newAB' + id + ' .abLabel').html()
      };
    }
  });

  $.each(addresses, function(key, value) {
    codeAddress(value.street_address + ', ' + value.postcode + ', ' + $('#ab_country option[value="' + value.country_id + '"]').text(), value.label);
  });

  google.maps.event.trigger(map, 'resize'); 
}

function codeAddress(address, label) {
  geocoder.geocode( { 'address': address}, function(results, status) {
    if (status == google.maps.GeocoderStatus.OK) {
//In this case it creates a marker, but you can get the lat and lng from the location.LatLng
      var marker = new google.maps.Marker({
        map: map, 
        position: results[0].geometry.location,
        clickable: true
      });

      marker.info = new google.maps.InfoWindow({
        content: label
      });

      google.maps.event.addListener(marker, 'click', function() {
        marker.info.open(map, marker);
      });

      markers.push(marker);

      latlngbounds.extend(results[0].geometry.location);

      map.fitBounds(latlngbounds);
    }
  });
}

$(function() {
  var mapsCall;

  $('#sectionMenu input:radio[value=map]').click(function() {
    if ( mapsCall === undefined ) {
      loadMapsScript();

      mapsCall = true;
    } else {
      showAddressesOnMap();
    }
  });
});
</script> 
