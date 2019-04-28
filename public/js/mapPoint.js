"use strict";

//Constructor
var mapPoint = function (travelId) {
    this.travel = travelId;
    this.control = null;
    this.currentPoints = [];
    this.dragSrcEl = null;
    this.handleDragStart = __bind(this.handleDragStart, this);
    this.handleDragEnd = __bind(this.handleDragEnd, this);
    this.handleDragEnter = __bind(this.handleDragEnter, this);
    this.handleDragOver = __bind(this.handleDragOver, this);
    this.handleDragLeave = __bind(this.handleDragLeave, this);
    this.info = __bind(this.info, this);
    this.handleDrop = __bind(this.handleDrop, this);
    this.rest = __bind(this.rest, this);
    this.dragableAndDropable = __bind(this.dragableAndDropable, this);
    this.deleteMark = __bind(this.deleteMark, this);
    this.showGallery = __bind(this.showGallery, this);


    mapPoint.prototype.plugin = {}; //This object contains all the exercices tipologies that appears in the document


    var _elementDrop = $('#routePoints');
    _elementDrop.bind({ //Events for drop
        'drop':      this.handleDrop,
        'dragend':   this.handleDragEnd,
        'dragover':  this.handleDragOver,
        'dragenter': this.handleDragEnter,
        'dragleave': this.handleDragLeave
    });


};

/***************************************************
 *
 * @param name: Name of the plugin must be unique
 * @param obj:plugin object
 ****************************************************/
mapPoint.prototype.addPlugin = function (name,obj) {
    if (this.plugin[name]) {
        console.error("You cannot have more than one instance of any plugin.");
    } else {
        this.plugin[name] = {};
        $.extend(this.plugin[name], obj);
        if (typeof (this.plugin[name].init) === "function") { //Si té init l utilitzem
            this.plugin[name].init();
        }
    }
};

mapPoint.prototype.removeRoute = function() {
    this.control.removeFrom(map)
};

mapPoint.prototype.calculateRoute = function() {
    var routePoints = $('#routePoints').find(".point-view");
    var _self = this;
    this.control = L.Routing.control({
        routeWhileDragging: true
    });

    $.each(routePoints,function(key,value) {
        var idPoint = $(value).data('id');
        var locationPoint = $('#' + idPoint).data('location');
        _self.control.spliceWaypoints(key, 2 ,  L.latLng(locationPoint.latitude,locationPoint.longitude));
    });

  //  control.route();
    this.control.addTo(map);

};

mapPoint.prototype.panelItinerary = function() {
    if ($('.leaflet-routing-container') != 'undefined' && this.control!=null) {
        if ($('.leaflet-routing-container').is(':visible')) {
            $('.leaflet-routing-container').hide()
        } else $('.leaflet-routing-container').show();
    }
};

//Create a location point object
mapPoint.prototype.createLocation = function(id,latitude,longitude,placeAddress,place_id,typeIcon,description,address,currentMark) {
    var l = {};
    l.id = id;
    l.latitude = latitude;
    l.longitude = longitude;
    l.placeAddress = placeAddress; //Mark address
    l.place_id = place_id;
    l.typeIcon = typeIcon;
    l.description = description;
    l.address = address;  //Our title
    l.currentMark = currentMark;

    return l;
};

mapPoint.prototype.addPoint = function(locationPoint) {

    var removeButton = this.createButton('remove','btn-danger','fa-trash',locationPoint.place_id);
    var goButton = this.createButton('go','btn-warning','fa-home',locationPoint.place_id);
    var infoButton = this.createButton('info','btn-info','fa-info',locationPoint.place_id);
    var notaButton = this.createButton('nota','btn-success','fa-sticky-note',locationPoint.place_id);

    var layerLoc= '<div class="row point-view" id="layer_'+locationPoint.place_id+'">' +
        '<div class="col-sm-7" draggable="true" id="'+locationPoint.place_id+'">' +
        '<span class="title-point">' +
        '<i class="'+locationPoint.typeIcon+'" style="font-size: 16px; line-height: 1.5em;margin-right:3px"></i><b>'+ locationPoint.address +'</b></span><br><i style="font-size:11px">'+locationPoint.placeAddress+'</i></div>' +
        '<div class="col-sm-5">' +
        goButton + infoButton + removeButton + notaButton +
        '</div>' +
        '</div>';

    $('#mapPoints').append(layerLoc);
    var currentPoint = "#"+ locationPoint.place_id;
    $(currentPoint).data('location',locationPoint);
    $(currentPoint).bind(
        {
            'dragstart': this.handleDragStart
        }
    );

    var layerPoint = "#layer_"+ locationPoint.place_id;
    $(layerPoint).bind(
        {
             'mouseover': function(e) {
                 e.stopPropagation();

                //var dades = $(e.target).data('location');
               //  dades.currentMark.togglePopup();
                //dades.currentMark.setIcon(SelectedIcon);
            },
            'mouseout': function(e) {
                e.stopPropagation();

            }

        }
    );


    //Buttons Accions
    var goButton = $('*[data-place="'+locationPoint.place_id+'"][data-function="go"]');
    var removeButton = $('*[data-place="'+locationPoint.place_id+'"][data-function="remove"]');
    var infoButton = $('*[data-place="'+locationPoint.place_id+'"][data-function="info"]');
    var notaButton = $('*[data-place="'+locationPoint.place_id+'"][data-function="nota"]');
    $(goButton).bind({'click':this.goMark});
    $(removeButton).bind({'click':this.deleteMark});
    $(infoButton).bind({'click':this.info});
    $(notaButton).bind({'click':this.nota});
};

mapPoint.prototype.save = function(locationPoint,currentMark) {
     //Serializing object point
    var _serializer = JSON.stringify(locationPoint);
    locationPoint.currentMark = currentMark;
    this.rest('POST', _serializer,locationPoint);

};

mapPoint.prototype.createButton = function(title,type,button_type,place_id) {
     return '<button data-place="'+place_id+'" style="margin:1px" data-function="'+title+'" data-target="'+'#'+title+'" data-toggle="modal" type="button" class="btn '+type+' btn-xs">'+title+'</button>';
};

mapPoint.prototype.info = function(e) {
    var current = $(e.target);
    var placeToGo = current.data('place');
    var l = $('#' + placeToGo).data('location');

    $('#location').html(l.placeAddress);
    $('#location_media').show();
    $('#fileupload').data('location', l.id);
    this.showGallery(l.id);
   // this.showNotes(l.id);
};
/*
Deprecated

mapPoint.prototype.showNotes = function(l) {
    $.ajax({
            type:'POST',
            contentType: 'application/json',
            url: '../../api/notes/' + l,
            dataType: 'json',
            data: l.id,
            success: function(response) {
                var notas='';
                $.each(response,function(key,value){
                    notas += '<p id="note'+key+'" style="margin: 10px;">'+'<span class="title-point">'+value.title+'</span> '+ '<i style="font-size:11px">'+value.description+'</i> </p>';
                });
                $('#notes').html(notas);
                console.log('Notes exists and Its showed ');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log( 'textStatus: ' + textStatus );
            }
        });
}
 */

mapPoint.prototype.showGallery = function(l) {
    if (typeof(this.plugin['locationGallery'].getLocationImages) === "function") { //Si té init l utilitzem
        this.plugin['locationGallery'].getLocationImages(l);
    }
};


mapPoint.prototype.goMark = function(e) {
    var current = $(e.target);
    var placeToGo = current.data('place');
    var l = $('#' + placeToGo).data('location');
    map.setView([l.latitude,l.longitude],30);
};


mapPoint.prototype.deleteMark = function(e) {
    var current = $(e.target);
    var placeToGo = current.data('place');
    var l = $('#' + placeToGo).data('location');
    map.removeLayer(l.currentMark);
    $('#layer_' + placeToGo).remove();
    this.rest('DELETE', null, l.id);
};

mapPoint.prototype.nota = function(e) {
    var current = $(e.target);
    var placeToGo = current.data('place');
    var l = $('#' + placeToGo).data('location');

    $("#addnota").attr("data-target", l.id );
    $('#addnota').modal();
};


mapPoint.prototype.addNota = function() {
    var _self = this;
    var title = document.getElementById("ntitle").value;
    var description = document.getElementById("description").value;
    var location = $("#addnota").data('target');

    var nota = {};
    nota.title = title;
    nota.description = description;
    nota.location = location;

    _self.saveNota(nota);
};

mapPoint.prototype.saveNota = function(nota) {
    var _serializer = JSON.stringify(nota);   

    $.ajax({
            type:'POST',
            contentType: 'application/json',
            url: '../api/annotations',
            dataType:'json',            
            data: _serializer,
            success: function(result) {
                console.log('Added note ' + result);
            },
            error: function(data,testStatus,jqXHR) {
                console.log('Error note ');
            }
        });        
};

mapPoint.prototype.resetRoute = function() {
    $('#routePoints').empty();
};

//Start dragging, change opacity
mapPoint.prototype.handleDragStart = function (e) {
    this.dragSrcEl = e.target;
    var _dataTransfer = e.originalEvent.dataTransfer;
    _dataTransfer.setData('text', e.target.id);
};

mapPoint.prototype.handleDrop = function (e) {

    if (e.stopPropagation) {
        e.stopPropagation(); // stops the browser from redirecting.
    }
    var _dataTransfer = e.originalEvent.dataTransfer;
    if (_dataTransfer.getData('text')!='') {
        var buttonRemove ='<button class="btn btn-danger btn-xs" data-place="'+_dataTransfer.getData('text')+'" id="remove-point-'+_dataTransfer.getData('text')+'">Remove</button>';
        var layerRoute = '<div class="point-view" id="route-'+_dataTransfer.getData('text')+'" data-route="true" data-id="'+_dataTransfer.getData('text')+'">' + $('#' + _dataTransfer.getData('text')).html() + ' ' +buttonRemove+'</div>';

        $('#routePoints').append(layerRoute);
        $('#routePoints').sortable();
        $('#remove-point-'+_dataTransfer.getData('text')).bind({'click':function() {
            $('#route-'+$(this).data('place')).remove();
        }})

    }
    return false;
};

mapPoint.prototype.handleDragEnter = function (e) {
    console.log('Enter dragging');
};

mapPoint.prototype.handleDragOver = function (e) {
    if (e.preventDefault) {
        e.preventDefault(); // Necessary. Allows us to drop.
    }
    var _dataTransfer = e.originalEvent.dataTransfer;
    _dataTransfer.dropEffect = 'move';
    return false;
};

mapPoint.prototype.handleDragEnd = function (e) {
};

mapPoint.prototype.handleDragLeave = function (e) {
};

//Rest API for Traveling
mapPoint.prototype.rest = function(typeRest,data,locationPoint) {
    var _self = this;

    if (typeRest=='POST') {
    $.ajax({
        type:typeRest,
        contentType: 'application/json',
        url: '../../api/user/' + locationPoint.user +'/location',
        dataType:'json',
        data: data,
        success: function(data,testStatus,jqXHR) {
            locationPoint.id = data;
            _self.addPoint(locationPoint);
            $('#infoForm').html('<p class="alert alert-success">Location Added</p>');
            $("#infoForm").show().delay(5000).fadeOut();
            console.log('Added point ' + data);
            socket.emit('add',data);
        },
        error: function(data,testStatus,jqXHR) {
           $('#infoForm').html('<p class="alert alert-danger">Error: Location not Added</p>');
           $("#infoForm").show().delay(5000).fadeOut();
        }
    });
    } else if (typeRest=='DELETE') {
        $.ajax({
            type:typeRest,
            url: '../../api/travel/'+this.travel+'/location/' + locationPoint,
            success: function(result) {
                $('#infoTravel').html('<p class="alert alert-success">Location Removed</p>');
                $("#infoTravel").show().delay(5000).fadeOut();
                console.log('Removing point ' + result);
            },
            error: function(data,testStatus,jqXHR) {
                console.log('Error removing location ' + testStatus);
            }
        });
    }
};

    /*
     var request = {
     placeId: placeId
     };
     var _self = this;
     var infowindow = new google.maps.InfoWindow();
     var mp = $('#mapPoints');
     var service = new google.maps.places.PlacesService(mp[0]);

     service.getDetails(request, function(place, status) {
     if (status == google.maps.places.PlacesServiceStatus.OK) {
     //alert('added place');
     var photos = place.photos;
     var images = "";
     if (photos) {
     images = '<img src="'+photos[0].getUrl({'maxWidth':200, 'maxHeight': 200})+'"/>';
     }

     $('#mapPoints').append('<div class="col-sm-3" data-placeId="'+placeId+'"><h3>'+address+'</h3><div class="image">'+images+'</div><div class="address">'+ place.formatted_address +'</div></div>');
     }
     });
}*/

mapPoint.prototype.returnTown = function(results) {
    var level_1;
    var level_2;
    for (var y = 0, length_2 = results.address_components.length; y < length_2; y++){
        var type = results.address_components[y].types[0];
        if ( type === "administrative_area_level_1") {
            level_1 = results.address_components[y].long_name;
            if (level_2) break;
        } else if (type === "locality"){
            level_2 = results.address_components[y].long_name;
            if (level_1) break;
        }
    }

    return [level_2, level_1];
};

mapPoint.prototype.getMark = function(pointtype,latitude,longitude,placeAdress,popup) {
    var currentMark;
    var typeIcon;

    switch (pointtype) {
        case "House":
            currentMark = L.marker([latitude,longitude],{icon: HouseMarker,title: placeAdress}).bindPopup(popup);
            typeIcon = "fa fa-bed";
            break;
        case "Airport":
            currentMark = L.marker([latitude,longitude],{icon: AirportMarker,title: placeAdress}).bindPopup(popup);
            typeIcon = "fa fa-plane";
            break;
        case "Monument":
            currentMark = L.marker([latitude,longitude],{icon: MonumentMarker,title: placeAdress}).bindPopup(popup);
            typeIcon = "fa fa-camera";
            break;
        case "City":
            currentMark = L.marker([latitude,longitude],{icon:CityMarker,title: placeAdress}).bindPopup(popup);
            typeIcon = "fa fa-building";
            break;
        case "Lunch":
            currentMark = L.marker([latitude,longitude],{icon:LunchMarker,title: placeAdress}).bindPopup(popup);
            typeIcon = "fa fa-cutlery";
            break;
        case "Bicycle":
            currentMark = L.marker([latitude,longitude],{icon:BicycleMarker,title: placeAdress}).bindPopup(popup);
            typeIcon = "fa fa-bicycle";
            break;
        case "Bus":
            currentMark = L.marker([latitude,longitude],{icon:BusMarker,title: placeAdress}).bindPopup(popup);
            typeIcon = "fa fa-bus";
            break;
        case "Automobile":
            currentMark = L.marker([latitude,longitude],{icon:AutomobileMarker,title: placeAdress}).bindPopup(popup);
            typeIcon = "fa fa-automobile";
            break;
        case "Train":
            currentMark = L.marker([latitude,longitude],{icon:TrainMarker,title: placeAdress}).bindPopup(popup);
            typeIcon = "fa fa-train";
            break;
        case "Ship":
            currentMark = L.marker([latitude,longitude],{icon:ShipMarker,title: placeAdress}).bindPopup(popup);
            typeIcon = "fa fa-ship";
            break;
        case "Coffee":
            currentMark = L.marker([latitude,longitude],{icon:BreakfastMarker,title: placeAdress}).bindPopup(popup);
            typeIcon = "fa fa-coffee";
            break;
        default:
            currentMark = L.marker([latitude,longitude],{title: placeAdress}).bindPopup(popup);
    }

    return {mark:currentMark,type:typeIcon};
};

mapPoint.prototype.codeAddress = function() {
    var _self = this;

    var title = document.getElementById("title").value;
    var address = document.getElementById("address").value;
    var link = document.getElementById("link").value;
    var comment = document.getElementById("comment").value;
    var travel = document.getElementById("travel").value;
    var user = document.getElementById("user").value;
    geocoder.geocode( { 'address': address}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            var e = document.getElementById("pointtype");
            var pointtype = e.options[e.selectedIndex].text;
            var latitude = results[0].geometry.location.lat();
            var longitude = results[0].geometry.location.lng();
            var place_id = results[0].place_id;
            var placeAdress = results[0].formatted_address;

            var typeIcon = "";
            var currentMark;
            var popup = "<b>"+placeAdress+"</b>";
            if (link!='') {
                popup = "<b><a href='"+link+"'>"+placeAdress+"</a></b><br/>" + comment;
            } else popup = "<b>"+placeAdress+"</b><br/>" + comment;

            var point = _self.getMark(pointtype,latitude,longitude,placeAdress,popup);
            point.mark.addTo(map);
            var location = {};
            location.placeAddress = placeAdress; //Mark address
            location.typeIcon = point.typeIcon;
            location.IdType =  e.options[e.selectedIndex].value;
            location.link = link;
            location.comment = comment;
            location.latitude = latitude;
            location.longitude = longitude;
            location.place_id = place_id;
            location.address = title; //Our title
            location.travel = travel;
            location.user = user;
            location.currentMark = null; //No podem serialitzar aquest objecte, el serialitzem desprès

            _self.save(location,point.currentMark); //Addind this point to map
            map.setView([latitude,longitude],8);

        } else {
            alert("Geocode was not successful for the following reason: " + status);
        }
    });
};


function __bind(fn, me) {
    return function () {
        return fn.apply(me, arguments);
    };
}