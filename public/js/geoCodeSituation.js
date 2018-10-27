var geocoder;
var map;
var myGeolocation = {};

$(document).ready(function(){
    initialize();

    $('.mapa').click(function(){

    })

});
/*
$(window).load(function() {
    loadScript();
});
*/



//Current position
function showLocation(position) {
    var latitude = position.coords.latitude;
    var longitude = position.coords.longitude;
    myGeolocation.latitude = latitude;
    myGeolocation.longitude = longitude;

    alert("Latitude : " + latitude + " Longitude: " + longitude);
}

function errorHandler(err) {
    if(err.code == 1) {
        alert("Error: Access is denied!");
    }

    else if( err.code == 2) {
        alert("Error: Position is unavailable!");
    }
}

function getLocation(){
    if(navigator.geolocation){
        // timeout at 60000 milliseconds (60 seconds)
        var options = {timeout:60000};
        navigator.geolocation.getCurrentPosition(showLocation, errorHandler, options);
    }
    else{
        alert("Sorry, browser does not support geolocation!");
    }
}



function initialize() {
    var autocomplete = new google.maps.places.Autocomplete(
        (document.getElementById('travel_title')),
        { types: ['geocode'] });
    var infowindow = new google.maps.InfoWindow();

    var mapCanvas = document.getElementById('map-canvas');
    var mapOptions = {
        center: new google.maps.LatLng(44.5403, -78.5463),
        zoom: 8,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(mapCanvas, mapOptions);
    var marker = new google.maps.Marker({
        map: map,
        anchorPoint: new google.maps.Point(0, -29)
    });

    google.maps.event.addListener(autocomplete, 'place_changed', function() {
        var place = autocomplete.getPlace();
        if (!place.geometry) {
            alert("Autocomplete's returned place contains no geometry");
        } else {

        }
        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
            var lat0 = map.getBounds().getNorthEast().lat();
            var lng0 = map.getBounds().getNorthEast().lng();
            var lat1 = map.getBounds().getSouthWest().lat();
            var lng1 = map.getBounds().getSouthWest().lng();
            $('#travel_geoLocation_lat0').val(lat0);
            $('#travel_geoLocation_lng0').val(lng0);
            $('#travel_geoLocation_lat1').val(lat1);
            $('#travel_geoLocation_lng1').val(lng1);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);  // Why 17? Because it looks good.
        }
        var latitude = place.geometry.location.lat();
        var longitude = place.geometry.location.lng();
        $('#travel_geoLocation_lat').val(latitude);
        $('#travel_geoLocation_lng').val(longitude);
        marker.setIcon(/** @type {google.maps.Icon} */({
            url: place.icon,
            size: new google.maps.Size(71, 71),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(17, 34),
            scaledSize: new google.maps.Size(35, 35)
        }));
        marker.setPosition(place.geometry.location);
        marker.setVisible(true);
        var address = '';
        if (place.address_components) {
            address = [
                (place.address_components[0] && place.address_components[0].short_name || ''),
                (place.address_components[1] && place.address_components[1].short_name || ''),
                (place.address_components[2] && place.address_components[2].short_name || '')
            ].join(' ');
        }

        infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
        infowindow.open(map, marker);
        $("#travel_description").text( place.name +' ' + address);

    });
}

/*
function buscardireccion() {
    var pais = $('.oficina_pais').val();
    var provincia = $('.oficina_provincia').val();
    var ciudad = $('.oficina_ciudad').val();
    var calle = $('.oficina_calle').val();
    var cp = $('.oficina_cp').val();

    var address = calle + ',' + ciudad + ',' + cp + ',' +provincia + ',' + pais;
    geocoder.geocode( { 'address': address}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            //console.log(results[0]);
            var address = results[0].address_components;
            var cp = address[address.length - 1].long_name;
            var poblacio = address[2].long_name;
            var provincia = address[3].long_name;
            var pais = address[5].long_name;
            map.setCenter(results[0].geometry.location);
            var marker = new google.maps.Marker({
                map: map,
                position: results[0].geometry.location
            });
            map.setZoom(18);
            $('.oficina_lat').val(marker.position.lat());
            $('.oficina_lng').val(marker.position.lng());
            $('.oficina_ciudad').val(poblacio);
            $('.oficina_provincia').val(provincia);
            $('.oficina_pais').val(pais);
            $('.oficina_cp').val(cp);
        } else {
            alert("Error en googlemaps: " + status);
        }
    });
}
*/
