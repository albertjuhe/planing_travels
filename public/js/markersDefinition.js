/*
 'red', 'darkred', 'orange', 'green', 'darkgreen', 'blue', 'purple', 'darkpuple', 'cadetblue'

 */

// Creates green marker with center point
var centerMarker = L.AwesomeMarkers.icon({
    icon: 'genderless',
    markerColor: 'green',
    prefix: 'fas'
});

//Hoouse
var HouseMarker = L.AwesomeMarkers.icon({
    icon: 'bed',
    markerColor: 'blue',
    prefix: 'fas'
});

//plane
var AirportMarker = L.AwesomeMarkers.icon({
    icon: 'plane',
    markerColor: 'orange',
    prefix: 'fas'
});

//monument
var MonumentMarker = L.AwesomeMarkers.icon({
    icon: 'camera',
    markerColor: 'purple',
    prefix: 'fas'
});

var CityMarker = L.AwesomeMarkers.icon({
    icon: 'building',
    markerColor: 'darkred',
    prefix: 'fas'
});

var LunchMarker = L.AwesomeMarkers.icon({
    icon: 'utensils',
    markerColor: 'cadetblue',
    prefix: 'fas'
});

var BicycleMarker = L.AwesomeMarkers.icon({
    icon: 'bicycle',
    markerColor: 'darkgreen',
    prefix: 'fas'
});

var BusMarker = L.AwesomeMarkers.icon({
    icon: 'bus',
    markerColor: 'orange',
    prefix: 'fas'
});

var AutomobileMarker = L.AwesomeMarkers.icon({
    icon: 'car',
    markerColor: 'orange',
    prefix: 'fas'
});

var TrainMarker = L.AwesomeMarkers.icon({
    icon: 'train',
    markerColor: 'orange',
    prefix: 'fas'
});

var ShipMarker = L.AwesomeMarkers.icon({
    icon: 'ship',
    markerColor: 'orange',
    prefix: 'fas'
});

var CoffeeMarker = L.AwesomeMarkers.icon({
    icon: 'coffee',
    markerColor: 'cadetblue',
    prefix: 'fas'
});

var ParkMarker = L.AwesomeMarkers.icon({
    icon: 'tree',
    markerColor: 'green',
    prefix: 'fas'
});

var HotelMarker = L.AwesomeMarkers.icon({
    icon: 'building',
    markerColor: 'blue',
    prefix: 'fas'
});

var BeachMarker = L.AwesomeMarkers.icon({
    icon: 'sun',
    markerColor: 'cadetblue',
    prefix: 'fas'
});

var MuseumMarker = L.AwesomeMarkers.icon({
    icon: 'university',
    markerColor: 'darkpurple',
    prefix: 'fas'
});

var ShopMarker = L.AwesomeMarkers.icon({
    icon: 'shopping-cart',
    markerColor: 'darkred',
    prefix: 'fas'
});

var CampingMarker = L.AwesomeMarkers.icon({
    icon: 'fire',
    markerColor: 'orange',
    prefix: 'fas'
});

var ViewpointMarker = L.AwesomeMarkers.icon({
    icon: 'binoculars',
    markerColor: 'darkgreen',
    prefix: 'fas'
});

var HospitalMarker = L.AwesomeMarkers.icon({
    icon: 'hospital',
    markerColor: 'red',
    prefix: 'fas'
});

var CinemaMarker = L.AwesomeMarkers.icon({
    icon: 'film',
    markerColor: 'purple',
    prefix: 'fas'
});

var BarMarker = L.AwesomeMarkers.icon({
    icon: 'glass-martini',
    markerColor: 'green',
    prefix: 'fas'
});

var RuinsMarker = L.AwesomeMarkers.icon({
    icon: 'landmark',
    markerColor: 'darkpurple',
    prefix: 'fas'
});

var CabinMarker = L.AwesomeMarkers.icon({
    icon: 'house-chimney',
    markerColor: 'green',
    prefix: 'fas'
});

var LighthouseMarker = L.AwesomeMarkers.icon({
    icon: 'tower-observation',
    markerColor: 'blue',
    prefix: 'fas'
});

var BridgeMarker = L.AwesomeMarkers.icon({
    icon: 'bridge',
    markerColor: 'darkpurple',
    prefix: 'fas'
});

var StadiumMarker = L.AwesomeMarkers.icon({
    icon: 'futbol',
    markerColor: 'orange',
    prefix: 'fas'
});

var ZooMarker = L.AwesomeMarkers.icon({
    icon: 'paw',
    markerColor: 'green',
    prefix: 'fas'
});

var GardenMarker = L.AwesomeMarkers.icon({
    icon: 'seedling',
    markerColor: 'cadetblue',
    prefix: 'fas'
});

var TheatreMarker = L.AwesomeMarkers.icon({
    icon: 'masks-theater',
    markerColor: 'purple',
    prefix: 'fas'
});

var FarmMarker = L.AwesomeMarkers.icon({
    icon: 'tractor',
    markerColor: 'orange',
    prefix: 'fas'
});

var CastleMarker = L.AwesomeMarkers.icon({
    icon: 'chess-rook',
    markerColor: 'darkred',
    prefix: 'fas'
});

var CaveMarker = L.AwesomeMarkers.icon({
    icon: 'mountain',
    markerColor: 'blue',
    prefix: 'fas'
});

var MarketMarker = L.AwesomeMarkers.icon({
    icon: 'store',
    markerColor: 'darkgreen',
    prefix: 'fas'
});

var HarbourMarker = L.AwesomeMarkers.icon({
    icon: 'anchor',
    markerColor: 'cadetblue',
    prefix: 'fas'
});

var SwimmingMarker = L.AwesomeMarkers.icon({
    icon: 'person-swimming',
    markerColor: 'blue',
    prefix: 'fas'
});

var SkiingMarker = L.AwesomeMarkers.icon({
    icon: 'person-skiing',
    markerColor: 'darkgreen',
    prefix: 'fas'
});

var LibraryMarker = L.AwesomeMarkers.icon({
    icon: 'book',
    markerColor: 'purple',
    prefix: 'fas'
});

var PharmacyMarker = L.AwesomeMarkers.icon({
    icon: 'prescription-bottle',
    markerColor: 'red',
    prefix: 'fas'
});

var BakeryMarker = L.AwesomeMarkers.icon({
    icon: 'bread-slice',
    markerColor: 'orange',
    prefix: 'fas'
});

var SelectedIcon = L.AwesomeMarkers.icon({
    prefix: 'fas', //font awesome rather than bootstrap
    markerColor: 'red', // see colors above
    icon: 'circle' //http://fortawesome.github.io/Font-Awesome/icons/
});