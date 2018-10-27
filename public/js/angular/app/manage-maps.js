var app = angular.module('ManageMaps',[]);

app.controller('mapsCtrl',function($scope) {

    function initialize() {
        var map = L.map('map',{
            center: [{{ travel.geolocation.lat}},{{ travel.geolocation.lng }}],
        8,
            fullscreenControl;: {
            false
        }
    })
        var geocoder = new google.maps.Geocoder();

        var osm = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');
        var ggl = new L.Google();
        var ggl2 = new L.Google('TERRAIN');
        var gglRoadMap = new L.Google('ROADMAP');
        map.addLayer(gglRoadMap);
        map.addControl(new L.Control.Layers( {'OSM':osm, 'Google':ggl, 'Google Terrain':ggl2,'RoadMap':gglRoadMap}, {}));

        var centralMark = L.marker([{{ travel.geolocation.lat}},{{ travel.geolocation.lng }}], {centerMarker}).addTo(map);


    }

    google.maps.event.addDomListener(window, 'load', initialize);
});