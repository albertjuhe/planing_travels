"use strict";

var locationGallery = function (pGallery) {

    this.pathGallery = pGallery;
    this.galleryZone = 'dz-ImageLocation';
    this.notesZone= 'notes';
};

locationGallery.prototype.getLocationImages = function(location) {
    var _self = this;
    $("#dz-ImageLocation").empty();
    $("#notes").empty();
    $.ajax({
        contentType: 'application/json',
        url: '../../api/locations/' + location,
        dataType:'json',
        success: function(data,testStatus,jqXHR) {
            $.each(data.images,function(index,value) {
                $('#' + _self.galleryZone).append('<img class="travelimg" src="' + _self.pathGallery +value.filename+'"/>');
            });
            $.each(data.notes,function(index,value) {
                $('#' + _self.notesZone).append('<p id="note'+value.id+'" style="margin: 10px;">'+'<span class="title-point">'+value.title+'</span> '+ '<i style="font-size:11px">'+value.description+'</i> </p>');
            });
            //console.log('Images ' + data);
        },
        error: function(data,testStatus,jqXHR) {
            console.log('error');
        },
        beforeSend: function(){
            $("#loaderDiv").show();
        }
    });
 };