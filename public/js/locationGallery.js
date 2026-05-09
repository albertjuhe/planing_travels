"use strict";

var locationGallery = function (pGallery, isOwner) {
    this.pathGallery = pGallery;
    this.galleryZone = 'dz-ImageLocation';
    this.notesZone = 'notes';
    this.isOwner = !!isOwner;
    this.currentLocationId = null;
};

/* ── Gallery ── */

locationGallery.prototype.getLocationImages = function (location) {
    var _self = this;
    this.currentLocationId = location;
    var $zone = $('#' + this.galleryZone);

    $zone.empty();
    $("#notes").empty();

    if (_self.isOwner) {
        $zone.append(_self._buildUploadHtml());
        $('#loc-image-input').on("change", function () {
            _self.uploadImage(location);
        });
    }

    $.ajax({
        contentType: 'application/json',
        url: '../../api/location/' + location + '/images',
        dataType: 'json',
        success: function (data) {
            $.each(data.images, function (index, value) {
                var $item = _self._buildImageItem(value.filename);
                $item.on('click', function () {
                    _self._openLightbox(_self.pathGallery + value.filename);
                });
                $zone.append($item);
            });
        },
        error: function () {}
    });

    /* Load notes */
    fetch('../../api/location/' + location + '/notes', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function (r) { return r.ok ? r.json() : null; })
    .then(function (data) {
        if (!data || !data.notes || !data.notes.length) { return; }
        data.notes.forEach(function (n) {
            var html = locationGallery._renderNoteContent(n.content);
            $('#' + _self.notesZone).append('<div class="note-item" style="margin-bottom:6px;">' + html + '</div>');
        });
    })
    .catch(function () {});
};

locationGallery.prototype._buildUploadHtml = function () {
    return '<div class="st-gallery-upload" id="loc-upload-wrap">' +
        '<div class="st-gallery-upload__icon">' +
        '<svg viewBox="0 0 16 16" width="16" height="16" fill="currentColor"><path d="M8 1v14M1 8h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>' +
        '</div>' +
        '<div class="st-gallery-upload__label">Add image</div>' +
        '<p class="st-gallery-upload__hint">Click to select or drag &amp; drop</p>' +
        '<input type="file" id="loc-image-input" accept="image/*"/>' +
        '<div class="st-gallery-upload__feedback" id="loc-upload-feedback"></div>' +
        '</div>';
};

locationGallery.prototype._buildImageItem = function (filename) {
    return $(
        '<div class="st-gallery__item">' +
        '<img src="' + this.pathGallery + filename + '" loading="lazy"/>' +
        '</div>'
    );
};

locationGallery.prototype._openLightbox = function (src) {
    var lb = document.getElementById('st-lightbox');
    var img = document.getElementById('st-lightbox-img');
    if (!lb || !img) { return; }
    img.src = src;
    lb.classList.add('is-open');
};

/* ── Upload ── */

locationGallery.prototype.uploadImage = function (locationId) {
    var _self = this;
    var fileInput = document.getElementById('loc-image-input');
    var feedback = document.getElementById('loc-upload-feedback');

    if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
        if (feedback) { feedback.style.color = '#e74c3c'; feedback.textContent = 'Select a file first.'; }
        return;
    }

    var formData = new FormData();
    formData.append('file', fileInput.files[0]);

    if (feedback) { feedback.style.color = '#7f8c8d'; feedback.textContent = 'Uploading\u2026'; }

    $.ajax({
        type: 'POST',
        url: '../../api/location/' + locationId + '/image',
        data: formData,
        processData: false,
        contentType: false,
        success: function (data) {
            if (feedback) { feedback.style.color = '#27ae60'; feedback.textContent = 'Uploaded!'; }
            fileInput.value = '';
            var $item = _self._buildImageItem(data.filename);
            $item.on('click', function () {
                _self._openLightbox(_self.pathGallery + data.filename);
            });
            $('#' + _self.galleryZone).append($item);
            setTimeout(function () {
                if (feedback) { feedback.textContent = ''; }
            }, 2000);
        },
        error: function () {
            if (feedback) { feedback.style.color = '#e74c3c'; feedback.textContent = 'Upload failed.'; }
        }
    });
};

/* ── Lightbox ── */

$(document).on('click', '#st-lightbox-close', function () {
    document.getElementById('st-lightbox').classList.remove('is-open');
});
$(document).on('click', '#st-lightbox', function (e) {
    if (e.target === this) {
        this.classList.remove('is-open');
    }
});
$(document).on('keydown', function (e) {
    if (e.key === 'Escape') {
        var lb = document.getElementById('st-lightbox');
        if (lb) { lb.classList.remove('is-open'); }
    }
});

/* ── Notes rich renderer ── */

locationGallery._escHtml = function (s) {
    return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
};
locationGallery._linkify = function (text) {
    return text.replace(/(https?:\/\/[^\s<>"]+)/g, '<a href="$1" target="_blank" rel="noopener">$1</a>');
};
locationGallery._renderNoteContent = function (content) {
    if (!content) { return ''; }
    var ytMatch = content.match(/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([\w-]{11})/);
    if (ytMatch) {
        return '<div class="note-embed"><iframe src="https://www.youtube.com/embed/' + ytMatch[1] + '" frameborder="0" allowfullscreen></iframe></div>' +
            '<p class="note-text">' + locationGallery._linkify(locationGallery._escHtml(content)) + '</p>';
    }
    var vmMatch = content.match(/(?:https?:\/\/)?(?:www\.)?vimeo\.com\/(\d+)/);
    if (vmMatch) {
        return '<div class="note-embed"><iframe src="https://player.vimeo.com/video/' + vmMatch[1] + '" frameborder="0" allowfullscreen></iframe></div>' +
            '<p class="note-text">' + locationGallery._linkify(locationGallery._escHtml(content)) + '</p>';
    }
    return '<p class="note-text">' + locationGallery._linkify(locationGallery._escHtml(content)) + '</p>';
};
