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
    this.editMark = __bind(this.editMark, this);


    mapPoint.prototype.plugin = {};

    var _elementDrop = $('#routePoints');
    _elementDrop.bind({ //Events for drop
        'drop': this.handleDrop,
        'dragend': this.handleDragEnd,
        'dragover': this.handleDragOver,
        'dragenter': this.handleDragEnter,
        'dragleave': this.handleDragLeave
    });

    /* Close any open dropdown when clicking outside */
    $(document).on('click.ptmenu', function () {
        document.querySelectorAll('.pt-menu__dropdown.is-open').forEach(function (d) { d.classList.remove('is-open'); });
    });

};

/***************************************************
 *
 * @param name: Name of the plugin must be unique
 * @param obj:plugin object
 ****************************************************/
mapPoint.prototype.addPlugin = function (name, obj) {
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

mapPoint.prototype.removeRoute = function () {
    map.removeControl(this.control);
    this.control = null;
};

mapPoint.prototype.calculateRoute = function () {
    var routePoints = $('#routePoints').find(".point-view");
    var _self = this;
    this.control = L.Routing.control(
        {
        routeWhileDragging: true
    }
    )

    $.each(routePoints, function (key, value) {
        var idPoint = $(value).data('id');
        var locationPoint = $('#' + idPoint).data('location');
        _self.control.spliceWaypoints(key, 2, L.latLng(locationPoint.latitude, locationPoint.longitude));
    });

    //  control.route();
    this.control.addTo(map);

};

mapPoint.prototype.panelItinerary = function () {
    if ($('.leaflet-routing-container') != 'undefined' && this.control != null) {
        if ($('.leaflet-routing-container').is(':visible')) {
            $('.leaflet-routing-container').hide()
        } else {
            $('.leaflet-routing-container').show();
        }
    }
};

//Create a location point object
mapPoint.prototype.createLocation = function (id, latitude, longitude, placeAddress, place_id, typeIcon, description, address, currentMark, url, IdType) {
    const l = {};
    l.id = id;
    l.latitude = latitude;
    l.longitude = longitude;
    l.placeAddress = placeAddress; //Mark address
    l.place_id = place_id;
    l.typeIcon = typeIcon;
    l.description = description;
    l.address = address;  //Our title
    l.currentMark = currentMark;
    l.url = url || '';
    l.IdType = IdType || '';

    return l;
};

mapPoint.prototype.addPoint = function (locationPoint) {

    var pid = locationPoint.place_id;

    var layerLoc =
        '<div class="point-view" id="layer_' + pid + '">' +
            '<div class="point-view__drag" draggable="true" id="' + pid + '">' +
                '<div class="point-view__icon">' +
                    '<i class="' + locationPoint.typeIcon + '"></i>' +
                '</div>' +
                '<div class="point-view__content">' +
                    '<span class="title-point"><b>' + locationPoint.address + '</b></span>' +
                    '<span class="address-point">' + locationPoint.placeAddress + '</span>' +
                '</div>' +
            '</div>' +
            '<div class="point-view__actions">' +
                '<div class="pt-menu" data-place="' + pid + '">' +
                    '<button type="button" class="pt-menu__trigger" data-place="' + pid + '" data-function="menu">' +
                        '<svg viewBox="0 0 16 16" width="16" height="16" fill="currentColor"><circle cx="8" cy="3" r="1.5"/><circle cx="8" cy="8" r="1.5"/><circle cx="8" cy="13" r="1.5"/></svg>' +
                    '</button>' +
                    '<div class="pt-menu__dropdown" id="ptmenu_' + pid + '">' +
                        '<button type="button" class="pt-menu__item" data-place="' + pid + '" data-function="go">' +
                            '<svg viewBox="0 0 16 16" width="16" height="16" fill="currentColor"><path d="M8 1a5 5 0 0 1 5 5c0 4-5 9-5 9S3 10 3 6a5 5 0 0 1 5-5zm0 3a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/></svg>' +
                            'Center on map' +
                        '</button>' +
                        '<button type="button" class="pt-menu__item" data-place="' + pid + '" data-function="info">' +
                            '<svg viewBox="0 0 16 16" width="16" height="16" fill="currentColor"><path d="M0 3h16v10H0V3zm1 1v8h14V4H1zm4 2a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm-1 5l2-3 2 2 2-3 3 4H3z"/></svg>' +
                            'View gallery' +
                        '</button>' +
                        '<button type="button" class="pt-menu__item" data-place="' + pid + '" data-function="nota">' +
                            '<svg viewBox="0 0 16 16" width="16" height="16" fill="currentColor"><path d="M2 2h12v12H2V2zm1 1v10h10V3H3zm2 2h6v1H5V5zm0 3h6v1H5V8zm0 3h4v1H5v-1z"/></svg>' +
                            'Add notes' +
                        '</button>' +
                        '<button type="button" class="pt-menu__item" data-place="' + pid + '" data-function="edit">' +
                            '<svg viewBox="0 0 16 16" width="16" height="16" fill="currentColor"><path d="M11.7 1.3a1 1 0 0 1 1.4 0l1.6 1.6a1 1 0 0 1 0 1.4L5.5 13.5 1 15l1.5-4.5 9.2-9.2z"/></svg>' +
                            'Edit location' +
                        '</button>' +
                        '<div class="pt-menu__divider"></div>' +
                        '<button type="button" class="pt-menu__item pt-menu__item--danger" data-place="' + pid + '" data-function="remove">' +
                            '<svg viewBox="0 0 16 16" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M3 4h10l-1 10H4L3 4zm4 0V2h2v2M1 4h14"/></svg>' +
                            'Delete location' +
                        '</button>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>';

    $('#mapPoints').append(layerLoc);
    var currentEl = document.getElementById(pid);
    if (currentEl) {
        $.data(currentEl, 'location', locationPoint);
        $(currentEl).bind({'dragstart': this.handleDragStart});
    }

    // Hide empty state
    var emptyState = document.getElementById('loc-empty-state');
    if (emptyState) { emptyState.style.display = 'none'; }

    $('*[data-place="' + pid + '"][data-function="go"]').bind({'click': this.goMark});
    $('*[data-place="' + pid + '"][data-function="remove"]').bind({'click': this.deleteMark});
    $('*[data-place="' + pid + '"][data-function="info"]').bind({'click': this.info});
    $('*[data-place="' + pid + '"][data-function="nota"]').bind({'click': this.nota});
    $('*[data-place="' + pid + '"][data-function="edit"]').bind({'click': this.editMark});

    /* Toggle dropdown */
    $('button[data-function="menu"][data-place="' + pid + '"]').bind('click', function (e) {
        e.stopPropagation();
        var drop = document.getElementById('ptmenu_' + pid);
        var isOpen = drop.classList.contains('is-open');
        document.querySelectorAll('.pt-menu__dropdown.is-open').forEach(function (d) { d.classList.remove('is-open'); });
        if (!isOpen) { drop.classList.add('is-open'); }
    });
};

mapPoint.prototype.save = function (locationPoint, currentMark) {
    //Serializing object point
    var _serializer = JSON.stringify(locationPoint);
    locationPoint.currentMark = currentMark;
    this.rest('POST', _serializer, locationPoint);

};

mapPoint.prototype.createButton = function (title, type, button_type, place_id) {
    var toggle = (title === 'edit') ? '' : 'data-toggle="modal" data-target="#' + title + '"';
    return '<button data-place="' + place_id + '" style="margin:1px" data-function="' + title + '" ' + toggle + ' type="button" class="btn ' + type + ' btn-xs">' + title + '</button>';
};

mapPoint.prototype.info = function (e) {
    var current = $(e.target);
    var placeToGo = current.data('place');
    var el = document.getElementById(placeToGo);
    var l = el ? $.data(el, 'location') : null;
    if (!l) { return; }

    $('#location').html(l.placeAddress);
    $('#location_media').addClass('is-visible').show();
    this.showGallery(l.id);
};

mapPoint.prototype.editMark = function (e) {
    var current = $(e.target);
    var placeToGo = current.data('place');
    var el = document.getElementById(placeToGo);
    var l = el ? $.data(el, 'location') : null;
    if (!l) { return; }

    $('#edit-location-id').val(l.id);
    $('#edit-title').val(l.address);
    $('#edit-link').val(l.url || '');
    $('#edit-comment').val(l.description || '');
    var typeSelect = document.getElementById('edit-pointtype');
    if (typeSelect && l.IdType) {
        for (var i = 0; i < typeSelect.options.length; i++) {
            if (typeSelect.options[i].value == l.IdType) {
                typeSelect.selectedIndex = i;
                break;
            }
        }
    }
    $('#editlocation').modal('show');
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

mapPoint.prototype.showGallery = function (l) {
    if (typeof (this.plugin['locationGallery'].getLocationImages) === "function") { //Si té init l utilitzem
        this.plugin['locationGallery'].getLocationImages(l);
    }
};


mapPoint.prototype.goMark = function (e) {
    var current = $(e.target);
    var placeToGo = current.data('place');
    var el = document.getElementById(placeToGo);
    var l = el ? $.data(el, 'location') : null;
    if (!l) { return; }
    map.setView([l.latitude, l.longitude], 30);
};


mapPoint.prototype.deleteMark = function (e) {
    var current = $(e.target);
    var placeToGo = current.data('place');
    var el = document.getElementById(placeToGo);
    var l = el ? $.data(el, 'location') : null;
    if (!l) { return; }
    map.removeLayer(l.currentMark);
    $('#layer_' + placeToGo).remove();
    this.rest('DELETE', null, l.id);
};

mapPoint.prototype.nota = function (e) {
    var current = $(e.target).closest('[data-place]');
    var placeToGo = current.data('place');
    var el = document.getElementById(placeToGo);
    var l = el ? $.data(el, 'location') : null;
    if (!l) { return; }

    var modal = document.getElementById('notesModal');
    if (!modal) { return; }
    modal.setAttribute('data-location-id', l.id);
    document.getElementById('notes-modal-title').textContent = l.address;
    document.getElementById('notes-list').innerHTML = '<div class="notes-loading">Loading notes…</div>';
    document.getElementById('note-content-input').value = '';
    var feedback = document.getElementById('notes-feedback');
    if (feedback) { feedback.style.display = 'none'; feedback.textContent = ''; }
    modal.classList.add('is-open');
    mapPoint._loadNotes(l.id);

    /* Wire up save button and close controls once (idempotent via flag) */
    if (!modal._notesWired) {
        modal._notesWired = true;

        var saveBtn = document.getElementById('notes-save-btn');
        var textarea = document.getElementById('note-content-input');
        var closeBtn = document.getElementById('nm-close');
        var overlay  = modal;

        saveBtn.addEventListener('click', function () {
            var content = textarea.value.trim();
            if (!content) {
                if (feedback) { feedback.textContent = 'Please write something first.'; feedback.style.display = 'block'; }
                return;
            }
            var locId = overlay.getAttribute('data-location-id');
            mapPoint._saveNote(locId, content);
        });

        textarea.addEventListener('keydown', function (ev) {
            if (ev.key === 'Enter' && (ev.ctrlKey || ev.metaKey)) {
                saveBtn.click();
            }
        });

        closeBtn.addEventListener('click', function () {
            overlay.classList.remove('is-open');
        });

        overlay.addEventListener('click', function (ev) {
            if (ev.target === overlay) { overlay.classList.remove('is-open'); }
        });

        document.addEventListener('keydown', function (ev) {
            if (ev.key === 'Escape' && overlay.classList.contains('is-open')) {
                overlay.classList.remove('is-open');
            }
        });
    }
};

/* ── Notes modal static helpers ── */
mapPoint._loadNotes = function (locationId) {
    var list = document.getElementById('notes-list');
    fetch('/api/location/' + locationId + '/notes', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
        var notes = data.notes || [];
        if (!notes.length) {
            list.innerHTML = '<div class="notes-empty">No notes yet. Add the first one below.</div>';
            return;
        }
        list.innerHTML = notes.map(function (n) {
            return '<div class="note-item" id="note-item-' + n.id + '">' +
                '<div class="note-item__body">' + mapPoint._renderNoteContent(n.content) + '</div>' +
                '<button class="note-item__delete" data-note-id="' + n.id + '" data-location-id="' + locationId + '" title="Delete">&#x2715;</button>' +
            '</div>';
        }).join('');
        list.querySelectorAll('.note-item__delete').forEach(function (btn) {
            btn.addEventListener('click', function () {
                mapPoint._deleteNote(this.dataset.noteId, this.dataset.locationId);
            });
        });
    })
    .catch(function () {
        list.innerHTML = '<div class="notes-empty">Could not load notes.</div>';
    });
};

mapPoint._renderNoteContent = function (content) {
    if (!content) { return ''; }
    var ytMatch = content.match(/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([\w-]{11})/);
    if (ytMatch) {
        var videoId = ytMatch[1];
        return '<div class="note-embed"><iframe src="https://www.youtube.com/embed/' + videoId + '" frameborder="0" allowfullscreen></iframe></div>' +
               '<p class="note-text">' + mapPoint._linkify(mapPoint._escHtml(content)) + '</p>';
    }
    var vmMatch = content.match(/(?:https?:\/\/)?(?:www\.)?vimeo\.com\/(\d+)/);
    if (vmMatch) {
        return '<div class="note-embed"><iframe src="https://player.vimeo.com/video/' + vmMatch[1] + '" frameborder="0" allowfullscreen></iframe></div>' +
               '<p class="note-text">' + mapPoint._linkify(mapPoint._escHtml(content)) + '</p>';
    }
    return '<p class="note-text">' + mapPoint._linkify(mapPoint._escHtml(content)) + '</p>';
};

mapPoint._linkify = function (text) {
    return text.replace(/(https?:\/\/[^\s<>"]+)/g, '<a href="$1" target="_blank" rel="noopener">$1</a>');
};

mapPoint._escHtml = function (s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
};

mapPoint._saveNote = function (locationId, content) {
    var feedback = document.getElementById('notes-feedback');
    var btn = document.getElementById('notes-save-btn');
    btn.disabled = true;
    fetch('/api/location/' + locationId + '/notes', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ content: content })
    })
    .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
    .then(function (result) {
        btn.disabled = false;
        if (result.ok) {
            document.getElementById('note-content-input').value = '';
            feedback.style.display = 'none';
            var modal = document.getElementById('notesModal');
            mapPoint._loadNotes(modal.getAttribute('data-location-id'));
        } else {
            feedback.textContent = (result.data && result.data.error) || 'Could not save note.';
            feedback.style.display = 'block';
        }
    })
    .catch(function () {
        btn.disabled = false;
        feedback.textContent = 'Network error.';
        feedback.style.display = 'block';
    });
};

mapPoint._deleteNote = function (noteId, locationId) {
    fetch('/api/location/' + locationId + '/notes/' + noteId, {
        method: 'DELETE',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function (r) {
        if (r.ok) {
            var el = document.getElementById('note-item-' + noteId);
            if (el) { el.remove(); }
            var list = document.getElementById('notes-list');
            if (list && !list.querySelector('.note-item')) {
                list.innerHTML = '<div class="notes-empty">No notes yet. Add the first one below.</div>';
            }
        }
    });
};


mapPoint.prototype.addNota = function () {
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

mapPoint.prototype.saveNota = function (nota) {
    var _serializer = JSON.stringify(nota);

    $.ajax({
        type: 'POST',
        contentType: 'application/json',
        url: '../api/annotations',
        dataType: 'json',
        data: _serializer,
        success: function (result) {
            console.log('Added note ' + result);
        },
        error: function (data, testStatus, jqXHR) {
            console.log('Error note ');
        }
    });
};

mapPoint.prototype.resetRoute = function () {
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
    if (_dataTransfer.getData('text') !== '') {
        var buttonRemove = '<button class="btn btn-danger btn-xs" data-place="' + _dataTransfer.getData('text') + '" id="remove-point-' + _dataTransfer.getData('text') + '">Remove</button>';
        var layerRoute = '<div class="point-view" id="route-' + _dataTransfer.getData('text') + '" data-route="true" data-id="' + _dataTransfer.getData('text') + '">' + $('#' + _dataTransfer.getData('text')).html() + ' ' + buttonRemove + '</div>';

        $('#routePoints').append(layerRoute);
        $('#routePoints').sortable();
        $('#remove-point-' + _dataTransfer.getData('text')).bind({
            'click': function () {
                $('#route-' + $(this).data('place')).remove();
            }
        })
    }
    return false;
};

mapPoint.prototype.handleDragEnter = function (e) {
    $('#routePoints').addClass('drop-target--active');
};

mapPoint.prototype.handleDragOver = function (e) {
    if (e.preventDefault) {
        e.preventDefault();
    }
    var _dataTransfer = e.originalEvent.dataTransfer;
    _dataTransfer.dropEffect = 'move';
    $('#routePoints').addClass('drop-target--active');
    return false;
};

mapPoint.prototype.handleDragEnd = function (e) {
    $('#routePoints').removeClass('drop-target--active');
};

mapPoint.prototype.handleDragLeave = function (e) {
    /* Only remove highlight when leaving the container entirely, not a child */
    if (!$('#routePoints')[0].contains(e.relatedTarget || e.toElement)) {
        $('#routePoints').removeClass('drop-target--active');
    }
};

//Rest API for Traveling
mapPoint.prototype.rest = function (typeRest, data, locationPoint) {
    var _self = this;

    if (typeRest === 'POST') {
        $.ajax({
            type: typeRest,
            contentType: 'application/json',
            url: '../../api/user/' + locationPoint.user + '/location',
            dataType: 'json',
            data: data,
            success: function (data, testStatus, jqXHR) {
                locationPoint.id = data && data.id ? data.id : data;
                _self.addPoint(locationPoint);
                $('#infoForm').html('<p class="alert alert-success">Location Added</p>');
                $("#infoForm").show().delay(5000).fadeOut();
                var fileInput = document.getElementById('add-image-file');
                if (fileInput && fileInput.files && fileInput.files.length > 0 && locationPoint.id) {
                    var formData = new FormData();
                    formData.append('file', fileInput.files[0]);
                    $.ajax({
                        type: 'POST',
                        url: '../../api/location/' + locationPoint.id + '/image',
                        data: formData,
                        processData: false,
                        contentType: false
                    });
                    fileInput.value = '';
                }
            },
            error: function (data, testStatus, jqXHR) {
                $('#infoForm').html('<p class="alert alert-danger">Error: Location not Added</p>');
                $("#infoForm").show().delay(5000).fadeOut();
            }
        });
    } else if (typeRest === 'DELETE') {
        $.ajax({
            type: typeRest,
            url: '../../api/travel/' + this.travel + '/location/' + locationPoint,
            success: function (result) {
                $('#infoTravel').html('<p class="alert alert-success">Location Removed</p>');
                $("#infoTravel").show().delay(5000).fadeOut();
                console.log('Removing point ' + result);
            },
            error: function (data, testStatus, jqXHR) {
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

mapPoint.prototype.returnTown = function (results) {
    var level_1;
    var level_2;
    for (var y = 0, length_2 = results.address_components.length; y < length_2; y++) {
        var type = results.address_components[y].types[0];
        if (type === "administrative_area_level_1") {
            level_1 = results.address_components[y].long_name;
            if (level_2) {
                break;
            }
        } else if (type === "locality") {
            level_2 = results.address_components[y].long_name;
            if (level_1) {
                break;
            }
        }
    }

    return [level_2, level_1];
};

mapPoint.prototype.getMark = function (pointtype, latitude, longitude, placeAdress, popup) {
    var currentMark;
    var typeIcon;

    switch (pointtype) {
        case "House":
            currentMark = L.marker([latitude, longitude], {icon: HouseMarker, title: placeAdress}).bindPopup(popup);
            typeIcon = "fa fa-bed";
            break;
        case "Airport":
            currentMark = L.marker([latitude, longitude], {icon: AirportMarker, title: placeAdress}).bindPopup(popup);
            typeIcon = "fa fa-plane";
            break;
        case "Monument":
            currentMark = L.marker([latitude, longitude], {icon: MonumentMarker, title: placeAdress}).bindPopup(popup);
            typeIcon = "fa fa-camera";
            break;
        case "City":
            currentMark = L.marker([latitude, longitude], {icon: CityMarker, title: placeAdress}).bindPopup(popup);
            typeIcon = "fa fa-building";
            break;
        case "Lunch":
            currentMark = L.marker([latitude, longitude], {icon: LunchMarker, title: placeAdress}).bindPopup(popup);
            typeIcon = "fa fa-cutlery";
            break;
        case "Bicycle":
            currentMark = L.marker([latitude, longitude], {icon: BicycleMarker, title: placeAdress}).bindPopup(popup);
            typeIcon = "fa fa-bicycle";
            break;
        case "Bus":
            currentMark = L.marker([latitude, longitude], {icon: BusMarker, title: placeAdress}).bindPopup(popup);
            typeIcon = "fa fa-bus";
            break;
        case "Automobile":
            currentMark = L.marker([latitude, longitude], {
                icon: AutomobileMarker,
                title: placeAdress
            }).bindPopup(popup);
            typeIcon = "fa fa-automobile";
            break;
        case "Train":
            currentMark = L.marker([latitude, longitude], {icon: TrainMarker, title: placeAdress}).bindPopup(popup);
            typeIcon = "fa fa-train";
            break;
        case "Ship":
            currentMark = L.marker([latitude, longitude], {icon: ShipMarker, title: placeAdress}).bindPopup(popup);
            typeIcon = "fa fa-ship";
            break;
        case "Coffee":
            currentMark = L.marker([latitude, longitude], {icon: BreakfastMarker, title: placeAdress}).bindPopup(popup);
            typeIcon = "fa fa-coffee";
            break;
        default:
            currentMark = L.marker([latitude, longitude], {title: placeAdress}).bindPopup(popup);
    }

    return {mark: currentMark, type: typeIcon};
};

mapPoint.prototype.saveEdit = function () {
    var _self = this;
    var locationId = document.getElementById('edit-location-id').value;
    var title = document.getElementById('edit-title').value;
    var link = document.getElementById('edit-link').value;
    var comment = document.getElementById('edit-comment').value;
    var typeSelect = document.getElementById('edit-pointtype');
    var typeLocationId = typeSelect ? typeSelect.options[typeSelect.selectedIndex].value : null;

    var payload = {
        title: title,
        url: link,
        description: comment,
        typeLocationId: typeLocationId
    };

    var fileInput = document.getElementById('edit-image-file');
    var hasFile = fileInput && fileInput.files && fileInput.files.length > 0;

    $.ajax({
        type: 'PATCH',
        contentType: 'application/json',
        url: '../../api/location/' + locationId,
        dataType: 'json',
        data: JSON.stringify(payload),
        success: function () {
            $('#infoTravel').html('<p class="alert alert-success">Location Updated</p>');
            $('#infoTravel').show().delay(5000).fadeOut();
            $('#mapPoints .point-view').each(function () {
                var inner = this.querySelector('[id]');
                if (inner) {
                    var l = $.data(inner, 'location');
                    if (l && l.id === locationId) {
                        var b = inner.querySelector('.title-point b');
                        if (b) { b.textContent = title; }
                        l.address = title;
                        l.url = link;
                        l.description = comment;
                    }
                }
            });

            if (hasFile) {
                var formData = new FormData();
                formData.append('file', fileInput.files[0]);
                $.ajax({
                    type: 'POST',
                    url: '../../api/location/' + locationId + '/image',
                    data: formData,
                    processData: false,
                    contentType: false,
                    complete: function () {
                        $('#editlocation').modal('hide');
                    }
                });
            } else {
                $('#editlocation').modal('hide');
            }
        },
        error: function () {
            $('#infoTravel').html('<p class="alert alert-danger">Error: Location not Updated</p>');
            $('#infoTravel').show().delay(5000).fadeOut();
        }
    });
};

mapPoint.prototype.codeAddress = function () {
    const _self = this;

    var title = document.getElementById("title").value;
    var placeAdress = document.getElementById("address").value;
    var link = document.getElementById("link").value;
    var comment = document.getElementById("comment").value;
    var travel = document.getElementById("travel").value;
    var user = document.getElementById("user").value;
    var latitude = document.getElementById("latPoint").value;
    var longitude = document.getElementById("lngPoint").value;
    var place_id = document.getElementById("placeId").value;
    var e = document.getElementById("pointtype");
    var pointtype = e.options[e.selectedIndex].text;

    var typeIcon = "";
    var currentMark;
    var popup = "<b>" + placeAdress + "</b>";
    if (link !== '') {
        popup = "<b><a href='" + link + "'>" + placeAdress + "</a></b><br/>" + comment;
    } else {
        popup = "<b>" + placeAdress + "</b><br/>" + comment;
    }

    var point = _self.getMark(pointtype, latitude, longitude, placeAdress, popup);
    point.mark.addTo(map);
    var location = {};
    location.placeAddress = placeAdress; //Mark address
    location.typeIcon = point.typeIcon;
    location.IdType = e.options[e.selectedIndex].value;
    location.link = link;
    location.comment = comment;
    location.latitude = latitude;
    location.longitude = longitude;
    location.place_id = place_id;
    location.address = title; //Our title
    location.travel = travel;
    location.user = user;
    location.currentMark = null; //No podem serialitzar aquest objecte, el serialitzem desprès

    _self.save(location, point.currentMark); //Addind this point to map
    map.setView([latitude, longitude], 8);
};


function __bind(fn, me) {
    return function () {
        return fn.apply(me, arguments);
    };
}
