"use strict";

//drag and drop lead positions
$(document).ready(function () {
    var container = document.getElementById('main-menu-td-container');
    var stagesDraggable = dragula([container]);

    //make every board dragable area
    stagesDraggable.on('drag', function (stage) {
        // add 'is-moving' class to element being dragged
        stage.classList.add('is-moving');
    });
    stagesDraggable.on('dragend', function (stage) {
        // remove 'is-moving' class from element after dragging has stopped
        stage.classList.remove('is-moving');
        // add the 'is-moved' class for 600ms then remove it
        window.setTimeout(function () {
            stage.classList.add('is-moved');
            window.setTimeout(function () {
                stage.classList.remove('is-moved');
            }, 600);
        }, 100);

        //update the list
        nxAjaxUxRequest($("#main-menu-list-table"));

    });
});