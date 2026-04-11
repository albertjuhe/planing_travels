"use strict";

var locationGallery = function (pGallery, isOwner) {

    this.pathGallery = pGallery;
    this.galleryZone = 'dz-ImageLocation';
    this.notesZone = 'notes';
    this.isOwner = !!isOwner;
    this.currentLocationId = null;
};

locationGallery.prototype.getLocationImages = function (location) {
    var _self = this;
    this.currentLocationId = location;
    $("#dz-ImageLocation").empty();
    $("#notes").empty();

    if (_self.isOwner) {
        var uploadHtml = '<div id="loc-upload-wrap" style="margin-bottom:10px;">' +
            '<label style="font-size:12px;font-weight:700;color:#7f8c8d;text-transform:uppercase;letter-spacing:.4px;display:block;margin-bottom:4px;">Upload image</label>' +
            '<input type="file" id="loc-image-input" accept="image/*" style="font-size:13px;"/>' +
            '<button id="loc-image-upload-btn" type="button" class="st-btn st-btn--primary" style="margin-top:6px;font-size:12px;padding:4px 12px;">' +
            '<svg viewBox="0 0 16 16" width="11" height="11" fill="currentColor"><path d="M8 1v14M1 8h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg> Add image</button>' +
            '<div id="loc-upload-feedback" style="font-size:12px;margin-top:4px;"></div>' +
            '</div>';
        $("#dz-ImageLocation").append(uploadHtml);
        $("#loc-image-upload-btn").on("click", function () {
            _self.uploadImage(location);
        });
    }

    $.ajax({
        contentType: 'application/json',
        url: '../../api/locations/' + location,
        dataType: 'json',
        success: function (data, testStatus, jqXHR) {
            $.each(data.images, function (index, value) {
                $('#' + _self.galleryZone).append('<img class="travelimg" src="' + _self.pathGallery + value.filename + '" style="max-width:100%;margin-bottom:6px;border-radius:6px;"/>');
            });
            $.each(data.notes, function (index, value) {
                $('#' + _self.notesZone).append('<p id="note' + value.id + '" style="margin: 10px;">' + '<span class="title-point">' + value.title + '</span> ' + '<i style="font-size:11px">' + value.description + '</i> </p>');
            });
        },
        error: function (data, testStatus, jqXHR) {
            console.log('error');
        }
    });
};

locationGallery.prototype.uploadImage = function (locationId) {
    var _self = this;
    var fileInput = document.getElementById('loc-image-input');
    var feedback = document.getElementById('loc-upload-feedback');

    if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
        if (feedback) { feedback.style.color = '#e74c3c'; feedback.textContent = 'Please select a file first.'; }
        return;
    }

    var formData = new FormData();
    formData.append('file', fileInput.files[0]);

    if (feedback) { feedback.style.color = '#7f8c8d'; feedback.textContent = 'Uploading…'; }

    $.ajax({
        type: 'POST',
        url: '../../api/location/' + locationId + '/image',
        data: formData,
        processData: false,
        contentType: false,
        success: function (data) {
            if (feedback) { feedback.style.color = '#27ae60'; feedback.textContent = 'Image uploaded.'; }
            fileInput.value = '';
            var img = '<img class="travelimg" src="' + _self.pathGallery + data.filename + '" style="max-width:100%;margin-bottom:6px;border-radius:6px;"/>';
            $('#' + _self.galleryZone).append(img);
        },
        error: function () {
            if (feedback) { feedback.style.color = '#e74c3c'; feedback.textContent = 'Upload failed.'; }
        }
    });
};
