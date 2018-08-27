"use strict";

var shareTravel = function (pGallery) {
};

shareTravel.prototype.shareAdd = function(user) {
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

