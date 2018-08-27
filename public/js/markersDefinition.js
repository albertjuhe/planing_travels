/*
 'red', 'darkred', 'orange', 'green', 'darkgreen', 'blue', 'purple', 'darkpuple', 'cadetblue'

 */

// Creates green marker with center point
var centerMarker = L.AwesomeMarkers.icon({
    icon: 'genderless',
    markerColor: 'green',
    prefix: 'fa'
});

//Hoouse
var HouseMarker = L.AwesomeMarkers.icon({
    icon: 'bed',
    markerColor: 'blue',
    prefix: 'fa'
});

//plane
var AirportMarker = L.AwesomeMarkers.icon({
    icon: 'plane',
    markerColor: 'orange',
    prefix: 'fa'
});

//monument
var MonumentMarker = L.AwesomeMarkers.icon({
    icon: 'camera',
    markerColor: 'purple',
    prefix: 'fa'
});

var CityMarker = L.AwesomeMarkers.icon({
    icon: 'building',
    markerColor: 'darkred',
    prefix: 'fa'
});

var LunchMarker = L.AwesomeMarkers.icon({
    icon: 'cutlery',
    markerColor: 'cadetblue',
    prefix: 'fa'
});

var BicycleMarker = L.AwesomeMarkers.icon({
    icon: 'bicycle',
    markerColor: 'darkgreen',
    prefix: 'fa'
});

var BusMarker = L.AwesomeMarkers.icon({
    icon: 'bus',
    markerColor: 'orange',
    prefix: 'fa'
});

var AutomobileMarker = L.AwesomeMarkers.icon({
    icon: 'automobile',
    markerColor: 'orange',
    prefix: 'fa'
});

var TrainMarker = L.AwesomeMarkers.icon({
    icon: 'train',
    markerColor: 'orange',
    prefix: 'fa'
});

var ShipMarker = L.AwesomeMarkers.icon({
    icon: 'ship',
    markerColor: 'orange',
    prefix: 'fa'
});

var CoffeeMarker = L.AwesomeMarkers.icon({
    icon: 'coffee',
    markerColor: 'cadetblue',
    prefix: 'fa'
});

var SelectedIcon = L.AwesomeMarkers.icon({
    prefix: 'fa', //font awesome rather than bootstrap
    markerColor: 'red', // see colors above
    icon: 'circle' //http://fortawesome.github.io/Font-Awesome/icons/
});