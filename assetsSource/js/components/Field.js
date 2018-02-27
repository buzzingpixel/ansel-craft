window.ANSEL = window.ANSEL || {};

function runField(F) {
    'use strict';

    if (! window.jQuery || ! F.controller || ! F.model) {
        setTimeout(function() {
            runField(F);
        }, 10);
        return;
    }

    F.controller.make('Field', {
        init: function() {
            var self = this;
            var $el = self.$el;

            $el.on(
                'drag dragstart dragend dragover dragenter dragleave drop',
                function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                })
                .on('dragover dragenter', function() {
                    $el.addClass('AnselField--DragInProgress');
                })
                .on('dragleave dragend drop', function() {
                    $el.removeClass('AnselField--DragInProgress');
                })
                .on('drop', function(e) {
                    console.log(e.originalEvent.dataTransfer.files);
                    $el.addClass('AnselField--IsUploading');
                });
        },

        processFile: function(e) {
            e.preventDefault();
            console.log(e);
        }
    });
}

runField(window.ANSEL);
