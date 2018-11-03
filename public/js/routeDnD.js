"use strict";

var gapMatchInteraction = function () {
    //The choice type in JSON must be the same than this type
    this.dragSrcEl = null;


    /*
     dragstart:
     Fired on an element when a drag is started. The user is requesting to drag the element where the dragstart event is fired. During this event, a listener would set information such as the drag data and image to be associated with the drag. This event is not fired when dragging a file into the browser from the OS. For information about this, see Starting a Drag Operation.
     dragenter:
     Fired when the mouse enters an element while a drag is occurring. A listener for this event should indicate whether a drop is allowed over this location. If there are no listeners, or the listeners perform no operations, then a drop is not allowed by default. This is also the event to listen for in order to provide feedback that a drop is allowed, such as displaying a highlight or insertion marker. For information about this, see Specifying Drop Targets.
     dragover:
     This event is fired as the mouse is moving over an element when a drag is occurring. Much of the time, the operation that occurs during a listener will be the same as the dragenter event. For information about this, see Specifying Drop Targets.
     dragleave:
     This event is fired when the mouse leaves an element while a drag is occurring. Listeners should remove any highlighting or insertion markers used for drop feedback.
     drag:
     This event is fired at the source of the drag and is the element where dragstart was fired during the drag operation.
     drop:
     The drop event is fired on the element where the drop occurred at the end of the drag operation. A listener would be responsible for retrieving the data being dragged and inserting it at the drop location. This event will only fire if a drop is desired. It will not fire if the user cancelled the drag operation, for example by pressing the Escape key, or if the mouse button was released while the mouse was not over a valid drop target. For information about this, see Performing a Drop.
     dragend:
     The source of the drag will receive a dragend event when the drag operation is complete, whether it was successful or not. This event is not fired when dragging a file into the browser from the OS. For more information about this, see Finishing a Drag.
     */

    this.handleDragStart = __bind(this.handleDragStart, this);
    this.handleDragEnd = __bind(this.handleDragEnd, this);
    this.handleDragEnter = __bind(this.handleDragEnter, this);
    this.handleDragOver = __bind(this.handleDragOver, this);
    this.handleDragLeave = __bind(this.handleDragLeave, this);
    this.handleDrop = __bind(this.handleDrop, this);
    this.dragableAndDropable = __bind(this.dragableAndDropable, this);

};

gapMatchInteraction.prototype.init = function () {
    console.log("Init " + this.type + " !!!");
};

/*
 *  @param value: assessmentItem
 *  @param questionId: the id of the form
 */
gapMatchInteraction.prototype.render = function(questionId,value,index) {
    var itemBody = "<div class=\"itemBody\"><span class=\"number_question\">"+ index +".</span>";
    var _value = $(value).find('itemBody');
    var _self = this;

    //Convert especial nodes textInteraction or inlineChoiceInteraction
    this.convertElements($(_value).find('gapMatchInteraction'));
    var gapText = $(_value).find('gapText');
    var boxes = "";
    $.each( gapText , function( key, value ) {
        boxes += '<span class="gapBox" draggable="true" data-group="'+questionId+'" id="'+ $(value).attr('identifier') + '"><b>' + __nodeToString(value)  + '</b></span>';
    });

    var itemBodyNodes = _value[0].childNodes; //Itembody contains questions under the tag
    if (typeof _value !== typeof undefined && _value !== false) {
        for(var i=0; i < itemBodyNodes.length; i++) {
            if (itemBodyNodes[i].nodeName != 'gapMatchInteraction') {
                /*
                 Node.ELEMENT_NODE 	1 	An Element node such that <p> or <div>.
                 Node.TEXT_NODE 	3 	The actual Text of Element or Attr.
                 */
                if (itemBodyNodes[i].nodeType==3) {
                    itemBody += itemBodyNodes[i].nodeValue;
                } else if (itemBodyNodes[i].nodeType==1) {
                    itemBody += __nodeToString(itemBodyNodes[i])
                } else console.log('Node error type in the itembody render');

            } else {
                //itemBodyNodes[i].getElementsByName("gaptext").remove();
                itemBody += itemBodyNodes[i].innerHTML;
            }
        }
    }
    itemBody += "</div>";
    //Building HTML Question
    var q = '#' + questionId;
    $(q).append('<div class="prompt">' + itemBody+ '</div>');
    $(q).append('<div id="'+questionId+'_question"></div>');
    $(q + '_question').append(boxes);

    this.dragableAndDropable(_self);

    //Put the answers
    var responseDeclaration = $(value).find('responseDeclaration');

    $.each( responseDeclaration , function( key, value ) {
        var _responseCorrect = [];
        var idResponse = $(value).attr('identifier');
        $.each( $(value).find('value') , function( key, value ) {
            _responseCorrect.push($(value).text());
        });
        $(q).data('solution',_responseCorrect);
    });

};

gapMatchInteraction.prototype.dragableAndDropable = function(self) {
    var _self = self;
    var _elementDrag = $('.gapBox');
    _elementDrag.bind({ //Events for drag
        'dragstart': _self.handleDragStart
    });

    var _elementDrop = $('.gap');
    _elementDrop.bind({ //Events for drop
        'drop':      _self.handleDrop,
        'dragend':   _self.handleDragEnd,
        'dragover':  _self.handleDragOver,
        'dragenter': _self.handleDragEnter,
        'dragleave': _self.handleDragLeave
    });
};

gapMatchInteraction.prototype.convertElements = function(value) {
    /*
     <input class="gapMatchInteractions" data-response="RESPONSE_02" xmlns="http://www.w3.org/1999/xhtml">
     */
    var gapText = $(value).find('gap');
    $.each( gapText , function( key, value ) {
        var textEntry = value;
        var gapEntry = document.createElement("span");
        gapEntry.setAttribute("id", $(value).attr('identifier'));
        gapEntry.setAttribute("class", "gap");
        gapEntry.innerHTML = "_______________";
        textEntry.parentNode.replaceChild(gapEntry, textEntry);
    });

    /*
     <select class="gapMatchInteractions" data-response="RESPONSE_02" name="RESPONSE_02">
     <option value="a">a</option>
     <option value="b">b</option>
     <option value="c">c</option>
     <option value="d">d</option>
     </select>

     var inlineChoiceInteraction = $(value).find('inlineChoiceInteraction');
     $.each( inlineChoiceInteraction , function( key, value ) {
     var textEntry = value;
     var selectEntry = document.createElement("select");
     selectEntry.setAttribute("data-response", $(value).attr('responseIdentifier'));
     selectEntry.setAttribute("class", "gapMatchInteractions");
     textEntry.parentNode.replaceChild(selectEntry, textEntry);
     $.each($(value).find('inlineChoice'), function( key, value ) {
     $(selectEntry).append('<option value="'+$(value).attr('identifier')+'">'+$(value).attr('identifier')+'</option>');
     });
     });
     */
};

//Solutions
gapMatchInteraction.prototype.solution = function (question) {
    var _solutions = $(question).data('solution'); //Real Solutions
    var _gapSolutions = $(question).find( '.gapBox');
    var correct = 0;

    for(var i= 0; i < _gapSolutions.length; i++) {
        if ($(_gapSolutions[i]).data('target')!=undefined)
        {
            var solutionValue = _gapSolutions[i].id + " " + $(_gapSolutions[i]).data('target');
            if ($.inArray(solutionValue, _solutions) === -1) {
                $(_gapSolutions[i]).addClass("incorrect");
            }
            else {
                $(_gapSolutions[i]).addClass("correct");
                correct++;
            }
        }
    }

    return (correct==_solutions.length ? 1:0);
};

//check this question
gapMatchInteraction.prototype.checkQuestion = function (question) {
    var _solutions = $(question).data('solution'); //Real Solutions
    var _gapSolutions = $(question).find( '.gapBox');
    var correct = 0;

    for(var i= 0; i < _gapSolutions.length; i++) {
        if ($(_gapSolutions[i]).data('target')!=undefined)
        {
            var solutionValue = _gapSolutions[i].id + " " + $(_gapSolutions[i]).data('target');
            if ($.inArray(solutionValue, _solutions) === -1) {
                $(_gapSolutions[i]).addClass("incorrect");
            }
            else {
                $(_gapSolutions[i]).addClass("correct");
                correct++;
            }
        }
    }

    return (correct==_solutions.length ? 1:0);
};

gapMatchInteraction.prototype.handleDrop = function (e) {
    var idTemp;

    if (e.stopPropagation) {
        e.stopPropagation(); // stops the browser from redirecting.
    }
    var _dataTransfer = e.originalEvent.dataTransfer;
    console.log('Drop');
    if ( this.dragSrcEl != e.target) {
        var idTemp = this.dragSrcEl.id;
        var _elementId = e.target.id;

        $('#' + _elementId).replaceWith( $('#' + idTemp) );
        $('#' + idTemp).data('target',_elementId);

    }
    return false;
};

//Start dragging, change opacity
gapMatchInteraction.prototype.handleDragStart = function (e) {
    this.dragSrcEl = e.target;
    var _dataTransfer = e.originalEvent.dataTransfer;

    _dataTransfer.setData('text/html', e.target.innerHTML);

};

gapMatchInteraction.prototype.handleDragEnter = function (e) {
    console.log('Enter dragging');
};

gapMatchInteraction.prototype.handleDragOver = function (e) {
    if (e.preventDefault) {
        e.preventDefault(); // Necessary. Allows us to drop.
    }
    var _dataTransfer = e.originalEvent.dataTransfer;
    _dataTransfer.dropEffect = 'move';
    return false;
};


gapMatchInteraction.prototype.handleDragEnd = function (e) {
};

gapMatchInteraction.prototype.handleDragLeave = function (e) {

};

/*
 Remove Events and make an object undraggable.
 */
gapMatchInteraction.prototype.unlashed = function(layer) {
    var _self = this;

    $(layer ).unbind({ //remove Events for drag and drop
        'dragenter': _self.handleDragEnter,
        'dragover':  _self.handleDragOver,
        'dragleave': _self.handleDragLeave,
        'drop':      _self.handleDrop,
        'dragstart': _self.handleDragStart,
        'dragend':   _self.handleDragEnd
    });
    $(layer).attr('draggable',false);
};

