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
        uploadKey: '',
        init: function() {
            var self = this;
            var $el = self.$el;

            self.uploadKey = self.$el.data('uploadKey');

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
                    $el.addClass('AnselField--IsUploading');
                    self.processFiles(e.originalEvent.dataTransfer.files);
                });
        },

        processFiles: function(filesArray) {
            console.log(filesArray);
        }
    });
}

runField(window.ANSEL);
