var app = angular.module('GoogleMapsModule',[]);

app.controller('googleMaps',function($scope) {

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
        }
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

    google.maps.event.addDomListener(window, 'load', initialize);
});